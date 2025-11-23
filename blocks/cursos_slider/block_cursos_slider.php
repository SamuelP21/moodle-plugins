<?php

use function blocks\services\is_user_student_any_course;
use blocks\services\CourseService;

require_once(__DIR__ . '/services/UserService.php');
require_once(__DIR__ . '/services/CourseService.php');
/**
 * Bloque que contiene el carrusel de cursos implementado con React
 */
class block_cursos_slider extends block_base
{
    /**
     * Inicializa las propiedades del bloque
     */
    public function init()
    {
        $this->title = get_string('cursos_slider', 'block_cursos_slider');
    }

    /**
     * Obtiene el contenido del bloque
     */
    public function get_content()
    {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        // Obtener la URL base del plugin
        $pluginbaseurl = $CFG->wwwroot . '/blocks/cursos_slider';
        $reactappurl = $pluginbaseurl . '/react/app';

        // Cargar los archivos JS y CSS necesarios
        $PAGE->requires->js(new moodle_url($reactappurl . '/dist/assets/index.js'));

        // Obtener los datos usando el servicio
        $courses = CourseService::get_user_courses_data();
        $is_student = is_user_student_any_course();

        // Preparar los datos para React
        $coursesData = array(
            'is_student' => $is_student,
            'courses' => array_values($courses),
            'exists_courses' => !empty($courses),
            'exists_courses_finished' => false, // Se actualizará abajo
            'exists_courses_not_in' => false, // Se actualizará abajo
            'courses_finished' => array(),
            'courses_not_in' => array(),
            'image_path' => $pluginbaseurl . '/react/app/src/assets'
        );

        // Obtener cursos terminados
        $courses_finished = array_values(array_filter($courses, function ($course) {
            return isset($course['is_incomplete']) && $course['is_incomplete'] === false;
        }));
        $coursesData['courses_finished'] = $courses_finished;
        $coursesData['exists_courses_finished'] = !empty($courses_finished);

        // Obtener próximos cursos si es estudiante
        if ($is_student) {
            $courses_not_in = CourseService::get_courses_user_not_in();

            $cursos = isset($courses_not_in['cursos']) && is_array($courses_not_in['cursos'])
                ? $courses_not_in['cursos']
                : [];

            $coursesData['courses_not_in'] = array_values($cursos);
            $coursesData['exists_courses_not_in'] = !empty($cursos);
        }


        // Renderizar el bloque con React
        $this->content->text = '<div id="cursos-slider-root"></div>';

        // Inicializar el bloque con React
        $this->content->text .= '<script>
            window.coursesData = ' . json_encode($coursesData) . ';
            window.addEventListener("load", function() {
                window.initializeCoursesSlider("cursos-slider-root");
            });
        </script>';

        return $this->content;
    }

    /**
     * Permite que el bloque tenga configuración
     */
    public function has_config()
    {
        return false;
    }

    /**
     * Define los formatos aplicables
     */
    public function applicable_formats()
    {
        return array(
            'all' => true
        );
    }
}
