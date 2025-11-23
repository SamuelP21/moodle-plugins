<?php

namespace blocks\services;

use core_completion\progress;
use \DateTime;

defined('MOODLE_INTERNAL') || die();

/**
 * Servicio relacionado con los Cursos
 */
class CourseService
{
    /**
     * Número máximo de cursos sugeridos por categoría
     */
    const MAX_COURSES = 3;

    /**
     * Obtiene los datos de los cursos en los que el usuario está inscrito
     * 
     * @return array|null Datos de los cursos en los que el usuario está inscrito
     */
    public static function get_user_courses_data(): array|null
    {
        global $DB, $USER, $CFG, $OUTPUT;

        static $courses;

        if ($courses !== null) {

            return $courses;
        }

        // Obtiene los cursos en los que el usuario está inscrito

        $courses = enrol_get_my_courses(fields: ['summary', 'enddate'], sort: 'startdate ASC');

        //debug($courses, true);

        // Modificamos los datos de los cursos para agregar la URL, la imagen, el progreso del curso, y si el curso está finalizado
        // También agregamos la cantidad de notificaciones de tareas pendientes de calificar en el curso si el usuario no es un estudiante
        foreach ($courses as &$course) {

            if (self::get_enrol_assigment($course->id) !== 'student') {

                $course->notifications = self::get_count_notifications($course->id);
            }

            $course->summary = format_text($course->summary, FORMAT_HTML);
            $course->url = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
            $course->image = self::get_course_image_url($course);
            $course->progress = self::get_course_progress($course, $USER->id);

            $course->is_incomplete = $course->progress < 100;

            // Verificar si el usuario es estudiante en el curso
            // Se obtiene el contexto del curso actual
            $context = \context_course::instance($course->id);
            // Obtenemos el rol del usuario
            $role = array_values(get_user_roles($context, $USER->id, true));
            // Verificamos si el usuario no es un estudiante(sino es un estudiante le enviamos la notificacion)
            if ($role[0]->shortname != 'student') {
                $course->noti = self::get_count_noti($course->id);
            }

            $course->completed_courses_date = new DateTime('now') > (new DateTime())->setTimestamp($course->enddate);
            $course = (array) $course;
        }

        $courses = array_values($courses);

        return $courses;
    }

    /**
     * Obtiene los cursos sugeridos en los que el usuario no está inscrito
     * 
     * Utiliza las categorías de los cursos en los que el usuario está inscrito para sugerir cursos en las mismas categorías
     * 
     * @return array|null Cursos sugeridos en los que el usuario no está inscrito
     * 
     * @example Uses: 
     *          - {course}  almacena información sobre todos los cursos en Moodle
     *          - {enrol} almacena información sobre los métodos de inscripción para los cursos
     *          - {user_enrolments} almacena información sobre las inscripciones de los usuarios a los cursos
     */
    public static function get_courses_user_not_in()
    {
        global $DB, $USER, $CFG, $OUTPUT;

        static $cursos;

        if ($cursos !== null) {

            return $cursos;
        }

        // Parámetros de la consulta
        $params = ['userid1' => $USER->id, 'userid2' => $USER->id];

        // Consulta SQL para obtener los cursos en los que el usuario no está inscrito en las mismas categorías de los cursos en los que está inscrito
        $query = "
            SELECT c.id AS id, c.fullname, c.summary, c.category
            FROM {course} c
            WHERE c.category IN (
                SELECT DISTINCT c.category
                FROM {course} c
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE ue.userid = :userid1
            )
            AND c.id NOT IN (
                SELECT e.courseid
                FROM {user_enrolments} ue
                JOIN {enrol} e ON e.id = ue.enrolid
                WHERE ue.userid = :userid2
            )
        ";

        // Realizamos la consulta a la base de datos

        $courses_not_enrolled_in = $DB->get_records_sql($query, $params);

        // Si no hay cursos no inscritos, devolvemos un array vacío

        if (!$courses_not_enrolled_in) return [];

        // Agrupamos los cursos por categoría y obtenemos la URL y la imagen de cada curso

        $cursos = array_values(array_reduce($courses_not_enrolled_in, function ($carry, $course) use ($CFG) {
            // Modificar solo esta línea para la URL correcta
            $course->url = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
            
            $course->image = self::get_course_image_url($course);
            $carry[$course->category][] = $course;
            return $carry;
        }));

        // Devolvemos un máximo de 3 cursos por categoría

        $cursos = array_reduce($cursos, function ($carry, $category) {

            $carry['cursos'] = [...$carry['cursos'], ...array_slice($category, 0, self::MAX_COURSES, preserve_keys: false)];

            return $carry;
        }, ['cursos' => []]);

        return $cursos;
    }

    /**
     * 
     * 
     */
    public static function get_count_noti($courseid)
    {
        global $DB, $USER;

        //$userid = $USER->id;

        $sql = "
            SELECT 
                COUNT(*) AS cantidad
            FROM {assign} a
            JOIN {course_modules} cm ON cm.instance = a.id
            JOIN {modules} m ON m.id = cm.module
            JOIN {assign_submission} s ON s.assignment = a.id
            WHERE a.course = :courseid
            AND m.name = 'assign'
            AND cm.deletioninprogress = 0
            AND s.status = 'submitted'
            AND NOT EXISTS (
                SELECT 1
                FROM {assign_grades} g
                WHERE g.assignment = a.id
                AND g.userid = s.userid
            )
        ";

        $params = ['courseid' => $courseid];

        $result = $DB->get_record_sql($sql, $params);
        //debug($result, true);
        if ($result) {
            return $result->cantidad;
        } else {
            return 0;
        }
    }


    /**
     * Obtiene la URL de la imagen del curso
     * 
     * 
     * @param object $course Curso del que se obtendrá la imagen
     * 
     * @return string URL de la imagen del curso o una imagen por defecto
     */
    private static function get_course_image_url(object $course): string
    {
        /** @var moodle_page $PAGE */
        global $PAGE;

        // Obtener el contexto del curso
        $context = \context_course::instance($course->id);

        // Obtiene el gestor de almacenamiento de archivos
        $fs = get_file_storage();

        // Obtener los archivos del área de resumen del curso, ordenados por 'sortorder'
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'sortorder', false);

        // Iterar sobre los archivos
        foreach ($files as $file) {
            // Si el archivo es una imagen válida
            if ($file->is_valid_image()) {
                // Comprobar si la imagen es un GIF
                $filename = $file->get_filename();
                $is_gif = strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'gif';
                
                // Agregar la propiedad is_gif al objeto curso
                $course->is_gif = $is_gif;

                // Crear la URL del archivo de imagen
                $url = \moodle_url::make_pluginfile_url(
                    $file->get_contextid(),  // ID del contexto
                    $file->get_component(),  // Componente del archivo
                    $file->get_filearea(),  // Área del archivo
                    null,  // ID del item (null para evitar el '0')
                    $file->get_filepath(),  // Ruta del archivo
                    $file->get_filename()  // Nombre del archivo
                );

                // Comprobar si la imagen es un GIF
                $filename = $file->get_filename();
                $is_gif = strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'gif';
                
                // Agregar la propiedad is_gif al objeto curso
                $course->is_gif = $is_gif;
                
                // Retornar la URL del archivo
                return $url->out();
            }
        }

        $theme_name = $PAGE->theme->name;
        
        // Si no hay imagen del curso, establecer is_gif como false
        $course->is_gif = false;

        // Si no hay imagen del curso, usar una imagen por defecto
        return "/blocks/cursos_slider/src/img/portada-defecto.jpg";
    }

    /**
     * Obtiene el progreso del curso
     * 
     * @param object $course Curso del que se obtendrá el progreso
     * @param int|null $userid ID del usuario del que se obtendrá el progreso
     * 
     * @return int Progreso del curso
     */
    private static function get_course_progress($course, $userid = null)
    {
        global $USER;

        $progress =  progress::get_course_progress_percentage($course, $userid ?? $USER->id);

        return  (int) $progress ?? 0;
    }

    /**
     * Obtiene la cantidad de notificaciones de tareas pendientes de calificar en un curso
     * 
     * @param int $courseid ID del curso
     * 
     * @return int Cantidad de notificaciones de tareas pendientes de calificar, o 0 si no hay tareas pendientes de calificar
     */
    public static function get_count_notifications(int $courseid): int
    {
        global $DB;

        $query = "
            SELECT 
                COUNT(*) AS cantidad
            FROM {assign} a
            JOIN {course_modules} cm ON cm.instance = a.id
            JOIN {modules} m ON m.id = cm.module
            JOIN {assign_submission} s ON s.assignment = a.id
            WHERE a.course = :courseid
            AND m.name = 'assign'
            AND cm.deletioninprogress = 0
            AND s.status = 'submitted'
            AND NOT EXISTS (
                SELECT 1
                FROM {assign_grades} g
                WHERE g.assignment = a.id
                AND g.userid = s.userid
            )
        ";

        $result = $DB->get_record_sql(sql: $query, params: ['courseid' => $courseid]);

        return $result->cantidad ?? 0;
    }

    /**
     * Obtiene el rol del usuario en un curso
     * 
     * @param int $courseid ID del curso
     * 
     * @return string|bool Rol del usuario en el curso, false si no tiene un rol en el curso
     */
    public static function get_enrol_assigment(int $courseid): string|bool
    {
        global $USER;

        $user_roles = get_user_roles(context: \context_course::instance($courseid), userid: $USER->id);

        $user_role = array_shift($user_roles);

        return $user_role->shortname ?? false;
    }

    private static function get_course_notifications($courseId) {
        // Implementar lógica para obtener notificaciones del curso
        return 0; // Por ahora retornamos 0 como placeholder
    }
}
