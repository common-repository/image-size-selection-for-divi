<?php
/*
Plugin Name: Image Size Selection for Divi
Description: This extension adds a Visual Builder compatible Divi module that allows the user to select images cropped to a set size.
Version:     1.0.3
Author:      Aaron Bolton
Author URI:  https://www.boltonstudios.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: issd-image-size-selection-for-divi
Domain Path: /languages

Image Size Selection for Divi is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Image Size Selection for Divi is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Image Size Selection for Divi. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


if ( ! function_exists( 'issd_initialize_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function issd_initialize_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/ImageSizeSelectionForDivi.php';
}
add_action( 'divi_extensions_init', 'issd_initialize_extension' );
endif;
