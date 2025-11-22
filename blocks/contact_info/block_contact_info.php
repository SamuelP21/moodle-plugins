<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contact Info block - Main class
 *
 * @package    block_contact_info
 * @copyright  2025 Tu Nombre
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_contact_info extends block_base {
    
    /**
     * Initialize the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_contact_info');
    }

    /**
     * Indica que este bloque tiene configuración global
     */
    public function has_config() {
        return true;
    }

    /**
     * Define en qué páginas puede aparecer este bloque
     */
    public function applicable_formats() {
        return array(
            'all' => true,  // Puede aparecer en cualquier página
        );
    }

    /**
     * Permite múltiples instancias del bloque
     */
    public function instance_allow_multiple() {
        return false;  // Solo una instancia por página
    }

    /**
     * Genera el contenido del bloque
     */
    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Obtener la configuración global del plugin
        $phone = get_config('block_contact_info', 'phone');
        $email = get_config('block_contact_info', 'email');
        $address = get_config('block_contact_info', 'address');

        // Si no hay configuración, mostrar mensaje
        if (empty($phone) && empty($email) && empty($address)) {
            $this->content->text = html_writer::div(
                get_string('noconfig', 'block_contact_info'),
                'alert alert-info'
            );
            return $this->content;
        }

        // Construir el HTML del contenido
        $html = html_writer::start_div('block-contact-info');
        
        if (!empty($phone)) {
            $html .= html_writer::start_div('contact-item phone-item');
            $html .= html_writer::tag('i', '', array('class' => 'fa fa-phone icon'));
            $html .= html_writer::start_div('contact-details');
            $html .= html_writer::tag('strong', get_string('phone', 'block_contact_info'));
            $html .= html_writer::tag('p', $phone);
            $html .= html_writer::end_div();
            $html .= html_writer::end_div();
        }

        if (!empty($email)) {
            $html .= html_writer::start_div('contact-item email-item');
            $html .= html_writer::tag('i', '', array('class' => 'fa fa-envelope icon'));
            $html .= html_writer::start_div('contact-details');
            $html .= html_writer::tag('strong', get_string('email', 'block_contact_info'));
            $html .= html_writer::tag('p', html_writer::link('mailto:' . $email, $email));
            $html .= html_writer::end_div();
            $html .= html_writer::end_div();
        }

        if (!empty($address)) {
            $html .= html_writer::start_div('contact-item address-item');
            $html .= html_writer::tag('i', '', array('class' => 'fa fa-map-marker icon'));
            $html .= html_writer::start_div('contact-details');
            $html .= html_writer::tag('strong', get_string('address', 'block_contact_info'));
            $html .= html_writer::tag('p', nl2br($address));
            $html .= html_writer::end_div();
            $html .= html_writer::end_div();
        }

        $html .= html_writer::end_div();

        $this->content->text = $html;

        return $this->content;
    }

    /**
     * Define los atributos HTML del bloque para estilos personalizados
     */
    public function html_attributes() {
        $attributes = parent::html_attributes();
        $attributes['class'] .= ' block_contact_info';
        return $attributes;
    }
}
