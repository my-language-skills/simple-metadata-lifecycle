<?php

/**
 * Creats settings subpage for Simple Metadata
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-lifecycle
 * @subpackage admin/settings
 * @since x.x.x (when the file was introduced)
 */

use \vocabularies\SMDLC_Metadata_Lifecycle as lifecycle_meta;


defined ("ABSPATH") or die ("No script assholes!");


/**
* Function to add pluginx settings subpage and registering settings and their sections.
*
* @since
*
*/

function smdlc_add_lyfecycle_settings() {
	//we don't create settings page in blog 1 (not necessary)
	if ((1 != get_current_blog_id() && is_multisite()) || !is_multisite()){

		//adding subapage to page of main plugin
		add_submenu_page('smd_set_page','Life Cycle Metadata', 'Life Cycle Metadata', 'manage_options', 'smdlc_set_page', 'smdlc_render_settings');

		//adding metaboxes and sections for settings
		add_meta_box('smdlc-metadata-location', 'Life Cycle Metadata', 'smdlc_render_metabox_schema_locations', 'smd_set_page', 'normal', 'core');

		add_settings_section( 'smdlc_meta_locations', '', '', 'smdlc_meta_locations' );

		add_meta_box('smdlc-metadata-properties', 'Properties Management', 'smdlc_render_metabox_properties', 'smdlc_set_page', 'normal', 'core');

		add_settings_section( 'smdlc_meta_properties', '', '', 'smdlc_meta_properties' );

		//registering settings for locations and properties management
		register_setting('smdlc_meta_locations', 'smdlc_locations');
		register_setting ('smdlc_meta_properties', 'smdlc_shares');

		register_setting ('smdlc_meta_properties', 'smdlc_');

		register_setting ('smdlc_meta_properties', 'smdlc_freezes');


		//collecting current values of options
		$post_types = smd_get_all_post_types();
		$locations = get_option('smdlc_locations');
		$shares = get_option('smdlc_shares');
		$shares11 = get_option('smdlc_');
		$freezes = get_option('smdlc_freezes');

		//initiazaling variables for network values
		$network_locations = [];
		$network_shares = [];

		$network_shares11 = [];
		$network_freezes = [];
		//in case of multisite installation, we collect options for network
		if (is_multisite()){
			$network_locations = get_blog_option(1, 'smdlc_net_locations');
			$network_shares = get_blog_option(1, 'smdlc_net_shares');
			$network_shares11 = get_blog_option(1, 'smdlc_net_');
			$network_freezes = get_blog_option(1, 'smdlc_net_freezes');
		}

		//creating fields for activating metadata in every public post
		foreach ($post_types as $post_type) {
			if ('metadata' == $post_type){
				$label = 'Book Info';
			} else {
				$label = ucfirst($post_type);
			}
			add_settings_field ('smdlc_locations['.$post_type.']', $label, function () use ($post_type, $locations, $network_locations){
				$checked = isset($locations[$post_type]) ? true : false;
				$disabled = isset($network_locations[$post_type]) && $network_locations[$post_type] ? 'disabled' : '';
				?>
					<input type="checkbox" name="smdlc_locations[<?=$post_type?>]" id="smdlc_locations[<?=$post_type?>]" value="1" <?php checked(1, $checked); echo $disabled;?>>
				<?php
				if ('disabled' == $disabled){
					?>
						<input type="hidden" name="smdlc_locations[<?=$post_type?>]" value="1">
					<?php
				}
			}, 'smdlc_meta_locations', 'smdlc_meta_locations');
		}

		//creating fields for every property in Life Cycle vocabulary
		foreach (lifecycle_meta::$lifecycle_properties as $key => $data) {

			add_settings_field ('smdlc_'.$key, ucfirst($data[0]), function () use ($key, $data, $shares11, $freezes, $network_shares11, $network_freezes){
				if (!empty($network_shares11)) {
					if ($network_shares11[$key] == '0') {
						$shares11 = get_option('smdlc_');
					// $shares11_class[$key] == '0';
					 $valeur_key_lifecycle = '4';

					}
					else {
						$shares11[$key] = $network_shares11[$key];
						 $valeur_key_lifecycle = $shares11[$key];
					}
				}else
				 {
					$disabled_ca = '';
					$valeur_key_lifecycle = '4';

				}
				?>
				<?php if ($shares11[$key]=='1') {

          function runMyFunction() {
            if (isset($_GET['field_name'])) {
              $locations2 = get_option('smdlc_locations');
              $key = $_GET['field_name'];
                global $wpdb;
                   //Get the posts table name
                   $postsTable = $wpdb->prefix . "posts";
                   //Get the postmeta table name
                   $postMetaTable = $wpdb->prefix . "postmeta";

                   //defining site-meta post type
                   $meta_type = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';

                   //fetching site-meta/book info post
                   $meta_post = $wpdb->get_results($wpdb->prepare("
                       SELECT ID FROM $postsTable WHERE post_type LIKE %s AND
                       post_status LIKE %s",$meta_type,'publish'),ARRAY_A);

                   //If we have more than one or 0 ids in the array then return and stop operation
                   //If we have no chapters or posts to distribute data also stop operation
                   if(count($meta_post) > 1 || count($meta_post) == 0){
                       return;
                   }

                   //unwrapping ID from subarrays
                   $meta_post_id = $meta_post[0]['ID'];


                   //getting metadata of site-meta/books info post
                   $meta_post_meta = $wpdb->get_results($wpdb->prepare("
                       SELECT `meta_key`, `meta_value` FROM $postMetaTable WHERE `post_id` LIKE %s
                       AND `meta_key` LIKE %s AND `meta_key` LIKE %s
                       AND `meta_value` <>''",$meta_post_id,'%%smdlc_%%','%%cycle%%'.$meta_type.'%%')
                           ,ARRAY_A);

                  //Array for storing metakey=>metavalue
                   $metaData = [];
                   //unwrapping data from subarrays
                   foreach($meta_post_meta as $meta){
                       $metaData[$meta['meta_key']] = $meta['meta_value'];
                   }
                   //if there are no fields of Life Cycle meta in site-meta/ book info, nothing to share or freeze, exit
                   if(count($metaData) == 0){
                       return;
                   }

                   foreach ($locations2 as $location => $val){
                     if ($location == $meta_type) {
                       continue;
                     }
                         //Getting all posts of $location type
                         $posts_ids = $wpdb->get_results($wpdb->prepare("
                         SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$location),ARRAY_A);
                         $posts_ids_meta_type = $wpdb->get_results($wpdb->prepare("
                         SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$meta_type),ARRAY_A);
                         //looping through all posts of type $locations
                         foreach ($posts_ids as $post_id) {
                           $post_id = $post_id['ID'];
                             $meta_key = 'smdlc_'.strtolower($key).'_lifecycle_'.$location;
                             $metadata_meta_key = 'smdlc_'.strtolower($key).'_lifecycle_'.$meta_type;
                               delete_post_meta($post_id, $meta_key);
                               delete_post_meta($post_id, $metadata_meta_key);

                         }
                         foreach ($posts_ids_meta_type as $post_id_meta_type) {
                           $post_id_meta_type = $post_id_meta_type['ID'];
                             $meta_key_meta_type = 'smdlc_'.strtolower($key).'_lifecycle_'.$location;
                             $metadata_meta_key_type = 'smdlc_'.strtolower($key).'_lifecycle_'.$meta_type;
                               delete_post_meta($post_id_meta_type, $meta_key_meta_type);
                               delete_post_meta($post_id_meta_type, $metadata_meta_key_type);

                         }
              }
            }
}

if (isset($_GET['hello'])) {
  runMyFunction();
  //refresh the page
  ?><meta http-equiv="refresh" content="0;URL=admin.php?page=smdlc_set_page"><?php
}
 if ($shares11[$key]=='1') {
echo '<a style="color:red; text-decoration: none; font-size: 14px;"href = "admin.php?page=smdlc_set_page&hello=true&field_name='.$key.'&sharekey='.$shares11[$key].'">X</a>';}

?>
       	&nbsp;&nbsp;
			<?php } ?>
			<label for="smdlc_disable[<?=$key?>]">Disable <input type="radio"  name="smdlc_[<?=$key?>]" value="1" id="smdlc_disable[<?=$key?>]" <?php if ($shares11[$key]=='1') { echo "checked='checked'"; }
				?>  <?php  if ($valeur_key_lifecycle == '1' || $valeur_key_lifecycle == '4') {echo "";}else {echo "disabled";}  ?> ></label>
				<label for="smdlc_local_value[<?=$key?>]">Local value <input type="radio"  name="smdlc_[<?=$key?>]" value="0" id="smdlc_local_value[<?=$key?>]" <?php if ($shares11[$key]=='0' || empty($shares11[$key])) { echo "checked='checked'"; }
				?>  <?php  if ($valeur_key_lifecycle == '0' || $valeur_key_lifecycle == '4') {echo "";}else {echo "disabled";}  ?>></label>
				<label  for="smdlc_share[<?=$key?>]">Share <input type="radio"  name="smdlc_[<?=$key?>]" value="2" id="smdlc_share[<?=$key?>]" <?php if ($shares11[$key]=='2') { echo "checked='checked'"; }
				?>  <?php  if ($valeur_key_lifecycle == '2' || $valeur_key_lifecycle == '4') {echo "";}else {echo "disabled";}  ?>></label>
				<label for="smdlc_freeze[<?=$key?>]">Freeze <input type="radio"  name="smdlc_[<?=$key?>]" value="3" id="smdlc_freeze[<?=$key?>]"  <?php if ($shares11[$key]=='3') { echo "checked='checked'"; }
				?> <?php  if ($valeur_key_lifecycle == '3' || $valeur_key_lifecycle == '4') {echo "";}else {echo "disabled";}  ?>></label>
					<br><span class="description"><?=$data[1]?></span>
					<?php

			}, 'smdlc_meta_properties', 'smdlc_meta_properties');
}
		}
		}

/**
* Function for rendering settings subpage.
*
* @since
*
*/

function smdlc_render_settings() {
	if(!current_user_can('manage_options')){
		return;
	}

	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
	?>
        <div class="wrap">
        	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) { ?>
        	<div class="notice notice-success is-dismissible">
				<p><strong>Settings saved.</strong></p>
			</div>
			<?php smdlc_update_overwrites(); }?>
			<h2>Simple Metadata Life Cycle Settings</h2>
            <div class="metabox-holder">
					<?php
					do_meta_boxes('smdlc_set_page', 'normal','');
					?>
            </div>
        </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                // close postboxes that should be closed
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('smdlc_set_page');
            });
            //]]>
        </script>
		<?php
}

/**
* Function for rendering 'Locations' metabox.
*
* @since
*
*/

function smdlc_render_metabox_schema_locations(){
	?>
	<div id="smdlc_meta_locations" class="smdlc_meta_locations">
		<span class="description">Description for Life Cycle locations metabox</span>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'smdlc_meta_locations' );
			do_settings_sections( 'smdlc_meta_locations' );
			submit_button();
			?>
		</form>
		<br>
		<?php
		function runMyFunction2() {
			if (isset($_GET['field_name'])) {
				$locations2 = get_option('smdlc_locations');
				$key = $_GET['field_name'];
					global $wpdb;
						 //Get the posts table name
						 $postsTable = $wpdb->prefix . "posts";
						 var_dump($postsTable);

						 //Get the postmeta table name
						 $postMetaTable = $wpdb->prefix . "postmeta";

						 //defining site-meta post type
						 $meta_type = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';
						 $metadata_meta_key_site = 'smdlc_';
					 $recuperation_de_la_table = $wpdb->get_results("
					 	DELETE FROM $postMetaTable  WHERE meta_key like '%{$metadata_meta_key_site}%' ");
			}
}
		if (isset($_GET['hello3'])) {
		runMyFunction2();
		//refresh the page ?>
		<meta http-equiv="refresh" content="0;URL=admin.php?page=smd_set_page">
		 <?php
		} ?>
		<form class=""  method="post">
			<tr><th scope="row">Delete all Data of the single sites</th><td > <?php  echo "<a <a onClick=\"javascript: return confirm('Are you sure to delete all meta-data of the plugin Lifeclycle in the site?');\" style='color:red; text-decoration: none; font-size: 14px;'href = 'admin.php?page=smd_set_page&hello3=true&field_name=patatevie&sharekey=onestla'>X</a>"; ?>
				</td></tr>
		</form>
		<p></p>
	</div>
	<?php
}

/**
* Function for rendering 'Life Cycle properties' metabox
*
* @since
*
*/

function smdlc_render_metabox_properties(){
	$locations = get_option('smdlc_locations');
	$level = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';
	$label = $level == 'metadata' ? 'Book Info' : 'Site-Meta';
	if (isset($locations[$level]) && $locations[$level]){
	?>
	<div id="smdlc_meta_properties" class="smdlc_meta_properties">
		<span class="description">Description for Life Cycle properties metabox</span>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'smdlc_meta_properties' );
			submit_button();
			do_settings_sections( 'smdlc_meta_properties' );
			?>
		</form>
		<p></p>
	</div>
	<?php
	} else {
		?>
			<p style="color: red;">Activate <?=$label?> location in order to manage properties.</p>
		<?php
	}
}

/**
* Function for updating options and forcing overwritings on settings update.
*
* @since
*
*/

 function smdlc_update_overwrites(){

 	//collecting options values
 	$locations = get_option('smdlc_locations') ?: [];
 	$shares11 = get_option('smdlc_') ?: [];
 	$freezes = get_option('smdlc_freezes') ?: [];

 	//if nothing is chosen to share or freeze, return
 	if(empty($shares11) && empty($freezes)){
 		return;
 	}

 	//Wordpress Database variable for database operations
 	global $wpdb;
     //Get the posts table name
     $postsTable = $wpdb->prefix . "posts";
     //Get the postmeta table name
     $postMetaTable = $wpdb->prefix . "postmeta";

     //defining site-meta post type
     $meta_type = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';

     //fetching site-meta/book info post
     $meta_post = $wpdb->get_results($wpdb->prepare("
         SELECT ID FROM $postsTable WHERE post_type LIKE %s AND
         post_status LIKE %s",$meta_type,'publish'),ARRAY_A);

     //If we have more than one or 0 ids in the array then return and stop operation
     //If we have no chapters or posts to distribute data also stop operation
     if(count($meta_post) > 1 || count($meta_post) == 0){
         return;
     }

     //unwrapping ID from subarrays
     $meta_post_id = $meta_post[0]['ID'];


     //getting metadata of site-meta/books info post
     $meta_post_meta = $wpdb->get_results($wpdb->prepare("
         SELECT `meta_key`, `meta_value` FROM $postMetaTable WHERE `post_id` LIKE %s
         AND `meta_key` LIKE %s AND `meta_key` LIKE %s
         AND `meta_value` <>''",$meta_post_id,'%%smdlc_%%','%%cycle%%'.$meta_type.'%%')
             ,ARRAY_A);

  	//Array for storing metakey=>metavalue
     $metaData = [];
     //unwrapping data from subarrays
     foreach($meta_post_meta as $meta){
         $metaData[$meta['meta_key']] = $meta['meta_value'];
     }
     //if there are no fields of Life Cycle meta in site-meta/ book info, nothing to share or freeze, exit
     if(count($metaData) == 0){
         return;
     }

     //checking if there is somthing to share for Life Cycle properties

		 if(!empty($shares11)){

	  		//looping through all active locations
	  		foreach ($shares11 as $key => $value) {
	  		if ($value=='2') {
	  		foreach ($locations as $location => $val){
	  			if ($location == $meta_type) {
	  				continue;
	  			}
	          	//Getting all posts of $location type
	          	$posts_ids = $wpdb->get_results($wpdb->prepare("
	          	SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$location),ARRAY_A);

	          	//looping through all posts of type $locations
	          	foreach ($posts_ids as $post_id) {
	          		$post_id = $post_id['ID'];

	          		foreach ($shares11 as $key => $value) {
									if ($value=='2') {
	          			$meta_key = 'smdlc_'.strtolower($key).'_lifecycle_'.$location;
	          			$metadata_meta_key = 'smdlc_'.strtolower($key).'_lifecycle_'.$meta_type;
	          			if((!get_post_meta($post_id, $meta_key) || '' == get_post_meta($post_id, $meta_key)) && isset($metaData[$metadata_meta_key])){
	          				update_post_meta($post_id, $meta_key, $metaData[$metadata_meta_key]);
	          			}
											}
	          		}
	          	}
	  					}
	  				}
	  			if ($value=='3') {
	  				foreach ($locations as $location => $val){
	  					if ($location == $meta_type) {
	  						continue;
	  					}
	  		        	//Getting all posts of $location type
	  		        	$posts_ids = $wpdb->get_results($wpdb->prepare("
	  		        	SELECT `ID` FROM `$postsTable` WHERE `post_type` = %s",$location),ARRAY_A);

	  		        	//looping through all posts of type $locations
	  		        	foreach ($posts_ids as $post_id) {
	  		        		$post_id = $post_id['ID'];

	  		        		foreach ($shares11 as $key => $value) {
											if ($value=='3') {
	  		        			$meta_key = 'smdlc_'.strtolower($key).'_lifecycle_'.$location;
	  		        			$metadata_meta_key = 'smdlc_'.strtolower($key).'_lifecycle_'.$meta_type;
	  		        			if(isset($metaData[$metadata_meta_key])){
	  		        				update_post_meta($post_id, $meta_key, $metaData[$metadata_meta_key]);
	  		        			}
												}
	  		        		}
	  		        	}

	  				}
	  			}
	  		}
	  	}
	  }


add_action('admin_menu', 'smdlc_add_lyfecycle_settings', 100);
add_action('updated_option', function( $option_name, $old_value, $value ){
	if ('smdlc_locations' == $option_name){
		$locations_general = get_option('smd_locations') ?: [];
		$value = empty($value) ? [] : $value;
		$locations_general = array_merge($locations_general, $value);

		if (isset($locations_general['metadata'])){
			unset($locations_general['metadata']);
		}
		if (isset($locations_general['site-meta'])){
			unset($locations_general['site-meta']);
		}

		update_option('smd_locations', $locations_general);
	}
}, 10, 3);
