<?php

/*
* Plugin Name: SD WP List Table
* Description: Simple demo of WP_List_Table
* Author: Shweta Danej
* Author URI:  https://shwetadanej.com
* Text Domain: sd
*/

if (!defined('ABSPATH')) {
    return;
}

define('SD_DIR', plugin_dir_path(__FILE__));
define('SD_CLASSES', SD_DIR . 'classes/');
define('SD_TEMPLATE', SD_DIR . 'templates/');

function sd_load_plugin()
{
    include SD_CLASSES . "class.sd_main.php";
}
add_action('plugins_loaded', 'sd_load_plugin');


/**
 * Callback function of plugin activation hook
 * This will check if website is multisite or not, if yes then loop through it and create table for each website
 *
 * @return void
 */
function sd_plugin_activation()
{

    $db_created = get_option("sd_db_created");
    if (!$db_created) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'colors';
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();
        $create_table = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
                color_id bigint(20) NOT NULL AUTO_INCREMENT,
                color_code varchar(255) DEFAULT NULL,
                created_date timestamp NULL DEFAULT current_timestamp(),
                updated_date timestamp NULL DEFAULT current_timestamp(),
                PRIMARY KEY id (color_id)
            )$charset_collate;";
        $result = dbDelta($create_table);

        if ($result) {
            $my_colors = array(
                '#F34334', '#EA1E63', '#A224B8', '#673BB7', '#4050B4', '#2096F3', '#72C5ED', '#04BDD7', '#029688', '#4BAF50', '#8CC14A', '#CBDC36', '#FFEA3E', '#FBC009', '#FE9700'
            );
            foreach ($my_colors as $key => $value) {
                $data = array(
                    'color_code' => $value,
                    'created_date' => current_time('mysql'),
                    'updated_date' => current_time('mysql'),
                );
                $format = array('%s', '%s', '%s');
                $wpdb->insert($table_name, $data, $format);
            }
        }
        update_option("sd_db_created", true);
    }
}
register_activation_hook(__FILE__, 'sd_plugin_activation');
