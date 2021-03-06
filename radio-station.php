<?php

/**
 * @package Radio Station
 * @version 2.3.3
 */
/*

Plugin Name: Radio Station
Plugin URI: https://netmix.com/radio-station
Description: Adds Show pages, DJ role, playlist and on-air programming functionality to your site.
Author: Tony Zeoli, Tony Hayes
Version: 2.3.3
Text Domain: radio-station
Domain Path: /languages
Author URI: https://netmix.com/radio-station
GitHub Plugin URI: netmix/radio-station

Copyright 2019 Digital Strategy Works  (email : info@digitalstrategyworks.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// === Setup ===
// - Define Plugin Constants
// - Define Plugin Data Slugs
// - Include Plugin Files
// - Plugin Options and Defaults
// - Plugin Loader Settings
// - Start Plugin Loader Instance
// - Include Plugin Admin Files
// - Load Plugin Text Domain
// - Check Plugin Version
// - Flush Rewrite Rules
// - Enqueue Plugin Script
// - Enqueue Plugin Stylesheet
// - Localize Time Strings
// === Template Filters ===
// - Automatic Pages Content Filter
// - Single Content Template Filter
// - Show Content Template Filter
// - Playlist Content Template Filter
// - Override Content Template Filter
// - DJ / Host / Producer Template Fix
// - Get DJ / Host Template
// - Get Producer Template
// - Single Template Hierarchy
// - Single Templates Loader
// - Archive Template Hierarchy
// x Archive Templates Loader
// - Add Links Back to Show
// - Show Posts Adjacent Links
// === Query Filters ===
// - Playlist Archive Query Filter
// === User Roles ===
// - Set Roles and Capabilities
// - maybe Revoke Edit Show Capability
// === Debugging ===
// - Set Debug Mode Constant
// - maybe Clear Transient Data
// - Debug Output and Logging


// -------------
// === Setup ===
// -------------

// -----------------------
// Define Plugin Constants
// -----------------------
// 2.3.1: added constant for Netmix Directory
define( 'RADIO_STATION_FILE', __FILE__ );
define( 'RADIO_STATION_DIR', dirname( __FILE__ ) );
define( 'RADIO_STATION_BASENAME', plugin_basename( __FILE__ ) );
define( 'RADIO_STATION_HOME_URL', 'https://netmix.com/radio-station/' );
define( 'RADIO_STATION_DOCS_URL', 'https://netmix.com/radio-station/docs/' );
define( 'RADIO_STATION_API_DOCS_URL', 'https://netmix.com/radio-station/docs/api/' );
define( 'RADIO_STATION_PRO_URL', 'https://netmix.com/radio-station-pro/' );
define( 'RADIO_STATION_NETMIX_DIR', 'https://netmix.com/' );

// ------------------------
// Define Plugin Data Slugs
// ------------------------
define( 'RADIO_STATION_LANGUAGES_SLUG', 'rs-languages' );
define( 'RADIO_STATION_HOST_SLUG', 'rs-host' );
define( 'RADIO_STATION_PRODUCER_SLUG', 'rs-producer' );

// --- check and define CPT slugs ---
// TODO: prefix original slugs and update post/taxonomy data
// ... and then add new slugs to template hierarchy ...
if ( get_option( 'radio_show_cpts_prefixed' ) ) {
	define( 'RADIO_STATION_SHOW_SLUG', 'rs-show' );
	define( 'RADIO_STATION_PLAYLIST_SLUG', 'rs-playlist' );
	define( 'RADIO_STATION_OVERRIDE_SLUG', 'rs-override' );
	define( 'RADIO_STATION_GENRES_SLUG', 'rs-genres' );
} else {
	define( 'RADIO_STATION_SHOW_SLUG', 'show' );
	define( 'RADIO_STATION_PLAYLIST_SLUG', 'playlist' );
	define( 'RADIO_STATION_OVERRIDE_SLUG', 'override' );
	define( 'RADIO_STATION_GENRES_SLUG', 'genres' );
}


// --------------------
// Include Plugin Files
// --------------------
// 2.3.0: include new data feeds file
// 2.3.0: renamed widget files to match new widget names
// 2.3.0: separate file for legacy support functions

// --- Main Includes ---
require RADIO_STATION_DIR . '/includes/post-types.php';
require RADIO_STATION_DIR . '/includes/support-functions.php';
require RADIO_STATION_DIR . '/includes/data-feeds.php';
require RADIO_STATION_DIR . '/includes/legacy.php';

// --- Shortcodes ---
require RADIO_STATION_DIR . '/includes/master-schedule.php';
require RADIO_STATION_DIR . '/includes/shortcodes.php';

// --- Widgets ---
require RADIO_STATION_DIR . '/includes/class-current-show-widget.php';
require RADIO_STATION_DIR . '/includes/class-upcoming-shows-widget.php';
require RADIO_STATION_DIR . '/includes/class-current-playlist-widget.php';
require RADIO_STATION_DIR . '/includes/class-radio-clock-widget.php';

// --- Player ---
// 2.3.1.1: load radio player prototype (if present)
$player_file = RADIO_STATION_DIR . '/player/radio-player.php';
if ( file_exists( $player_file ) ) {
	require $player_file;
}

// --- Feature Development ---
// 2.3.0: add feature branch development includes
// 2.3.1: added radio player widget file
$features = array( 'class-radio-player-widget', 'import-export' );
foreach ( $features as $feature ) {
	$filepath = RADIO_STATION_DIR . '/includes/' . $feature . '.php';
	if ( file_exists ( $filepath ) ) {
		include $filepath;
	}
}


// ---------------------------
// Plugin Options and Defaults
// ---------------------------
// 2.3.0: added plugin options
$timezones = radio_station_get_timezone_options( true );
$languages = radio_station_get_language_options( true );
$options = array(

	// === Broadcast ===

	// --- Streaming URL ---
	'streaming_url' => array(
		'type'    => 'text',
		'options' => 'URL',
		'label'   => __( 'Streaming URL', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Enter the Streaming URL for your Radio Station. This will be discoverable via Data Feeds and used in the upcoming Radio Player.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// --- Fallback Stream Format ---
	// 'fallback_url' => array(
	// 	'type'    => 'select',
	// 	'options' => array( 'mp3', 'aac' ), // ...
	// 	'label'   => __( 'Streaming Format', 'radio-station' ),
	//	'default' => 'mp3',
	// 	'helper'  => __( 'Select streaming fallback for streaming URL.', 'radio-station' ),
	// 	'tab'     => 'general',
	// 	'section' => 'broadcast',
	// ),

	// --- Fallback Stream URL ---
	// 'fallback_url' => array(
	// 	'type'    => 'text',
	// 	'options' => 'URL',
	// 	'label'   => __( 'Fallback Stream URL', 'radio-station' ),
	//  'default' => '',
	// 	'helper'  => __( 'Enter an alternative Streaming URL for Player fallback.', 'radio-station' ),
	// 	'tab'     => 'general',
	// 	'section' => 'broadcast',
	// ),

	// --- Fallback Stream Format ---
	// 'fallback_url' => array(
	// 	'type'    => 'text',
	// 	'options' => array( 'mp3', 'aac' ), // ...
	// 	'label'   => __( 'Fallback Format', 'radio-station' ),
	//	'default' => 'mp3',
	// 	'helper'  => __( 'Select streaming fallback for fallback URL.', 'radio-station' ),
	// 	'tab'     => 'general',
	// 	'section' => 'broadcast',
	// ),

	// --- Main Radio Language ---
	'radio_language'    => array(
		'type'    => 'select',
		'options' => $languages,
		'label'   => __( 'Main Broadcast Language', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Select the main language used on your Radio Station.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'broadcast',
	),

	// === Times ===

	// --- Timezone Location ---
	'timezone_location' => array(
		'type'    => 'select',
		'options' => $timezones,
		'label'   => __( 'Location Timezone', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Select your Broadcast Location for Radio Timezone display.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'times',
	),

	// --- Clock Time Format ---
	'clock_time_format' => array(
		'type'    => 'select',
		'options' => array(
			'12' => __( '12 Hour Format', 'radio-station' ),
			'24' => __( '24 Hour Format', 'radio-station' ),
		),
		'label'   => __( 'Clock Time Format', 'radio-station' ),
		'default' => '12',
		'helper'  => __( 'Default Time Format for display output. Can be overridden in each shortcode or widget.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'times',
	),

	// --- REST Data Routes ---
	'enable_data_routes' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Enable Data Routes', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Enables Station Data Routes via WordPress REST API.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
	),

	// --- Data Feed Links ---
	'enable_data_feeds' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Enable Data Feeds', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Enable Station Data Feeds via WordPress Feed links.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
	),

	// --- Ping Netmix Directory ---
	// note: disabled by default for WordPress.org repository compliance
	'ping_netmix_directory' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Ping Netmix Directory', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'If you have a Netmix Directory listing, enable this to ping the directory whenever you update your schedule.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
	),

	// --- Clear Transients ---
	'clear_transients' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Clear Transients', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Clear Schedule transients with every pageload. Less efficient but more reliable.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
	),

	// --- Transient Caching ---
	'transient_caching' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Show Transients', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Use Show Transient Data to improve Schedule calculation performance.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
		'pro'     => true,
	),

	// --- Show Shift Feeds ---
	/* 'show_shift_feeds' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Show Shift Feeds', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Convert RSS Feeds for a single Show to a Show shift feed, allowing a visitor to subscribe to a Show feed to be notified of Show shifts.', 'radio-station' ),
		'tab'     => 'general',
		'section' => 'feeds',
		'pro'     => true,
	), */

	// === Master Schedule Page ===

	// --- Schedule Page ---
	'schedule_page'     => array(
		'type'    => 'select',
		'options' => 'PAGEID',
		'label'   => __( 'Master Schedule Page', 'radio-station' ),
		'default' => '',
		'helper'  => __( 'Select the Page you are displaying the Master Schedule on.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Automatic Schedule Display ---
	'schedule_auto' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Replaces selected page content with Master Schedule. Alternatively customize with the shortcode: ', 'radio-station' ) . ' [master-schedule]',
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Default Schedule View ---
	'schedule_view'       => array(
		'type'    => 'select',
		'label'   => __( 'Schedule View Default', 'radio-station' ),
		'default' => 'table',
		'options' => array(
			'table'   => __( 'Table View', 'radio-station' ),
			'list'    => __( 'List View', 'radio-station' ),
			'div'     => __( 'Divs View', 'radio-station' ),
			'tabs'    => __( 'Tabbed View', 'radio-station' ),
			'default' => __( 'Legacy Table', 'radio-station' ),
		),
		'helper'  => __( 'View type to use for automatic display on Master Schedule Page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- Schedule Clock Display ---
	'schedule_clock'       => array(
		'type'    => 'select',
		'label'   => __( 'Schedule Clock?', 'radio-station' ),
		'default' => 'clock',
		'options' => array(
			''         => __( 'None', 'radio-station' ),
			'clock'    => __( 'Clock', 'radio-station' ),
			'timezone' => __( 'Timezone', 'radio-station' ),
		),
		'helper'  => __( 'Radio Time section display above program Schedule.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
	),

	// --- [Pro] Schedule Switcher ---
	'schedule_switcher'   => array(
		'type'    => 'checkbox',
		'label'   => __( 'View Switching', 'radio-station' ),
		'default' => '',
		'value'   => 'yes',
		'helper'  => __( 'Enable View Switching on the Master Schedule.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
		'pro'     => true,
	),

	// --- [Pro] Available Views ===
	// 2.3.2: added additional views option
	'schedule_views'      => array(
		'type'    => 'multicheck',
		'label'   => __( 'Available Views', 'radio-station' ),
		// note: unstyled list view not included in defaults
		'default' => array( 'table', 'tabs' ),
		'value'		=> 'yes',
		'options'	=> array(
			'table' => __( 'Table View', 'radio-station' ),
			'tabs'  => __( 'Tabbed View', 'radio-station' ),
			'list'  => __( 'List View', 'radio-station' ),
			// 'grid'  => __( 'Grid View', 'radio-station' ),
			// 'calendar' => __( 'Calendar View', 'radio-station' );
		),
		'helper'  => __( 'Switcher Views available on Master Schedule.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'schedule',
		'pro'     => true,
	),

	// === Show Page ===

	// --- Show Blocks Position ---
	'show_block_position' => array(
		'type'    => 'select',
		'label'   => __( 'Info Blocks Position', 'radio-station' ),
		'options' => array(
			'left'  => __( 'Float Left', 'radio-station' ),
			'right' => __( 'Float Right', 'radio-station' ),
			'top'   => __( 'Float Top', 'radio-station' ),
		),
		'default' => 'left',
		'helper'  => __( 'Where to position Show info blocks relative to Show Page content.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// ---- Show Section Layout ---
	'show_section_layout' => array(
		'type'    => 'select',
		'label'   => __( 'Show Content Layout', 'radio-station' ),
		'options' => array(
			'tabbed'   => __( 'Tabbed', 'radio-station' ),
			'standard' => __( 'Standard', 'radio-station' ),
		),
		'default' => 'tabbed',
		'helper'  => __( 'How to display extra sections below Show description. In content tabs or standard layout down the page.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Show Header Image ---
	// 2.3.2: added plural to option label
	'show_header_image' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Content Header Images', 'radio-station' ),
		'value'   => 'yes',
		'default' => '',
		'helper'  => __( 'If your chosen template does not display the Featured Image, enable this and use the Content Header Image box on the Show edit screen instead.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Latest Show Posts ---
	// 'show_latest_posts' => array(
	// 	'type'    => 'numeric',
	// 	'label'   => __( 'Latest Show Posts', 'radio-station' ),
	// 	'step'    => 1,
	// 	'min'     => 0,
	// 	'max'     => 100,
	// 	'default' => 3,
	// 	'helper'  => __( 'Number of Latest Blog Posts to Show above Show Page tabs.', 'radio-station' ),
	// 	'tab'     => 'pages',
	// 	'section' => 'show',
	// ),

	// --- Show Posts Per Page ---
	'show_posts_per_page' => array(
		'type'    => 'numeric',
		'label'   => __( 'Posts per Page', 'radio-station' ),
		'step'    => 1,
		'min'     => 0,
		'max'     => 1000,
		'default' => 10,
		'helper'  => __( 'Linked Show Posts per page on the Show Page tab/display.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- Show Playlists per Page ---
	'show_playlists_per_page' => array(
		'type'    => 'numeric',
		'step'    => 1,
		'min'     => 0,
		'max'     => 1000,
		'label'   => __( 'Playlists per Page', 'radio-station' ),
		'default' => 10,
		'helper'  => __( 'Playlists per page on the Show Page tab/display', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'show',
	),

	// --- [Pro] Show Episodes per Page ---
	// 'show_episodes_per_page' => array(
	// 	'type'    => 'number',
	// 	'label'   => __( 'Episodes per Page', 'radio-station' ),
	// 	'step'    => 1,
	// 	'min'     => 1,
	// 	'max'     => 1000,
	// 	'default' => 10,
	// 	'helper'  => __( 'Number of Show Episodes per page on the Show page tab/display.', 'radio-station' ),
	// 	'tab'     => 'pages',
	// 	'section' => 'show',
	// 	'pro'     => true,
	// ),

	// ==== Archives ===

	// --- Shows Archive Page ---
	'show_archive_page'       => array(
		'label'   => __( 'Shows Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Show archive list.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// --- Automatic Display ---
	'show_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Show Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [shows-archive]',
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// ? --- Redirect Shows Archive --- ?
	// 'show_archive_override' => array(
	// 	'label'   => __( 'Redirect Shows Archive', 'radio-station' ),
	// 	'type'    => 'checkbox',
	// 	'value'   => 'yes',
	// 	'default' => '',
	// 	'helper'  => __( 'Redirect Custom Post Type Archive for Shows to Shows Archive Page.', 'radio-station' ),
	// 	'tab'     => 'pages',
	// 	'section' => 'archives',
	// ),

	// --- Overrides Archive Page ---
	'override_archive_page'       => array(
		'label'   => __( 'Overrides Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Override archive list.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// --- Automatic Display ---
	'override_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Override Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [overrides-archive]',
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// ? --- Redirect Overrides Archive --- ?
	// 'override_archive_override' => array(
	// 	'label'   => __( 'Redirect Overrides Archive', 'radio-station' ),
	// 	'type'    => 'checkbox',
	// 	'value'   => 'yes',
	// 	'default' => '',
	// 	'helper'  => __( 'Redirect Custom Post Type Archive for Overrides to Overrides Archive Page.', 'radio-station' ),
	// 	'tab'     => 'pages',
	// 	'section' => 'archives',
	// ),

	// --- Playlists Archive Page ---
	'playlist_archive_page' => array(
		'label'   => __( 'Playlists Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Playlist archive list.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// --- Automatic Display ---
	'playlist_archive_auto' => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Playlist Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [playlists-archive]',
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// ? --- Redirect Playlists Archive --- ?
	// 'playlist_archive_override' => array(
	// 	'label'   => __( 'Redirect Playlists Archive', 'radio-station' ),
	// 	'type'    => 'checkbox',
	// 	'value'   => 'yes',
	// 	'default' => '',
	// 	'helper'  => __( 'Redirect Custom Post Type Archive for Playlists to Playlist Archive Page.', 'radio-station' ),
	// 	'tab'     => 'pages',
	// 	'section' => 'archives',
	// ),

	// --- Genres Archive Page ---
	'genre_archive_page' => array(
		'label'   => __( 'Genres Archive Page', 'radio-station' ),
		'type'    => 'select',
		'options' => 'PAGEID',
		'default' => '',
		'helper'  => __( 'Select the Page for displaying the Genre archive list.', 'radio-station' ),
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// --- Automatic Display ---
	'genre_archive_auto'         => array(
		'label'   => __( 'Automatic Display', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => 'yes',
		'helper'  => __( 'Replaces selected page content with default Genre Archive. Alternatively customize display using the shortcode:', 'radio-station' ) . ' [genres-archive]',
		'tab'     => 'pages',
		'section' => 'archives',
	),

	// ? --- Redirect Genres Archives --- ?
	// 'genre_archive_override' => array(
	//  'label'   => __( 'Redirect Genres Archive', 'radio-station' ),
	//	'type'    => 'checkbox',
	//	'value'   => 'yes',
	//	'default' => '',
	//	'helper'  => __( 'Redirect Taxonomy Archive for Genres to Genres Archive Page.', 'radio-station' ),
	//	'tab'     => 'pages',
	//	'section' => 'archives',
	// ),

	// === Templates ===

	// --- Templates Change Note ---
	'templates_change_note'      => array(
		'type'    => 'note',
		'label'   => __( 'Templates Change Note', 'radio-station' ),
		'helper'  => __( 'Since 2.3.0, the way that Templates are implemented has changed.', 'radio-station' )
		             . ' ' . __( 'See the Documentation for more information:', 'radio-station' )
		             . ' <a href="' . RADIO_STATION_DOCS_URL . 'display/#page-templates" target="_blank">' . __( 'Templates Documentation', 'radio-station' ) . '</a>',
		'tab'     => 'templates',
		'section' => 'single',
	),

	// --- Show Template ---
	'show_template'              => array(
		'label'   => __( 'Show Template', 'radio-station' ),
		'type'    => 'select',
		'options' => array(
			'page'     => __( 'Theme Page Template (page.php)', 'radio-station' ),
			'post'     => __( 'Theme Post Template (single.php)', 'radio-station' ),
			'singular' => __( 'Theme Singular Template (singular.php)', 'radio-station' ),
			'legacy'   => __( 'Legacy Plugin Template', 'radio-station' ),
		),
		'default' => 'page',
		'helper'  => __( 'Which template to use for displaying Show content.', 'radio-station' ),
		'tab'     => 'templates',
		'section' => 'single',
	),

	// --- Combined Template Method ---
	'show_template_combined'     => array(
		'label'   => __( 'Combined Method', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => '',
		'helper'  => __( 'Advanced usage. Use both a custom template AND content filtering for a Show. (Not compatible with Legacy templates.)', 'radio-station' ),
		'tab'     => 'templates',
		'section' => 'single',
	),

	// --- Playlist Template ---
	'playlist_template'          => array(
		'label'   => __( 'Playlist Template', 'radio-station' ),
		'type'    => 'select',
		'options' => array(
			'page'   => __( 'Theme Page Template (page.php)', 'radio-station' ),
			'post'   => __( 'Theme Post Template (single.php)', 'radio-station' ),
			'legacy' => __( 'Legacy Plugin Template', 'radio-station' ),
		),
		'default' => 'page',
		'helper'  => __( 'Which template to use for displaying Playlist content.', 'radio-station' ),
		'tab'     => 'templates',
		'section' => 'single',
	),

	// --- Combined Template Method ---
	'playlist_template_combined' => array(
		'label'   => __( 'Combined Method', 'radio-station' ),
		'type'    => 'checkbox',
		'value'   => 'yes',
		'default' => '',
		'helper'  => __( 'Advanced usage. Use both a custom template AND content filtering for a Playlist. (Not compatible with Legacy templates.)', 'radio-station' ),
		'tab'     => 'templates',
		'section' => 'single',
	),

	// === Widgets ===

	// --- AJAX Loading ---
	'ajax_widgets' => array(
		'type'    => 'checkbox',
		'label'   => __( 'AJAX Load Widgets?', 'radio-station' ),
		'default' => 'yes',
		'value'   => '',
		'helper'  => __( 'Defaults plugin widgets to AJAX loading. Can also be set on individual widgets.', 'radio-station' ),
		'tab'     => 'widgets',
		'section' => 'loading',
	),

	// --- Dynamic Reloading ---
	'dynamic_reload' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Dynamic Reloading?', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Automatically reload all plugin widgets on change of current Show. Can also be set on individual widgets.', 'radio-station' ),
		'tab'     => 'widgets',
		'section' => 'loading',
		'pro'     => true,
	),


	// === Roles / Capabilities / Permissions  ===
	// 2.3.0: added new capability and role options

	// --- Show Editor Role Note ---
	'show_editor_role_note'      => array(
		'type'    => 'note',
		'label'   => __( 'Show Editor Role', 'radio-station' ),
		'helper'  => __( 'Since 2.3.0, a new Show Editor role has been added with Publish and Edit capabilities for all Radio Station Post Types.', 'radio-station' )
		             . ' ' . __( 'You can assign this Role to any user to give them full Station Schedule updating permissions.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// --- Author Role Capabilities ---
	'add_author_capabilities' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Add to Author Capabilities', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Allow users with WordPress Author role to publish and edit their own Shows and Playlists.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// --- Editor Role Capabilities ---
	'add_editor_capabilities' => array(
		'type'    => 'checkbox',
		'label'   => __( 'Add to Editor Capabilities', 'radio-station' ),
		'default' => 'yes',
		'value'   => 'yes',
		'helper'  => __( 'Allow users with WordPress Editor role to edit all Radio Station post types.', 'radio-station' ),
		'tab'     => 'roles',
		'section' => 'permissions',
	),

	// ? --- Disallow Shift Changes --- ?
	// 'disallow_shift_changes' => array(
	// 	'type'    => 'checkbox',
	// 	'label'   => __( 'Disallow Shift Changes', 'radio-station' ),
	// 	'default' => array(),
	// 	'options' => array(
	// 		'authors'   => __( 'WordPress Authors', 'radio-station' ),
	// 		'editors'   => __( 'WorddPress Editors', 'radio-station' ),
	// 		'hosts'     => __( 'Assigned DJs / Hosts', 'radio-station' ),
	// 		'producers' => __( 'Assigned Producers', 'radio-station' ),
	// 	),
	// 	'helper'  => __( 'Prevents users of these Roles changing Show Shift times.', 'radio-station' ),
	// 	'tab'     => 'roles',
	// 	'section' => 'permissions',
	// 	'pro'     => true,
	// ),

	// === Tabs and Sections ===

	// --- Tab Labels ---
	// 2.3.2: add widget options tab
	'tabs'                    => array(
		'general'   => __( 'General', 'radio-station' ),
		'pages'     => __( 'Pages', 'radio-station' ),
		'templates' => __( 'Templates', 'radio-station' ),
		'widgets'   => __( 'Widgets', 'radio-station' ),
		'roles'     => __( 'Roles', 'radio-station' ),
	),

	// --- Section Labels ---
	// 2.3.2: add widget loading section
	'sections'                => array(
		'station'     => __( 'Station', 'radio-station' ),
		'broadcast'   => __( 'Broadcast', 'radio-station' ),
		'times'       => __( 'Times', 'radio-station' ),
		'feeds'       => __( 'Feeds', 'radio-station' ),
		'single'      => __( 'Single Templates', 'radio-station' ),
		'archive'     => __( 'Archive Templates', 'radio-station' ),
		'schedule'    => __( 'Schedule Page', 'radio-station' ),
		'show'        => __( 'Show Pages', 'radio-station' ),
		'archives'    => __( 'Archives', 'radio-station' ),
		'loading'     => __( 'Widget Loading', 'radio-station' ),
		'permissions' => __( 'Permissions', 'radio-station' ),
	),
);

// ----------------------
// Plugin Loader Settings
// ----------------------
// 2.3.0: added plugin loader settings
$slug = 'radio-station';
$settings = array(
	// --- Plugin Info ---
	'slug'         => $slug,
	'file'         => __FILE__,
	'version'      => '0.0.1',

	// --- Menus and Links ---
	'title'        => 'Radio Station',
	'parentmenu'   => 'radio-station',
	'home'         => RADIO_STATION_HOME_URL,
	'docs'		   => RADIO_STATION_DOCS_URL,
	'support'      => 'https://github.com/netmix/radio-station/issues/',
	'ratetext'     => __( 'Rate on WordPress.org', 'radio-station' ),
	'share'        => RADIO_STATION_HOME_URL . '?share',
	'sharetext'    => __( 'Share the Plugin Love', 'radio-station' ),
	'donate'       => 'https://patreon.com/radiostation',
	'donatetext'   => __( 'Support this Plugin', 'radio-station' ),
	'readme'       => false,
	'settingsmenu' => false,

	// --- Options ---
	'namespace'    => 'radio_station',
	'settings'     => 'rs',
	'option'       => 'radio_station',
	'options'      => $options,

	// --- WordPress.Org ---
	'wporgslug'    => 'radio-station',
	'wporg'        => true,
	'textdomain'   => 'radio-station',

	// --- Freemius ---
	'freemius_id'  => '4526',
	'freemius_key' => 'pk_aaf375c4fb42e0b5b3831e0b8476b',
	'hasplans'     => false,
	'hasaddons'    => false,
	'plan'         => 'free',
);

// -------------------------
// Set Plugin Option Globals
// -------------------------
global $radio_station_data;
$radio_station_data['options'] = $options;
$radio_station_data['settings'] = $settings;

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
require RADIO_STATION_DIR . '/loader.php';
$instance = new radio_station_loader( $settings );

// --------------------------
// Include Plugin Admin Files
// --------------------------
// 2.2.7: added conditional load of admin includes
// 2.2.7: moved all admin functions to radio-station-admin.php
if ( is_admin() ) {
	require RADIO_STATION_DIR . '/radio-station-admin.php';
	require RADIO_STATION_DIR . '/includes/post-types-admin.php';

	// --- Contextual Help ---
	// 2.3.0: maybe load contextual help config
	if ( file_exists( RADIO_STATION_DIR . '/help/contextual-help-config.php' ) ) {
		include RADIO_STATION_DIR . '/help/contextual-help-config.php';
	}
}

// -----------------------
// Load Plugin Text Domain
// -----------------------
add_action( 'plugins_loaded', 'radio_station_init' );
function radio_station_init() {
	// 2.3.0: use RADIO_STATION_DIR constant
	load_plugin_textdomain( 'radio-station', false, RADIO_STATION_DIR . '/languages' );
}

// --------------------
// Check Plugin Version
// --------------------
// 2.3.0: check plugin version for updates and announcements
add_action( 'init', 'radio_station_check_version', 9 );
function radio_station_check_version() {

	// --- get current and stored versions ---
	// 2.3.2: use plugin version function
	$version = radio_station_plugin_version();
	$stored_version = get_option( 'radio_station_version', false );

	// --- check current against stored version ---
	if ( !$stored_version ) {

		// --- no stored plugin version, add it now ---
		update_option( 'radio_station_version', $version );

		if ( version_compare( $version, '2.3.0', '>=' ) ) {
			// --- flush rewrite rules (for new post type and rest route rewrites) ---
			// (handled separately as 2.3.0 is first version with version checking)
			add_option( 'radio_station_flush_rewrite_rules', true );
		}

	} elseif ( version_compare( $version, $stored_version, '>' ) ) {

		// --- updates from before to after x.x.x ---
		// (code template if/when needed for future release updates)
		// if ( ( version_compare( $version, 'x.x.x', '>=' ) )
		//   && ( version_compare( $stored_version, 'x.x.x', '<' ) ) ) {
		//		// eg. trigger a single thing to do
		//		add_option( 'radio_station_do_thing_once', true );
		// }

		// --- bump stored version to current version ---
		update_option( 'radio_station_previous_version', $stored_version );
		update_option( 'radio_station_version', $version );
	}
}

// -------------------
// Flush Rewrite Rules
// -------------------
// (on plugin activation / deactivation)
// 2.2.3: added this for custom post types rewrite flushing
// 2.2.8: fix for mismatched flag function name
register_activation_hook( __FILE__, 'radio_station_flush_rewrite_flag' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
function radio_station_flush_rewrite_flag() {
	add_option( 'radio_station_flush_rewrite_rules', true );
}

// ----------------------
// Enqueue Plugin Scripts
// ----------------------
// 2.3.0: added for enqueueing main Radio Station script
add_action( 'wp_enqueue_scripts', 'radio_station_enqueue_scripts' );
function radio_station_enqueue_scripts() {

	// --- enqueue custom stylesheet if found ---
	// 2.3.0: added for automatic custom style loading
	radio_station_enqueue_style( 'custom' );

	// --- enqueue plugin script ---
	// 2.3.0: added jquery dependency for inline script fragments
	radio_station_enqueue_script( 'radio-station', array( 'jquery' ), true );

	// -- enqueue javascript timezone script ---
	// $suffix = '.min';
	// if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
	// 	$suffix = '';
	// }
	// $jstz_url = plugins_url( 'js/jstz' . $suffix . '.js', RADIO_STATION_FILE );
	// wp_enqueue_script( 'jstz', $jstz_url, array(), '1.0.6', false );
}

// ---------------------
// Enqueue Plugin Script
// ---------------------
function radio_station_enqueue_script( $scriptkey, $deps = array(), $infooter = false ) {

	// --- set stylesheet filename and child theme path ---
	$filename = $scriptkey . '.js';

	// 2.3.0: check template hierarchy for file
	$template = radio_station_get_template( 'both', $filename, 'js' );
	if ( $template ) {

		// 2.3.2: use plugin version for releases
		$plugin_version = radio_station_plugin_version();
		if ( 5 == strlen( $plugin_version ) ) {
			$version = $plugin_version;
		} else {
			$version = filemtime( $template['file'] );
		}

		$url = $template['url'];

		// --- enqueue script ---
		wp_enqueue_script( $scriptkey, $url, $deps, $version, $infooter );
	}
}

// -------------------------
// Enqueue Plugin Stylesheet
// -------------------------
// ?.?.?: widgets.css style conditional enqueueing moved to within widget classes
// 2.3.0: added abstracted method for enqueueing plugin stylesheets
// 2.3.0: moved master schedule style enqueueing to conditional in master-schedule.php
function radio_station_enqueue_style( $stylekey ) {

	// --- check style enqueued switch ---
	global $radio_station_styles;
	if ( !isset( $radio_station_styles ) ) {
		$radio_station_styles = array();
	}
	if ( !isset( $radio_station_styles[$stylekey] ) ) {

		// --- set stylesheet filename and child theme path ---
		$filename = 'rs-' . $stylekey . '.css';

		// 2.3.0: check template hierarchy for file
		$template = radio_station_get_template( 'both', $filename, 'css' );
		if ( $template ) {

			// --- use found template values ---
			// 2.3.2: use plugin version for releases
			$plugin_version = radio_station_plugin_version();
			if ( 5 == strlen( $plugin_version ) ) {
				$version = $plugin_version;
			} else {
				$version = filemtime( $template['file'] );
			}
			$url = $template['url'];

			// --- enqueue styles in footer ---
			wp_enqueue_style( 'rs-' . $stylekey, $url, array(), $version, 'all' );

			// --- set style enqueued switch ---
			$radio_station_styles[$stylekey] = true;
		}
	}
}

// ---------------------
// Localize Time Strings
// ---------------------
add_action( 'wp_enqueue_scripts', 'radio_station_localize_script' );
function radio_station_localize_script() {

	// --- create settings objects ---
	$js = "var radio = {}; radio.timezone = {}; radio.time = {}; radio.labels = {}; radio.units = {};";

	// --- set AJAX URL ---
	// 2.3.2: add admin AJAX URL
	$js .= "radio.ajax_url = '" . esc_url( admin_url( 'admin-ajax.php' ) ) . "';" . PHP_EOL;

	// --- clock time format ---
	$clock_format = radio_station_get_setting( 'clock_time_format' );
	$js .= "radio.clock_format = '" . esc_js( $clock_format ) . "';" . PHP_EOL;

	// --- detect touchscreens ---
	// ref: https://stackoverflow.com/a/52855084/5240159
	$js .= "if (window.matchMedia('(pointer: coarse)').matches) {radio.touchscreen = true;} else {radio.touchscreen = false;}" . PHP_EOL;

	// --- set debug flag ---
	if ( defined( 'RADIO_STATION_DEBUG' ) && RADIO_STATION_DEBUG ) {
		$js .= "radio.debug = true;" . PHP_EOL;
	} else {
		$js .= "radio.debug = false;" . PHP_EOL;
	}

	// --- radio timezone ---
	// 2.3.2: added get timezone function
	$timezone = radio_station_get_timezone();

	// if ( isset( $offset ) ) {
	if ( stristr( $timezone, 'UTC' ) ) {

		if ( 'UTC' == $timezone ) {
			$offset = '0';
		} else {
			$offset = str_replace( 'UTC', '', $timezone );
		}
		$js .= "radio.timezone.offset = " . esc_js( $offset * 60 * 60 ) . "; ";
		if ( '0' == $offset ) {
			$offset = '';
		} elseif ( $offset > 0 ) {
			$offset = '+' . $offset;
		}
		$js .= "radio.timezone.code = 'UTC" . esc_js( $offset ) . "'; ";
		$js .= "radio.timezone.utc = '" . esc_js( $offset ) . "'; ";
		$js .= "radio.timezone.utczone = true; ";

	} else {

		// --- get offset and code from timezone location ---
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		$offset_hours = $offset / ( 60 * 60 );
		if ( 0 == $offset ) {
			$utc_offset = '';
		} elseif ( $offset > 0 ) {
			$utc_offset = '+' . $offset_hours;
		} else {
			$utc_offset = $offset_hours;
		}
		$utc_offset = 'UTC' . $utc_offset;
		$code = radio_station_get_timezone_code( $timezone );
		$js .= "radio.timezone.location = '" . esc_js( $timezone ) . "'; ";
		$js .= "radio.timezone.offset = " . esc_js( $offset ) . "; ";
		$js .= "radio.timezone.code = '" . esc_js( $code ) . "'; ";
		$js .= "radio.timezone.utc = '" . esc_js( $utc_offset ) . "'; ";
		$js .= "radio.timezone.utczone = false; ";

	}

	if ( defined( 'RADIO_STATION_USE_SERVER_TIMES' ) && RADIO_STATION_USE_SERVER_TIMES ) {
		$js .= "radio.timezone.adjusted = false; ";
	} else {
		$js .= "radio.timezone.adjusted = true; ";
	}

	// --- set user timezone offset ---
	// (and convert offset minutes to seconds)
	$js .= "radio.timezone.useroffset = (new Date()).getTimezoneOffset() * 60;" . PHP_EOL;

	// --- translated months array ---
	// 2.3.2: also translate short month labels
	$js .= "radio.labels.months = new Array(";
	$short = "radio.labels.smonths = new Array(";
	$months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
	foreach ( $months as $i => $month ) {
		$month = radio_station_translate_month( $month );
		$short_month = radio_station_translate_month( $month, true );
		$month = str_replace( "'", "", $month );
		$short_month = str_replace( "'", "", $short_month );
		$js .= "'" . esc_js( $month ) . "'";
		$short .= "'" . esc_js( $short_month ) . "'";
		if ( $i < ( count( $months ) - 1 ) ) {
			$js .= ", ";
			$short .= ", ";
		}
	}
	$js .= ");" . PHP_EOL;
	$js .= $short . ");" . PHP_EOL;

	// --- translated days array ---
	// 2.3.2: also translate short day labels
	$js .= "radio.labels.days = new Array(";
	$short = "radio.labels.sdays = new Array(";
	$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
	foreach ( $days as $i => $day ) {
		$day = radio_station_translate_weekday( $day );
		$short_day = radio_station_translate_weekday( $day, true );
		$day = str_replace( "'", "", $day );
		$short_day = str_replace( "'", "", $short_day );
		$js .= "'" . esc_js( $day ) . "'";
		$short .= "'" . esc_js( $short_day ) . "'";
		if ( $i < ( count( $days ) - 1 ) ) {
			$js .= ", ";
			$short .= ", ";
		}
	}
	$js .= ");" . PHP_EOL;
	$js .= $short . ");" . PHP_EOL;

	// --- translated time unit strings ---
	// TODO: convert units to single object
	$js .= "radio.units.am = '" . esc_js( radio_station_translate_meridiem( 'am' ) ) . "'; ";
	$js .= "radio.units.pm = '" . esc_js( radio_station_translate_meridiem( 'pm' ) ) . "'; ";
	$js .= "radio.units.second = '" . esc_js( __( 'Second', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.seconds = '" . esc_js( __( 'Seconds', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.minute = '" . esc_js( __( 'Minute', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.minutes = '" . esc_js( __( 'Minutes', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.hour = '" . esc_js( __( 'Hour', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.hours = '" . esc_js( __( 'Hours', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.day = '" . esc_js( __( 'Day', 'radio-station' ) ) . "'; ";
	$js .= "radio.units.days = '" . esc_js( __( 'Days', 'radio-station' ) ) . "'; ";

	// --- add inline script ---
	wp_add_inline_script( 'radio-station', $js );

}

// -----------------------------------------
// Fix to Redirect Plugin Settings Menu Link
// -----------------------------------------
// 2.3.2: added settings submenu page redirection fix
add_action( 'init', 'radio_station_settings_page_redirect' );
function radio_station_settings_page_redirect() {

	// --- bug out if not admin page ---
	if ( !is_admin() ) {
		return;
	}

	// --- but out if not plugin settings page ---
	if ( !isset( $_REQUEST['page'] ) || ( 'radio-station' != $_REQUEST['page'] ) ) {
		return;
	}

	// --- check if link is for options-general.php ---
	if ( strstr( $_SERVER['REQUEST_URI'], '/options-general.php' ) ) {

		// --- redirect to plugin settings page (admin.php) ---
		$url = add_query_arg( 'page', 'radio-station', admin_url( 'admin.php' ) );
		wp_redirect( $url );
		exit;
	}
}


// -----------------
// === Templates ===
// -----------------

// ------------
// Get Template
// ------------
// 2.3.0: added for template file hierarchy
function radio_station_get_template( $type, $template, $paths = false ) {

	global $radio_station_data;

	// --- maybe set default paths ---
	if ( !$paths ) {
		if ( isset( $radio_station_data['template-dirs'] ) ) {
			$dirs = $radio_station_data['template-dirs'];
		}
		$paths = array( 'templates', '' );
	} elseif ( is_string( $paths ) ) {
		if ( 'css' == $paths ) {
			if ( isset( $radio_station_data['style-dirs'] ) ) {
				$dirs = $radio_station_data['style-dirs'];
			}
			$paths = array( 'css', 'styles' );
		} elseif ( 'js' == $paths ) {
			if ( isset( $radio_station_data['script-dirs'] ) ) {
				$dirs = $radio_station_data['script-dirs'];
			}
			$paths = array( 'js', 'scripts' );
		}
	}

	if ( !isset( $dirs ) ) {
		$dirs = array();
		$styledir = get_stylesheet_directory();
		$styledirurl = get_stylesheet_directory_uri();
		$templatedir = get_template_directory();
		$templatedirurl = get_template_directory_uri();

		// --- maybe generate default hierarchies ---
		foreach ( $paths as $path ) {
			$dirs[] = array(
				'path'    => $styledir . '/' . $path,
				'urlpath' => $styledirurl . '/' . $path,
			);
		}
		if ( $styledir != $templatedir ) {
			foreach ( $paths as $path ) {
				$dirs[] = array(
					'path'    => $templatedir . '/' . $path,
					'urlpath' => $templatedirurl . '/' . $path,
				);
			}
		}
		if ( defined( 'RADIO_STATION_PRO_DIR' ) ) {
			foreach ( $paths as $path ) {
				$dirs[] = array(
					'path'    => RADIO_STATION_PRO_DIR . '/' . $path,
					'urlpath' => plugins_url( $path, RADIO_STATION_PRO_FILE ),
				);
			}
		}
		foreach ( $paths as $path ) {
			$dirs[] = array(
				'path'    => RADIO_STATION_DIR . '/' . $path,
				'urlpath' => plugins_url( $path, RADIO_STATION_FILE ),
			);
		}
	}
	$dirs = apply_filters( 'radio_station_template_dir_hierarchy', $dirs, $template, $paths );

	// --- loop directory hierarchy to find first template ---
	foreach ( $dirs as $dir ) {

		$template_path = $dir['path'] . '/' . $template;
		$template_url = $dir['urlpath'] . '/' . $template;

		if ( file_exists( $template_path ) ) {
			if ( 'file' == (string) $type ) {
				return $template_path;
			} elseif ( 'url' === (string) $type ) {
				return $template_url;
			} else {
				return array( 'file' => $template_path, 'url' => $template_url );
			}
		}
	}

	return false;
}

// ------------------------------
// Automatic Pages Content Filter
// ------------------------------
// 2.3.0: standalone filter for automatic page content
// 2.3.1: re-add filter so the_content can be processed multuple times
add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
function radio_station_automatic_pages_content( $content ) {

	// global $radio_station_data;
	// if ( isset( $radio_station_data['doing_excerpt'] ) && $radio_station_data['doing_excerpt'] ) {
	//	return $content;
	// }

	// --- for automatic output on selected master schedule page ---
	$schedule_page = radio_station_get_setting( 'schedule_page' );
	if ( !is_null( $schedule_page ) && !empty( $schedule_page ) ) {
		if ( is_page( $schedule_page ) ) {
			$automatic = radio_station_get_setting( 'schedule_auto' );
			if ( 'yes' === (string) $automatic ) {
				$view = radio_station_get_setting( 'schedule_view' );
				$atts = array( 'view' => $view );
				$atts = apply_filters( 'radio_station_automatic_schedule_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[master-schedule' . $atts_string . ']';
				remove_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				$content = do_shortcode( $shortcode );
				// 2.3.1: re-add filter so the_content may be processed multuple times
				add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				return $content;
			}
		}
	}

	// --- show archive page ---
	// 2.3.0: added automatic display of show archive page
	$show_archive_page = radio_station_get_setting( 'show_archive_page' );
	if ( !is_null( $show_archive_page ) && !empty( $show_archive_page ) ) {
		if ( is_page( $show_archive_page ) ) {
			$automatic = radio_station_get_setting( 'show_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				$view = radio_station_get_setting( 'show_archive_view' );
				if ( $view ) {$atts['view'] = $view;}
				$atts = apply_filters( 'radio_station_automatic_show_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[shows-archive' . $atts_string . ']';
				remove_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				$content = do_shortcode( $shortcode );
				// 2.3.1: re-add filter so the_content may be processed multuple times
				add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				return $content;
			}
		}
	}

	// --- override archive page ---
	// 2.3.0: added automatic display of override archive page
	$override_archive_page = radio_station_get_setting( 'override_archive_page' );
	if ( !is_null( $override_archive_page ) && !empty( $override_archive_page ) ) {
		if ( is_page( $override_archive_page ) ) {
			$automatic = radio_station_get_setting( 'override_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				$view = radio_station_get_setting( 'override_archive_view' );
				if ( $view ) {$atts['view'] = $view;}
				$atts = apply_filters( 'radio_station_automatic_override_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[overrides-archive' . $atts_string . ']';
				remove_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				$content = do_shortcode( $shortcode );
				// 2.3.1: re-add filter so the_content may be processed multuple times
				add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				return $content;
			}
		}
	}

	// --- playlist archive page ---
	// 2.3.0: added automatic display of playlist archive page
	$playlist_archive_page = radio_station_get_setting( 'playlist_archive_page' );
	if ( !is_null( $playlist_archive_page ) && !empty( $playlist_archive_page ) ) {
		if ( is_page( $playlist_archive_page ) ) {
			$automatic = radio_station_get_setting( 'playlist_archive_auto' );
			if ( 'yes' == $automatic ) {
				$atts = array();
				$view = radio_station_get_setting( 'playlist_archive_view' );
				if ( $view ) {$atts['view'] = $view;}
				$atts = apply_filters( 'radio_station_automatic_playlist_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[playlists-archive' . $atts_string . ']';
				remove_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				$content = do_shortcode( $shortcode );
				// 2.3.1: re-add filter so the_content may be processed multuple times
				add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				return $content;
			}
		}
	}

	// --- genre archive page ---
	// 2.3.0: added automatic display of genre archive page
	$genre_archive_page = radio_station_get_setting( 'genre_archive_page' );
	if ( !is_null( $genre_archive_page ) && !empty( $genre_archive_page ) ) {
		if ( is_page( $genre_archive_page ) ) {
			$automatic = radio_station_get_setting( 'genre_archive_auto' );
			if ( 'yes' === (string) $automatic ) {
				$atts = array();
				$view = radio_station_get_setting( 'genre_archive_view' );
				if ( $view ) {$atts['view'] = $view;}
				$atts = apply_filters( 'radio_station_automatic_genre_archive_atts', $atts );
				$atts_string = '';
				if ( is_array( $atts ) && ( count( $atts ) > 0 ) ) {
					foreach ( $atts as $key => $value ) {
						$atts_string = ' ' . $key . '="' . $value . '"';
					}
				}
				$shortcode = '[genres-archive' . $atts_string. ']';
				remove_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				$content = do_shortcode( $shortcode );
				// 2.3.1: re-add filter so the_content may be processed multuple times
				add_filter( 'the_content', 'radio_station_automatic_pages_content', 11 );
				return $content;
			}
		}
	}

	return $content;
}

// ------------------------------
// Single Content Template Filter
// ------------------------------
// 2.3.0: moved here and abstracted from templates/single-show.php
// 2.3.0: standalone filter name to allow for replacement
function radio_station_single_content_template( $content, $post_type ) {

	// --- check if single plugin post type ---
	if ( !is_singular( $post_type ) ) {
		return $content;
	}

	// --- check for user content templates ---
	$theme_dir = get_stylesheet_directory();
	$templates = array(
		$theme_dir . '/templates/single-' . $post_type . '-content.php',
		$theme_dir . '/single-' . $post_type . '-content.php',
		RADIO_STATION_DIR . '/templates/single-' . $post_type . '-content.php',
	);
	// 2.3.0: fallback to show content template for overrides
	if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
		$templates[] = $theme_dir . '/templates/single-' . RADIO_STATION_SHOW_SLUG . '-content.php';
		$templates[] = $theme_dir . '/single-' . RADIO_STATION_SHOW_SLUG . '-content.php';
		$templates[] = RADIO_STATION_DIR . '/templates/single-' . RADIO_STATION_SHOW_SLUG . '-content.php';
	}
	$templates = apply_filters( 'radio_station_' . $post_type . '_content_templates', $templates, $post_type );
	foreach ( $templates as $template ) {
		if ( file_exists( $template ) ) {
			$content_template = $template;
			break;
		}
	}
	if ( !isset( $content_template ) ) {
		return $content;
	}

	// --- enqueue template styles ---
	radio_station_enqueue_style( 'templates' );

	// --- enqueue dashicons for frontend ---
	wp_enqueue_style( 'dashicons' );

	// --- start buffer and include content template ---
	ob_start();
	include $content_template;
	$output = ob_get_contents();
	ob_end_clean();

	// --- filter and return buffered content ---
	$output = str_replace( '<!-- the_content -->', $content, $output );
	$output = apply_filters( 'radio_station_content_' . $post_type, $output, get_the_ID() );

	return $output;
}

// ----------------------------
// Show Content Template Filter
// ----------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_show_content_template', 11 );
function radio_station_show_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_show_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_SHOW_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_show_content_template', 11 );
	return $output;
}

// --------------------------------
// Playlist Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
function radio_station_playlist_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_PLAYLIST_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_playlist_content_template', 11 );
	return $output;
}

// --------------------------------
// Override Content Template Filter
// --------------------------------
// 2.3.0: standalone filter name to allow for replacement
add_filter( 'the_content', 'radio_station_override_content_template', 11 );
function radio_station_override_content_template( $content ) {
	remove_filter( 'the_content', 'radio_station_override_content_template', 11 );
	$output = radio_station_single_content_template( $content, RADIO_STATION_OVERRIDE_SLUG );
	// 2.3.1: re-add filter so the_content can be processed multuple times
	add_filter( 'the_content', 'radio_station_override_content_template', 11 );
	return $output;
}

// ---------------------------------
// DJ / Host / Producer Template Fix
// ---------------------------------
// 2.2.8: temporary fix to not 404 author pages for DJs without blog posts
// Ref: https://wordpress.org/plugins/show-authors-without-posts/
add_filter( '404_template', 'radio_station_author_host_pages' );
function radio_station_author_host_pages( $template ) {

	global $wp_query;
	if ( !is_author() ) {

		if ( get_query_var( 'host' ) ) {

			// --- get user by ID or name ---
			$host = get_query_var( 'host' );
			if ( absint( $host ) > - 1 ) {
				$user = get_user_by( 'ID', $host );
			} else {
				$user = get_user_by( 'slug', $host );
			}

			// --- check if specified user has DJ/host role ---
			if ( $user && in_array( 'dj', $user->roles ) ) {
				$host_template = radio_station_get_host_template();
				if ( $host_template ) {
					$template = $host_template;
				}
			}

		} elseif ( get_query_var( 'producer' ) ) {

			// --- get user by ID or name ---
			$producer = get_query_var( 'producer' );
			if ( absint( $producer ) > - 1 ) {
				$user = get_user_by( 'ID', $producer );
			} else {
				$user = get_user_by( 'slug', $producer );
			}

			// --- check if specified user has producer role ---
			if ( $user && in_array( 'producer', $user->roles ) ) {
				$producer_template = radio_station_get_producer_template();
				if ( $producer_template ) {
					$template = $producer_template;
				}
			}

		} elseif ( get_query_var( 'author' ) && ( 0 == $wp_query->posts->post ) ) {

			// --- get the author user ---
			if ( get_query_var( 'author_name' ) ) {
				$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
			} else {
				$author = get_userdata( get_query_var( 'author' ) );
			}

			if ( $author ) {

				// --- check if author has DJ, producer or administrator role ---
				if ( in_array( 'dj', $author->roles )
				     || in_array( 'producer', $author->roles )
				     || in_array( 'administrator', $author->roles ) ) {

					// TODO: maybe check if user is assigned to any shows ?
					$template = get_author_template();
				}
			}

		}

	}

	return $template;
}

// ----------------------
// Get DJ / Host Template
// ----------------------
// 2.3.0: added get DJ template function
// (modified template hierarchy from get_page_template)
function radio_station_get_host_template() {

	$templates = array();
	$hostname = get_query_var( 'host' );
	if ( $hostname ) {
		$hostname_decoded = urldecode( $hostname );
		if ( $hostname_decoded !== $hostname ) {
			$templates[] = 'host-' . $hostname_decoded . '.php';
		}
		$templates[] = 'host-' . $hostname . '.php';
	}
	$templates[] = 'single-host.php';

	$templates = apply_filters( 'radio_station_host_templates', $templates );

	return get_query_template( RADIO_STATION_HOST_SLUG, $templates );
}

// ---------------------
// Get Producer Template
// ---------------------
// 2.3.0: added get producer template function
// (modified template hierarchy from get_page_template)
function radio_station_get_producer_template() {

	$templates = array();
	$producername = get_query_var( 'producer' );
	if ( $producername ) {
		$producername_decoded = urldecode( $producername );
		if ( $producername_decoded !== $producername ) {
			$templates[] = 'producer-' . $producername_decoded . '.php';
		}
		$templates[] = 'producer-' . $producername . '.php';
	}
	$templates[] = 'single-producer.php';

	$templates = apply_filters( 'radio_station_producer_templates', $templates );

	return get_query_template( RADIO_STATION_PRODUCER_SLUG, $templates );
}

// -------------------------
// Single Template Hierarchy
// -------------------------
function radio_station_single_template_hierarchy( $templates ) {

	global $post;

	// --- remove single.php as the show / playlist fallback ---
	// (allows for user selection of page.php or single.php later)
	if ( ( RADIO_STATION_SHOW_SLUG === (string) $post->post_type )
		 || ( RADIO_STATION_OVERRIDE_SLUG === (string) $post->post_type )
	     || ( RADIO_STATION_PLAYLIST_SLUG === (string) $post->post_type ) ) {
		$i = array_search( 'single.php', $templates );
		if ( false !== $i ) {
			unset( $templates[$i] );
		}
	}

	return $templates;
}

// -----------------------
// Single Templates Loader
// -----------------------
add_filter( 'single_template', 'radio_station_load_template', 10, 3 );
function radio_station_load_template( $single_template, $type, $templates ) {

	global $post;

	// --- handle single templates ---
	$post_type = $post->post_type;
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_PLAYLIST_SLUG );
	// TODO: RADIO_STATION_EPISODE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG
	if ( in_array( $post_type, $post_types ) ) {

		// --- check for existing template override ---
		// note: single.php is removed from template hierarchy via filter
		remove_filter( 'single_template', 'radio_station_load_template' );
		add_filter( 'single_template_hierarchy', 'radio_station_single_template_hierarchy' );
		$template = get_single_template();
		remove_filter( 'single_template_hierarchy', 'radio_station_single_template_hierarchy' );

		// --- use legacy template ---
		if ( $template ) {

			// --- use the found user template ---
			$single_template = $template;

			// --- check for combined template and content filter ---
			$combined = radio_station_get_setting( $post_type . '_template_combined' );
			if ( 'yes' != $combined ) {
				remove_filter( 'the_content', 'radio_station_' . $post_type . '_content_template', 11 );
			}

		} else {

			// --- get template selection ---
			// 2.3.0: removed default usage of single show/playlist templates (not theme agnostic)
			// 2.3.0: added option for use of template hierarchy
			$show_template = radio_station_get_setting( $post_type . '_template' );

			// --- maybe use legacy template ---
			if ( 'legacy' === (string) $show_template ) {
				return RADIO_STATION_DIR . '/templates/legacy/single-' . $post_type . '.php';
			}

			// --- use post or page template ---
			if ( 'post' == $show_template ) {
				$templates = array( 'single.php' );
			} elseif ( 'page' == $show_template ) {
				$templates = array( 'page.php' );
			}

			// --- add standard fallbacks to singular and index ---
			$templates[] = 'singular.php';
			$templates[] = 'index.php';
			$single_template = get_query_template( $post_type, $templates );
		}
	}

	return $single_template;
}

// --------------------------
// Archive Template Hierarchy
// --------------------------
add_filter( 'archive_template_hierarchy', 'radio_station_archive_template_hierarchy' );
function radio_station_archive_template_hierarchy( $templates ) {

	// --- add extra template search path of /templates/ ---
	$post_types = array_filter( (array) get_query_var( 'post_type' ) );
	if ( count( $post_types ) == 1 ) {
		$post_type = reset( $post_types );
		$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_OVERRIDE_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
		if ( in_array( $post_type, $post_types ) ) {
			$template = array( 'templates/archive-' . $post_type . '.php' );
			// 2.3.0: add fallback to show archive template for overrides
			if ( RADIO_STATION_OVERRIDE_SLUG == $post_type ) {
				$template[] = 'templates/archive-' . RADIO_STATION_SHOW_SLUG . '.php';
			}
			$templates = array_merge( $template, $templates );
		}
	}

	return $templates;
}

// ------------------------
// Archive Templates Loader
// ------------------------
// TODO: implement standard archive page overrides via plugin settings
// add_filter( 'archive_template', 'radio_station_post_type_archive_template', 10, 3 );
function radio_station_post_type_archive_template( $archive_template, $type, $templates ) {
	global $post;

	// --- check for archive template override ---
	$post_types = array( RADIO_STATION_SHOW_SLUG, RADIO_STATION_PLAYLIST_SLUG, RADIO_STATION_HOST_SLUG, RADIO_STATION_PRODUCER_SLUG );
	foreach ( $post_types as $post_type ) {
		if ( is_post_type_archive( $post_type ) ) {
			$override = radio_station_get_setting( $post_type . '_archive_override' );
			if ( 'yes' !== (string) $override ) {
				$archive_template = get_page_template();
				add_filter( 'the_content', 'radio_station_' . $post_type . '_archive', 11 );
			}
		}
	}

	return $archive_template;
}

// -------------------------
// Add Links to Back to Show
// -------------------------
// 2.3.0: add links to show from show posts and playlists
add_filter( 'the_content', 'radio_station_add_show_links', 20 );
function radio_station_add_show_links( $content ) {

	global $post;

	// note: playlists are linked via single-playlist-content.php template

	// --- filter to allow related post types ---
	$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );

	if ( in_array( $post->post_type, $post_types ) ) {

		// --- link show posts ---
		$related_show = get_post_meta( $post->ID, 'post_showblog_id', true );
		if ( $related_show ) {
			$positions = array( 'after' );
			$positions = apply_filters( 'radio_station_link_to_show_positions', $positions, $post->post_type );
			if ( $positions && is_array( $positions ) && ( count( $positions ) > 0 ) ) {
				if ( in_array( 'before', $positions ) || in_array( 'after', $positions ) ) {
					$before = $after = '';
					$show = get_post( $related_show );
					$post_type_object = get_post_type_object( $post->post_type );
					$singular = $post_type_object->labels->singular_name;
					$plural = $post_type_object->labels->name;
					$permalink = get_permalink( $show->ID );
					if ( in_array( 'before', $positions ) ) {
						$show_link_before = sprintf( __( '%s for Show', 'radio-station' ), $singular );
						$show_link_before .= ': <a href="' . esc_url( $permalink ) . '">' . $show->post_title . '</a>';
						$before = $show_link_before . '<br><br>';
						$before = apply_filters( 'radio_station_link_to_show_before', $before, $post, $show );
					}
					if ( in_array( 'after', $positions ) ) {
						$post_type_sections = array( 'post' );
						if ( defined( 'RADIO_STATION_EPISODE_SLUG' ) ) {
							$post_type_sections[] = RADIO_STATION_EPISODE_SLUG;
						}
						if ( in_array( $post->post_type, $post_type_sections ) ) {
							$post_type_ref = '#show-' . str_replace( 'rs-', '', $post->post_type ) . 's';
							$anchor = sprintf( __( 'All %s for Show %s', 'radio-station' ), $plural, $show->post_title );
							$show_link_after = '<a href="' . esc_url( $permalink ) . $post_type_ref . '">&larr; ' . $anchor . '</a>';
						}
						if ( isset( $show_link_after ) ) {
							$after = '<br>' . $show_link_after;
						}
						$after = apply_filters( 'radio_station_link_to_show_after', $after, $post, $show );
					}
					$content = $before . $content . $after;
				}
			}

			// --- adjacent post links debug test ---
			if ( RADIO_STATION_DEBUG ) {
				$content .= "Previous Link: " . get_previous_post_link() . "<br>";
				$content .= "Next Link: " . get_next_post_link() . "<br>";
			}
		}

	}

	return $content;
}

// -------------------------
// Show Posts Adjacent Links
// -------------------------
// 2.3.0: added show post adjacent links filter
add_filter( 'next_post_link', 'radio_station_get_show_post_link', 11, 5 );
add_filter( 'previous_post_link', 'radio_station_get_show_post_link', 11, 5 );
function radio_station_get_show_post_link( $output, $format, $link, $adjacent_post, $adjacent ) {

	global $post;

	// --- filter to allow disabling ---
	$link_show_posts = apply_filters( 'radio_station_link_show_posts', true, $post );
	if ( !$link_show_posts ) {
		return $output;
	}

	// --- filter to allow related post types ---
	$post_types = apply_filters( 'radio_station_show_related_post_types', array( 'post' ) );
	if ( !in_array( $post->post_type, $post_types ) ) {
		return $output;
	}

	// --- get related show ---
	$related_show = get_post_meta( $post->ID,  'post_showblog_id', true );
	if ( !$related_show ) {
		return $output;
	}

	// --- get adjacent post query ---
	$args = array(
		'post_type'	=> $post->post_type,
		'meta_query'	=> array(
			array(
				'key'		=> 'post_showblog_id',
				'value'		=> $related_show,
				'compare'	=> '=',
			),
		),
		'order_by'	=> 'post_date',
	);

	// --- setup previous or next post ---
	$post_type_object = get_post_type_object( $post->post_type );
	if ( 'previous' == $adjacent ) {
		$rel = 'prev';
		$args['order'] = 'DESC';
		$title = __( 'Previous Show', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name;
		$show_posts = get_posts( $args );
	} elseif ( 'next' == $adjacent ) {
		$rel = 'next';
		$args['order'] = 'ASC';
		$title = __( 'Next Show', 'radio-station' ) . ' ' . $post_type_object->labels->singular_name;
		$show_posts = get_posts( $args );
	}

	// --- loop posts to get adjacent post ---
	$found_current_post = $adjacent_post = false;
	if ( $show_posts && is_array( $show_posts ) && ( count( $show_posts ) > 0 ) ) {
		foreach ( $show_posts as $show_post ) {
			if ( $found_current_post ) {
				$related_id = get_post_meta( $show_post->ID, 'post_showblog_id', true );
				if ( $related_id == $related_show ) {
					$adjacent_post = $show_post;
					break;
				}
			}
			if ( $show_post->ID == $post->ID ) {
				$found_current_post = true;
			}
		}

		if ( $adjacent_post ) {

			// --- adjacent post title ---
			$post_title = $adjacent_post->post_title;
			if ( empty( $adjacent_post->post_title ) ) {
				$post_title = $title;
			}
			$post_title = apply_filters( 'the_title', $post_title, $adjacent_post->ID );

			// --- adjacent post link ---
			// (from get_adjacent_post_link)
			$date = mysql2date( get_option( 'date_format' ), $adjacent_post->post_date );
			$string = '<a href="' . esc_url( get_permalink( $adjacent_post ) ) . '" rel="' . esc_attr( $rel ) . '" title="' . $title . '">';
			$inlink = str_replace( '%title', $post_title, $link );
			$inlink = str_replace( '%date', $date, $inlink );
			$inlink = $string . $inlink . '</a>';
			$output = str_replace( '%link', $inlink, $format );

		}

	}

	return $output;
}


// =============
// Query Filters
// =============

// -----------------------------
// Playlist Archive Query Filter
// -----------------------------
// 2.3.0: added to replace old archive template meta query
add_filter( 'pre_get_posts', 'radio_station_show_playlist_query' );
function radio_station_show_playlist_query( $query ) {

	if ( RADIO_STATION_PLAYLIST_SLUG == $query->get( 'post_type' ) ) {

		// --- not needed if using legacy template ---
		$styledir = get_stylesheet_directory();
		if ( file_exists( $styledir . '/archive-playlist.php' )
		     || file_exists( $styledir . '/templates/archive-playlist.php' ) ) {
			return;
		}
		// 2.3.0: also check in parent theme directory
		$templatedir = get_template_directory();
		if ( $templatedir != $styledir ) {
			if ( file_exists( $templatedir . '/archive-playlist.php' )
			     || file_exists( $templatedir . '/templates/archive-playlist.php' ) ) {
				return;
			}
		}

		// --- check if show ID or slug is set --
		// TODO: maybe use get_query_var here ?
		if ( isset( $_GET['show_id'] ) ) {
			$show_id = absint( $_GET['show_id'] );
			if ( $show_id < 0 ) {
				unset( $show_id );
			}
		} elseif ( isset( $_GET['show'] ) ) {
			$show = sanitize_title( $_GET['show'] );
			global $wpdb;
			$show_query = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = '" . RADIO_STATION_SHOW_SLUG . "' AND post_name = %s";
			$show_query = $wpdb->prepare( $show_query, $show );
			$show_id = $wpdb->get_var( $show_query );
			if ( !$show_id ) {
				unset( $show_id );
			}
		}

		// --- maybe add the playlist meta query ---
		if ( isset( $show_id ) ) {
			$meta_query = array(
				'key'   => 'playlist_show_id',
				'value' => $show_id,
			);
			$query->set( $meta_query );
		}
	}
}


// ------------------
// === User Roles ===
// ------------------

// ----------------------------
// Set DJ Role and Capabilities
// ----------------------------
if ( is_multisite() ) {
	add_action( 'init', 'radio_station_set_roles', 10, 0 );
	// 2.3.1: added possible fix for roles not being set on multisite
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
} else {
	add_action( 'admin_init', 'radio_station_set_roles', 10, 0 );
}
function radio_station_set_roles() {

	global $wp_roles;

	// --- set only necessary capabilities for DJs ---
	$caps = array(
		'edit_shows'               => true,
		'edit_published_shows'     => true,
		'edit_others_shows'        => true,
		'read_shows'               => true,
		'edit_playlists'           => true,
		'edit_published_playlists' => true,
		// by default DJs cannot edit others playlists
		// 'edit_others_playlists'    => false,
		'read_playlists'           => true,
		'publish_playlists'        => true,
		'read'                     => true,
		'upload_files'             => true,
		'edit_posts'               => true,
		'edit_published_posts'     => true,
		'publish_posts'            => true,
		'delete_posts'             => true,
	);

	// --- add the DJ role ---
	// 2.3.0: translate DJ role name
	// 2.3.0: change label from 'DJ' to 'DJ / Host'
	// 2.3.0: check/add profile capabilities to hosts
	$wp_roles->add_role( 'dj', __( 'DJ / Host', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['dj']['capabilities'];
	// 2.3.1.1: added check if role caps is an array
	if ( !is_array( $role_caps ) ) {
		$role_caps = array();
	}
	$host_caps = array( 'edit_hosts', 'edit_published_hosts', 'delete_hosts', 'read_hosts', 'publish_hosts' );
	foreach ( $host_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
			$wp_roles->add_cap( 'dj', $cap, true );
		}
	}

	// --- add Show Producer role ---
	// 2.3.0: add equivalent capability role for Show Producer
	$wp_roles->add_role( 'producer', __( 'Show Producer', 'radio-station' ), $caps );
	$role_caps = $wp_roles->roles['producer']['capabilities'];
	// 2.3.1.1: added check if role caps is an array
	if ( !is_array( $role_caps ) ) {
		$role_caps = array();
	}
	$producer_caps = array( 'edit_producers', 'edit_published_producers', 'delete_producers', 'read_producers', 'publish_producers' );
	foreach ( $producer_caps as $cap ) {
		if ( !array_key_exists( $cap, $role_caps ) || !$role_caps[$cap] ) {
			$wp_roles->add_cap( 'producer', $cap, true );
		}
	}

	// --- grant all capabilities to Show Editors ---
	// 2.3.0: set Show Editor role capabilities
	$caps = array(
		'edit_shows'             => true,
		'edit_published_shows'   => true,
		'edit_others_shows'      => true,
		'edit_private_shows'     => true,
		'delete_shows'           => true,
		'delete_published_shows' => true,
		'delete_others_shows'    => true,
		'delete_private_shows'   => true,
		'read_shows'             => true,
		'publish_shows'          => true,

		'edit_playlists'             => true,
		'edit_published_playlists'   => true,
		'edit_others_playlists'      => true,
		'edit_private_playlists'     => true,
		'delete_playlists'           => true,
		'delete_published_playlists' => true,
		'delete_others_playlists'    => true,
		'delete_private_playlists'   => true,
		'read_playlists'             => true,
		'publish_playlists'          => true,

		'edit_overrides'             => true,
		'edit_overrides_playlists'   => true,
		'edit_others_overrides'      => true,
		'edit_private_overrides'     => true,
		'delete_overrides'           => true,
		'delete_published_overrides' => true,
		'delete_others_overrides'    => true,
		'delete_private_overrides'   => true,
		'read_overrides'             => true,
		'publish_overrides'          => true,

		'edit_hosts'           => true,
		'edit_published_hosts' => true,
		'edit_others_hosts'    => true,
		'delete_hosts'         => true,
		'read_hosts'           => true,
		'publish_hosts'        => true,

		'edit_producers'           => true,
		'edit_published_producers' => true,
		'edit_others_producers'    => true,
		'delete_producers'         => true,
		'read_producers'           => true,
		'publish_producers'        => true,

		'read'                 => true,
		'upload_files'         => true,
		'edit_posts'           => true,
		'edit_others_posts'    => true,
		'edit_published_posts' => true,
		'publish_posts'        => true,
		'delete_posts'         => true,
	);

	// --- add the Show Editor role ---
	// 2.3.0: added Show Editor role
	$wp_roles->add_role( 'show-editor', __( 'Show Editor', 'radio-station' ), $caps );

	// --- check plugin setting for authors ---
	if ( radio_station_get_setting( 'add_author_capabilities' ) == 'yes' ) {

		// --- grant show edit capabilities to author users ---
		$author_caps = $wp_roles->roles['author']['capabilities'];
		// 2.3.1.1: added check if role caps is an array
		if ( !is_array( $author_caps ) ) {
			$author_caps = array();
		}
		$extra_caps = array(
			'edit_shows',
			'edit_published_shows',
			'read_shows',
			'publish_shows',

			'edit_playlists',
			'edit_published_playlists',
			'read_playlists',
			'publish_playlists',

			'edit_overrides',
			'edit_published_overrides',
			'read_overrides',
			'publish_overrides',
		);
		foreach ( $extra_caps as $cap ) {
			if ( !array_key_exists( $cap, $author_caps ) || ( !$author_caps[$cap] ) ) {
				$wp_roles->add_cap( 'author', $cap, true );
			}
		}
	}

	// --- specify edit caps (for editors and admins) ---
	// 2.3.0: added show override, host and producer capabilities
	$edit_caps = array(
		'edit_shows',
		'edit_published_shows',
		'edit_others_shows',
		'edit_private_shows',
		'delete_shows',
		'delete_published_shows',
		'delete_others_shows',
		'delete_private_shows',
		'read_shows',
		'publish_shows',

		'edit_playlists',
		'edit_published_playlists',
		'edit_others_playlists',
		'edit_private_playlists',
		'delete_playlists',
		'delete_published_playlists',
		'delete_others_playlists',
		'delete_private_playlists',
		'read_playlists',
		'publish_playlists',

		'edit_overrides',
		'edit_published_overrides',
		'edit_others_overrides',
		'edit_private_overrides',
		'delete_overrides',
		'delete_published_overrides',
		'delete_others_overrides',
		'delete_private_overrides',
		'read_overrides',
		'publish_overrides',

		'edit_hosts',
		'edit_published_hosts',
		'edit_others_hosts',
		'delete_hosts',
		'delete_others_hosts',
		'read_hosts',
		'publish_hosts',

		'edit_producers',
		'edit_published_producers',
		'edit_others_producers',
		'delete_producers',
		'delete_others_producers',
		'read_producers',
		'publish_producers',
	);

	// --- check plugin setting for editors ---
	if ( radio_station_get_setting( 'add_editor_capabilities' ) == 'yes' ) {

		// --- grant show edit capabilities to editor users ---
		$editor_caps = $wp_roles->roles['editor']['capabilities'];
		// 2.3.1.1: added check if capabilities is an array
		if ( !is_array( $editor_caps ) ) {
			$editor_caps = array();
		}
		foreach ( $edit_caps as $cap ) {
			if ( !array_key_exists( $cap, $editor_caps ) || ( !$editor_caps[$cap] ) ) {
				$wp_roles->add_cap( 'editor', $cap, true );
			}
		}
	}

	// --- grant all plugin capabilities to admin users ---
	$admin_caps = $wp_roles->roles['administrator']['capabilities'];
	// 2.3.1.1: added check if capabilities is an array
	if ( !is_array( $admin_caps ) ) {
		$admin_caps = array();
	}
	foreach ( $edit_caps as $cap ) {
		if ( !array_key_exists( $cap, $admin_caps ) || ( !$admin_caps[$cap] ) ) {
			$wp_roles->add_cap( 'administrator', $cap, true );
		}
	}

}

// ---------------------------------
// maybe Revoke Edit Show Capability
// ---------------------------------
// (revoke ability to edit show if user is not assigned to it)
add_filter( 'user_has_cap', 'radio_station_revoke_show_edit_cap', 10, 3 );
function radio_station_revoke_show_edit_cap( $allcaps, $caps, $args ) {

	global $post, $wp_roles;

	// --- check if super admin ---
	// ? fix to not revoke edit caps from super admin ?
	// (not implemented, causing connection reset error)
	// if ( function_exists( 'is_super_admin' ) && is_super_admin() ) {
	//	return $allcaps;
	// }

	// --- get the current user ---
	$user = wp_get_current_user();

	// --- get roles with publish shows capability ---
	$edit_show_roles = array( 'administrator' );
	if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {
		foreach ( $wp_roles->roles as $name => $role ) {
			// 2.3.0: fix to skip roles with no capabilities assigned
			if ( isset( $role['capabilities'] ) ) {
				foreach ( $role['capabilities'] as $capname => $capstatus ) {
					// 2.3.0: change publish_shows cap check to edit_shows
					if ( ( 'edit_shows' === $capname ) && (bool) $capstatus ) {
						if ( !in_array( $name, $edit_show_roles ) ) {
							$edit_show_roles[] = $name;
						}
					}
				}
			}
		}
	}

	// --- check if current user has any of these roles ---
	// 2.2.8: remove strict in_array checking
	$found = false;
	foreach ( $edit_show_roles as $role ) {
		if ( in_array( $role, $user->roles ) ) {
			$found = true;
		}
	}

	if ( !$found ) {

		// --- limit this to published shows ---
		// 2.3.0: added object and property_exists check to be safe
		if ( is_object( $post ) && property_exists( $post, 'post_type' ) && isset( $post->post_type ) ) {

			// 2.3.0: removed is_admin check (so works with frontend edit show link)
			// 2.3.0: moved check if show is published inside
			if ( RADIO_STATION_SHOW_SLUG == $post->post_type ) {

				// --- get show hosts and producers ---
				$hosts = get_post_meta( $post->ID, 'show_user_list', true );
				$producers = get_post_meta( $post->ID, 'show_producer_list', true );

				if ( !$hosts || empty( $hosts ) ) {
					$hosts = array();
				}
				if ( !$producers || empty( $producers ) ) {
					$producers = array();
				}

				// ---- revoke editing capability if not assigned to this show ---
				// 2.2.8: remove strict in_array checking
				// 2.3.0: also check new Producer role
				if ( !in_array( $user->ID, $hosts ) && !in_array( $user->ID, $producers ) ) {

					// --- remove the edit_shows capability ---
					$allcaps['edit_shows'] = false;

					// 2.3.0: move check if show is published inside
					if ( 'publish' == $post->post_status ) {
						$allcaps['edit_published_shows'] = false;
					}
				}
			}
		}
	}

	return $allcaps;
}

// =================
// --- Debugging ---
// =================

// -----------------------
// Set Debug Mode Constant
// -----------------------
// 2.3.0: added debug mode constant
// 2.3.2: added saving debug mode constant
if ( !defined( 'RADIO_STATION_DEBUG' ) ) {
	$rs_debug = false;
	if ( isset( $_REQUEST['rs-debug'] ) && ( '1' == $_REQUEST['rs-debug'] ) ) {
		$rs_debug = true;
	}
	define( 'RADIO_STATION_DEBUG', $rs_debug );
}
if ( !defined( 'RADIO_STATION_SAVE_DEBUG' ) ) {
	$rs_save_debug = false;
	if ( isset( $_REQUEST['rs-save-debug'] ) && ( '1' == $_REQUEST['rs-save-debug'] ) ) {
		$rs_save_debug = true;
	}
	define( 'RADIO_STATION_SAVE_DEBUG', $rs_save_debug );
}

// --------------------------
// maybe Clear Transient Data
// --------------------------
// 2.3.0: clear show transients if debugging
// 2.3.1: added action to init hook
// 2.3.1: check clear show transients option
add_action( 'init', 'radio_station_clear_transients' );
function radio_station_clear_transients() {
	$clear_transients = radio_station_get_setting( 'clear_transients' );
	if ( RADIO_STATION_DEBUG || ( 'yes' == $clear_transients ) ) {
		// 2.3.2: do not clear on AJAX calls
		if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {
			delete_transient( 'radio_station_current_schedule' );
			delete_transient( 'radio_station_next_show' );
			// 2.3.3: remove current show transient
			// delete_transient( 'radio_station_current_show' );
		}
	}
}

// ------------------------
// Debug Output and Logging
// ------------------------
// 2.3.0: added debugging function
function radio_station_debug( $data, $echo = true, $file = false ) {

	// --- maybe output debug info ---
	if ( $echo ) {
		// 2.3.0: added span wrap for hidden display
		// 2.3.1.1: added class for page source searches
		echo '<span class="radio-station-debug" style="display:none;">';
		// phpcs:ignore WordPress.Security.OutputNotEscaped
		echo $data;
		echo '</span>' . PHP_EOL;
	}

	// --- check for logging constant ---
	if ( defined( 'RADIO_STATION_DEBUG_LOG' ) ) {
		if ( !$file && RADIO_STATION_DEBUG_LOG ) {
			$file = 'radio-station.log';
		} elseif ( false === RADIO_STATION_DEBUG_LOG ) {
			$file = false;
		}
	}

	// --- write to debug file ---
	if ( $file ) {
		if ( !is_dir( RADIO_STATION_DIR . '/debug' ) ) {
			wp_mkdir_p( RADIO_STATION_DIR . '/debug' );
		}
		$file = RADIO_STATION_DIR . '/debug/' . $file;
		error_log( $data, 3, $file );
	}
}
