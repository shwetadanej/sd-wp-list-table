<?php

class SD_Table extends WP_List_Table
{
    /**
     * Constructor of SD_Table class
     */
    public function __construct()
    {
        parent::__construct([
            'singular' => __('Color', 'sd'), //singular name of the listed records, this will show in screen options
            'plural'   => __('Colors', 'sd'), //plural name of the listed records, this will show in screen options
            'ajax'     => false //does this table support ajax?
        ]);
    }

    /**
     * Prepare all the items of this wp list table
     *
     * @return void
     */
    public function prepare_items()
    {
     
        $per_page     = $this->get_items_per_page('colors_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items  = $this->sd_get_all_colors_count();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page
        ]);

        $this->items = $this->table_data($current_page);
        $this->process_bulk_action();
    }

    /**
     * Set default columns
     *
     * @param array $item
     * @param string $column_name
     * @return void
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'color_id':
            case 'color_code':
            case 'created_date':
            case 'updated_date':
                return $item[$column_name];
            default:
                return print_r($item, true); // print array for the debugging purpose
        }
    }

    /**
     * Fetch all columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'color_id'    => __('Color Id', 'sd'),
            'color_code' => __('Color Code', 'sd'),
            'created_date'    => __('Created Date', 'sd'),
            'updated_date'    => __('Update Date', 'sd'),
        ];

        return $columns;
    }

    /**
     * Add your sortable columns to this array
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'color_id' => array('color_id', true),
            'color_code' => array('color_code', false),
            'created_date' => array('created_date', false),
            'updated_date' => array('updated_date', false),
        );

        return $sortable_columns;
    }

    /**
     * Set message to show when no records found
     *
     * @return void
     */
    public function no_items()
    {
        _e('No colors available.', 'sd');
    }

    /**
     * Get total count of the table record
     *
     * @return integer
     */
    public function sd_get_all_colors_count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}colors";

        return $wpdb->get_var($sql);
    }

    /**
     * Get page wise table data
     *
     * @param integer $page_number
     * @return array
     */
    public function table_data($page_number = 1)
    {

        global $wpdb;

        $per_page     = $this->get_items_per_page('colors_per_page', 20);
        $sql = "SELECT * FROM {$wpdb->prefix}colors";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    /**
     * Add your bulk actions
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }

    /**
     * Callback for bulk action delete
     *
     * @param array $item
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item['color_id']
        );
    }

    /**
     * Delete color query
     *
     * @param integer $id
     * @return void
     */
    public static function sd_delete_color($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}colors",
            array('color_id' => $id),
            array('%d')
        );
    }

    /**
     * Bulk process
     *
     * @return void
     */
    public function process_bulk_action()
    {
        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                $this->sd_delete_color($id);
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
    }
}
new SD_Table();
