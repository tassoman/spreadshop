<?php
/*
Plugin Name: Spreadshop
Plugin URI: http://blog.tassoman.com/wordpress-plugins/spreadshop
Description: Put a free shop into your blog with Spreadshirt and start selling your personalized gadgets and t-shirts!
Version: 1.8
Author: Tassoman
Author URI: http://blog.tassoman.com
*/

/*
 * spreadshop.php was created on Sun Nov 25 23:34:15 GMT+01:00 2007 at 23:34:15 by tassoman
 *
 * Copyright (C) 2007 - tassoman
 *
 * http://www.softwarelibero.it/gnudoc/gpl.it.txt
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License Version 2.x
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Translations:
 * Italian:	Tassoman		(tassoman@gmail.com)
 * German:		Frank Bueltge	(frank@bueltge.de)
 * French:		David Allard	(guerdal@free.fr)
 * ShowArticle and spread_article_filter functions are written by Steffen Forkmann (steffen.forkmann@msu-solutions.de)
 *
 */

define('SPREAD_VER', 180);
define('SPREAD_DIR', 'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'/spreadshop');
define('SPREAD_PAGE', '<!--spreadshop-->');

if ( function_exists('load_plugin_textdomain') ) {
	load_plugin_textdomain('spreadshop', SPREAD_DIR);
}

if ( function_exists('register_activation_hook') ) {
	register_activation_hook( basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__), 'spread_installation' );
}
if ( function_exists('register_deactivation_hook') ) {
	register_deactivation_hook( basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__), 'spread_uninstallation' );
}

/**
 * WordPress backoffice menu integration
 *
 */
function add_spread_menu() {
	if(function_exists('add_submenu_page'))
		add_submenu_page('edit.php', __('Spreadshop plugin','spreadshop'), 'Spreadshop', 'level_2', basename(__FILE__), 'spread_sub_page');
}

/**
 * Hooks the custom menu into the general menu
 *
 */
if(function_exists('add_action')) {
	add_action('admin_menu','add_spread_menu');
}

/**
 * Create the environment
 *
 */
function spread_installation() {
	# TODO
}

/**
 * Delete any information about plugin
 *
 */
function spread_uninstallation() {
	# TODO
}

/**
 * Administration page rendering
 *
 */
function spread_sub_page() {
	require_once(APPPATH.DIRECTORY_SEPARATOR.SPREAD_DIR.'admin-page.php');
}

require_once(APPPATH.DIRECTORY_SEPARATOR.SPREAD_DIR.'filters.php');

function randomSpreadArticle($size = 'small') {
	spread_random_article_filter($size);
}

add_filter('the_content', 'spread_shop_filter');
add_filter('the_content', 'spread_article_filter');

?>
