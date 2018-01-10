<?php
/**
 * Plugin Name: weMail
 * Description: weMail plugin description...
 * Plugin URI: https://wedevs.com/wemail/
 * Author: weDevs
 * Author URI: https://wedevs.com/
 * Version: 0.0.1
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wemail
 * Domain Path: /languages
 *
 * Copyright (c) 2017 weDevs LLC (email: info@wedevs.com). All rights reserved.
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

final class WeDevs_WeMail {

    /**
     * Plugin version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '0.0.1';

    /**
     * DB version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $db_version = '0.0.1';

    /**
     * Minimum PHP version required
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $min_php = '5.4.0';

    /**
     * weMail text domain
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $text_domain = 'wemail';

    /**
     * Contains the core related class instance
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $core;

    /**
     * @var object
     *
     * @since 1.0.0
     *
     * @var WeDevs_WeMail
     */
    private static $instance;

    /**
     * Contains all registerd modules
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $modules = [];

    /**
     * Admin notice messages
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $admin_notices = [];

    /**
     * Initializes the WeDevs_WeMail() class
     *
     * @since 1.0.0
     *
     * Find any existing WeDevs_WeMail() instance
     * and if it doesn't find one, creates one.
     *
     * @return WeDevs_WeMail
     */
    public static function get_instance() {
        if (! isset( self::$instance ) && ! ( self::$instance instanceof WeDevs_WeMail ) ) {
            self::$instance = new WeDevs_WeMail;
            self::$instance->boot();
        }

        return self::$instance;
    }

    /**
     * Bootstrap the plugin
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function boot() {
        // Check requirements
        if ( ! $this->met_requirements() ) {
            add_action( 'admin_notices', [ $this, 'admin_notices' ] );
            return;
        }

        // Define constants
        $this->define_constants();

        // Include required files
        $this->includes();

        // Hook into actions and filters
        $this->init_hooks();
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param string $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->core->modules->modules ) ) {
            return $this->core->modules->modules[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    public function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                $request = is_admin();
                break;

            case 'ajax' :
                $request = defined( 'DOING_AJAX' );
                break;

            case 'cron' :
                $request = defined( 'DOING_CRON' );
                break;

            case 'frontend' :
                $request = ! is_admin() && defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' );
                break;

            default:
                $request = false;
        }

        return $request;
    }

    /**
     * Validate plugin requirements
     *
     * @since 1.0.0
     *
     * @return boolean
     */
    private function met_requirements() {
        if ( version_compare( PHP_VERSION, $this->min_php, '<' ) ) {
            $this->admin_notices['unmet_php_version'] = sprintf(
                __( '%s requires PHP version %s but you are using version %s', 'wemail' ),
                '<strong>weMail</strong>',
                '<strong>' . $this->min_php . '</strong>',
                '<strong>' . PHP_VERSION . '</strong>'
            );

            return false;
        }

        return true;
    }

    /**
     * `admin_notices` hook
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_notices() {
        foreach ( $this->admin_notices as $notice ) {
            printf( '<div class="error"><p>' . $notice . '</p></div>' );
        }
    }

    /**
     * Define plugin constants
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function define_constants() {
        define( 'WEMAIL_VERSION', $this->version );
        define( 'WEMAIL_FILE', __FILE__ );
        define( 'WEMAIL_PATH', dirname( WEMAIL_FILE ) );
        define( 'WEMAIL_INCLUDES', WEMAIL_PATH . '/includes' );
        define( 'WEMAIL_MODULES', WEMAIL_INCLUDES . '/Modules' );
        define( 'WEMAIL_URL', plugins_url( '', WEMAIL_FILE ) );
        define( 'WEMAIL_ASSETS', WEMAIL_URL . '/assets' );
        define( 'WEMAIL_VIEWS', WEMAIL_PATH . '/views' );
    }

    /**
     * Include plugin files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require_once WEMAIL_PATH . '/vendor/autoload.php';
        require_once WEMAIL_INCLUDES . '/functions.php';
    }

    /**
     * Hooks methods to WP actions
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_hooks() {
        register_activation_hook( WEMAIL_FILE, [ '\WeDevs\WeMail\Install', 'install' ] );

        add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
        add_action( 'init', [ $this, 'init' ], 0 );
    }

    /**
     * Load plugin i18n text domain
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'wemail', false, WEMAIL_PATH . '/i18n/languages/' );
    }

    /**
     * Create class instances
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init() {
        // Before init action.
        do_action( 'wemail_before_init' );

        $this->core = new StdClass();

        $this->core->scripts = new WeDevs\WeMail\Framework\Scripts();
        $this->core->modules = new WeDevs\WeMail\Modules\Modules();
        $this->core->rest    = new WeDevs\WeMail\Rest();

        if ( $this->is_request( 'admin' ) ) {
            $this->core->admin_scripts = new WeDevs\WeMail\Admin\Scripts();
            $this->core->admin_menu    = new WeDevs\WeMail\Admin\Menu();
        }

        if ( $this->is_request( 'ajax' ) ) {
            $this->core->ajax = new WeDevs\WeMail\Ajax();
        }

        // Init action.
        do_action( 'wemail_init' );
    }

}

/**
 * Init the wemail plugin
 *
 * @since 1.0.0
 *
 * @return WeDevs_WeMail
 */
function wemail() {
    return WeDevs_WeMail::get_instance();
}

// kick it off
wemail();
