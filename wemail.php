<?php
/**
 * Plugin Name: weMail
 * Description: Send Beautiful Email Newsletters with WordPress
 * Plugin URI: https://getwemail.io/
 * Author: weDevs
 * Author URI: https://wedevs.com/
 * Version: 0.1.0
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wemail
 * Domain Path: /i18n/languages
 *
 * Copyright (c) 2019 weMail (email: info@getwemail.io). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **************************************************************************
 */

// don't call the file directly
if (! defined( 'ABSPATH' ) ) {
    exit;
}

class_exists('WeDevs\WeMail\WeMail') || require_once __DIR__.'/vendor/autoload.php';

use WeDevs\WeMail\WeMail;

define( 'WEMAIL_FILE', __FILE__ );
define( 'WEMAIL_PATH', dirname( WEMAIL_FILE ) );
define( 'WEMAIL_ASSETS', plugins_url( '/assets', __FILE__ ) );

/**
 * Init the wemail plugin
 *
 * @since 1.0.0
 *
 * @return WeDevs_WeMail
 */
function wemail() {
    return WeMail::instance();
}

// kick it off
wemail();
