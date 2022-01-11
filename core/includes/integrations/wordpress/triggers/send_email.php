<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_send_email' ) ) :

	/**
	 * Load the send_email trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_send_email {

        public function is_active(){

            //Backwards compatibility for the "Email integration" integration
            if( defined( 'WPWH_EMAILS_PLUGIN_NAME' ) ){
                return false;
            }

            return true;
        }

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'filter',
                    'hook' => 'wp_mail',
                    'callback' => array( $this, 'ironikus_trigger_send_email' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

            $translation_ident = "trigger-send_email-description";

            $parameter = array(
				'to' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A string containing one or multiple emails (as a comma-separated list) of the receivers of the email.', 'trigger-send_email-content' ) ),
				'subject' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The subject of the email.', 'trigger-send_email-content' ) ),
				'message' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The main mesage (body) of the email.', 'trigger-send_email-content' ) ),
				'headers' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the outgoing email.', 'trigger-send_email-content' ) ),
				'attachments' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array of given email attachments.', 'trigger-send_email-content' ) ),
			);

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Email sent',
				'webhook_slug' => 'send_email',
				'post_delay' => false,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'wp_mail',
                        'url' => 'https://developer.wordpress.org/reference/hooks/wp_mail/',
                    ),
				)
			) );

			$settings = array();

            return array(
                'trigger'           => 'send_email',
                'name'              => WPWHPRO()->helpers->translate( 'Email sent', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'an email was sent', $translation_ident ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires while an email is being sent from your WordPress site.', $translation_ident ),
                'description'       => $description,
                'callback'          => 'test_send_email',
                'integration'       => 'wordpress',
                'premium'           => false,
            );

        }

        public function ironikus_trigger_send_email( $atts ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'send_email' );
			$response_data = array();

			foreach( $webhooks as $webhook ){
				$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

				if( $webhook_url_name !== null ){
					$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $atts );
				} else {
					$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $atts );
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_send_email', $atts, $response_data );

			return $atts;
		}

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            $data = array (
				"to" => 'test@test.demo',
				"subject" => 'This is the subject',
				"message" => htmlspecialchars( 'This is a <strong>HTML</strong> message!' ),
				"headers" => array(
					"Content-Type: text/html; charset=UTF-8",
					"From: Sender Name <anotheremail@someemail.demo>",
					"Cc: Receiver Name <receiver@someemail.demo>",
					"Cc: onlyemail@someemail.demo",
					"Bcc: bccmail@someemail.demo",
					"Reply-To: Reply Name <replytome@someemail.demo>",
				),
				"attachments" => array(
					"/Your/full/server/path/wp-content/uploads/2020/06/my-custom-file.jpg",
					"/Your/full/server/path/wp-content/uploads/2020/06/another-custom-file.jpg",
				)
			);

            return $data;
        }

    }

endif; // End if class_exists check.