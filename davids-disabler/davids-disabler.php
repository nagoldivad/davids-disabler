<?php
/**
 * David's Disabler
 *
 * @package           PluginPackage
 * @author            David Logan
 * @copyright         None
 * @license           Unlicense
 *
 * @wordpress-plugin
 * Plugin Name:       David's Disabler
 * Plugin URI:        https://nagoldivad.com
 * Description:       Disable all the things.
 * Version:           0.9
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            David Logan
 * Author URI:        https://nagoldivad.com/
 * License:           Unlicense
 * License URI:       https://unlicense.org/
 * Update URI:        
 * Text Domain:       davids-disabler
 * Domain Path:       /languages
 * Requires Plugins:  
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * plugin styles
 */

// Hook to enqueue styles
add_action('admin_enqueue_scripts', 'dd_plugin_enqueue_styles');
function dd_plugin_enqueue_styles() {
    // Get the plugin directory URL
    $plugin_url = plugin_dir_url(__FILE__);
    // Enqueue the stylesheet
    wp_enqueue_style('dd-plugin-style', $plugin_url . 'dd-styles.css', array(), '1.0', 'all');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Array of all options (for convenience)
 */

 function tgd_all_options() {
    return array(
        'disable_comments_new',
        'disable_comments_hide_existing',
        'disable_comments_hide_backend',
        'disable_xmlrpc',
        'disable_rest',
        'disable_admin_bar',
        'disable_file_editing',
        'disable_post_revisions',
        'disable_emoji',
        'disable_media_attachments',
        'disable_admin_footer_text',
        'disable_admin_footer_version'
    );
 }


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Activation
 */

function tgd_activate() {
    $tgd_options = tgd_all_options();
    $options_r = array();
    foreach ($tgd_options as $string) {
        $options_r[$string] = false;
    }
    add_option('tgd_options', $options_r);
}
register_activation_hook(__FILE__, 'tgd_activate');


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Deactivation
 */

function tgd_deactivate() {
    // remove the option
    delete_option('tgd_options');
}
register_deactivation_hook(__FILE__,'tgd_deactivate');


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Add Options page to Tools submenu
 */

function tgd_options_page() {
    add_submenu_page(
        'tools.php',            // parent slug
        'David\'s Disabler',    // page title
        'David\'s Disabler',    // menu title
        'manage_options',       // capability
        'tgd',                  // menu slug?
        'tgd_options_page_html' // callback
    );
}
add_action('admin_menu', 'tgd_options_page');


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Register settings
 */

function tgd_register_settings() {
    register_setting(
        'tgd_options_group',   // Option group
        'tgd_options',         // Option name
        'tgd_sanitize_options'  // Sanitize callback
    );
}
add_action('admin_init', 'tgd_register_settings');

// Sanitize callback function
function tgd_sanitize_options($input) {
    $sanitized_input = array();
    $checkboxes = tgd_all_options();
    foreach ($checkboxes as $checkbox) {
        $sanitized_input[$checkbox] = isset($input[$checkbox]) ? true : false;
    }
    return $sanitized_input;
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Get the options array
 */

// Create a global?



/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Create the form
 */

function tgd_options_page_html() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php
            $options_r = get_option('tgd_options');
            // --- DEBUG:
            // echo "<div style='margin: 16px 0'><code>"; //DEBUG
            // var_dump($options_r); //DEBUG
            // echo "</code></div>"; //DEBUG
        ?>
        
        <form action="options.php" method="post">
            <?php settings_fields('tgd_options_group'); ?>
            <?php
                settings_fields('tgd_options_group');
                do_settings_sections('tgd_options_group');
            ?>

                <p class="checkboxes__comments-title">Comments:</p>
        
                <div class="checkbox__wrapper"> 
                    <input type="checkbox" name="tgd_options[disable_comments_new]" value="1" <?php checked($options_r['disable_comments_new'], 1); ?> /><span class="checkbox__label-bold">Disable new comments</span> &nbsp; Disallows new comments on all post types, removes the comment metabox from the front-end, but still shows existing comments. Also prevents comments and pingbacks remotely via REST or XML-RPC
                </div>
        
                <div class="checkbox__wrapper"> 
                    <input type="checkbox" name="tgd_options[disable_comments_hide_existing]" value="1" <?php checked($options_r['disable_comments_hide_existing'], 1); ?> /><span class="checkbox__label-bold">Disable all comments</span> &nbsp; Removes the existing comments from the front-end.
                </div>
            
                <div class="checkbox__wrapper"> 
                    <input type="checkbox" name="tgd_options[disable_comments_hide_backend]" value="1" <?php checked($options_r['disable_comments_hide_backend'], 1); ?> /><span class="checkbox__label-bold">Disable access to comments in the Admin backend</span> &nbsp; Removes the comments from the admin menu and admin bar, prevents access to comments editing pages. 
                </div>

                <p class="checkboxes__comments-title">Remote access:</p>

                <div class="checkbox__wrapper">    
                    <label>
                        <input type="checkbox" name="tgd_options[disable_xmlrpc]" value="1" <?php checked($options_r['disable_xmlrpc'], 1); ?> /><span class="checkbox__label-bold">Disable XML-RPC</span> &nbsp; Prevents remote access to your WordPress.
                    </label>
                </div>

                <div class="checkbox__wrapper">    
                    <label>
                        <input type="checkbox" name="tgd_options[disable_rest]" value="1" <?php checked($options_r['disable_rest'], 1); ?> /><span class="checkbox__label-bold">Disable REST</span> &nbsp; Prevents external applications from interacting with your WordPress site. This can break certain plugins.
                    </label>
                </div>

                <p class="checkboxes__comments-title">Visual:</p>

                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_admin_bar]" value="1" <?php checked($options_r['disable_admin_bar'], 1); ?> /><span class="checkbox__label-bold">Disable admin bar</span> &nbsp; Prevents the admin bar from appearing for all users on the front-end of your site.
                    </label>
                </div>

                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_emoji]" value="1" <?php checked($options_r['disable_emoji'], 1); ?> /><span class="checkbox__label-bold">Disable emoji</span> &nbsp; Prevents the loading of WordPress emoji support.
                    </label>
                </div>

                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_admin_footer_text]" value="1" <?php checked($options_r['disable_admin_footer_text'], 1); ?> /><span class="checkbox__label-bold">Disable admin footer text</span> &nbsp; Removes the footer text "Thank you for using WordPress".
                    </label>
                </div>

                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_admin_footer_version]" value="1" <?php checked($options_r['disable_admin_footer_version'], 1); ?> /><span class="checkbox__label-bold">Disable admin WordPress version</span> &nbsp; Removes the footer text in the admin back-end that gives the WordPress version.
                    </label>
                </div>

                <p class="checkboxes__comments-title">Functionality:</p>
               
                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_file_editing]" value="1" <?php checked($options_r['disable_file_editing'], 1); ?> /><span class="checkbox__label-bold">Disable file editing</span> &nbsp; Removes the ability to edit theme and plugin files directly from the WordPress dashboard. The file editors are accessed from the Tools menu.
                    </label>
                </div>
                    
                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_post_revisions]" value="1" <?php checked($options_r['disable_post_revisions'], 1); ?> /><span class="checkbox__label-bold">Disable post revisions</span> &nbsp; Turns off post revisions for all post types.
                    </label>
                </div>

                <div class="checkbox__wrapper">
                    <label>
                        <input type="checkbox" name="tgd_options[disable_media_attachments]" value="1" <?php checked($options_r['disable_media_attachments'], 1); ?> /><span class="checkbox__label-bold">Disable media attachments</span> &nbsp; Prevents attachment pages (single media item pages) from being accessible.
                    </label>
                </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable the comments
 */

$my_options = get_option('tgd_options');

if ($my_options['disable_comments_new'] || $my_options['disable_comments_hide_existing']) {
    // Remove comment support from all post types
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if(post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
}

if ($my_options['disable_comments_hide_existing']) {
    // Hide existing comments *This doesn't seem to work
    add_filter('comments_array', '__return_empty_array', 10, 2);
    // Additional filter to ensure comments are hidden
    add_filter('comments_template', function() {
        return ABSPATH . 'wp-includes/theme-compat/blank.php';
    });
}

if ($my_options['disable_comments_hide_backend']) {
    // Remove comments page in menu
    add_action('admin_menu', function() {
        remove_menu_page('edit-comments.php');
    });
    add_action('admin_init', 'redirect_comments_page');
    function redirect_comments_page() {
        // Check if the current screen is the comments page
        $screen = get_current_screen();
        if (is_admin() && isset($screen->id) && $screen->id === 'edit-comments') {
            // Redirect to the Dashboard or any other page
            wp_redirect(admin_url());
            exit;
        }
    }
    // Remove comments metabox from dashboard *NOT WORKING
    add_action('wp_dashboard_setup', function() {
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    });
    // Remove comments links from admin bar
    add_action('wp_before_admin_bar_render', function() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    });
}

if ($my_options['disable_comments_new'] || $my_options['disable_comments_hide_existing']) {
    // Disable comments via REST API for anonymous users
    add_filter('rest_allow_anonymous_comments', '__return_false');
    // Remove comment-related REST API endpoints
    add_filter('rest_endpoints', function ($endpoints) {
        if (isset($endpoints['/wp/v2/comments'])) {
            unset($endpoints['/wp/v2/comments']);
        }
        if (isset($endpoints['/wp/v2/comments/(?P<id>[\d]+)'])) {
            unset($endpoints['/wp/v2/comments/(?P<id>[\d]+)']);
        }
        return $endpoints;
    });
}

if ($my_options['disable_comments_new'] || $my_options['disable_comments_hide_existing']) {
    // Disable pingbacks
    add_filter('xmlrpc_methods', function($methods) {
        unset($methods['pingback.ping']);
        return $methods;
    });
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable the XML-RPC
 */

// disable XML-RPC functionality
if ($my_options['disable_xmlrpc']) {
    add_filter('xmlrpc_enabled', '__return_false');
    // remove the XML-RPC link from the HTTP headers
    remove_action('wp_head', 'rsd_link');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable the XML-RPC
 */

if ($my_options['disable_rest']) {
    add_filter('rest_enabled', '__return_false');
    add_filter('rest_jsonp_enabled', '__return_false');
    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('template_redirect', 'rest_output_link_header', 11, 0);
    remove_action('auth_cookie_malformed', 'rest_cookie_collect_status');
    remove_action('auth_cookie_expired', 'rest_cookie_collect_status');
    remove_action('auth_cookie_bad_username', 'rest_cookie_collect_status');
    remove_action('auth_cookie_bad_hash', 'rest_cookie_collect_status');
    remove_action('auth_cookie_valid', 'rest_cookie_collect_status');
    remove_filter('rest_authentication_errors', 'rest_cookie_check_errors', 100);
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable the Admin bar
 */

// Disable admin bar for all users except administrators
// function disable_admin_bar() {
//     if (!current_user_can('manage_options')) {
//         add_filter('show_admin_bar', '__return_false');
//     }
// }
// add_action('after_setup_theme', 'disable_admin_bar');

if ($my_options['disable_admin_bar']) {
    // Disable admin bar for all users
    add_filter('show_admin_bar', '__return_false');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable file editing
 */

if ($my_options['disable_file_editing']) {
    function disable_theme_plugin_editor() {
        define('DISALLOW_FILE_EDIT', true);
    }
    add_action('init', 'disable_theme_plugin_editor');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable post revisions
 */

if ($my_options['disable_post_revisions']) {
    add_filter('wp_revisions_to_keep', '__return_false');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable emoji
 */

if ($my_options['disable_emoji']) {
    // Disable the emoji's
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    // Disable TinyMCE emojis
    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
    function disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        } else {
            return array();
        }
    }
    // Prevent emojis from loading on the front end
    add_filter( 'emoji_svg_url', '__return_false' );
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Disable media attachments
 */

if ($my_options['disable_media_attachments']) {
    function disable_media_attachments() {
        if (is_attachment()) {
            global $post;
            if ($post && $post->post_parent) {
                wp_redirect(get_permalink($post->post_parent), 301);
                exit;
            } else {
                wp_redirect(home_url(), 301);
                exit;
            }
        }
    }
    add_action('template_redirect', 'disable_media_attachments');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Remove the admin footer text WordPress version
 */

 if ($my_options['disable_admin_footer_version']) {
    function remove_wp_version_footer() {
        remove_filter('update_footer', 'core_update_footer');
    }
    add_action('admin_menu', 'remove_wp_version_footer');
}


/* ---- ---- ---- ---- ---- ---- ---- ---- 
 * Remove WordPress version from the admin footer
 */

 if ($my_options['disable_admin_footer_text']) {
    function remove_admin_footer_text() {
        return ''; // Return an empty string to remove the text
    }
    add_filter('admin_footer_text', 'remove_admin_footer_text');
}
