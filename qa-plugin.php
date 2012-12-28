<?php

/*
	Plugin Name: Permissions2Categories
	Plugin URI: 
	Plugin Description: Allows permissions to be set for categories.
	Plugin Version: 1.0.
	Plugin Date: 2012-12-27
	Plugin Author: Kirill Fuchs
	Plugin Author URI: http://codesubstance.com/
	Plugin License: 
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}
	
	qa_register_plugin_overrides('p2c_overrides.php');
	qa_register_plugin_layer('p2c_layer.php', 'Permissions 2 Categories Layer');
	qa_register_plugin_module('process', 'p2c-module.php', 'p2c_category_permission', 'Permissions2Categories');
	qa_register_plugin_module('page', 'p2c-page.php', 'p2c_page', 'Permissions 2 Categories Page');
	

/*
	Omit PHP closing tag to help avoid accidental output
*/