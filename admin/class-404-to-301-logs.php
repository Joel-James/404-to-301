<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * WP_List_Table is marked as private by WordPress. So they may change it.
 * Details here - https://codex.wordpress.org/Class_Reference/WP_List_Table
 * So we have copied this class and using independently to avoid future issues. 
 */
if ( ! class_exists('WP_List_Table_404') ) {

    global $wp_version;
    if( $wp_version >= 4.4 ) {
        include_once I4T3_PLUGIN_DIR . '/admin/core/class-wp-list-table-4.4.php';
    } else {
        include_once I4T3_PLUGIN_DIR . '/admin/core/class-wp-list-table-old.php';
    }
}

/**
 * The listing page class for error logs.
 *
 * This class defines all the methods to output the error logs display table using
 * WordPress listing table class.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage ErrorLogsTable
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _404_To_301_Logs extends WP_List_Table_404 {

    /**
     * The table name of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @author  Joel James.
     * @var      string    $table    The table name of this plugin in db.
     */
    private static $table;

    /**
     * Initialize the class and set its properties.
     *
     * @since  2.0.0
     * @access public
     * @var    string $table The name of the table of plugin
     * 
     * @return void
     */
    public function __construct() {

        self::$table = I4T3_TABLE;

        parent::__construct(
            array(
                'singular' => __('404 Error Log', '404-to-301'), //singular name of the listed records
                'plural' => __('404 Error Logs', '404-to-301'), //plural name of the listed records
                'ajax' => false //does this table support ajax?
            )
        );
    }

    /**
     * Error log data to be displayed.
     *
     * Getting the error log data from the database and converts it to
     * the required structure.
     *
     * @param int $per_page    Items per page
     * @param int $page_number Page number of the list
     *
     * @since  2.0.0
     * @access public
     * @global object        $wpdb WP DB object
     * @uses   apply_filters
     * 
     * @return array $error_data Array of error log data.
     */
    public static function i4t3_get_log_data($per_page = 20, $page_number = 1) {

        global $wpdb;

        $offset = ( $page_number - 1 ) * $per_page;

        // If no sort, default to title
        $orderby = ( isset( $_REQUEST['orderby']) ) ? self::i4t3_get_sort_column_filtered( $_REQUEST['orderby']) : 'date';

        // If no order, default to asc
        $order = ( isset( $_REQUEST['order']) && 'desc' == $_REQUEST['order'] ) ? 'DESC' : 'ASC';

        $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . self::$table . " ORDER BY $orderby $order LIMIT %d OFFSET %d", array( $per_page, $offset ) ), 'ARRAY_A' );

        return $result;
    }

    /**
     * Filter the sorting parameters.
     *
     * This is used to filter the sorting parameters in order
     * to prevent SQL injection atacks. We will accept only our
     * required values. Else we will assign a default value.
     * 
     * @param  string $column Value from url
     *
     * @since  2.0.3
     * @access public
     * 
     * @return string $filtered_column.
     */
    public static function i4t3_get_sort_column_filtered($column) {

        $allowed_columns = array( 'date', 'url', 'ref', 'ip' );

        if( in_array($column, $allowed_columns ) ) {
            $filtered_column = esc_sql( $column );
        } else {
            $filtered_column = 'date';
        }
        
        return $filtered_column;
    }

    /**
     * Delete a single record from table.
     *
     * This function is used to clear the selected errors
     * from error logs table.
     * 
     * @param int $id ID of error log
     *
     * @since  2.1.0
     * @access public
     * 
     * @return void
     */
    public static function delete_error_logs($id) {
        
        global $wpdb;

        $wpdb->delete(
            self::$table,
            array(
                'id' => $id
            ),
            array('%d')
        );
    }

    /**
     * Delete all records at once from database.
     *
     * This function is used to clear the error logs table.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return void
     */
    public static function delete_error_all_logs() {

        global $wpdb;
        // delete from logs table
        $wpdb->query("DELETE FROM " . self::$table . "");
    }

    /**
     * Get the count of total records in table.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return mixed
     */
    public static function record_count() {

        global $wpdb;

        $sql = "SELECT COUNT(id) FROM " . self::$table;

        return $wpdb->get_var($sql);
    }

    /**
     * Empty record text.
     *
     * Custom text to display where there is nothing to display in error
     * log table.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    public function no_items() {
        
        _e( 'Ulta pulta..! Seems like you had no errors to log.', '404-to-301' );
    }

    /**
     * Default columns in list table.
     *
     * To show columns in error log list table. If there is nothing
     * for switch, printing the whole array.
     * 
     * @param array  $item        Column data
     * @param string $column_name Column name
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array
     */
    public function column_default( $item, $column_name ) {
        
        switch( $column_name ) {
            case 'date':
            case 'url':
            case 'ref':
            case 'ip':
            case 'ua':
            case 'redirect':
                return $item[ $column_name ];
            default:
                //Show the whole array for troubleshooting purposes
                return print_r( $item, true );
        }
    }

    /**
     * To output checkbox for bulk actions.
     *
     * This function is used to add new checkbox for all entries in
     * the listing table. We use this checkbox to perform bulk actions.
     * 
     * @param array $item Column data
     *
     * @since  2.1.0
     * @access public
     * 
     * @return string
     */
    function column_cb( $item ) {

        return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s"/>', $item['id'] );
    }
    
    /**
     * To output redirect option of the link.
     *
     * This function is used to add custom column for setting
     * custom redirect for a 404 link.
     * 
     * @param array $item Column data
     *
     * @since  2.1.1
     * @access public
     * 
     * @return  string
     */
    function column_redirect( $item ) {

        $title = ( ! empty( $item['redirect'] ) ) ? $item['redirect'] : __( 'Default', '404-to-301' );
        
        return '<a href="javascript:void(0)" title="' .  __('Customize', '404-to-301') . '" class="i4t3_redirect_thickbox" url_404="' . $item['url'] . '">' . $title . '</a>';
    }

    /**
     * To modify the date column data
     *
     * This function is used to modify the column data for date in listing table.
     * We can change styles, texts etc. using this function.
     *
     * @param array $item Column data
     * 
     * @since  2.0.0
     * @access public
     * 
     * @return string $date_data Date column text data.
     */
    function column_date( $item ) {

        $delete_nonce = wp_create_nonce('i4t3_delete_log');

        $title = apply_filters('i4t3_log_list_date_column', date("j M Y, g:i a", strtotime($item['date'])));
        
        $confirm = __('Are you sure you want to delete this item?', '404-to-301');
        
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&log=%s&_wpnonce=%s" onclick="return confirm(\'%s\');">' . __('Delete', '404-to-301') . '</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce, $confirm)
        );

        return $title . $this->row_actions($actions);
    }

    /**
     * To modify the url column data
     *
     * This function is used to modify the column data for url in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * @param array $item Column data
     *
     * @since  2.0.0
     * @access public
     * 
     * @return string $url_data Url column text data.
     */
    public function column_url( $item ) {

        // Apply filter - i4t3_log_list_url_column
        $url_data = apply_filters( 'i4t3_log_list_url_column', $this->get_empty_text('<p class="i4t3-url-p">' . $item['url'] . '</p>', $item['url']));

        return $url_data;
    }

    /**
     * To modify the ref column data
     *
     * This function is used to modify the column data for ref in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * @param array $item Column data
     *
     * @since  2.0.0
     * @access public
     * 
     * @return string $ref_data Ref column text data.
     */
    public function column_ref( $item ) {

        // Apply filter - i4t3_log_list_ref_column
        $ref_data = apply_filters( 'i4t3_log_list_ref_column', $this->get_empty_text('<a href="' . $item['ref'] . '" target="_blank">' . $item['ref'] . '</a>', $item['ref'] ) );

        return $ref_data;
    }

    /**
     * To modify the user agent column data
     *
     * This function is used to modify the column data for user agent in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * @param array $item Column data
     *
     * @since  2.0.9
     * @access public
     * 
     * @return string $ua_data Ref column text data.
     */
    public function column_ua($item) {

        // Apply filter - i4t3_log_list_ref_column
        $ua_data = apply_filters( 'i4t3_log_list_ua_column', $this->get_empty_text( $item['ua'], $item['ua'] ) );

        return $ua_data;
    }

    /**
     * To modify the ip column data
     *
     * This function is used to modify the column data for ip in listing table.
     * We can change styles, texts etc. using this function.
     * 
     * @param array $item Column data
     *
     * @since  2.0.9
     * @access public
     * 
     * @return string $ip Ref column text data.
     */
    public function column_ip( $item ) {

        // Apply filter - i4t3_log_list_ref_column
        $ip = apply_filters( 'i4t3_log_list_ip_column', $this->get_empty_text( $item['ip'], $item['ip'] ) );

        return $ip;
    }

    /**
     * Column titles
     *
     * Custom column titles to be displayed in listing table. You can change this to anything
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $columns Array of cloumn titles.
     */
    function get_columns() {

        $columns = array(
            'cb' => '<input type="checkbox" style="width: 5%;" />',
            'date' => __( 'Date', '404-to-301' ),
            'url' => __( '404 Path', '404-to-301' ),
            'ref' => __( 'From', '404-to-301' ), // referer
            'ip' => __( 'IP Address', '404-to-301' ),
            'ua' => __('User Agent', '404-to-301'),
            'redirect' => __( 'Redirect', '404-to-301' )
        );

        return $columns;
    }

    /**
     * Make columns sortable
     *
     * To make our custom columns in list table sortable. We have included
     * 4 columns except 'User Agent' column here.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $sortable_columns Array of columns to enable sorting.
     */
    public function get_sortable_columns() {

        $sortable_columns = array(
            'date' => array('date', true),
            'url' => array('url', false),
            'ref' => array('ref', false),
            'ip' => array('ip', false)
        );

        return $sortable_columns;
    }

    /**
     * Bulk actions drop down
     *
     * Options to be added to the bulk actions drop down for users
     * to select. We have added 'Delete' actions.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $actions Options to be added to the action select box.
     */
    public function get_bulk_actions() {

        $actions = array(
            'bulk-delete' => __( 'Delete Selected', '404-to-301' ),
            'bulk-all-delete' => __( 'Delete All', '404-to-301' )
        );

        return $actions;
    }

    /**
     * Main function to output the listing table using WP_List_Table class
     *
     * As name says, this function is used to prepare the lsting table based
     * on the custom rules and filters that we have given.
     * This function extends the lsiting table class and uses our custom data
     * to list in the table.
     * Here we set pagination, columns, sorting etc.
     * $this->items - Push our custom log data to the listing table.
     *
     * @global object $wpdb WP DB object
     * @since  2.0.0
     * @access public
     * @uses   hide_errors() To hide if there are SQL query errors.
     * 
     * @return void
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page( 'logs_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args(
            array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            )
        );

        $this->items = self::i4t3_get_log_data( $per_page, $current_page );
    }

    /**
     * To perform bulk actions.
     *
     * This function is used to check if bulk action is set in post.
     * If set it will call the required functions to perform the task.
     *
     * @since  2.1.0
     * @access public
     * @uses   wp_verify_nonce To verify if the request is from WordPress.
     * 
     * @return void
     */
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if( ! wp_verify_nonce( $nonce, 'i4t3_delete_log' ) ) {
                wp_die( 'Go get a life script kiddies' );
            } else {
                self::delete_error_logs( absint( $_GET['log'] ) );
                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }
        }

        $this->bulk_delete_actions();
    }

    /**
     * To perform bulk delete actions.
     *
     * This function is used to perform the bulk delete
     * actions. Selected data delete and whole data delete
     * is being performed here.
     *
     * @since  2.1.0
     * @access public
     * @uses   wp_verify_nonce  To verify if the request is from WordPress.
     * 
     * @return void
     */
    private function bulk_delete_actions() {

        if( isset($_POST['_wpnonce'] ) ) {

            $nonce = '';
            $action = '';
            // security check!
            if ( ! empty( $_POST['_wpnonce'] ) ) {
                $nonce = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];
            }

            if ( ! wp_verify_nonce( $nonce, $action ) ) {
                wp_die( 'Go get a life script kiddies' );
            }

            // If the delete bulk action is triggered
            else if ( ( isset( $_POST['action']) && $_POST['action'] == 'bulk-delete' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' ) ) {
                $delete_ids = esc_sql($_POST['bulk-delete']);
                // loop over the array of record IDs and delete them
                foreach( $delete_ids as $id ) {
                    self::delete_error_logs( $id );
                }
                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }

            // If the delete all bulk action is triggered
            elseif( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-all-delete' ) || ( isset( $_POST['action2']) && $_POST['action2'] == 'bulk-all-delete' ) ) {
                self::delete_error_all_logs();
                wp_redirect(esc_url(add_query_arg()));
                exit;
            }
        }
    }

    /**
     * To make clear error text if value is N/A.
     *
     * This function is used to show the N/A text in red colour if the field value
     * is not available.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return string
     */
    public function get_empty_text( $data, $na = 'N/A' ) {

        if ( $na == 'N/A' ) {
            return '<p class="i4t3-url-p">' . __('N/A', '404-to-301') . '</p>';
        }

        return $data;
    }
}
