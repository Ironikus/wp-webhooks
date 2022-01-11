<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_plugin_activated' ) ) :

	/**
	 * Load the plugin_activated trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_plugin_activated {

        public function is_active(){

            //Backwards compatibility for the "Manage Plugins" integration
            if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
                return false;
            }

            return true;
        }

        public function get_details(){

            $translation_ident = "trigger-plugin_activated-description";

            $parameter = array(
				'plugin_slug' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of the plugin. You will find an example within the demo data.', $translation_ident ) ),
				'network_wide' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the plugin was activated for the whole network of a multisite, false if not.', $translation_ident ) ),
			);

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Plugin activated',
				'webhook_slug' => 'plugin_activated',
				'post_delay' => false,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'activated_plugin',
                        'url' => 'https://developer.wordpress.org/reference/hooks/activated_plugin/',
                     ),
				)
			) );

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_manage_plugins_plugin_activated_network' => array(
						'id'          => 'wpwhpro_manage_plugins_plugin_activated_network',
						'type'        => 'select',
						'multiple'    => true,
						'choices'      => array(
                            'single' => WPWHPRO()->helpers->translate( 'Single site', $translation_ident ),
                            'multi' => WPWHPRO()->helpers->translate( 'Multisite', $translation_ident ),
                        ),
						'label'       => WPWHPRO()->helpers->translate('Fire trigger on single or multisite.', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('In case you run a multisite network, select if you want to trigger the webhook on multisite activations, single site activations or both. If nothing is selected, both are triggered.', $translation_ident)
					),
				)
			);

            return array(
                'trigger'           => 'plugin_activated',
                'name'              => WPWHPRO()->helpers->translate( 'Plugin activated', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a plugin was activated', $translation_ident ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a plugin was activated.', $translation_ident ),
                'description'       => $description,
                'callback'          => 'test_plugin_activated',
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
				'network_wide' => 'false',
			);

            return $data;
        }

    }

endif; // End if class_exists check.