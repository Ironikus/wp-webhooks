<?php
if ( ! class_exists( 'WP_Webhooks_Action_ironikus_test' ) ) :

	/**
	 * Load the ironikus_test action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Action_ironikus_test {

		function __construct(){
            $this->page_name    = WPWHPRO()->settings->get_page_name();
		    $this->page_title   = WPWHPRO()->settings->get_page_title();

            add_filter( 'wpwhpro/webhooks/get_webhooks_actions', array( $this, 'add_action_details' ), 10 );
			add_filter( 'wpwhpro/webhooks/add_webhook_actions', array( $this, 'add_action_callback' ), 1000, 4 );
        }

        /**
         * Register the webhook details
         *
         * @param array $actions
         * @return array The adjusted webhook details
         */
		public function add_action_details( $actions ){

			$actions['ironikus_test'] = $this->action_ironikus_test_content();

			return $actions;
		}

		/**
		 * Register the actual functionality of the webhook
		 *
		 * @param mixed $response
		 * @param string $action
		 * @param string $response_ident_value
		 * @param string $response_api_key
		 * @return mixed The response data for the webhook caller
		 */
		public function add_action_callback( $response, $action, $response_ident_value, $response_api_key ){

			switch( $action ){
				case 'ironikus_test':
					$response = $this->action_ironikus_test();
					break;
			}

			return $response;
		}

        /*
	 * The core logic to test a webhook
	 */
	public function action_ironikus_test_content(){

        $translation_ident = "action-ironikus-test-description";

		$parameter = array(
			'test_var'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A test var. Include the following value to get a success message back: test-value123', 'action-ironikus-test-content' ) )
		);

		$returns = array(
			'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-ironikus-test-content' ) ),
			'test_var'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The variable that was set for the request.', 'action-ironikus-test-content' ) ),
			'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-ironikus-test-content' ) ),
		);

		ob_start();
		?>
        <pre>
$return_args = array(
    'success' => false,
    'msg' => '',
    'test_var' => 'test-value123'
);
        </pre>
            <?php
            $returns_code = ob_get_clean();

            ob_start();
?>
<?php echo sprintf( WPWHPRO()->helpers->translate( "This webhook action is only used for testing purposes to test if %s works properly on your WordPress website.", $translation_ident ), WPWH_NAME ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>ironikus_test</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>ironikus_test</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>ironikus_test</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "The second argument you need to set is <strong>test_var</strong>. Please set it to <strong>test-value123</strong>", $translation_ident ); ?></li>
</ol>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipp", $translation_ident ); ?></h4>
<?php echo sprintf( WPWHPRO()->helpers->translate( "This webhook makes sense if you want to test if %s works properly on your WordPress website. You can try to setup different values to see how the webhook interacts with your site.", $translation_ident ), WPWH_NAME ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can also use it to test the functionality using our Ironikus assistant:", $translation_ident ); ?>
<a title="ironikus.com" target="_blank" href="https://ironikus.com/assistant/">https://ironikus.com/assistant/</a>
<?php
            $description = ob_get_clean();

            return array(
                'action'            => 'ironikus_test',
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Test the functionality of this plugin by sending over a demo request.', 'action-ironikus-test-content' ),
                'description'       => $description
            );

        }

        public function action_ironikus_test(){

            $response_body = WPWHPRO()->helpers->get_response_body();
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