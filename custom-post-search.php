<?php
/**
 * Plugin Name: Custom Post Search
 * Plugin URI: https://github.com/SimonChaw/custom-post-search/
 * Description: A simple search tool for users to use on the front end to search through custom post types.
 * Author: Shadow Software Solutions
 * Author URI: https://simonchawla.com
 * Version: 1.0.0
 * Text Domain: custom-post-search
 *
 * Custom Post Search is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Custom Post Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Custom Post Search. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package CPS
 * @category Core
 * @author Simon Chawla
 * @version 1.0.0
 */
 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'CPS' ) ) :
/**
 * Main Class.
 *
 * @since 1.0
 */
final class CPS {
	/**
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * @since 1.0
	 */
	public $settings;

  /**
	 * Main CPS Instance.
	 *
	 * Insures that only one instance of CPS exists in memory at any one
	 * time. Removes the need for Globals
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses CPS::setup_constants() Setup the constants needed.
	 * @uses CPS::includes() Include the required files.
	 * @see CPS()
	 * @return object|CPS The one true Custom_Post_Search Object
	 */
	public static function instance() {
      if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CPS ) ) {
  			self::$instance = new CPS;
  			self::$instance->setup_constants();
  			self::$instance->includes();
				self::$instance->settings = new CPS_Settings();
  		}
	    return self::$instance;
	}
  /**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'CPS_VERSION' ) ) {
			define( 'CPS_VERSION', '1.0.0' );
		}
		// Plugin Folder Path.
		if ( ! defined( 'CPS_PLUGIN_DIR' ) ) {
			define( 'CPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		// Plugin Folder URL.
		if ( ! defined( 'CPS_PLUGIN_URL' ) ) {
			define( 'CPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		// Plugin Root File.
		if ( ! defined( 'CPS_PLUGIN_FILE' ) ) {
			define( 'CPS_PLUGIN_FILE', __FILE__ );
		}
	}
  /**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {
    require_once CPS_PLUGIN_DIR . 'includes/scripts.php';
		require_once CPS_PLUGIN_DIR . 'includes/actions.php';
    require_once CPS_PLUGIN_DIR . 'includes/admin/admin-page.php';
		require_once CPS_PLUGIN_DIR . 'includes/settings.php';
	}
}
endif; // End if class_exists check.
/**
 * Main function for accessing CPS instance
 *
 * @since 1.0
 * @return object|CPS The one true CPS Instance.
 */
function CPS() {
	return CPS::instance();
}
// Get CPS Running.
CPS();
