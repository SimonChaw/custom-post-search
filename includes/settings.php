<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
* CPS Settings class
*
* @package     CPS
* @subpackage  Classes/CPS_Settings
* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
* @since       1.0
*/
class CPS_Settings {

  /* ID of seting
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $id;

  /* All of the setting info
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $info;

	/* Holds the template for rendering the search module on the front end.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $template;

	/* The searchable post type
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $post_type;

	/* The searchable meta values for the post type
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $searchable_metas;

  function __construct(){
    $this->check_if_exists();
		// Load exisiting settings
		$this->loadExistingSettings();
  }

  public function check_if_exists(){
    // We should only run this check if user is logged in. ehhhhh maybe not?
		if ( true ) {
			// Check if settings already exist.
			global $wpdb;
			$id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = 'SETTINGS' AND post_type = 'CPS_SETTINGS'" );
			if (! $id ) {
				// No post found, we have to setup.
				// Create post.
				$post_id = $this->setup();
        $this->id = $post_id;
			} else {
				// get all post meta.
        $this->id = $id;
			}
		}
  }

  public function setup(){
    $postarr = [
			'post_title' => 'SETTINGS',
			'post_type' => 'CPS_SETTINGS',
			'post_content' => '',
		];
		$post_id = wp_insert_post($postarr);
		if ($post_id != 0) {
			// Post successfully created.
			return $post_id;
		} else {
			return false;
		}
  }

	public function loadExistingSettings(){
		$this->post_type = get_post_meta($this->id, 'cps_post_type', true);
		global $wpdb;
		$this->searchable_metas = [];
		$metas = $wpdb->get_results("SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE post_id IN (SELECT post_id FROM $wpdb->posts WHERE post_type='$this->post_type')");
		foreach ($metas as $meta) {
			if (substr($meta->meta_key, 0, 1) !== "_" && strpos($meta->meta_key, 'cps') !== 0 ) {
				// Check if extended settings exist for this meta
				$searchable = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'cps_{$meta->meta_key}_searchable'");
				$friendly_label = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'cps_{$meta->meta_key}_friendly_label'");
				$meta_array = array(
					'searchable' => empty($searchable) ? false : $searchable,
					'friendly_label' => empty($friendly_label) ? "" : $friendly_label,
					'key' => $meta->meta_key,
				);
				// If the settings are being called from the front end we should exculde meta filters that aren't enabled.
				if ( $this->is_admin_request() || ( !$this->is_admin_request()  &&  $searchable ) ) {
					$this->searchable_metas[] = $meta_array;
				}
			}
		}
		$this->template = get_post_meta($this->id, 'cps_template', true);
	}

	public function update_extended_settings($metas){
		foreach ($metas as $meta) {
			update_post_meta($this->id, "cps_{$meta->key}_searchable", $meta->searchable);
			update_post_meta($this->id, "cps_{$meta->key}_friendly_label", $meta->friendly_label);
		}
	}

  public function save(){
		update_post_meta($this->id, 'cps_post_type', $this->post_type);
		update_post_meta($this->id, 'cps_template', $this->template);
  }

	/**
	 * Check if this is a request at the backend.
	 *
	 * @return bool true if is admin request, otherwise false.
	 */
	private function is_admin_request() {
		/**
		 * Get current URL.
		 *
		 * @link https://wordpress.stackexchange.com/a/126534
		 */
		$current_url = home_url( add_query_arg( null, null ) );

		/**
		 * Get admin URL and referrer.
		 *
		 * @link https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/pluggable.php#L1076
		 */
		$admin_url = strtolower( admin_url() );
		$referrer  = strtolower( wp_get_referer() );

		/**
		 * Check if this is a admin request. If true, it
		 * could also be a AJAX request from the frontend.
		 */
		if ( 0 === strpos( $current_url, $admin_url ) ) {
			/**
			 * Check if the user comes from a admin page.
			 */
			if ( 0 === strpos( $referrer, $admin_url ) ) {
				return true;
			} else {
				/**
				 * Check for AJAX requests.
				 *
				 * @link https://gist.github.com/zitrusblau/58124d4b2c56d06b070573a99f33b9ed#file-lazy-load-responsive-images-php-L193
				 */
				if ( function_exists( 'wp_doing_ajax' ) ) {
					return ! wp_doing_ajax();
				} else {
					return ! ( defined( 'DOING_AJAX' ) && DOING_AJAX );
				}
			}
		} else {
			return false;
		}
	}
}
