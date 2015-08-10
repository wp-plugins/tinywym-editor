<?php
/*
Plugin Name: tinyWYM Editor
Description: Converts WordPress's WYSIWYG visual editor into a WYSIWYM editor
Version:     1.0
Author:      Andrew Rickards
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: twym_editor
Domain Path: /languages

	tinyWYM is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	any later version.
 
	tinyWYM  is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
 
	You should have received a copy of the GNU General Public License
	along with tinyWYM. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.

*/

//* If this file is called directly, abort ====================================
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//* Load Plugin textdomain ====================================================
add_action( 'plugins_loaded', 'twym_load_textdomain' );

function twym_load_textdomain() {

	load_plugin_textdomain( 'twym_editor', false, '/tinywym-editor/languages/' );

}

//* Remove Theme's Editor Stylesheet ==========================================
add_action( 'admin_init', 'twym_remove_editor_styles' );

function twym_remove_editor_styles() {

	remove_editor_styles();

}

//* Add Editor Stylesheet =====================================================
add_filter( 'mce_css', 'twym_add_editor_styles' );

function twym_add_editor_styles( $mce_css ) {

	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= plugins_url( 'css/tinywym-styles.css', __FILE__ );

	return $mce_css;

}

//* Register tinyMCE Plugin & Button ==========================================
add_action( 'init', 'twym_register_mce_plugin' );

function twym_register_mce_plugin() {

	// Check user permissions
	if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		return;
	}

	// Check if WYSIWYG is enabled
	if ( get_user_option('rich_editing') == 'true') {

		add_filter( 'mce_external_plugins', 'twym_add_mce_plugin' );
		add_filter( 'mce_buttons', 'twym_register_mce_button' );

		// Admin Styles for Modal Form
		add_action( 'admin_enqueue_scripts', 'twym_enqueue_admin_style' );

	}

}

//* Register Plugin
function twym_add_mce_plugin( $plugin_array ) {

	$plugin_array['tinyWYM'] = plugins_url( 'js/mce-plugin.js', __FILE__ );

	return $plugin_array;

}

//* Register Button
function twym_register_mce_button( $buttons ) {

	array_push( $buttons, 'twym_any_tag' );

	return $buttons;

}

//* Enqueue Admin Styles for Modal Form =======================================
function twym_enqueue_admin_style() {

	wp_register_style( 'custom_wp_admin_css', plugins_url( 'css/modal-styles.css', __FILE__ ), false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
		
}

//* Load Translation File =====================================================
add_filter( 'mce_external_languages', 'twym_load_translation');

function twym_load_translation( $locales ) {

	$locales[ 'twym_editor' ] = plugin_dir_path ( __FILE__ ) . 'translations.php';

	return $locales;

}