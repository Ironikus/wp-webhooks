<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_plugin_deleted' ) ) :

	/**
	 * Load the plugin_deleted trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_plugin_deleted {

        public function is_active(){

            //Backwards compatibility for the "Manage Plugins" integration
            if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
                return false;
            }

            return true;
        }

        public function get_details(){

            $translation_ident = "trigger-plugin_deleted-description";

            $parameter = array(
				'plugin_slug' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of the plugin. You will find an example within the demo data.', $translation_ident ) ),
				'deleted_status' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) Returns true in case the plugin was successfully deleted. false if not.', $translation_ident ) ),
			);

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Plugin deleted',
				'webhook_slug' => 'plugin_deleted',
				'post_delay' => false,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'deleted_plugin',
                        'url' => 'https://developer.wordpress.org/reference/hooks/deleted_plugin/',
                     ),
				)
			) );

			$settings = array();

            return array(
                'trigger'           => 'plugin_deleted',
                'name'              => WPWHPRO()->helpers->translate( 'Plugin deleted', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a plugin was deleted', $translation_ident ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a plugin was deleted.', $translation_ident ),
                'description'       => $description,
                'callback'          => 'test_plugin_deleted',
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            $data = array(
				'plugin_slug' => 'plugin-folder/plugin-file.php',
				'deleted_status' => 'true',
			);

            return $data;
        }

    }

endif; // End if class_exists check.