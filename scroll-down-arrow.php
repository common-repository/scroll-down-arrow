<?php
/**
 * Plugin Name: Scroll Down Arrow
 * Plugin URI: https://www.echoknowledgebase.com/documentation/how-to-use-scroll-down-arrow/
 * Description: Give users a visual clue that there is more content further down the page.
 * Version: 1.1.0
 * Author: Echo Plugins
 * Author URI: https://www.echoknowledgebase.com
 * Text Domain: scroll-down-arrow
 * Domain Path: /languages
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Scroll Down Arrow is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Scroll Down Arrow is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Down Arrow. If not, see <http://www.gnu.org/licenses/>.
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'EPDA_PLUGIN_NAME' ) ) {
	define( 'EPDA_PLUGIN_NAME', 'Scroll Down Arrow' );
}

/**
 * Main class to load the plugin.
 *
 * Singleton
 */
final class EPDA_Scroll_Down_Arrow {

	/* @var EPDA_Scroll_Down_Arrow */
	private static $instance;

	public static $version = '1.1.0';
	public static $plugin_dir;
	public static $plugin_url;
	public static $plugin_file = __FILE__;

	/* @var EPDA_Config_DB */
	public $da_config_obj;

	/**
	 * Initialise the plugin
	 */
	private function __construct() {
		self::$plugin_dir = plugin_dir_path(  __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Retrieve or create a new instance of this main class (avoid global vars)
	 *
	 * @static
	 * @return EPDA_Scroll_Down_Arrow
	 */
	public static function instance() {

		if ( ! empty( self::$instance ) && ( self::$instance instanceof EPDA_Scroll_Down_Arrow ) ) {
			return self::$instance;
		}

		self::$instance = new EPDA_Scroll_Down_Arrow();
		self::$instance->setup_system();
		self::$instance->setup_plugin();

		add_action( 'plugins_loaded', array( self::$instance, 'load_text_domain' ), 11 );
	}

	/**
	 * Setup class autoloading and other support functions. Setup custom core features.
	 */
	private function setup_system() {

		// autoload classes ONLY when needed by executed code rather than on every page request
		require_once self::$plugin_dir . 'includes/system/class-epda-autoloader.php';

		// register settings
		self::$instance->da_config_obj = new EPDA_Config_DB();

		// load non-classes
		require_once self::$plugin_dir . 'includes/system/plugin-setup.php';
		require_once self::$plugin_dir . 'includes/system/scripts-registration.php';
		require_once self::$plugin_dir . 'includes/system/plugin-links.php';
	}

	/**
	 * Setup plugin before it runs. Include functions and instantiate classes based on user action
	 */
	private function setup_plugin() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new EPDA_Config_Ctrl();
			return;
		}

		// ADMIN or CLI
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$this->setup_backend_classes();
			return;
		}

		// FRONT-END with admin-bar

		// FRONT-END (no ajax, possibly admin bar)

		new EPDA_Arrow_View();
	}

	/**
	 * Setup up classes when on ADMIN pages
	 */
	private function setup_backend_classes() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
		$request_page = empty( $_REQUEST['page'] ) ? '' : sanitize_key( $_REQUEST['page'] );

		// admin core classes
		require_once self::$plugin_dir . 'includes/admin/admin-menu.php';
		require_once self::$plugin_dir . 'includes/admin/admin-functions.php';

		// Down Arrow request
		if ( in_array( $request_page, ['epda-down-arrow', 'epda-down-arrow-config'] ) ) {

			add_action( 'admin_enqueue_scripts', 'epda_load_admin_plugin_pages_resources' );

			switch ( $request_page ) {
				// Configuration page
				case 'epda-down-arrow-config':
					add_action( 'admin_enqueue_scripts', 'epda_load_admin_config_script' );
					break;

				default: break;
			}
		}
	}

	/**
	/**
	 * Loads the plugin language files from ./languages directory.
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'scroll-down-arrow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	// Don't allow this singleton to be cloned.
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

	// Don't allow un-serializing of the class except when testing
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}
}

/**
 * Returns the single instance of this class
 * @return EPDA_Scroll_Down_Arrow - this class instance
 */
function epda_get_instance() {
	return EPDA_Scroll_Down_Arrow::instance();
}
epda_get_instance();