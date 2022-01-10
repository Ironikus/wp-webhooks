<?php
if ( ! class_exists( 'WP_Webhooks_Pro' ) ) :

	/**
	 * Main WP_Webhooks_Pro Class.
	 *
	 * @since 1.0.0
	 * @package WPWHPRO
	 * @author Ironikus <info@ironikus.com>
	 */
	final class WP_Webhooks_Pro {

		/**
		 * The real instance
		 *
		 * @var WP_Webhooks_Pro
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * WPWHPRO settings Object.
		 *
		 * @var object|WP_Webhooks_Pro_Settings
		 * @since 1.0.0
		 */
		public $settings;

		/**
		 * WPWHPRO helpers Object.
		 *
		 * @var object|WP_Webhooks_Pro_Helpers
		 * @since 1.0.0
		 */
		public $helpers;

		/**
		 * WPWHPRO SQL Object.
		 *
		 * @var object|WP_Webhooks_Pro_SQL
		 * @since 2.0.0
		 */
		public $sql;

		/**
		 * WPWHPRO API Object.
		 *
		 * @var object|WP_Webhooks_Pro_API
		 * @since 1.0.0
		 */
		public $api;

		/**
		 * WPWHPRO Webhook Object.
		 *
		 * @var object|WP_Webhooks_Pro_Webhook
		 * @since 1.0.0
		 */
		public $webhook;

		/**
		 * WPWHPRO Integrations Object.
		 *
		 * @var object|WP_Webhooks_Pro_Integrations
		 * @since 3.2.0
		 */
		public $integrations;

		/**
		 * WPWHPRO Extensions Object.
		 *
		 * @var object|WP_Webhooks_Pro_Extensions
		 * @since 4.2.3
		 */
		public $extensions;

		/**
		 * WPWHPRO Polling Object.
		 *
		 * @var object|WP_Webhooks_Pro_Polling
		 * @since 1.1.1
		 */
		public $polling;

		/**
		 * WPWHPRO Post Delay Object.
		 *
		 * @var object|WP_Webhooks_Pro_Post_Delay
		 * @since 1.1.3
		 */
		public $delay;

		/**
		 * WPWHPRO Authentication Object.
		 *
		 * @var object|WP_Webhooks_Pro_Authentication
		 * @since 2.0.0
		 */
		public $auth;

		/**
		 * WPWHPRO Advanced Custom Fields Object.
		 *
		 * @var object|WP_Webhooks_Pro_ACF
		 * @since 3.2.0
		 */
		public $acf;

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
		 * Main WP_Webhooks_Pro Instance.
		 *
		 * Insures that only one instance of WP_Webhooks_Pro exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @staticvar array $instance
		 * @return object|WP_Webhooks_Pro The one true WP_Webhooks_Pro
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Webhooks_Pro ) ) {
				self::$instance                 = new WP_Webhooks_Pro;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers        = new WP_Webhooks_Pro_Helpers();
				self::$instance->settings       = new WP_Webhooks_Pro_Settings();
				self::$instance->sql            = new WP_Webhooks_Pro_SQL();
				self::$instance->delay			= new WP_Webhooks_Pro_Post_Delay();
				self::$instance->auth			= new WP_Webhooks_Pro_Authentication();
				self::$instance->api            = new WP_Webhooks_Pro_API();
				self::$instance->webhook        = new WP_Webhooks_Pro_Webhook();
				self::$instance->integrations   = new WP_Webhooks_Pro_Integrations();
				self::$instance->extensions		= new WP_Webhooks_Pro_Extensions();
				self::$instance->polling      	= new WP_Webhooks_Pro_Polling();
				self::$instance->acf      		= new WP_Webhooks_Pro_ACF();

				/**
				 * Used to launch our integrations
				 */
				do_action( 'wpwh_plugin_configured' );

				//Run plugin
				new WP_Webhooks_Pro_Run();

				//Schedule our daily maintenance event
				if( ! wp_next_scheduled( 'wpwh_daily_maintenance' ) ){
					wp_schedule_event( time(), 'daily', 'wpwh_daily_maintenance' );
				}

				/**
				 * Fire a custom action to allow extensions to register
				 * after WP Webhooks Pro was successfully registered
				 */
				do_action( 'wpwhpro_plugin_loaded' );
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
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-helpers.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-settings.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-sql.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-post-delay.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-auth.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-api.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-webhook.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-integrations.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-extensions.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-polling.php';
			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-acf.php';

			require_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-run.php';
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
			register_deactivation_hook( WPWH_PLUGIN_FILE, array( self::$instance, 'register_deactivation_hook_callback' ) );
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

		/**
		 * Register the plugin deactivation callback
		 * 
		 * This function is called on plugin deactivation
		 *
		 * @access public
		 * @since 3.3.0
		 * @return void
		 */
		public function register_deactivation_hook_callback() {
			//Remove wpwh_daily_maintenance cron job
			$next_event = wp_next_scheduled( 'wpwh_daily_maintenance' );

			if( $next_event ){
				wp_unschedule_event( $next_event, 'wpwh_daily_maintenance' );
			}
		}

	}

endif; // End if class_exists check.