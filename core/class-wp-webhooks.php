<?php
if ( ! class_exists( 'WP_Webhooks' ) ) :

	/**
	 * Main WP_Webhooks Class.
	 *
	 * @since 1.0.0
	 * @package WPWH
	 * @author Ironikus <info@ironikus.com>
	 */
	final class WP_Webhooks {

		/**
		 * The real instance
		 *
		 * @var WP_Webhooks
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * WPWH settings Object.
		 *
		 * @var object|WP_Webhooks_Settings
		 * @since 1.0.0
		 */
		public $settings;

		/**
		 * WPWH helpers Object.
		 *
		 * @var object|WP_Webhooks_Helpers
		 * @since 1.0.0
		 */
		public $helpers;

		/**
		 * WPWH Webhook Object.
		 *
		 * @var object|WP_Webhooks_Webhook
		 * @since 1.0.0
		 */
		public $webhook;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ironikus' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ironikus' ), '1.0.0' );
		}

		/**
		 * Main WP_Webhooks Instance.
		 *
		 * Insures that only one instance of WP_Webhooks exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @staticvar array $instance
		 * @return object|WP_Webhooks The one true WP_Webhooks
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Webhooks ) ) {
				self::$instance                 = new WP_Webhooks;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers        = new WP_Webhooks_Helpers();
				self::$instance->settings       = new WP_Webhooks_Settings();
				self::$instance->webhook        = new WP_Webhooks_Webhook();

				new WP_Webhooks_Run();

				/**
				 * Fire a custom action to allow extensions to register
				 * after WP Webhooks was successfully registered
				 */
				do_action( 'wpwh_plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-helpers.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-settings.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-webhook.php';

			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( WPWH_TEXTDOMAIN, FALSE, dirname( plugin_basename( WPWH_PLUGIN_FILE ) ) . '/language/' );
		}

	}

endif; // End if class_exists check.