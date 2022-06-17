<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Register all hooks related to admin area of the website.
 *
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @category   Core
 * @link       https://duckdev.com/products/404-to-301/
 * @package    JJ4T3
 * @subpackage Admin
 */
class JJ4T3_Admin {

	/**
	 * Error listing table.
	 *
	 * @var object
	 */
	public $list_table;

	/**
	 * Initialize the class.
	 *
	 * Register all hooks in this class.
	 * All admin facing functionality.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function __construct() {

		add_filter( 'admin_init', array( $this, 'add_buffer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'rename_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'set-screen-option', array( 'JJ4T3_Log_Listing', 'set_screen' ), 10, 3 );
		add_action( 'admin_footer', array( $this, 'add_thickbox' ), 100 );
		add_action( 'wp_ajax_jj4t3_redirect_thickbox', array( 'JJ4T3_Log_Listing', 'open_redirect' ), 100 );
		add_action( 'wp_ajax_jj4t3_redirect_form', array( 'JJ4T3_Log_Listing', 'save_redirect' ) );
		add_action( 'admin_footer', array( 'JJ4T3_Log_Listing', 'get_redirect_content' ), 100 );
		add_filter( 'plugin_action_links', array( $this, 'action_links' ), 10, 5 );
		add_action( 'plugins_loaded', array( $this, 'upgrade' ) );

		// Show review request.
		add_action( 'admin_notices', array( $this, 'review_notice' ) );
		add_action( 'admin_init', array( $this, 'review_action' ) );
	}

	/**
	 * Output buffer function.
	 *
	 * To avoid header already sent issues.
	 *
	 * @link   https://tommcfarlin.com/wp_redirect-headers-already-sent/
	 * @since  2.1.4
	 * @access public
	 *
	 * @uses   ob_start() To load buffer.
	 *
	 * @return void
	 */
	public function add_buffer() {

		ob_start();
	}

	/**
	 * Register the stylesheet for the Dashboard.
	 *
	 * This function is used to register all the required stylesheets for
	 * dashboard.
	 * Styles will be registered only for our plugin pages for performance.
	 *
	 * @since  2.0.0
	 * @access public
	 * @global string $pagenow Current page.
	 * @uses   wp_enqueue_style To register styles.
	 *
	 * @return void
	 */
	public function styles() {
		global $pagenow;

		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && in_array(
			$_GET['page'],
			array(
				'jj4t3-settings',
				'jj4t3-logs',
			),
			true
		)
		) {
			wp_enqueue_style(
				JJ4T3_NAME,
				JJ4T3_URL . 'assets/css/admin.min.css',
				array(),
				JJ4T3_VERSION,
				'all'
			);
		}
	}

	/**
	 * Register the scripts for the Dashboard.
	 *
	 * This function is used to register all the required scripts for
	 * dashboard.
	 * Scripts will be registered only for our plugin pages for performance.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @global string $pagenow Current page.
	 *
	 * @uses   wp_localize_script To translate strings in js.
	 *
	 * @uses   wp_enqueue_script  To register script.
	 * @return void
	 */
	public function scripts() {
		global $pagenow;

		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && in_array(
			$_GET['page'],
			array(
				'jj4t3-settings',
				'jj4t3-logs',
			),
			true
		)
		) {
			wp_enqueue_script(
				JJ4T3_NAME,
				JJ4T3_URL . 'assets/js/admin.min.js',
				array( 'jquery' ),
				JJ4T3_VERSION,
				false
			);

			// Strings to translate in js.
			$strings = array( 'redirect' => esc_html__( 'Custom Redirect', '404-to-301' ) );

			wp_localize_script( JJ4T3_NAME, 'jj4t3strings', $strings );
		}
	}

	/**
	 * Creating admin menus for 404 to 301.
	 *
	 * Creates one main menu and few sub menu items.
	 * Menu menu will be linked to 404 error logs by default.
	 * Set menu access permissions using JJ4T3_ACCESS constant.
	 * Regitsering action hook - "jj4t3_admin_page".
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @uses   add_submenu_page() Action hook to add new admin menu sub page.
	 *
	 * @return void
	 */
	public function admin_menu() {

		// Main menu for error logs list.
		$hook = add_menu_page(
			__( '404 Error Logs', '404-to-301' ),
			__( '404 Errors', '404-to-301' ),
			JJ4T3_ACCESS,
			'jj4t3-logs',
			array(
				$this,
				'error_list',
			),
			'dashicons-redo',
			90
		);

		// Render screen options on listing table.
		add_action( "load-$hook", array( $this, 'screen_option' ) );

		// 404 to 301 settings menu.
		add_submenu_page(
			'jj4t3-logs',
			__( '404 to 301 Settings', '404-to-301' ),
			__( '404 Settings', '404-to-301' ),
			JJ4T3_ACCESS,
			'jj4t3-settings',
			array(
				$this,
				'admin_page',
			)
		);

		/**
		 * Action hook to register new submenu item.
		 *
		 * You can user this action hook to register any custom
		 * submenu items to 404 to 301 menu.
		 * This can be used by add-ons of our plugins.
		 *
		 * @since 2.0.0
		 */
		do_action( 'i4t3_admin_page' );
	}

	/**
	 * To make screen options for error listing.
	 *
	 * This function is used to show screen options like entries per page,
	 * show/hide columns etc.
	 * This feature is extended from WP_List_Table class.
	 *
	 * @since  2.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function screen_option() {

		$args = array(
			'label'   => __( 'Error Logs', '404-to-301' ),
			'default' => 20,
			'option'  => 'logs_per_page',
		);

		add_screen_option( 'per_page', $args );

		// Error log listing table.
		$this->list_table = new JJ4T3_Log_Listing();
	}

	/**
	 * Show error listing table view.
	 *
	 * This method displays the listing table HTML to the page.
	 * Registering action hook - "jj4t3_log_list_above_form".
	 * Registering action hook - "jj4t3_log_list_below_form".
	 *
	 * @since  2.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function error_list() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( '404 Error Logs', '404-to-301' ); ?></h2>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<?php
							$this->list_table->prepare_items();
							/**
							 * Action hook to add something above listing page.
							 *
							 * Use this action hook to add custom filters and search
							 * boxes to the listing table top section.
							 *
							 * @param object $this Listing page class object.
							 *
							 * @since 3.0.0
							 */
							do_action( 'jj4t3_log_list_above_form', $this->list_table );
							?>
							<form method="get">
								<input type="hidden" name="page" value="jj4t3-logs"/>
								<?php $this->list_table->display(); ?>
							</form>
							<?php
							/**
							 * Action hook to add something below the listing page.
							 *
							 * Use this action hook to add custom filters and search
							 * boxes to the listing table bottom section.
							 *
							 * @param object $this Listing page class object.
							 *
							 * @since 3.0.0
							 */
							do_action( 'jj4t3_log_list_below_form', $this->list_table );
							?>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}

	/**
	 * Rename admin menu text to : 404 to 301.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @global array $menu Menus registered in this site.
	 *
	 * @return void
	 */
	public function rename_menu() {

		global $menu;

		$menu[90][0] = __( '404 to 301', '404-to-301' );
	}

	/**
	 * Registering i4t3 options.
	 * This function is used to register all settings options to the db using
	 * WordPress settings API.
	 * If we want to register another setting, we can include that here.
	 *
	 * @since  2.0.0
	 * @access public
	 * @uses   hooks  register_setting Hook to register i4t3 options in db.
	 *
	 * @return void
	 */
	public function register_settings() {

		register_setting( 'i4t3_gnrl_options', 'i4t3_gnrl_options' );
	}

	/**
	 * Admin options page display.
	 *
	 * Admin page template to manage plugin settings and
	 * other options.
	 * All template related things can be managed from this
	 * file.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function admin_page() {

		include_once JJ4T3_DIR . 'includes/admin/views/admin.php';
	}

	/**
	 * This function includes required scripts for thickbox.
	 *
	 * This function registers scripts required for WordPress thickbox modal.
	 *
	 * @since  2.1.1
	 * @access public
	 *
	 * @return mixed
	 */
	public function add_thickbox() {

		return add_thickbox();
	}

	/**
	 * Action links for plugins listing page.
	 *
	 * Add quick links to plugin settings page, error listing page
	 * from the plugins listing page.
	 *
	 * @param array $links Links array.
	 * @param array $file  File name.
	 *
	 * @return array
	 */
	public function action_links( $links, $file ) {

		$plugin_file = basename( '404-to-301.php' );

		if ( basename( $file ) === $plugin_file ) {
			$settings_link  = '<a href="admin.php?page=jj4t3-settings">' . __( 'Settings', '404-to-301' ) . '</a>';
			$settings_link .= ' | <a href="admin.php?page=jj4t3-logs">' . __( 'Logs', '404-to-301' ) . '</a>';

			// Add quick links to plugins listing page.
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Upgrade plugin on updates.
	 *
	 * If there are structural changes to make after new version release,
	 * make required changes.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function upgrade() {

		$current_version = get_option( 'i4t3_version_no', false );

		if ( ! $current_version || ( $current_version < JJ4T3_VERSION ) ) {
			if ( ! class_exists( 'JJ4T3_Activator_Deactivator_Uninstaller' ) ) {
				include_once JJ4T3_DIR . 'includes/class-jj4t3-activator-deactivator-uninstaller.php';
			}

			// Run upgrade actions.
			JJ4T3_Activator_Deactivator_Uninstaller::activate();

			// Update the plugin version number.
			update_option( 'i4t3_version_no', JJ4T3_VERSION );
		}
	}

	/**
	 * Show admin to ask for review in wp.org.
	 *
	 * Show admin notice only inside our plugin's settings page.
	 * Hide the notice permanently if user dismissed it.
	 *
	 * @since 3.0.4
	 *
	 * @return void|bool
	 */
	public function review_notice() {
		global $pagenow;

		// Only on our page.
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && in_array(
			$_GET['page'],
			array(
				'jj4t3-settings',
				'jj4t3-logs',
				'jj4t3-logs-addons',
			)
		)
		) {
			// Only for admins.
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}
			// Get the notice time.
			$notice_time = get_option( 'i4t3_review_notice' );
			// If not set, set now and bail.
			if ( ! $notice_time ) {
				// Set to next week.
				return add_option( 'i4t3_review_notice', time() + 604800 );
			}

			// Current logged in user.
			$current_user = wp_get_current_user();
			// Did the current user already dismiss?.
			$dismissed = get_user_meta( $current_user->ID, 'i4t3_review_notice_dismissed', true );
			// Continue only when allowed.
			if ( (int) $notice_time <= time() && ! $dismissed ) {
				?>
				<div class="notice notice-success">
					<p>
						<?php
						printf(
							__( 'Hey %1$s, I noticed you\'ve been using %2$s404 to 301%3$s for more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.', '404-to-301' ),
							empty( $current_user->display_name ) ? esc_html__( 'there', '404-to-301' ) : esc_html( ucwords( $current_user->display_name ) ),
							'<strong>',
							'</strong>'
						);
						?>
					</p>
					<p>
						<a href="https://wordpress.org/support/plugin/404-to-301/reviews/#new-post" target="_blank"><?php esc_html_e( 'Ok, you deserve it', '404-to-301' ); ?></a>
					</p>
					<p>
						<a href="<?php echo esc_url( add_query_arg( 'jj4t3_rating', 'later' ) ); // later. ?>"><?php esc_html_e( 'Nope, maybe later', '404-to-301' ); ?></a>
					</p>
					<p>
						<a href="<?php echo esc_url( add_query_arg( 'jj4t3_rating', 'dismiss' ) ); // dismiss link. ?>"><?php esc_html_e( 'I already did', '404-to-301' ); ?></a>
					</p>
				</div>
				<?php
			}
		}
	}

	/**
	 * Handle review notice actions.
	 *
	 * If dismissed set a user meta for the current
	 * user and do not show again.
	 * If agreed to review later, update the review
	 * timestamp to after 2 weeks.
	 *
	 * @since 3.0.4
	 *
	 * @return void
	 */
	public function review_action() {
		// Get the current review action.
		$action = jj4t3_from_request( 'jj4t3_rating' );

		switch ( $action ) {
			case 'later':
				// Let's show after another 2 weeks.
				update_option( 'i4t3_review_notice', time() + 1209600 );
				break;
			case 'dismiss':
				// Do not show again to this user.
				update_user_meta( get_current_user_id(), 'i4t3_review_notice_dismissed', 1 );
				break;
		}
	}
}
