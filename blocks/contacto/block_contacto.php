<?php
class block_contacto extends block_base
{
    public function init()
    {
        $this->title = get_string('pluginname', 'block_contacto');
    }

    public function has_config()
    {
        return true;
    }

    public function applicable_formats()
    {
        return array(
            'all' => true,
        );
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function get_content()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        global $OUTPUT;

        $this->content = new stdClass;

        // Obtener configuración
        $phone = get_config('block_contacto', 'phone');
        $email = get_config('block_contacto', 'email');
        $address = get_config('block_contacto', 'address');

        $this->content->text = $OUTPUT->render_from_template('block_contacto/content', [
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'hasconfig' => !empty($phone) || !empty($email) || !empty($address),
        ]);

        $this->content->footer = '';

        return $this->content;
    }
}
