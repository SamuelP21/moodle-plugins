<?php
defined('MOODLE_INTERNAL') || die();

// Crear una categoría en el menú de administración
if ($hassiteconfig) {
    // Añadir enlace directo al dashboard en "Administración del sitio"
    $ADMIN->add('root', new admin_externalpage(
        'local_dashboard',
        get_string('pluginname', 'local_dashboard'),
        new moodle_url('/local/dashboard/index.php'),
        'local/dashboard:view'
    ));
}
