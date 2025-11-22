<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook 1: Añade el enlace en el MENÚ DE NAVEGACIÓN PRINCIPAL (barra lateral)
 * Este hook se ejecuta cuando Moodle construye la navegación global.
 */
function local_dashboard_extend_navigation(global_navigation $nav)
{
	global $USER;

	// Solo para usuarios logueados
	if (!isloggedin() || isguestuser()) {
		return;
	}

	// Verificar capability
	if (!has_capability('local/dashboard:view', context_system::instance())) {
		return;
	}

	// Añadir nodo al menú principal
	$node = $nav->add(
		get_string('dashboard', 'local_dashboard'),
		new moodle_url('/local/dashboard/index.php'),
		navigation_node::TYPE_CUSTOM,
		null,
		'localdashboard',
		new pix_icon('i/dashboard', '')
	);

	// Mostrar en la navegación plana (drawer)
	$node->showinflatnavigation = true;
}

/**
 * Hook 2: Añade el enlace en el MENÚ DEL USUARIO (arriba a la derecha)
 * Este hook se ejecuta cuando Moodle construye el menú del usuario.
 */
function local_dashboard_extend_navigation_user($navigation, $user, $usercontext, $course, $coursecontext)
{
	global $USER;

	// Solo para el usuario actual
	if ($user->id != $USER->id) {
		return;
	}

	// Verificar capability
	if (!has_capability('local/dashboard:view', context_system::instance())) {
		return;
	}

	// Añadir nodo al menú del usuario
	$navigation->add(
		get_string('dashboard', 'local_dashboard'),
		new moodle_url('/local/dashboard/index.php'),
		navigation_node::TYPE_SETTING,
		null,
		'localdashboard',
		new pix_icon('i/dashboard', '')
	);
}

/**
 * HELPERS para obtener datos del usuario.
 */


/**
 * Obtiene las estadísticas del usuario.
 * 
 * @param int $userid 
 * @return object $stats
 */
function local_dashboard_get_user_stats($userid)
{

	global $DB;

	// Inicializar el objeto de estadísticas.
	$stats = new stdClass();

	// Contar cursos inscritos
	$sql = "SELECT COUNT(DISTINCT c.id)
            FROM {course} c
            JOIN {enrol} e ON e.courseid = c.id
            JOIN {user_enrolments} ue ON ue.enrolid = e.id
            WHERE ue.userid = :userid AND c.id != 1";

	$stats->courses = $DB->count_records_sql($sql, ['userid' => $userid]);

	// Contar actividades completadas
	$sql = "SELECT COUNT(*)
            FROM {course_modules_completion} cmc
            WHERE cmc.userid = :userid AND cmc.completionstate > 0";

	$stats->completed_activities = $DB->count_records_sql($sql, ['userid' => $userid]);

	// Contar actividades pendientes
	$sql = "SELECT COUNT(DISTINCT cm.id)
            FROM {course_modules} cm
            JOIN {course} c ON c.id = cm.course
            JOIN {enrol} e ON e.courseid = c.id
            JOIN {user_enrolments} ue ON ue.enrolid = e.id
            WHERE ue.userid = :userid 
            AND c.id != 1
            AND cm.completion > 0
            AND cm.deletioninprogress = 0";

	$stats->pending_activities = $DB->count_records_sql($sql, ['userid' => $userid]);

	return $stats;
}

/**
 * Obtiene los cursos del usuario.
 * 
 * @param int $userid 
 * @return object $courses
 */
function local_dashboard_get_user_courses($userid)
{
	global $DB;

	$sql = "SELECT c.id, c.fullname, c.shortname, c.summary
            FROM {course} c
            JOIN {enrol} e ON e.courseid = c.id
            JOIN {user_enrolments} ue ON ue.enrolid = e.id
            WHERE ue.userid = :userid AND c.id != 1
            ORDER BY c.fullname";

	return $DB->get_records_sql($sql, ['userid' => $userid]);
}
