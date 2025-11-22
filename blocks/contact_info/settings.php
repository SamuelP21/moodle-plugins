<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    
    // Configuración del teléfono
    $settings->add(new admin_setting_configtext(
        'block_contact_info/phone',
        get_string('configphone', 'block_contact_info'),
        get_string('configphone_desc', 'block_contact_info'),
        '+34 900 123 456',
        PARAM_TEXT
    ));

    // Configuración del email
    $settings->add(new admin_setting_configtext(
        'block_contact_info/email',
        get_string('configemail', 'block_contact_info'),
        get_string('configemail_desc', 'block_contact_info'),
        'contacto@ejemplo.com',
        PARAM_EMAIL
    ));

    // Configuración de la dirección
    $settings->add(new admin_setting_configtextarea(
        'block_contact_info/address',
        get_string('configaddress', 'block_contact_info'),
        get_string('configaddress_desc', 'block_contact_info'),
        "Calle Ejemplo, 123\n28001 Madrid, España",
        PARAM_TEXT
    ));
}
