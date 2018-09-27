<?php

use \vocabularies\SMDLC_Metadata_Lifecycle as lifecycle_meta;


//Creating settings subpage for Simple Metadata

defined ("ABSPATH") or die ("No script assholes!");

/**
 * Function to add plugin settings subpage and registering settings and their sections
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

		register_setting ('smdlc_meta_properties', 'smdlc_freezes');

		
		//collecting current values of options
		$post_types = smd_get_all_post_types();
		$locations = get_option('smdlc_locations');
		$shares = get_option('smdlc_shares');
		$freezes = get_option('smdlc_freezes');

		//initiazaling variables for network values
		$network_locations = [];
		$network_shares = [];
		$network_freezes = [];

		//in case of multisite installation, we collect options for network
		if (is_multisite()){
			$network_locations = get_blog_option(1, 'smdlc_net_locations');
			$network_shares = get_blog_option(1, 'smdlc_net_shares');
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

		//creating fields for every property in lifecycle vocabulary
		foreach (lifecycle_meta::$lifecycle_properties as $key => $data) {

			add_settings_field ('smdlc_'.$key, ucfirst($data[0]), function () use ($key, $data, $shares, $freezes, $network_shares, $network_freezes){
				$checked_share = isset($shares[$key]) ? true : false;
				$checked_freeze = isset($freezes[$key]) ? true : false;
				$disabled_share = isset($network_shares[$key]) && $network_shares[$key] ? 'disabled' : '';
				$disabled_freeze = isset($network_freezes[$key]) && $network_freezes[$key] ? 'disabled' : '';
				?>
					<label for="smdlc_shares[<?=$key?>]"><i>Share</i> <input type="checkbox" name="smdlc_shares[<?=$key?>]" id="smdlc_shares[<?=$key?>]" value="1" <?php checked(1, $checked_share); echo $disabled_share?>></label>
					<label for="smdlc_freezes[<?=$key?>]"><i>Freeze</i> <input type="checkbox" name="smdlc_freezes[<?=$key?>]" id="smdlc_freezes[<?=$key?>]" value="1" <?php checked(1, $checked_freeze); echo $disabled_freeze?>></label>
					<br><span class="description"><?=$data[1]?></span>
				<?php
				if ('disabled' == $disabled_share){
					?>
						<input type="hidden" name="smdlc_shares[<?=$key?>]" value="1">
					<?php
				}
				if ('disabled' == $disabled_freeze){
					?>
						<input type="hidden" name="smdlc_freezes[<?=$key?>]" value="1">
					<?php
				}
			}, 'smdlc_meta_properties', 'smdlc_meta_properties');

		}
	}
}

/**
 * Function for rendering settings subpage
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
 * Function for rendering 'Locations' metabox
 */
function smdlc_render_metabox_schema_locations(){
	?>
	<div id="smdlc_meta_locations" class="smdlc_meta_locations">
		<span class="description">Description for lifecycle locations metabox</span>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'smdlc_meta_locations' );
			do_settings_sections( 'smdlc_meta_locations' );
			submit_button();
			?>
		</form>
		<p></p>
	</div>
	<?php
}

/**
 * Function for rendering 'lifecycle properties' metabox
 */
function smdlc_render_metabox_properties(){
	$locations = get_option('smdlc_locations');
	$level = is_plugin_active('pressbooks/pressbooks.php') ? 'metadata' : 'site-meta';
	$label = $level == 'metadata' ? 'Book Info' : 'Site-Meta';
	if (isset($locations[$level]) && $locations[$level]){
	?>
	<div id="smdlc_meta_properties" class="smdlc_meta_properties">
		<span class="description">Description for lifecycle properties metabox</span>
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
 * Function for updating options and forcing overwritings on settings update
 */
function smdlc_update_overwrites(){

	//collecting options values
	$locations = get_option('smdlc_locations') ?: [];
	$shares = get_option('smdlc_shares') ?: [];
	$freezes = get_option('smdlc_freezes') ?: [];

	//if nothing is chosen to share or freeze, return
	if(empty($shares) && empty($freezes)){
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
        AND `meta_value` <>''",$meta_post_id,'%%smdlc_%%','%%_vocab%%'.$meta_type.'%%')
            ,ARRAY_A);
    
 	//Array for storing metakey=>metavalue
    $metaData = [];
    //unwrapping data from subarrays
    foreach($meta_post_meta as $meta){
        $metaData[$meta['meta_key']] = $meta['meta_value'];
    }
    //if there are no fields of lifecycle meta in site-meta/ book info, nothing to share or freeze, exit
    if(count($metaData) == 0){
        return;
    }

    //checking if there is somthing to share for lifecycle properties
	if(!empty($shares)){

		//looping through all active locations
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

        		foreach ($shares as $key => $value) {
        			$meta_key = 'smdlc_'.strtolower($key).'_life_vocab_'.$location;
        			$metadata_meta_key = 'smdlc_'.strtolower($key).'_life_vocab_'.$meta_type;
        			if((!get_post_meta($post_id, $meta_key) || '' == get_post_meta($post_id, $meta_key)) && isset($metaData[$metadata_meta_key])){
        				update_post_meta($post_id, $meta_key, $metaData[$metadata_meta_key]);
        			}
        		}
        	}

		}
	}

	//checking if there is somthing to share for lifecycle properties
	if(!empty($freezes)){

		//looping through all active locations
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

        		foreach ($freezes as $key => $value) {
        			$meta_key = 'smdlc_'.strtolower($key).'_life_vocab_'.$location;
        			$metadata_meta_key = 'smdlc_'.strtolower($key).'_life_vocab_'.$meta_type;
        			if(isset($metaData[$metadata_meta_key])){
        				update_post_meta($post_id, $meta_key, $metaData[$metadata_meta_key]);
        			}
        		}
        	}

		}
	}
}

add_action('admin_menu', 'smdlc_add_lyfecycle_settings', 100);
add_action('updated_option', function( $option_name, $old_value, $value ){
	if ('smdlc_freezes' == $option_name){
		$shares = get_option('smdlc_shares') ?: [];
		$value = empty($value) ? [] : $value;
		$shares = array_merge($shares, $value);
		
		update_option('smdlc_shares', $shares);
	}

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
