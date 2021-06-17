<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_woocommerce_api' ) ) :

	/**
	 * Load the woocommerce_api action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_woocommerce_api {

        /*
        * The core logic to test a webhook
        */
        public function get_details(){

            $translation_ident = "action-woocommerce_api-content";

            $parameter = array(
				'consumer_key'      => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Your API consumer key. Please see the description for more information.', $translation_ident ) ),
				'consumer_secret'   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Your API consumer secret. Please see the description for more information.', $translation_ident ) ),
				'api_base'          => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The action you want to use. E.g. products/1234 - Please see the description for more information.', $translation_ident ) ),
				'api_method'        => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The method of the api call. E.g. get - Please see the description for more information.', $translation_ident ) ),
				'api_data'      	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Additional data you want to send to the api call. Please see the description for more information.', $translation_ident ) ),
				'api_options'      	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Extra arguments. Please see the description for more information.', $translation_ident ) ),
				'do_action'      	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The webhook data and response data.', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
			?>
            <pre>
$return_args = array(
	'success' => false,
	'msg' => '',
	'data' => array()
);
        </pre>
			<?php
			$returns_code = ob_get_clean();

			ob_start();
			?>
                <p><?php echo WPWHPRO()->helpers->translate( 'This webhook enables you to use the full finctionality of the woocommerce REST API. It also works with all integrated extensions like <strong>Woocommerce Memberships</strong>, <strong>Woocommerce Subscriptions</strong> or <strong>Woocommerce Bookings</strong>.', 'action-create_woocommerce_order-content' ); ?></p>
				
				<p><?php echo WPWHPRO()->helpers->translate( 'Dou to the complexity of this webhook, we added some neat docs for you within our official documentation: ', $translation_ident ); ?> <a href="https://wp-webhooks.com/docs/article-categories/wp-webhooks-pro-woocommerce/" target="_blank" title="Go to our docs">https://wp-webhooks.com/docs/article-categories/wp-webhooks-pro-woocommerce/</a></p>
				
				<p><?php echo WPWHPRO()->helpers->translate( 'You can also add a custom action, that fires after the webhook was called. Simply specify your webhook identifier (e.g. my_csutom_webhook) and call it within your theme or plugin (e.g. add_action( "my_csutom_webhook", "my_csutom_webhook_callback" ) ).', $translation_ident ); ?></p>
            <?php
			$description = ob_get_clean();

            return array(
                'action'            => 'woocommerce_api', //required
                'name'               => WPWHPRO()->helpers->translate( 'Woocommerce API call', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'The full power of the woocommerce API, packed within a webhook.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'woocommerce',
                'premium'          => true,
            );


        }

    }

endif; // End if class_exists check.