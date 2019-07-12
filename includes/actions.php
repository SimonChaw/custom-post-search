<?php
/**
 * Actions
 *
 * @package     CPS
 * @subpackage  Functions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
 // Exit if accessed directly
 if ( ! defined( 'ABSPATH' ) ) exit;

 /**
 * Get the current settings configured for CPS
 *
 * @since  1.0
 * @return mixed
 */
function get_cps_settings(){
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    header( 'Content-Type: application/json' );
    echo json_encode( CPS()->settings );
    wp_die();
  }
}
add_action('wp_ajax_get_cps_settings', 'get_cps_settings', 10);

/**
* Get the current settings configured for CPS but hide fields that aren't searchable.
*
* @since  1.0
* @return mixed
*/
function get_nopriv_cps_settings(){
 //
 CPS()->settings->loadExistingSettings( true );

 if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
   header( 'Content-Type: application/json' );
   echo json_encode( CPS()->settings );
   wp_die();
 }
}
add_action('wp_ajax_cps_settings', 'get_nopriv_cps_settings', 10);
add_action('wp_ajax_nopriv_cps_settings', 'get_nopriv_cps_settings', 10);

/**
* Get all the posts that are the CPS type.
*
* @since  1.0
* @return mixed
*/
function get_cps_posts(){
  $posts = array();
  global $wpdb;
  $post_type = CPS()->settings->post_type;

  $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='$post_type'");

  foreach ($posts as &$post) {
    $post = (array)$post;
    $metas = get_post_meta($post['ID']);
    foreach ($metas as $key => $meta) {
      $post[$key] = $meta[0];
    }
    $post = (object)$post;
  }

  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
   header( 'Content-Type: application/json' );
   echo json_encode( $posts );
   wp_die();
  }
}
add_action('wp_ajax_get_cps_posts', 'get_cps_posts', 10);
add_action('wp_ajax_nopriv_get_cps_posts', 'get_cps_posts', 10);

/**
* Update the settings for CPS
*
* @since  1.0
* @return mixed
*/

function save_cps_settings(){
  $settings = CPS()->settings;

  $settings->post_type = $_POST['post_type'];
  $settings->template = urldecode($_POST['template']);
  $settings->save();
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
   header( 'Content-Type: application/json' );
   echo json_encode( array( 'success' =>  true ) );
   wp_die();
  }
}

add_action('wp_ajax_save_cps_settings', 'save_cps_settings', 10);

/**
* Update the extended settings for CPS
*
* @since  1.0
* @return mixed
*/

function update_cps_extended_settings(){
  $metas = json_decode(urldecode($_POST['extended_settings']));
  CPS()->settings->update_extended_settings($metas);
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
   header( 'Content-Type: application/json' );
   echo json_encode( array( 'success' =>  true ) );
   wp_die();
  }
}

add_action('wp_ajax_update_cps_extended_settings', 'update_cps_extended_settings', 10);

/**
* Register the short code for the search module.
*
* @since  1.0
* @return mixed
*/
function cps_search_module(){
  ob_start();
	?>
  <div id="cps_search_module" class="w-full">
   <div class="w-full" v-if="ready">
     <div class="input-group">
       <div class="input-group-prepend">
         <span class="input-group-text" id="inputGroupPrepend">
           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
         </span>
       </div>
       <input type="text" class="form-control" id="validationCustomUsername" v-model="search_term" v-on:input="filter" placeholder="Enter search terms here" aria-describedby="inputGroupPrepend" required>
       <select v-model="search_by" placeholder="Search By:" class="input-group-append" style="width:30%;padding: 0 .5rem">
        <option value="">Search by field</option>
        <option v-for="(key) in settings.searchable_metas" v-bind:value="key.key">{{key.friendly_label ? key.friendly_label : key.key}}</option>
       </select>
       <div class="invalid-feedback alert alert-success">
         Search by: First name, Last name
       </div>
     </div>
     <div class="w-full">
       <div class="w-full d-flex flex-wrap" v-if="posts">
           <transition name="fade" v-for="(<?php echo CPS()->settings->post_type ?>) in posts">
              <div class="cps-post-wrapper" v-if="<?php echo CPS()->settings->post_type ?>.show">
               <?php echo CPS()->settings->template; ?>
              </div>
           </transition>
       </div>
       <div v-else class="alert alert-warning"> No entries for {{settings.post_type}} were found! </div>
       <transition name="fade">
         <div v-if="none_found && posts" class="alert alert-warning d-flex align-items-center" style="margin-top:1.5rem;">
           <div class="d-flex" style="padding:0.5rem 0;"><svg style="fill:blueviolet;width:20px;margin-right:1.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
           <div>No matching entries found!</div>
         </div>
       </transition>
     </div>
    </div>
    <div v-else class="w-full d-flex justify-content-center">
      <img src="<?php echo CPS_PLUGIN_URL ?>assets/img/loading.svg" />
    </div>
   </div>
  <?php
	return ob_get_clean();
}

add_shortcode( 'cps_search', 'cps_search_module' );
