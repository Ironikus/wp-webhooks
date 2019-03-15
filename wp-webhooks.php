<?php
/**
 * Plugin Name: WP Webhooks
 * Plugin URI: https://ironikus.com/downloads/wp-webhooks/
 * Description: Automate your WordPress system using webhooks
 * Version: 1.0.0
 * Author: Ironikus
 * Author URI: https://ironikus.com/
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
define( 'WPWH_VERSION',        '1.0.0' );

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

// News ID
define( 'WPWH_NEWS_FEED_ID', 1 );

/**
 * Load the main instance for our core functions
 */
require_once WPWH_PLUGIN_DIR . 'core/class-wp-webhooks.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @return object|WP_Webhooks
 */
function WPWH() {
	return WP_Webhooks::instance();
}

WPWH();