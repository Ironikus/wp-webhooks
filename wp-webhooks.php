<?php
/**
 * Plugin Name: WP Webhooks
 * Plugin URI: https://wp-webhooks.com/
 * Description: Put your website on autopilot by using webhooks to get rid of manual tasks and focus on what's really important for your business.
 * Version: 3.3.0
 * Author: Ironikus
 * Author URI: https://wp-webhooks.com/about/
 * License: GPL2
 *
 * You should have received a copy of the GNU General Public License
 * along with TMG User Filter. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

// Plugin name.
define( 'WPWH_NAME',           'WP Webhooks' );

// Plugin version.
define( 'WPWH_VERSION',        '3.3.0' );

// Determines if the plugin is loaded
define( 'WPWH_SETUP',          true );

// Plugin Root File.
define( 'WPWH_PLUGIN_FILE',    __FILE__ );

// Plugin base.
define( 'WPWH_PLUGIN_BASE',    plugin_basename( WPWH_PLUGIN_FILE ) );

// Plugin Folder Path.
define( 'WPWH_PLUGIN_DIR',     plugin_dir_path( WPWH_PLUGIN_FILE ) );

// Plugin Folder URL.
define( 'WPWH_PLUGIN_URL',     plugin_dir_url( WPWH_PLUGIN_FILE ) );

// Plugin Root File.
define( 'WPWH_TEXTDOMAIN',     'wp-webhooks' );

if( ! defined( 'WPWHPRO_SETUP' ) ){

	/**
	 * Load the main instance for our core functions
	 */
	require_once WPWH_PLUGIN_DIR . 'core/class-wp-webhooks-pro.php';

	/**
	 * The main function to load the only instance
	 * of our master class.
	 *
	 * @return object|WP_Webhooks_Pro
	 */
	function WPWHPRO() {
		return WP_Webhooks_Pro::instance();
	}

	WPWHPRO();

} else {

	add_action( 'admin_notices', 'wpwh_premium_version_custom_notice', 100 );
	function wpwh_premium_version_custom_notice(){

		ob_start();
		?>
		<div class="notice notice-warning">
			<p><?php echo 'To use <strong>WP Webhooks Pro</strong> properly, please deactivate <strong>WP Webhooks</strong>.'; ?></p>
		</div>
		<?php
		echo ob_get_clean();

	}

}