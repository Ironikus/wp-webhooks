<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_ironikus_test' ) ) :

	/**
	 * Load the ironikus_test action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_ironikus_test {

	public function get_details(){

		$translation_ident = "action-ironikus-test-description";

		$parameter = array(
			'test_var'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A test var. Include the following value to get a success message back: test-value123', 'action-ironikus-test-content' ) )
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-ironikus-test-content' ) ),
			'test_var'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The variable that was set for the request.', 'action-ironikus-test-content' ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-ironikus-test-content' ) ),
		);

			$returns_code = array (
				'success' => true,
				'msg' => 'Test value successfully filled.',
				'test_var' => 'test-value123',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Test action',
				'webhook_slug' => 'ironikus_test',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'The second argument you need to set is <strong>test_var</strong>. Please set it to <strong>test-value123</strong>', $translation_ident )
				),
				'tipps' => array(
					sprintf( WPWHPRO()->helpers->translate( "This webhook makes sense if you want to test if %s works properly on your WordPress website. You can try to setup different values to see how the webhook interacts with your site.", $translation_ident ), WPWH_NAME )
				),
			) );

			return array(
				'action'			=> 'ironikus_test',
				'name'			  => WPWHPRO()->helpers->translate( 'Test action', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'send a demo action', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Test the functionality of this plugin by sending over a demo request.', 'action-ironikus-test-content' ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'test_var' => ''
			);
	
			$test_var = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'test_var' ) );
	
			if( $test_var == 'test-value123' ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate("Test value successfully filled.", 'action-test-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("test_var was not filled properly. Please set it to 'test-value123'", 'action-test-success' );
			}
	
			$return_args['test_var'] = $test_var;
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.