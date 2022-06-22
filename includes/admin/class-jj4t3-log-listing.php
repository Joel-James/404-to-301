<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;


// List table class from WordPress core.
if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * The listing page class for error logs.
 *
 * This class defines all the methods to output the error logs display table using
 * WordPress listing table class.
 *
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @category   Core
 * @link       https://duckdev.com/products/404-to-301/
 * @package    JJ4T3
 * @subpackage ErrorLogListing
 */
class JJ4T3_Log_Listing extends WP_List_Table {

	/**
	 * Group by column name.
	 *
	 * @var    string
	 * @since  3.0.0
	 * @access private
	 */
	private $group_by = '';

	/**
	 * Initialize the class and set properties.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( '404 Error Log', '404-to-301' ),
				'plural'   => __( '404 Error Logs', '404-to-301' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare listing table using WP_List_Table class.
	 *
	 * As name says, this function is used to prepare the lsting table based
	 * on the custom rules and filters that we have given.
	 * This function extends the lsiting table class and uses our custom data
	 * to list in the table.
	 * Here we set pagination, columns, sorting etc.
	 * $this->items - Push our custom log data to the listing table.
	 * Registering filter - "jj4t3_logs_list_per_page".
	 *
	 * @since  2.0.0
	 * @access public
	 * @global object $wpdb WP DB object
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		// Execute bulk actions.
		$actions = $this->process_actions();

		// Redirect after actions, or after securoty check.
		$this->safe_redirect( $actions );

		// Set group by column.
		$this->set_groupby();

		/**
		 * Filter to alter no. of items per page.
		 *
		 * Change no. of items listed on a page. This value can be changed from
		 * error listing page screen options.
		 *
		 * @since 2.0.0
		 */
		$per_page = apply_filters( 'jj4t3_logs_list_per_page', $this->get_items_per_page( 'logs_per_page', 20 ) );

		// Current page number.
		$page_number = $this->get_pagenum();

		// Total error logs.
		$total_items = $this->total_logs();

		// Set pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		// Set error logs data for the current page.
		$this->items = $this->get_error_logs( $per_page, $page_number );
	}

	/**
	 * Get error logs data.
	 *
	 * Get error logs data from our custom database table.
	 * Apply all filtering, sorting and paginations.
	 * Registering filter - "jj4t3_logs_list_result".
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @global object $wpdb        WP DB object
	 *
	 * @param int $per_page    Logs per page.
	 * @param int $page_number Current page number.
	 *
	 * @return array
	 */
	private function get_error_logs( $per_page = 20, $page_number = 1 ) {
		global $wpdb;

		// Current offset.
		$offset = ( $page_number - 1 ) * $per_page;
		// Set group b query, if set.
		$groupby_query = empty( $this->group_by ) ? '' : 'GROUP BY ' . $this->group_by;
		// Get count of grouped items.
		$count = empty( $this->group_by ) ? '' : ', COUNT(id) as count ';

		// Get error logs.
		$result = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT *%1$s FROM %2$s WHERE status != 0 %3$s ORDER BY %4$s %5$s LIMIT %6$d OFFSET %7$d',
				$count,
				JJ4T3_TABLE,
				$groupby_query,
				$this->get_order_by(),
				$this->get_order(),
				$per_page,
				$offset
			),
			ARRAY_A
		);

		/**
		 * Filter to alter the error logs listing data result.
		 *
		 * BE CAREFUL when you use this filter. If you alter the structure
		 * the entire listing table may get affected.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'jj4t3_logs_list_result', $result );
	}

	/**
	 * Get sort by column name.
	 *
	 * This is used to filter the sorting parameters in order
	 * to prevent SQL injection atacks. We will accept only our
	 * required values. Else we will assign a default value.
	 * Registering filter - "jj4t3_log_list_orderby".
	 *
	 * @since  2.0.3
	 * @access public
	 * @uses   esc_sql() To escape string for SQL.
	 *
	 * @return string Filtered column name.
	 */
	private function get_order_by() {
		/**
		 * Filter to alter the log listing orderby param.
		 *
		 * Only accepted, valid column name will be accepted.
		 *
		 * @since 2.0.0
		 */
		$orderby = apply_filters( 'jj4t3_log_list_orderby', jj4t3_from_request( 'orderby', 'date' ) );

		/**
		 * Filter to alter the allowed order by values.
		 *
		 * Only these columns will be allowed. It is a security
		 * measure too.
		 *
		 * @param array array of allowed column names.
		 *
		 * @since 2.0.0
		 */
		$allowed_columns = apply_filters( 'jj4t3_log_list_orderby_allowed', array( 'date', 'url', 'ref', 'ip' ) );

		// Make sure only valid columns are considered.
		$allowed_columns = array_intersect( $allowed_columns, array_keys( jj4t3_log_columns() ) );

		// Check if given column is allowed.
		if ( in_array( $orderby, $allowed_columns, true ) ) {
			return sanitize_sql_orderby( $orderby );
		}

		return 'date';
	}

	/**
	 * Filter the sorting parameters.
	 *
	 * This is used to filter the sorting parameters in order
	 * to prevent SQL injection atacks. We will accept only our
	 * required values. Else we will assign a default value.
	 * Registering filter - "jj4t3_log_list_order".
	 *
	 * @since  2.0.3
	 * @access private
	 *
	 * @return string Filtered column name.
	 */
	private function get_order() {
		// Get order column name from request.
		$order = jj4t3_from_request( 'order', 'DESC' ) === 'asc' ? 'ASC' : 'DESC';

		/**
		 * Filter to alter the log listing order param.
		 *
		 * Only ASC and DESC will be accepted.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'jj4t3_log_list_order', $order );
	}

	/**
	 * Set gropuby value for grouping results.
	 *
	 * Groupby filter to avoid duplicate values in error log
	 * listing table. If a groupby column is set, it will show
	 * the count along with the logs.
	 * Registering filter - "jj4t3_log_list_groupby_allowed".
	 * Registering filter - "jj4t3_log_list_groupby".
	 *
	 * @since  3.0.0
	 * @access private
	 */
	private function set_groupby() {
		/**
		 * Filter to alter the allowed group by values.
		 *
		 * Only these columns will be allowed. It is a security
		 * measure too.
		 *
		 * @param array array of allowed column names.
		 *
		 * @since 2.0.0
		 */
		$allowed_values = apply_filters( 'jj4t3_log_list_groupby_allowed', array( 'url', 'ref', 'ip', 'ua' ) );

		// Make sure only valid columns are considered.
		$allowed_values = array_intersect( $allowed_values, array_keys( jj4t3_log_columns() ) );

		// Get group by value from request.
		$group_by = jj4t3_from_request( 'group_by_top', '' );

		/**
		 * Filter to alter the log listing groupby param.
		 *
		 * Only allowed column names are accepted.
		 *
		 * @since 2.0.0
		 */
		$group_by = apply_filters( 'jj4t3_log_list_groupby', $group_by );

		// Verify if the group by value is allowed.
		if ( ! in_array( $group_by, $allowed_values, true ) ) {
			return;
		}

		$this->group_by = $group_by;
	}

	/**
	 * Get the count of total logs in table.
	 *
	 * Since we are using a custom table for data in
	 * listing, we need to get count of total items for proper pagination.
	 * Registering filter - "jj4t3_log_list_count".
	 *
	 * @since  2.0.3
	 * @access private
	 *
	 * @global object $wpdb WP DB object
	 * @return int Total count.
	 */
	private function total_logs() {
		global $wpdb;

		if ( empty( $this->group_by ) ) {
			$total = $wpdb->get_var( "SELECT COUNT(id) FROM " . JJ4T3_TABLE );
		} else {
			$total = $wpdb->get_var( "SELECT COUNT(DISTINCT " . $this->group_by . ") FROM " . JJ4T3_TABLE );
		}

		/**
		 * Filter to alter total logs count.
		 *
		 * You MAY NOT have to use this filter.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'jj4t3_log_list_count', $total );
	}

	/**
	 * Listing table column titles.
	 *
	 * Custom column titles to be displayed in listing table.
	 * Registering filter - "jj4t3_log_list_column_names".
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array $columns Array of column titles.
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" style="width: 5%;" />',
			'date'     => __( 'Date', '404-to-301' ),
			'url'      => __( '404 Path', '404-to-301' ),
			'ref'      => __( 'From', '404-to-301' ),
			'ip'       => __( 'IP Address', '404-to-301' ),
			'ua'       => __( 'User Agent', '404-to-301' ),
			'redirect' => __( 'Customization', '404-to-301' ),
		);

		/**
		 * Filter hook to change column titles.
		 *
		 * If you are adding custom columns, remember to add
		 * those to "jj4t3_log_list_column_default" filter too.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_column_names', $columns );
	}

	/**
	 * Make columns sortable.
	 *
	 * To make our custom columns in list table sortable.
	 * Do not enable sorting for redirect and ua columns.
	 * Registering filter - "jj4t3_log_list_sortable_columns".
	 *
	 * @since  2.0.0
	 * @access protected
	 *
	 * @return array Array of columns to enable sorting.
	 */
	protected function get_sortable_columns() {
		$columns = array(
			'date' => array( 'date', true ),
			'url'  => array( 'url', false ),
			'ref'  => array( 'ref', false ),
			'ip'   => array( 'ip', false ),
		);

		/**
		 * Filter hook to change column titles.
		 *
		 * @note  DO NOT add extra columns.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_sortable_columns', $columns );
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * If there are no errors logged yet, show custom error message
	 * instead of default one.
	 * Registering filter - "jj4t3_log_list_no_items_message".
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function no_items() {
		$message = __( 'Ah! You are so clean that you still got ZERO errors.', '404-to-301' );

		$message = apply_filters( 'jj4t3_log_list_no_items_message', $message );

		/**
		 * Filter hook to change no items message.
		 *
		 * @since 3.0.0
		 */
		echo esc_html( $message );
	}

	/**
	 * Default columns in list table.
	 *
	 * To show columns in error log list table. If there is nothing
	 * for switch, printing the whole array.
	 * Registering filter - "jj4t3_log_list_column_default".
	 *
	 * @param array  $item        Column data.
	 * @param string $column_name Column name.
	 *
	 * @since  2.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		$columns = array_keys( jj4t3_log_columns() );

		/**
		 * Filter hook to change column names.
		 *
		 * @note  DO NOT add extra columns.
		 *
		 * @since 3.0.0
		 */
		$columns = apply_filters( 'jj4t3_log_list_column_default', $columns );

		// If current column is allowed.
		if ( in_array( $column_name, $columns, true ) ) {
			return $item[ $column_name ];
		}

		return '';
	}

	/**
	 * To output checkbox for bulk actions.
	 *
	 * This function is used to add new checkbox for all entries in
	 * the listing table. We use this checkbox to perform bulk actions.
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.1.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s"/>', $item['id'] );
	}

	/**
	 * Date column content.
	 *
	 * This function is used to modify the column data for date in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "jj4t3_log_list_date_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_date( $item ) {
		$delete_nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );

		$title = mysql2date( 'j M Y, g:i a', $item['date'] );

		$confirm = __( 'Are you sure you want to delete this item?', '404-to-301' );

		$actions = array( 'delete' => sprintf( '<a href="?page=jj4t3-logs&action=%s&bulk-delete=%s&_wpnonce=%s" onclick="return confirm(\'%s\');">' . __( 'Delete', '404-to-301' ) . '</a>', 'delete', absint( $item['id'] ), $delete_nonce, $confirm ) );

		/**
		 * Filter to change date colum html content.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_date_column', $title . $this->row_actions( $actions ) );
	}

	/**
	 * URL column content.
	 *
	 * This function is used to modify the column data for url in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "jj4t3_log_list_url_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string URL column html content
	 */
	public function column_url( $item ) {
		// Get default text if empty value.
		$url = $this->get_empty_content( $item['url'] );
		if ( ! $url ) {
			$url = '<span class="jj4t3-url-p">' . esc_url( $item['url'] ) . '</span>';
		}

		/**
		 * Filter to change url colum content.
		 *
		 * Remember this filter value is a partial url field.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_url_column', $this->get_group_content( $url, 'url', $item ) );
	}

	/**
	 * Referer column content.
	 *
	 * This function is used to modify the column data for ref in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "jj4t3_log_list_ref_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string Ref column html content.
	 */
	public function column_ref( $item ) {
		// Get default text if empty value.
		$ref = $this->get_empty_content( $item['ref'] );
		if ( ! $ref ) {
			$ref = '<a href="' . esc_url( $item['ref'] ) . '" target="_blank">' . esc_url( $item['ref'] ) . '</a>';
		}

		/**
		 * Filter to change referer url colum content.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_ref_column', $this->get_group_content( $ref, 'ref', $item ) );
	}

	/**
	 * User agent column content.
	 *
	 * This function is used to modify the column data for user agent in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "jj4t3_log_list_ua_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.9
	 * @access public
	 *
	 * @return string User Agent column html content
	 */
	public function column_ua( $item ) {
		// Sanitize text content.
		$ua = sanitize_text_field( $item['ua'] );

		/**
		 * Filter to change user agent colum content.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_ua_column', $this->get_group_content( $ua, 'ua', $item ) );
	}

	/**
	 * IP column content.
	 *
	 * This function is used to modify the column data for ip in listing table.
	 * We can change styles, texts etc. using this function.
	 * Registering filter - "jj4t3_log_list_ip_column".
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.9
	 * @access public
	 *
	 * @return string IP column html content.
	 */
	public function column_ip( $item ) {
		// Get default text if empty value.
		$ip = $this->get_empty_content( $item['ip'] );
		if ( ! $ip ) {
			$ip = sanitize_text_field( $item['ip'] );
		}

		/**
		 * Filter to change IP colum content.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_ip_column', $this->get_group_content( $ip, 'ip', $item ) );
	}

	/**
	 * Custom redirect column content.
	 *
	 * This function is used to modify the column data for custom redirect in listing table.
	 *
	 * @param array $item Column data.
	 *
	 * @since  2.0.9
	 * @access public
	 *
	 * @return string HTML content for redirect column.
	 */
	public function column_redirect( $item ) {
		// Link for redirect.
		$link = esc_url( $item['redirect'] );

		// Get default text if empty value.
		$title = empty( $link ) ? __( 'Default', '404-to-301' ) : $link;

		$redirect = '<a href="javascript:void(0)" title="' . __( 'Customize', '404-to-301' ) . '" class="jj4t3_redirect_thickbox" url_404="' . esc_url( $item['url'] ) . '" wpnonce="' . wp_create_nonce( "jj4t3_redirect_nonce" ) . '">' . $title . '</a>';

		return $redirect;
	}

	/**
	 * Get default text if empty.
	 *
	 * Get an error text with custom class to show if the
	 * current column value is empty or n/a.
	 *
	 * @param string $content Content to display.
	 * @param string $column  Column name.
	 * @param array  $item    Items array.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return string
	 */
	private function get_group_content( $content, $column, $item ) {
		$count_text = '';
		// Check if current column name is grouped.
		// Add count text then.
		if ( ! empty( $item['count'] ) && $item['count'] > 1 && $column === $this->group_by ) {
			$count_text = " (<strong>" . $item['count'] . "</strong>)";
		}

		return '<p>' . $content . $count_text . '</p>';
	}

	/**
	 * Get default text if empty.
	 *
	 * Get an error text with custom class to show if the
	 * current column value is empty or n/a.
	 *
	 * @param string $value Field value.
	 *
	 * @since  3.0.0
	 * @access private
	 *
	 * @return string|boolean
	 */
	private function get_empty_content( $value ) {
		// Get default error text.
		if ( strtolower( $value ) === 'n/a' || empty( $value ) ) {
			return '<span class="jj4t3-url-p">n/a</span>';
		}

		return false;
	}

	/**
	 * Bulk actions drop down.
	 *
	 * Options to be added to the bulk actions drop down for users
	 * to select. We have added 'Delete' actions.
	 * Registering filter - "jj4t3_log_list_bulk_actions".
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array $actions Options to be added to the action select box.
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk_delete'     => __( 'Delete Selected', '404-to-301' ),
			'bulk_clean'      => __( 'Delete All', '404-to-301' ),
			'bulk_delete_all' => __( 'Delete All (Keep redirects)', '404-to-301' ),
		);

		/**
		 * Filter hook to change actions.
		 *
		 * @note  If you are adding extra actions
		 *    Make sure it's actions are properly added.
		 *
		 * @since 3.0.0
		 */
		return apply_filters( 'jj4t3_log_list_bulk_actions', $actions );
	}

	/**
	 * Add extra action dropdown for grouping the error logs.
	 *
	 * @param string $which Top or Bottom.
	 *
	 * @access protected
	 * @since  3.0.0
	 *
	 * @return void
	 */
	public function extra_tablenav( $which ) {
		if ( $this->has_items() && 'top' === $which ) {

			// This filter is already documented above.
			$allowed_values = apply_filters( 'jj4t3_log_list_groupby_allowed', array( 'url', 'ref', 'ip', 'ua' ) );
			// Allowed/available columns.
			$available_columns = jj4t3_log_columns();
			// Consider only available columns.
			$column_names = array_intersect( $allowed_values, array_keys( $available_columns ) );
			// Add dropdown.
			echo '<div class="alignleft actions bulkactions">';
			echo '<select name="group_by_top" class="404_group_by">';
			echo '<option value="">' . esc_html__( 'Group by', '404-to-301' ) . '</option>';
			foreach ( $column_names as $column ) {
				echo '<option value="' . esc_attr( $column ) . '" ' . selected( $column, $this->group_by ) . '>' . esc_attr( $available_columns[ $column ] ) . '</option>';
			}
			echo '</select>';
			submit_button( __( 'Apply', '404-to-301' ), 'button', 'filter_action', false, array( 'id' => 'post-query' ) );
			echo '</div>';

			/**
			 * Action hook to add extra items in actions area.
			 *
			 * @param object $this  Class instance.
			 * @param string $which Current location (top or bottom).
			 */
			do_action( 'jj4t3_log_list_extra_tablenav', $this, $which );
		}
	}

	/**
	 * To perform bulk actions.
	 *
	 * After security check, perform bulk actions selected by
	 * the user. Only allowed actions will be performed.
	 *
	 * @since  2.1.0
	 * @access private
	 * @uses   check_admin_referer() For security check.
	 *
	 * @return bool
	 */
	private function process_actions() {
		// Get current action.
		$action = $this->current_action();

		// Get allowed actions array.
		$allowed_actions = array_keys( $this->get_bulk_actions() );

		// Verify only allowed actions are passed.
		if ( ! in_array( $action, $allowed_actions, true ) && 'delete' !== $action ) {
			return false;
		}

		$nonce = jj4t3_from_request( '_wpnonce' );

		// Nonce verification.
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'bulk-404errorlogs' ) ) {
			return false;
		}

		// IDs of log entries to process.
		$ids = jj4t3_from_request( 'bulk-delete', true );

		// Run custom bulk actions.
		// Add other custom actions in switch..
		switch ( $action ) {
			// Normal selected deletes.
			case 'delete':
			case 'bulk_delete':
			case 'bulk_clean':
			case 'bulk_delete_all':
				$this->delete_logs( $ids, $action );
				break;
			// Add custom actions here.
		}

		return true;
	}

	/**
	 * Remove sensitive values from the URL.
	 *
	 * If WordPress nonce or admin referrer is found in url
	 * remove that and redirect to same page.
	 *
	 * @param boolean $action_performed If any actions performed.
	 *
	 * @access private
	 * @since  3.0.0
	 *
	 * @return void
	 */
	private function safe_redirect( $action_performed = false ) {
		// If sensitive data found, remove those and redirect.
		if ( ! empty( $_GET['_wp_http_referer'] ) || ! empty( $_GET['_wpnonce'] ) || $action_performed ) {
			$strings = array( '_wp_http_referer', '_wpnonce' );
			// Remove processed actions.
			if ( $action_performed ) {
				$strings[] = 'action';
				$strings[] = 'action2';
			}
			// Remove params.
			$url = remove_query_arg( $strings );
			wp_redirect( $url );
			exit();
		}
	}

	/**
	 * Delete error logs.
	 *
	 * Bulk action processor to delete error logs according to
	 * the user selection. We are using IF ELSE loop instead of
	 * switch to easily handle conditions.
	 *
	 * @param mixed  $ids    ID(s) of the log(s).
	 * @param string $action Current bulk action.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function delete_logs( $ids, $action ) {
		global $wpdb;

		if ( is_numeric( $ids ) && 'delete' === $action ) {
			// If a single log is being deleted.
			$query = "DELETE FROM " . JJ4T3_TABLE . " WHERE id = " . absint( $ids );
		} elseif ( is_array( $ids ) && 'bulk_delete' === $action ) {
			// If multiple selected logs are being deleted.
			$ids   = implode( ',', array_map( 'absint', $ids ) );
			$query = "DELETE FROM " . JJ4T3_TABLE . " WHERE id IN($ids)";
		} elseif ( 'bulk_delete_all' === $action ) {
			// If deleting all logs except custom redirected ones.
			// Delete the duplicate entries from custom redirects.
			$query = "DELETE t1 FROM " . JJ4T3_TABLE . " t1, " . JJ4T3_TABLE . " t2 WHERE (t1.id < t2.id AND t1.url = t2.url) OR t1.redirect IS NULL OR t1.redirect = ''";
		} elseif ( 'bulk_clean' === $action ) {
			// If deleting all logs.
			$query = "DELETE FROM " . JJ4T3_TABLE;
		} else {
			// Incase if invalid log ids.
			return;
		}

		// Run query to delete logs.
		$wpdb->query( $query );
	}

	/**
	 * Set screen options of error log listing.
	 *
	 * @param string $status Status.
	 * @param string $option Option name.
	 * @param mixed  $value  Value of the option.
	 *
	 * @since  2.1.0
	 * @access public
	 *
	 * @return string
	 */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Get custom redirect modal content
	 *
	 * @since  2.2.0
	 * @access public
	 *
	 * @global object $wpdb WP DB object
	 * @return void
	 */
	public static function open_redirect() {
		// Yes, security check is a must when you alter something.
		check_ajax_referer( 'jj4t3_redirect_nonce', 'nonce' );

		// The user should have the capability.
		if ( ! current_user_can( JJ4T3_ACCESS ) ) {
			wp_die();
		}

		// Verify if the 404 value is found.
		if ( empty( $_POST['url_404'] ) ) {
			wp_die();
		}

		$url_404 = esc_url_raw( $_POST['url_404'] );

		global $wpdb;

		// Get custom redirect value from db, if exist.
		$result = $wpdb->get_row( $wpdb->prepare( "SELECT redirect, options FROM " . JJ4T3_TABLE . " WHERE url = %s AND redirect IS NOT NULL LIMIT 0,1", $url_404 ), 'OBJECT' );

		// Get custom redirect type and url.
		$url = empty( $result->redirect ) ? '' : esc_url_raw( $result->redirect );

		// Get custom options.
		$options = empty( $result->options ) ? array() : maybe_unserialize( $result->options );

		// Get result in an array.
		$data = array(
			'url_404' => $url_404,
			'url'     => $url,
		);

		// Set the custom options for the 404.
		$data['type']     = empty( $options['type'] ) ? jj4t3_get_option( 'redirect_type' ) : intval( $options['type'] );
		$data['redirect'] = isset( $options['redirect'] ) ? intval( $options['redirect'] ) : - 1;
		$data['log']      = isset( $options['log'] ) ? intval( $options['log'] ) : - 1;
		$data['alert']    = isset( $options['alert'] ) ? intval( $options['alert'] ) : - 1;

		/**
		 * Filter to alter custom redirect modal response array.
		 *
		 * You should return response in array.
		 *
		 * @since 3.0.0
		 */
		wp_send_json( apply_filters( 'jj4t3_log_list_custom_redirect_open', $data ) );
	}

	/**
	 * Save custom redirect value.
	 *
	 * When user set a custom redirect url for a 404 link, save the data
	 * from modal by updating all error logs of the current 404 links.
	 *
	 * @since  2.2.0
	 * @access public
	 *
	 * @note   Always die() for wp_ajax
	 *
	 * @global object $wpdb WP DB object
	 * @return void
	 */
	public static function save_redirect() {
		// Yes, security check is a must when you alter something.
		check_ajax_referer( 'jj4t3_redirect_nonce', 'jj4t3_redirect_nonce' );

		// The user should have the capability.
		if ( ! current_user_can( JJ4T3_ACCESS ) ) {
			wp_die();
		}

		// Custom options for the 404 path.
		$options = maybe_serialize(
			array(
				'redirect' => intval( jj4t3_from_request( 'jj4t3_custom_redirect_redirect' ) ),
				'log'      => intval( jj4t3_from_request( 'jj4t3_custom_redirect_log' ) ),
				'alert'    => intval( jj4t3_from_request( 'jj4t3_custom_redirect_alert' ) ),
				'type'     => intval( jj4t3_from_request( 'jj4t3_custom_redirect_type' ) ),
			)
		);

		// Get 404 url.
		$url = jj4t3_from_request( 'jj4t3_custom_redirect', false ) ? esc_url_raw( jj4t3_from_request( 'jj4t3_custom_redirect' ) ) : '';

		global $wpdb;

		// Get custom redirect url.
		$url_404 = jj4t3_from_request( 'jj4t3_redirect_404', false ) ? esc_url_raw( jj4t3_from_request( 'jj4t3_redirect_404' ) ) : '';

		/**
		 * Action hook to run before updating a custom redirect.
		 *
		 * If you want to change the query or stop the update query, just wp_die()
		 * after your custom function.
		 *
		 * @param string $url_404 404 link.
		 * @param string $url     Link to redirect.
		 *
		 * @since 3.0.0
		 */
		do_action( 'jj4t3_log_list_custom_redirect_save', $url_404, $url );

		// Run update query and set custom redirect.
		$wpdb->query( $wpdb->prepare( "UPDATE " . JJ4T3_TABLE . " SET redirect = %s, options = %s WHERE url = %s", $url, $options, $url_404 ) );

		// Die ajax request.
		wp_die();
	}

	/**
	 * This function displays the custom redirect modal html content
	 *
	 * @since 2.2.0
	 * @acess public
	 *
	 * @return void
	 */
	public static function get_redirect_content() {
		if ( current_user_can( JJ4T3_ACCESS ) ) {
			include_once JJ4T3_DIR . 'includes/admin/views/custom-redirect.php';
		}
	}
}
