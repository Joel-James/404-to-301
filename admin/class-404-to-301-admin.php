<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueue the dashboard-specific stylesheet, JavaScript
 * and all other admin side functions.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage Admin
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _404_To_301_Admin {

    /**
     * The options from db.
     *
     * @since  2.0.0
     * @access private
     * @var    string  $gnrl_options Get the options saved in db.
     */
    private $gnrl_options;

    /**
     * The logs list table.
     *
     * @since  2.1.0
     * @access private
     * @var    mixed   $list_table Class object for listing table.
     */
    private $list_table;

    /**
     * Initialize the class and set its properties.
     *
     * @since  2.0.0
     * @access public 
     * 
     * @var string $gnrl_options The option settings of the plugin.
     * 
     * @return void
     */
    public function __construct() {

        $this->gnrl_options = get_option('i4t3_gnrl_options');
    }

    /**
     * Register the stylesheet for the Dashboard.
     *
     * This function is used to register all the required stylesheets for
     * dashboard. Styles will be registered only for i4t3 pages for performance.
     *
     * @since  2.0.0
     * @access public
     * @uses   wp_enqueue_style To register style
     * 
     * @return void
     */
    public function enqueue_styles() {

        global $pagenow;

        if( ( $pagenow == 'admin.php' ) && ( in_array( $_GET['page'], array('i4t3-settings', 'i4t3-logs' ) ) ) ) {
            wp_enqueue_style(
                I4T3_NAME,
                plugin_dir_url(__FILE__) . 'css/min/admin.css',
                array(),
                I4T3_VERSION,
                'all'
            );
        }
    }

    /**
     * Register the scripts for the Dashboard.
     *
     * This function is used to register all the required scripts for
     * dashboard. Scripts will be registered only for i4t3 pages for performance.
     *
     * @since  2.0.0
     * @access public
     * @uses   wp_enqueue_script To register script
     * 
     * @return void
     */
    public function enqueue_scripts() {

        global $pagenow;

        if( ( $pagenow == 'admin.php' ) && ( in_array( $_GET['page'], array( 'i4t3-settings', 'i4t3-logs' ) ) ) ) {
            wp_enqueue_script(
                I4T3_NAME,
                plugin_dir_url(__FILE__) . 'js/admin.js',
                array('jquery'),
                I4T3_VERSION,
                false
            );
            // Internationalization
            wp_localize_script(
                I4T3_NAME,
                'i4t3strings',
                array(
                    'redirect' => esc_html__( 'Custom Redirect', I4T3_DOMAIN ),
                )
            );
        }
    }

    /**
     * Run upgrade functions
     *
     * If 404 to 301 is upgraded, we may need to perform few updations in db
     * 
     * @since  2.0.0
     * @access public
     * @uses   get_option() To get the activation redirect option from db.
     * 
     * @return void
     */
    public function i4t3_upgrade_if_new() {

        if( ! get_option( 'i4t3_version_no' ) || ( get_option( 'i4t3_version_no' ) < I4T3_VERSION ) ) {
            // call activator class once more
            if( ! class_exists( '_404_To_301_Activator' ) ) {
                include_once I4T3_PLUGIN_DIR . '/includes/class-404-to-301-activator.php';
            }
            _404_To_301_Activator::activate();
            // update plugin version
            update_option('i4t3_version_no', I4T3_VERSION );
        }
    }

    /**
     * Changing email notification recipient
     *
     * Using filter to change email notification recipient address from
     * default admin email.
     * 
     * @since  2.0.7
     * @access public
     * @uses   get_option() To get the email address option from db.
     * 
     * @return string $email.
     */
    public function i4t3_change_notify_email( $email ) {
        
        if( ! empty( $this->gnrl_options['email_notify_address'] ) ) {
            $email_option = $this->gnrl_options['email_notify_address'];
            if( is_email( $email_option ) ) {
                $email = $email_option;
            }
        }
        
        return $email;
    }

    /**
     * Creating admin menus for 404 to 301.
     *
     * @since  2.0.0
     * @access public
     * @uses   action hook add_submenu_page Action hook to add new admin menu sub page.
     * 
     * @return void
     */
    public function i4t3_create_404_to_301_menu() {

        // Error log menu
        $hook = add_menu_page(
            __( '404 Error Logs', '404-to-301' ),
            __( '404 Error Logs', '404-to-301' ),
            I4T3_ADMIN_PERMISSION,
            'i4t3-logs',
            array( $this, 'i4t3_render_list_page' ),
            'dashicons-redo',
            90
        );

        add_action( "load-$hook", array( $this, 'screen_option' ) );

        // 404 to 301 settings menu
        add_submenu_page(
            'i4t3-logs',
            __('404 to 301 Settings', '404-to-301'),
            __('404 Settings', '404-to-301'),
            I4T3_ADMIN_PERMISSION,
            'i4t3-settings',
            array( $this, 'i4t3_admin_page' )
        );
        
        // admin menu item acion hook
        do_action('i4t3_admin_page');
    }

    /**
     * To set the screen of the error listing page.
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
     * To make screen options for 404 to 301 listing.
     *
     * This function is used to show screen options like entries per page,
     * show/hide columns etc.
     *
     * @since  2.1.0
     * @access public
     * 
     * @return void
     */
    public function screen_option() {

        $option = 'per_page';
        $args = array(
            'label' => __('Error Logs', '404-to-301'),
            'default' => 20,
            'option' => 'logs_per_page'
        );

        add_screen_option( $option, $args );

        $this->list_table = new _404_To_301_Logs();
    }

    /**
     * Output buffer function
     *
     * To avoid header already sent issue
     * 
     * @link   https://tommcfarlin.com/wp_redirect-headers-already-sent/
     * @since  2.1.4
     * @access public
     * 
     * @return void
     */
    public function add_buffer() {

        ob_start();
    }

    /**
     * Creating log table page.
     *
     * @since  2.0.0
     * @access public
     * @uses   class  _404_To_301_Logs To initialize and load the log listing table.
     * 
     * @return void
     */
    public function i4t3_render_list_page() { ?>
        
        <div class="wrap">
            <h2><?php _e('404 Error Logs', '404-to-301'); ?></h2>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                            <?php
                            $this->list_table->prepare_items();
                            $this->list_table->display();
                            ?>
                            </form>
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
     * @var    global $menu Menus registered in this site.
     * 
     * @return void
     */
    public function i4t3_rename_plugin_menu() {
        
        global $menu;
        // change menu text
        $menu[90][0] = __('404 to 301', '404-to-301');
    }

    /**
     * Admin options page display.
     *
     * Includes admin page contents to manage i4t3 settings.
     * All html parts will be included in this page.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    public function i4t3_admin_page() {

        include_once  I4T3_PLUGIN_DIR . '/admin/partials/404-to-301-admin-display.php';
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
    public function i4t3_options_register() {

        register_setting(
            'i4t3_gnrl_options',
            'i4t3_gnrl_options'
        );
    }

    /**
     * Custom footer text for i4t3 pages.
     *
     * Function to alter the default footer text to show i4t3 credits only on i4t3 pages.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return mixed
     */
    public function i4t3_dashboard_footer() {
        
        // current page global var
        global $pagenow;
        
        if( ( $pagenow == 'admin.php' ) && ( in_array( $_GET['page'], array( 'i4t3-settings', 'i4t3-logs' ) ) ) ) {

            _e('Thank you for choosing 404 to 301 to improve your website', '404-to-301');
            echo ' | ';
            printf(__('Kindly give this plugin a %srating%s', '404-to-301'), '<a href="https://wordpress.org/support/view/plugin-reviews/404-to-301?filter=5#postform">', ' &#9733; &#9733; &#9733; &#9733; &#9733;</a>');
        } else {
            return;
        }
    }

    /**
     * Custom Plugin Action Link.
     *
     * Function to add a quick link to i4t3, when being listed on your
     * plugins list view.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return array $links Links to display.
     */
    public function i4t3_plugin_action_links($links, $file) {
        
        $plugin_file = basename('404-to-301.php');
        
        if ( basename( $file ) == $plugin_file ) {
            $settings_link = '<a href="admin.php?page=i4t3-settings">' . __('Settings', '404-to-301') . '</a>';
            $settings_link .= ' | <a href="admin.php?page=i4t3-logs">' . __('Logs', '404-to-301') . '</a>';
            
            array_unshift( $links, $settings_link );
        }
        
        return $links;
    }
    
    /**
     * This function includes required scripts for custom modal
     * 
     * This function registers scripts required for WordPress
     * thickbox modal.
     * 
     * @since  2.1.1
     * @access public
     * 
     * @return void
     */
    public function add_thickbox() {
        
        return add_thickbox();
    }
    
    /**
     * Get custom redirect modal content
     * 
     * @global object $wpdb WP DB object
     * @since  2.2.0
     * @access public
     * 
     * @note Always die() for wp_ajax
     * 
     * @return JSON
     */
    public function open_custom_redirect() {
        
        // verify if required value is available
        if ( ! isset( $_POST['url_404'] ) || is_null( $_POST['url_404'] ) ) {
            die();
        }
        // 404 path url
        $url_404 = trim( $_POST['url_404'] );
        
        global $wpdb;
        // make sure that the errors are hidden
        $wpdb->hide_errors();
        // get the custom redirect data for the given 404 path
        $sql = "SELECT redirect FROM " . I4T3_TABLE . " WHERE url = '" . $url_404 . "' AND redirect IS NOT NULL LIMIT 0,1";
        $url =  $wpdb->get_var($sql);
        // make sure that the result is not error
        $url = ( empty( $url ) ) ? '' : $url;
        // make response array
        $data = array(
            'url_404' => $url_404,
            'url' => $url
        );
        // resturn josn output and die
        wp_send_json( $data );
    }
    
    /**
     * Save custom redirect value
     * 
     * @global object $wpdb WP DB object
     * @since  2.2.0
     * @access public
     * 
     * @note Always die() for wp_ajax
     * 
     * @return void
     */
    public function save_custom_redirect() {
        
        // verify the nonce for ajax
        $secure = check_ajax_referer( 'i4t3_custom_redirect_nonce', 'nonce', false );
        if( ! $secure ) {
            die( 'Go take a bath' );
        }
        
        // if required values are not given, kill
        if ( ! isset( $_POST['url_404'] ) || ! isset( $_POST['url'] ) ) {
            die();
        }
        // get the required values from request
        $url_404 = $_POST['url_404'];
        $url = $_POST['url'];
        
        global $wpdb;
        // make sure that the errors are hidden
        $wpdb->hide_errors();
        // update the custom redirect value for the 404 path
        $wpdb->query(
            $wpdb->prepare( 
                "UPDATE " . I4T3_TABLE . "
                SET redirect = '%s'
                WHERE url = '%s'", 
                $url, 
                $url_404
            )
        );
        
        die();
    }

    /**
     * This function displays the custom redirect modal html content
     * 
     * @since 2.2.0
     * @acess public
     * 
     * @return void
     */
    public function get_redirect_content() {
        
        include_once I4T3_PLUGIN_DIR . '/admin/partials/404-to-301-admin-custom-redirect.php';
    }
    
    /**
     * This function updates terms and conditions options
     * 
     * @since 2.2.0
     * @acess public
     * 
     * @return void
     */
    public function agreement_notice() {

        if( isset( $_GET['i4t3_agreement'] ) ) {
            $agreement = ($_GET['i4t3_agreement'] == 0) ? 0 : 1;
            update_option( 'i4t3_agreement', $agreement );
        }
    }

    /**
     * Get debug data.
     *
     * Function to output the debug data for the plugin. This will be useful
     * when asking for support. Just copy and paste these data to the email.
     *
     * Please DO NOT translate this part, as this need to be provided for debugging only.
     *
     * @since    2.0.0
     * @var 		array 	$gnrl_options 	Array of plugin settings
     * @var 		array 	$active_plugins  Array of active plugins path
     * @return	$html		Html content to diplay.
     * @author	Joel James
     */
    public function i4t3_get_debug_data() {

        $html = '';
        $gnrl_options = get_option('i4t3_gnrl_options');
        $active_plugins = get_option('active_plugins', array());
        $active_theme = wp_get_theme();

        // Dump the plugin settings data
        if (!empty($gnrl_options)) {
            $html .= '<h4>' . __('Settings Data', '404-to-301') . '</h4><p><pre>';
            foreach ($gnrl_options as $key => $option) {
                $html .= $key . ' : ' . $option . '<br/>';
            }
            $html .= '</pre></p><hr/>';
        }
        // Output basic info about the site
        $html .= '<h4>' . __('Basic Details', '404-to-301') . '</h4><p>
		' . __('WordPress Version', '404-to-301') . ' : ' . get_bloginfo('version') . '<br/>
		' . __('PHP Version', '404-to-301') . ' : ' . PHP_VERSION . '<br/>
                ' . __('Plugin Version', '404-to-301') . ' : ' . I4T3_VERSION . '<br/>
		' . __('Home Page', '404-to-301') . ' : ' . home_url() . '<br/></p><hr/>';

        if ($active_theme->exists()) {

            $html .= '<h4>' . __('Active Theme Details', '404-to-301') . '</h4><p>
		' . __('Name', '404-to-301') . ' : ' . $active_theme->get('Name') . '<br/>
		' . __('Version', '404-to-301') . ' : ' . $active_theme->get('Version') . '<br/>
		' . __('Theme URI', '404-to-301') . ' : ' . $active_theme->get('ThemeURI') . '<br/></p><hr/>';
        }

        // Dump the active plugins data
        if (!empty($active_plugins)) {
            $html .= '<h4>' . __('Active Plugins', '404-to-301') . '</h4><p>';
            foreach ($active_plugins as $plugin) {
                $html .= $plugin . '<br/>';
            }
            $html .= '</p>';
        }

        return $html;
    }
}
