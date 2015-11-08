<?php
/**
 * MPD Addon: Restrict Sites
 * 
 * This MPD addon allows you to restrict what sites can be the sourse of any Duplication
 * 
 * @ignore
 * @since 0.6
 * @author Mario Jaconelli <mariojaconelli@gmail.com>
 * 
 */

/**
 * @ignore
 */
function restrict_addon_mpd_settings(){

	mpd_settings_field('restrict_option_setting', __( 'Restrict MPD to certain sites', MPD_DOMAIN ), 'restrict_option_setting_render');
	mpd_settings_field('restrict_some_option_setting', __( 'Restrict MPD on some sites', MPD_DOMAIN ), 'restrict_some_option_setting_render');
    mpd_settings_field('master_site_setting', __( 'Select a Master Site', MPD_DOMAIN ), 'master_site_settings_render');
    
     
}

add_action( 'mdp_end_plugin_setting_page', 'restrict_addon_mpd_settings');

function restrict_option_setting_render(){
  
  if($options = get_option( 'mdp_settings' )){
		$mdp_restrict_radio_label_value = $options['restrict_option_setting'];
	}else{
		$mdp_restrict_radio_label_value = 'none';
	};

  ?>
	<div id="mpd_restrict_radio_choice_wrap">

		<div class="mdp-inputcontainer">
			<input type="radio" class="mdp_radio" name='mdp_settings[restrict_option_setting]' id="mpd_restrict_none" <?php checked($mdp_restrict_radio_label_value, 'none'); ?> value="none">
		
			<label class="mdp_radio_label" for="radio-choice-1"><?php _e('No Restrictions', MPD_DOMAIN ) ?></label>
			    
			<input type="radio" class="mdp_radio" name='mdp_settings[restrict_option_setting]' id="mpd_restrict_some" <?php checked($mdp_restrict_radio_label_value, 'some'); ?> value="some">
		
			<label class="mdp_radio_label" for="radio-choice-2"><?php _e('Restrict Some Sites', MPD_DOMAIN ) ?></label>
		
			<input type="radio" class="mdp_radio" name='mdp_settings[restrict_option_setting]' id="mpd_restrict_set_master" <?php checked($mdp_restrict_radio_label_value, 'master'); ?> value="master">
		
			<label class="mdp_radio_label" for="radio-choice-3"><?php _e('Select a Master Site', MPD_DOMAIN) ?></label>
	    </div>

	    <p class="mpdtip"><?php _e('You can, if you want, limit MPD functionality to only certain sites.', MPD_DOMAIN ) ?></p>

    </div>

  <?php

}
function mpd_get_restrict_some_sites_options(){

	if(!$options = get_option( 'mdp_settings' )){

		return null;

	}else{

		$options = get_option( 'mdp_settings' );
		$requested_restricted_sites = array();
		
		foreach ($options as $key => $value) {
        
        	if (substr($key, 0, 24) == "mpd_restrict_some_sites_") {

            	$requested_restricted_sites[] = $value;

        	}

    	}

    	return $requested_restricted_sites;

	}

}
function restrict_some_option_setting_render(){
  
  $restricted_ids 	= mpd_get_restrict_some_sites_options();
  $sites   			= mpd_wp_get_sites();

  ?>
		<?php foreach ($sites as $site): ?>
			
			<?php 	

				$blog_details 	= get_blog_details($site['blog_id']);
				$checkme		= ''; 

			?>

			<?php 
				
				if(in_array($site['blog_id'], $restricted_ids)){
					$checkme = 'checked="checked"';
				}

			?>
				<input type='checkbox' class="restrict-some-checkbox" name='mdp_settings[mpd_restrict_some_sites_<?php echo $site['blog_id'] ?>]' <?php echo $checkme; ?> value='<?php echo $site['blog_id']; ?>'> <?php echo $blog_details->blogname; ?> <br >
			

		<?php endforeach;?>

		<p class="mpdtip"><?php _e('Select some sites where you do not want MDP functionality. Note: You should not select all sites here as this will result in no MPD functionality.', MPD_DOMAIN ) ?></p>
  <?php

}

function master_site_settings_render(){

  $options = get_option( 'mdp_settings' );
  $sites   = mpd_wp_get_sites();

   if($options = get_option( 'mdp_settings' )){
		$mdp_restrict_master_label_value = $options['master_site_setting'];
	};
  ?>
  
  <select name="mdp_settings[master_site_setting]" class="mpd-master-site" style="width:300px;">

		<option></option>
		<?php foreach ($sites as $site): ?>
			<?php $blog_details = get_blog_details($site['blog_id']); ?>

			<option value="<?php echo $site['blog_id'] ?>" <?php selected($mdp_restrict_master_label_value, $site['blog_id']); ?>>

			    <?php echo $blog_details->blogname;?>

			</option>

		<?php endforeach ?>
  		
  </select>

  <p class="mpdtip"><?php _e('If you want to only allow duplication to take place from one site then select it here.', MPD_DOMAIN)?></p>
  <?php

}

function mpd_add_addon_script_to_settings_page(){
	
	$screenid = get_current_screen()->id;

	if($screenid == 'settings_page_multisite_post_duplicator'){
	?>
	<script>
		jQuery(document).ready(function() {
			jQuery(".mpd-master-site").parent().parent().hide();
			jQuery(".restrict-some-checkbox").parent().parent().hide();
			
			jQuery(".mpd-master-site").select2({
				placeholder: '<?php _e("Select a Master Site", MPD_DOMAIN) ?>'	
			});

			if(jQuery('#mpd_restrict_set_master').is(':checked') ){
      				jQuery(".mpd-master-site").parent().parent().show();
  			}
  			if(jQuery('#mpd_restrict_some').is(':checked') ){
      				jQuery(".restrict-some-checkbox").parent().parent().show();
  			}

  			jQuery('#mpd_restrict_radio_choice_wrap .mdp_radio').change(function() {
  		
  				if (jQuery(this).val() == 'master') {
  					jQuery(".mpd-master-site").parent().parent().show('fast');
  				}else{
  					jQuery(".mpd-master-site").parent().parent().hide('fast');
  				};

  				if (jQuery(this).val() == 'some') {
  					jQuery(".restrict-some-checkbox").parent().parent().show('fast');
  				}else{
  					jQuery(".restrict-some-checkbox").parent().parent().hide('fast');
  				};
  		
  			});

		});
		
	</script>
	<?php
	}
}
add_action('admin_head', 'mpd_add_addon_script_to_settings_page');