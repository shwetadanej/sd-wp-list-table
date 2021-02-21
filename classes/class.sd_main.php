<?php

class SD_Main
{

    /**
     * Creating object variable for color list
     *
     * @var object
     */
    public $sd_table;

    /**
     * Constructor of this class
     */
    public function __construct()
    {
        add_filter('set-screen-option', array(__CLASS__, 'sd_set_screen_options'), 10, 3);
        add_action('admin_menu', array($this, 'sd_create_admin_menu'));
    }

    /**
     * Set screen options for custom wp list table
     *
     * @param mixed $status
     * @param string $option
     * @param int $value
     * @return void
     */
    public static function sd_set_screen_options($status, $option, $value)
    {
        return $value;
    }

    /**
     * Creating menu for table
     *
     * @return void
     */
    public function sd_create_admin_menu()
    {
        $screen_name = add_menu_page(
            __('SD WP List Table', 'sd'),
            __('SD WP List Table', 'sd'),
            'manage_options',
            'sd-wp-list-table',
            array($this, 'sd_wp_list_table_callback')
        );

        add_action("load-$screen_name", array($this, 'sd_screen_option'));
    }

    /**
     * Screen options
     */
    public function sd_screen_option()
    {
        include SD_CLASSES."class.sd_table.php";
        $option_name = 'per_page';
        $args   = [
            'label'   => 'Colors',
            'default' => 20,
            'option'  => 'colors_per_page'
        ];

        add_screen_option($option_name, $args);
        $this->sd_table = new SD_Table();
    }

    /**
     * WP list table menu callback
     */
    public function sd_wp_list_table_callback()
    {
        include SD_TEMPLATE."sd_table.php";
    }
}
new SD_Main();