<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://jeoga.com/
 * @since             1.0.0
 * @package           oye-notification
 *
 * @wordpress-plugin
 * Plugin Name:       Oye Notification
 * Plugin URI:        https://github.com/mrkhanakia/wp_oye_notification.git
 * Description:       Show Notification Bar on your website.
 * Version:           1.0.0
 * Author:            Aman Khanakia
 * Author URI:        http://jeoga.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       oye-notification
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OYE_NOTIFICATION', 'oye-notification' );
define( 'OYE_NOTIFICATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-oye-notification-activator.php
 */
function activate_plugin_name() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-oye-notification-activator.php';
	// Plugin_Name_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-oye-notification-deactivator.php
 */
function deactivate_plugin_name() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-oye-notification-deactivator.php';
	// Plugin_Name_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
// require plugin_dir_path( __FILE__ ) . 'includes/class-oye-notification.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */


require plugin_dir_path( __FILE__) . 'vendor/autoload.php';

function rms_enqueue_script() {
	wp_enqueue_script( OYE_NOTIFICATION, plugin_dir_url( __FILE__ ) . 'public/js/oye-notification-public.js', array( 'jquery' ), OYE_NOTIFICATION_VERSION, false );
	wp_enqueue_style( OYE_NOTIFICATION, plugin_dir_url( __FILE__ ) . 'public/css/oye-notification-public.css', array(), OYE_NOTIFICATION_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'rms_enqueue_script' );


function run_oye_notification() {
	// Our custom post type function
	function create_posttype() {
	 
	    register_post_type( 'notification_bar',
            array(
                'labels' => array(
                    'name' => 'Notification Bar',
                ),
                'menu_position' => 15,
                'supports' => array( 'title', 'custom-fields' ),
                'rewrite'   => array( 'slug' => false, 'with_front' => false ), 
                'has_archive' => true,
                'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
                'publicly_queriable' => true,  // you should be able to query it
                'show_ui' => true,  // you should be able to edit it in wp-admin
            )
		);
	}
	// Hooking up our function to theme setup
	add_action( 'init', 'create_posttype' );


	
	add_filter( 'rwmb_meta_boxes', 'mb_composer_example_register_meta_boxes' );
	function mb_composer_example_register_meta_boxes( $meta_boxes ) {
	    $meta_boxes[] = array(
            'id'    => 'notification_data',
            'title'   => __('Notification Section', ''),
            'pages'   => array('notification_bar'),
            'context' => 'normal',
            'priority'  => 'high',
            'fields'  => array(
                array(
                    'name'    => __('Notification Text', ''),
                    'id'    => "notification_bar_text",
                    'type'        => 'textarea',
                    'desc'    => __(''),
                ),            
                array(
                    'name'    => __('Notification Start Date', ''),
                    'id'    => "notification_bar_startdate",
                    'type'        => 'date',
                    'desc'    => __(''),
                ),            
                array(
                    'name'    => __('Notification End Date', ''),
                    'id'    => "notification_bar_enddate",
                    'type'        => 'date',
                    'desc'    => __(''),
                ),            
                array(
                    'name'    => __('Notification Background Color', ''),
                    'id'    => "notification_bar_background",
                    'type'        => 'color',
                    'desc'    => __(''),
                ),            
                array(
                    'name'    => __('Notification Text Color', ''),
                    'id'    => "notification_bar_textcolor",
                    'type'        => 'color',
                    'desc'    => __(''),
                ),            
            ),
        );

	    return $meta_boxes;
	}

	function render_template() {
        $args = array( 'post_type' => 'notification_bar', 'posts_per_page' => 20,'orderby' => 'menu_order', 'post_status' => 'publish');
        $posts = get_posts($args);

		
		$loader = new \Twig_Loader_Filesystem(plugin_dir_path( __FILE__).'public/views');
        $twig = new \Twig_Environment($loader, array(
            // 'cache' => '/path/to/compilation_cache',
        ));
        $template = $twig->load('notification-bar.twig');
        foreach ($posts as $key => $post) {
            $meta_data = get_post_meta( $post->ID);
            $template_args = [
                'notification_bar_text' => get_post_meta($post->ID, 'notification_bar_text', true),
                'notification_bar_startdate' => get_post_meta($post->ID, 'notification_bar_startdate', true),
                'notification_bar_enddate' => get_post_meta($post->ID, 'notification_bar_enddate', true),
                'notification_bar_background' => get_post_meta($post->ID, 'notification_bar_background', true),
                'notification_bar_textcolor' => get_post_meta($post->ID, 'notification_bar_textcolor', true),
            ];

            // var_dump($template_args);
            $mydate = date('Y-m-d');
            if ($template_args['notification_bar_text']
                && $template_args['notification_bar_startdate'] 
                && $template_args['notification_bar_enddate'] 
                && $template_args['notification_bar_startdate']  <= $mydate 
                && $mydate <= $template_args['notification_bar_enddate']
                && $template_args['notification_bar_startdate'] <= $template_args['notification_bar_enddate']) {
                echo $template->render($template_args);
            }
        }
	}
	
	add_action( 'wp_footer', 'render_template');
}

run_oye_notification();
