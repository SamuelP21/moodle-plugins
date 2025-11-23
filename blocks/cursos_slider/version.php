<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_cursos_slider';
$plugin->version = 2024061800;
$plugin->requires = 2020110900; // Requiere Moodle 3.10 o superior.
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.0.0';

$plugin->cron = 0; // Period for cron to check this plugin (secs)
