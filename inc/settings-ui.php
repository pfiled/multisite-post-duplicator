<?php
add_action( 'admin_menu', 'mdp_add_admin_menu' );
add_action( 'admin_init', 'mdp_settings_init' );


function mdp_add_admin_menu(  ) { 

	add_submenu_page( 'options-general.php', 'Multisite Post Duplicator Settings', 'Multisite Post Duplicator Settings', 'manage_options', 'multisite_post_duplicator', 'mdp_options_page' );

}


function mdp_settings_init(  ) { 

	register_setting( 'mdp_plugin_setting_page', 'mdp_settings' );

	add_settings_section(
		'mdp_mdp_plugin_setting_page_section', 
		'<h2>'. __( 'Multisite Post Duplicator Settings Page' . '</h2>', 'mdp' ), 
		'mdp_settings_section_callback', 
		'mdp_plugin_setting_page'
	);
	add_settings_field( 
		' ', 
		__( 'What Post Types you want to show the MPD Meta Box?', 'mdp' ), 
		'meta_box_show_radio_render', 
		'mdp_plugin_setting_page', 
		'mdp_mdp_plugin_setting_page_section' 
	);

	$mpd_post_types = get_post_types();
	$loopcount = 1;
	foreach ($mpd_post_types as $mpd_post_type){

		add_settings_field( 
			'meta_box_post_type_selector_' . $mpd_post_type, 
			$loopcount == 1 ? "Select post types to show the MPD Meta Box on" : "" , 
			'meta_box_post_type_selector_render', 
			'mdp_plugin_setting_page', 
			'mdp_mdp_plugin_setting_page_section',
			array('mdpposttype' => $mpd_post_type)
		);

		$loopcount++;
	}
	
	add_settings_field( 
		'mdp_default_prefix', 
		__( 'Default Prefix', 'mdp' ), 
		'mdp_default_prefix_render', 
		'mdp_plugin_setting_page', 
		'mdp_mdp_plugin_setting_page_section' 
	);

}

function meta_box_show_radio_render(){

	if($options = get_option( 'mdp_settings' )){
		$mdp_radio_label_value = $options['meta_box_show_radio'];
	}else{
		$mdp_radio_label_value = 'all';
	};

	?>
	<div id="mpd_radio_choice_wrap">
		<input type="radio" class="mdp_radio" name='mdp_settings[meta_box_show_radio]' id="meta_box_show_choice_all" <?php checked( $mdp_radio_label_value, 'all'); ?> value="all">
		<label class="mdp_radio_label" for="radio-choice-1">All post types</label>
	    
		<input type="radio" class="mdp_radio" name='mdp_settings[meta_box_show_radio]' id="meta_box_show_choice_some" <?php checked( $mdp_radio_label_value, 'some'); ?> value="some">
	    <label class="mdp_radio_label" for="radio-choice-2">Some post types</label>

	    <input type="radio" class="mdp_radio" name='mdp_settings[meta_box_show_radio]' id="meta_box_show_choice_none" <?php checked( $mdp_radio_label_value, 'none'); ?> value="none">
	    <label class="mdp_radio_label" for="radio-choice-2">No post types</label>
    </div>
	<?php
}


function meta_box_post_type_selector_render($args) { 

	$options = get_option( 'mdp_settings' );
	$mpd_post_type = $args['mdpposttype'];
	$the_name = "mdp_settings[meta_box_post_type_selector_" . $mpd_post_type . "]";
	if(isset($options['meta_box_post_type_selector_' . $mpd_post_type ])){
		$checkedLookup = checked( $options['meta_box_post_type_selector_' . $mpd_post_type ], $mpd_post_type, false);
	}elseif(!$options){
		$checkedLookup = 'checked="checked"';
	}else{
		$checkedLookup = '';
	};

	?>

		<input type='checkbox' name='<?php echo $the_name; ?>' <?php echo $checkedLookup;?> value='<?php echo $mpd_post_type; ?>'> <?php echo $mpd_post_type; ?> <br >


	<?php

}


function mdp_default_prefix_render(  ) { 

	$options = get_option( 'mdp_settings' );
	?>
	<input type='text' name='mdp_settings[mdp_default_prefix]' value='<?php echo $options ? $options['mdp_default_prefix'] : "Copy of"; ?>'>
	<?php

}

function mdp_settings_section_callback(  ) { 

	echo __( 'Here you can change the default settings for Multisite Post Duplicator. Note that these settings are global for all the sites on your multisite network', 'mdp' );

}


function mdp_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<?php
		settings_fields( 'mdp_plugin_setting_page' );
		do_settings_sections( 'mdp_plugin_setting_page' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>