<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    // Permiso para agregar el bloque en CURSOS
    'block/contacto:addinstance' => array(
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW,        // El Manager puede agregar y editar
            'editingteacher' => CAP_PREVENT, // El Profesor NO puede agregarlo
            'student' => CAP_PREVENT,      // El Estudiante NO puede agregarlo
            'guest' => CAP_PREVENT
        ),
    ),


    'block/contacto:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
    ),
);
