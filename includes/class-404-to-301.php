<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Damn it.! Dude you are looking for what?' );
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage Core
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _404_To_301 {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since  2.0.0
     * @access protected
     * @var    _404_To_301_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name, plugin version and the plugin table name that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct() {

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - _404_To_301_Loader. Orchestrates the hooks of the plugin.
     * - _404_To_301_Admin. Defines all hooks for the dashboard.
     * - _404_To_301_Public. Defines all hooks for the public functions.
     * - _404_To_301_Logs. Defines all hooks for listing logs.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since  2.0.0
     * @access private
     * 
     * @return void
     */
    private function load_dependencies() {

        include_once I4T3_PLUGIN_DIR . '/includes/class-404-to-301-loader.php';
        include_once I4T3_PLUGIN_DIR . '/includes/class-404-to-301-i18n.php';
        include_once I4T3_PLUGIN_DIR . '/admin/class-404-to-301-admin.php';
        include_once I4T3_PLUGIN_DIR . '/admin/class-404-to-301-logs.php';
        include_once I4T3_PLUGIN_DIR . '/public/class-404-to-301-public.php';

        $this->loader = new _404_To_301_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since  1.0.0
     * @access private
     * 
     * @return void
     */
    private function set_locale() {
        
        $plugin_i18n = new _404_To_301_i18n();
        
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     * 
     * This function is used to register all styles and JavaScripts for admin side.
     *
     * @since  2.0.0
     * @access private
     * @uses   add_action()
     * @uses   add_filter()
     * 
     * @return void
     */
    private function define_admin_hooks() {

        $plugin_admin = new _404_To_301_Admin();

        $this->loader->add_filter('admin_init', $plugin_admin, 'add_buffer');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'i4t3_create_404_to_301_menu');
        $this->loader->add_action('admin_menu', $plugin_admin, 'i4t3_rename_plugin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'i4t3_options_register');
        $this->loader->add_filter('admin_footer_text', $plugin_admin, 'i4t3_dashboard_footer');
        $this->loader->add_filter('plugin_action_links', $plugin_admin, 'i4t3_plugin_action_links', 10, 5);
        $this->loader->add_action('plugins_loaded', $plugin_admin, 'i4t3_upgrade_if_new');
        $this->loader->add_filter('i4t3_notify_admin_email_address', $plugin_admin, 'i4t3_change_notify_email');
        $this->loader->add_filter('set-screen-option', $plugin_admin, 'set_screen', 10, 3);
        $this->loader->add_action('admin_footer', $plugin_admin, 'add_thickbox', 100);
        $this->loader->add_action('admin_footer', $plugin_admin, 'get_redirect_content');
        $this->loader->add_action('wp_ajax_i4t3_redirect_thickbox', $plugin_admin, 'open_custom_redirect');
        $this->loader->add_action('wp_ajax_i4t3_redirect_form', $plugin_admin, 'save_custom_redirect');
        $this->loader->add_action('admin_init', $plugin_admin, 'agreement_notice');
    }

    /**
     * Register all of the hooks related to handle 404 actions of the plugin.
     *
     * @since  2.0.0
     * @access private
     * @uses   add_filter()
     * 
     * @return void
     */
    private function define_public_hooks() {

        $plugin_public = new _404_To_301_Public();
        
        // Main Hook to perform redirections on 404s
        $this->loader->add_filter('template_redirect', $plugin_public, 'i4t3_redirect_404');
        $this->loader->add_filter('the_content', $plugin_public, 'load_from_cdn');
    }
	
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    public function run() {
        
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return i4t3_Loader Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        
        return $this->loader;
    }
}
