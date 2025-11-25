<?php
/*
 * Plugin Name:       Sanasana
 * @package           Sanasana
 * Author:            Intelindev Team
 * Description:       Sanasana allows managing your own list of prices with custom styles.
 * Version:           1.0.5
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 * Text Domain:       sanasana
*/

if ( ! defined( 'ABSPATH' ) ) { die( 'Invalid request.' ); }

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

use SanasanaInit\General\Activate;
use SanasanaInit\General\Deactivate;
use SanasanaInit\SanasanaInit;

function activate_sanasana_plugin() {
    Activate::activate();
}

function deactivate_sanasana_plugin() {
    Deactivate::deactivate();
}

register_activation_hook(__FILE__, 'activate_sanasana_plugin');
register_deactivation_hook(__FILE__, 'deactivate_sanasana_plugin');

if (class_exists('SanasanaInit\SanasanaInit')) {
    SanasanaInit::register_services();
}
