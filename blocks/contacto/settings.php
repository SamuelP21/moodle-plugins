<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'block_contacto/phone',
        get_string('configphone', 'block_contacto'),
        '',
        '+34 900 000 000',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'block_contacto/email',
        get_string('configemail', 'block_contacto'),
        '',
        'info@correo.es',
        PARAM_EMAIL
    ));

    $settings->add(new admin_setting_configtextarea(
        'block_contacto/address',
        get_string('configaddress', 'block_contacto'),
        '',
        'Calle de la calle, 00000',
        PARAM_TEXT
    ));
}
