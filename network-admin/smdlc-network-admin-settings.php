<?php

//network settings functionality

use \vocabularies\SMDLC_Metadata_Lifecycle as lifecycle_meta;

defined ("ABSPATH") or die ("No script assholes!");

/**
 * Function for adding network settings page
 */
function smdlc_add_network_settings() {

    //adding settings metaboxes and settigns sections
    add_meta_box('smdlc-metadata-network-location', 'Life Cycle Metadata', 'smdlc_network_render_metabox_schema_locations', 'smd_net_set_page', 'normal', 'core');
    add_meta_box('smdlc-network-metadata-properties', 'Life Cycle Properties Management', 'smdlc_network_render_metabox_properties', 'smd_net_set_page', 'normal', 'core');

    add_settings_section( 'smdlc_network_meta_locations', '', '', 'smdlc_network_meta_locations' );

    add_settings_section( 'smdlc_network_meta_properties', '', '', 'smdlc_network_meta_properties' );

    //registering settings
    register_setting('smdlc_network_meta_locations', 'smdlc_net_locations');
	register_setting ('smdlc_network_meta_properties', 'smdlc_net_');
	register_setting ('smdlc_network_meta_properties', 'smdlc_net_freezes');


	// getting options values from DB
	$post_types = smd_get_all_post_types();
	$locations = get_option('smdlc_net_locations');
	$shares11 = get_option('smdlc_net_');
	$freezes = get_option('smdlc_net_freezes');

	//adding settings for locations
	foreach ($post_types as $post_type) {
		if ('metadata' == $post_type){
			$label = 'Book Info';
		} else {
			$label = ucfirst($post_type);
		}
		add_settings_field ('smdlc_net_locations['.$post_type.']', $label, function () use ($post_type, $locations){
			$checked = isset($locations[$post_type]) ? true : false;
			?>
				<input type="checkbox" name="smdlc_net_locations[<?=$post_type?>]" id="smdlc_net_locations[<?=$post_type?>]" value="1" <?php checked(1, $checked);?>>
			<?php
		}, 'smdlc_network_meta_locations', 'smdlc_network_meta_locations');
	}

	//adding settings for educational properties management
  foreach (lifecycle_meta::$lifecycle_properties as $key => $data) {
		add_settings_field ('smdlc_net_'.$key, ucfirst($data[0]), function () use ($key, $data, $shares11, $freezes){
      $shares11[$key] = !empty($shares11[$key]) ? $shares11[$key] : '0';

			?>
      <label for="smdlc_net_disable[<?=$key?>]">Disable <input type="radio"  name="smdlc_net_[<?=$key?>]" value="1" id="smdlc_net_disable[<?=$key?>]" <?php if ($shares11[$key]=='1') { echo "checked='checked'"; }
      ?>  ></label>
      <label for="smdlc_net_local_value[<?=$key?>]">Local value <input type="radio"  name="smdlc_net_[<?=$key?>]" value="0" id="smdlc_net_local_value[<?=$key?>]" <?php if ($shares11[$key]=='0' ) { echo "checked='checked'"; }
      ?>  ></label>
      <label  for="smdlc_net_share[<?=$key?>]">Share <input type="radio"  name="smdlc_net_[<?=$key?>]" value="2" id="smdlc_net_share[<?=$key?>]" <?php if ($shares11[$key]=='2') { echo "checked='checked'"; }
      ?>  ></label>
      <label for="smdlc_net_freeze[<?=$key?>]">Freeze <input type="radio"  name="smdlc_net_[<?=$key?>]" value="3" id="smdlc_net_freeze[<?=$key?>]"  <?php if ($shares11[$key]=='3') { echo "checked='checked'"; }
      ?> ></label>
				<br><span class="description"><?=$data[1]?></span>
			<?php
		}, 'smdlc_network_meta_properties', 'smdlc_network_meta_properties');
	}

}

/**
 * Function for rendering settings page
 */
function smdlc_render_network_settings(){
	wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
	    ?>
	    <div class="wrap">
	    	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) { ?>
        	<div class="notice notice-success is-dismissible">
				<p><strong>Settings saved.</strong></p>
			</div>
			<?php } ?>
		    <div class="metabox-holder">
			    <?php
			    	do_meta_boxes('smdlc_net_set_page', 'normal','');
			    ?>
		    </div>
	    </div>
	    <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                // close postboxes that should be closed
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('<?php echo 'smdlc_net_set_page'; ?>');
            });
            //]]>
		</script>
		<?php
}

/**
 * Function for rendering metabox of locations
 */
function smdlc_network_render_metabox_schema_locations(){
	?>
	<div id="smdlc_network_meta_locations" class="smdlc_network_meta_locations">
		<span class="description">Description for Life Cycle network locations metabox</span>
		<form method="post" action="edit.php?action=smdlc_update_network_locations">
			<?php
			settings_fields( 'smdlc_network_meta_locations' );
			do_settings_sections( 'smdlc_network_meta_locations' );
			submit_button();
			?>
		</form>
		<p></p>
	</div>
	<?php
}

/**
 * Function for rendering metabox for properties management
 */
function smdlc_network_render_metabox_properties(){
	?>
	<div id="smdlc_network_meta_properties" class="smdlc_network_meta_properties">
		<span class="description">Description for Life Cycle network properties metabox</span>
		<form method="post" action="edit.php?action=smdlc_update_network_options">
			<?php
			settings_fields( 'smdlc_network_meta_properties' );
			do_settings_sections( 'smdlc_network_meta_properties' );
			submit_button();
			?>
		</form>
		<p></p>
	</div>
	<?php
}

/**
 * Handler for locations settings update
 */
function smdlc_update_network_locations() {

	check_admin_referer('smdlc_network_meta_locations-options');

	//Wordpress Database variable for database operations
    global $wpdb;

	$locations = isset($_POST['smdlc_net_locations']) ? $_POST['smdlc_net_locations'] : array();

	//collecting locations of general meta accumulative option from POST request
	$locations_general = get_blog_option(1, 'smd_net_locations') ?: array();

	$locations_general = array_merge($locations_general, $locations);

	if (isset($locations_general['metadata'])){
		unset($locations_general['metadata']);
	}
	if (isset($locations_general['site-meta'])){
		unset($locations_general['site-meta']);
	}

	update_blog_option(1, 'smdlc_net_locations', $locations);
	update_blog_option(1, 'smd_net_locations', $locations_general);

	//Grabbing all the site IDs
    $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    //Going through the sites
    foreach ($siteids as $site_id) {
    	if (1 == $site_id){
    		continue;
    	}

    	switch_to_blog($site_id);

    	//we merge values received from network settings with local values of every blog
    	$locations_local = get_option('smdlc_locations') ?: array();
    	$locations_local_general = get_option('smd_locations') ?: array();

    	$locations_local = array_merge($locations_local, $locations);
    	$locations_local_general = array_merge($locations_local_general, $locations_general);

    	//updating local options
    	update_option('smdlc_locations', $locations_local);
    	update_option('smd_locations', $locations_local_general);

    }

    restore_current_blog();

	// At the end we redirect back to our options page.
    wp_redirect(add_query_arg(array('page' => 'smd_net_set_page',
    'settings-updated' => 'true'), network_admin_url('settings.php')));

    exit;
}

/**
 * Handler for properties settings update
 */
function smdlc_update_network_options() {

	check_admin_referer('smdlc_network_meta_properties-options');

	//Wordpress Database variable for database operations
    global $wpdb;

    //collecting network options values from request

    $shares11 = isset($_POST['smdlc_net_']) ? $_POST['smdlc_net_'] : array();
    //if property is frozen, it's automatically shared


    //updating network options in DB
	update_blog_option(1, 'smdlc_net_', $shares11);

	//Grabbing all the site IDs
    $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

    //Going through the sites
    foreach ($siteids as $site_id) {

    	if (1 == $site_id){
    		continue;
    	}

    	switch_to_blog($site_id);

    	//> we merge values received from network settings with local values of every blog

    	$shares11_local = get_option('smdlc_') ?: array();
    	$shares11_local = array_merge($shares11_local, $shares11);
    	//<

    	//updating local options
    	update_option('smdlc_', $shares11_local);

    	smdlc_update_overwrites();
    }

    restore_current_blog();

	// At the end we redirect back to our options page.
    wp_redirect(add_query_arg(array('page' => 'smd_net_set_page',
    'settings-updated' => 'true'), network_admin_url('settings.php')));

    exit;
}


add_action( 'network_admin_menu', 'smdlc_add_network_settings', 1000); //third parameter means priority, bigger => later executed hooked function
add_action( 'network_admin_edit_smdlc_update_network_locations', 'smdlc_update_network_locations');
add_action( 'network_admin_edit_smdlc_update_network_options', 'smdlc_update_network_options');
