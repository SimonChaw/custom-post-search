<?php
/**
 * Scripts
 *
 * @package     SMDB
 * @subpackage  Functions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Scripts
 *
 * @since 1.0
 * @global $post
 * @param string $hook Page hook
 * @return void
 */

function load_scripts( $hook ) {
        // Load all regular scripts.
        $js_dir = CPS_PLUGIN_URL . 'assets/js/';
        $css_dir = CPS_PLUGIN_URL . 'assets/css/';

        if ( ( is_admin() && $hook === 'toplevel_page_cps' ) || !is_admin() ) {
          // Get Vue
          wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js', [], '2.5.17');
          wp_enqueue_script('axios', 'https://unpkg.com/axios@0.19.0/dist/axios.min.js', [], '0.19.0');
        }

        //Don'  t load the scripts unless the user is on the plugin admin page
        if ( $hook === 'toplevel_page_cps' ) {
          // Bootstrap
          wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', [], '4.3.1');
          wp_register_script( 'cps-admin-scripts', $js_dir . 'admin-app.js', ['jquery', 'jquery-form'], CPS_VERSION, false );
          wp_enqueue_script( 'cps-admin-scripts' );
          wp_register_style('cps-admin-style', $css_dir . 'admin.css', [], CPS_VERSION, false);
          wp_enqueue_style('cps-admin-style');
          wp_localize_script( 'cps-admin-scripts', 'ajax_object', array(
              'ajaxurl' => admin_url( 'admin-ajax.php' , '')
          ) );
        } else if ( !is_admin() ) {
          wp_register_script( 'cps-front-end-scripts', $js_dir . 'front-end.js', ['jquery', 'jquery-form'], CPS_VERSION, false );
          wp_register_style('cps-front-end-style', $css_dir . 'master.css', [], CPS_VERSION, false);
          wp_enqueue_style('cps-front-end-style');
          wp_enqueue_script( 'cps-front-end-scripts' );
          wp_localize_script( 'cps-front-end-scripts', 'ajax_object', array(
              'ajaxurl' => admin_url( 'admin-ajax.php' , '')
          ) );
        }
}

add_action( 'admin_enqueue_scripts', 'load_scripts', 100 );
add_action( 'wp_enqueue_scripts', 'load_scripts', 100);
