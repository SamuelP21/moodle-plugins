<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');  // ← Cargar las funciones helper

require_login();

// Verificar capabilities.
$context = context_system::instance();
require_capability('local/dashboard:view', $context);

// Configuracion de la pagina.
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/dashboard/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_dashboard'));
$PAGE->set_heading(get_string('pluginname', 'local_dashboard'));
$PAGE->set_pagelayout('standard');

// Cargar los estilos CSS
$PAGE->requires->css('/local/dashboard/scss/styles.css');

// Obtener datos del usuario.
$userid = $USER->id;
$stats = local_dashboard_get_user_stats($userid);
$courses = local_dashboard_get_user_courses($userid);

// preparar datos para el template.
$data = [
    'username' => fullname($USER),
    'totalcourses' => $stats->courses,
    'completedactivities' => $stats->completed_activities,
    'pendingactivities' => $stats->pending_activities,
    'courses' => array_values($courses),
    'hascourses' => !empty($courses)
];

// Renderizar la pagina.
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_dashboard/index_page', $data);
echo $OUTPUT->footer();
