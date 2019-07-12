<?php
/**
 * Admin Actions
 *
 * @package     CPS
 * @subpackage  Admin/Page
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
function render_admin_view(){
  ?>
  <div id="app" class="container mt-5 d-flex flex-wrap justify-content-between">
    <div class="w-100 d-flex align-items-center flex-wrap">
      <img src="<?php echo CPS_PLUGIN_URL ?>/assets/img/shadow.png" alt="" style="width:250px;">
      <h3 style="color:black;width:50%;text-align:center;"> Custom Post Search Settings</h3>
    </div>
    <div class="card mr-2" style="max-width: unset;flex-grow:2;">
      <h5 class="card-title">Create a new search module:</h5>
      <div class="card-text">
        <div class="w-full d-flex justify-content-between">
          <div>Searchable post type:</div>
          <div>
            <select v-model="settings.post_type" class="form-control" style="width: 200px;">
              <option v-for="(type) in postTypes" >{{ type.name }}</option>
            </select>
          </div>
        </div>
        <div class="mt-2 mb-2">Create your template for each item:</div>
        <?php wp_editor( '', 'edit', $settings = array() ); ?>
      </div>
      <transition name="fade">
        <div v-show="messageShow" class="alert alert-success mt-3" role="alert">
          Settings have been updated.
        </div>
      </transition>
      <div class="w-full mt-2">
        <div v-on:click="updateCPSSettings" class="float-right btn btn-primary">
          <span v-if="!updating">Save Changes</span>
          <img v-else src="<?php echo CPS_PLUGIN_URL ?>/assets/img/loading.gif" style="width:1.8em;" alt="">
        </div>
      </div>
    </div>

    <div class="card" style="max-width:unset;flex-grow:1;">
      <h5 class="card-title">Searchable Terms</h5>
      <div class="card-text">Here you can select which meta fields users can search by and assign them a user friendly label.</div>
      <table class="table table-bordered table-striped mt-2">
        <thead>
          <tr>
            <th width="120">Use Field (?)</th>
            <th>Field</th>
            <th>User-Friendly Label</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(field) in settings.searchable_metas">
            <td class="text-center"><input v-model="field.searchable" type="checkbox" style="margin:0 auto;"/></td>
            <td>{{field.key}}</td>
            <td><input v-model="field.friendly_label" class="form-control" type="text"/></td>
          </tr>
        </tbody>
      </table>
      <transition name="fade">
        <div v-show="extendedMessageShow" class="alert alert-success" role="alert">
          Extended settings have been updated.
        </div>
      </transition>
      <div class="w-full mt-2">
        <div v-on:click="updateCPSExtendedSettings" class="float-right btn btn-primary" style="transition: 1s all ease-in-out;">
          <span v-if="!extendedUpdating">Save Changes</span>
          <img v-else src="<?php echo CPS_PLUGIN_URL ?>/assets/img/loading.gif" style="width:1.8em;" alt="">
        </div>
      </div>
    </div>
  </div>
  <?php
}
function setup_menu(){
  add_menu_page( 'Custom Post Search Settings', 'CP Search', 'manage_options', 'cps', 'init', 'dashicons-search', 100);
}
function init(){
  render_admin_view();
}
add_action('admin_menu', 'setup_menu');
