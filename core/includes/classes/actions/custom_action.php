<?php
if ( ! class_exists( 'WP_Webhooks_Action_custom_action' ) ) :

	/**
	 * Load the custom_action action
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Action_custom_action {

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

			$actions['custom_action'] = $this->action_custom_action_content();

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
				case 'custom_action':
					$response = $this->action_custom_action();
					break;
			}

			return $response;
		}

        /*
	 * The core logic to use a custom action webhook
	 */
	public function action_custom_action_content(){

		$translation_ident = 'action-ironikus-custom_action-content';

		$parameter = array(
			'wpwh_identifier'       => array(
				'short_description' => WPWHPRO()->helpers->translate( 'This value is send over within the WordPress hooks to identify the incoming action. You can use it to fire your customizatios only on specific webhooks.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( "Set this argument to identify your webhook call within the add_filter() function. It can be used to diversify between multiple calls that use this custom action. You can set it to e.g. <strong>validate-user</strong> and then check within the add_filter() callback against it to only fire it for this specific webhook call. You can also define this argument within the URL as a parameter, e.g. <code>&wpwh_identifier=my-custom-identifier</code>. In case you have defined the wpwh_identifier within the payload and the URL, we prioritize the parameter set within the payload.", $translation_ident ),
			),
		);

		$returns = array(
			'custom'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'This webhook returns whatever you define withiin the filters. Please check the description for more detials.', $translation_ident ) ),
		);

		ob_start();
		?>
        <pre>
//This is how the default response looks like
$return_args = array(
    'success' => false,
    'msg' => ''
);
        </pre>
            <?php
            $returns_code = ob_get_clean();

            ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to fire a custom wordpress filter within your WordPress system via a webhook call. You can use it to fully cusotmize the webhook logic for your needs.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>custom_action</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand on how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>custom_action</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>custom_action</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the custom execution of the action.", $translation_ident ); ?></li>
</ol>
<?php echo WPWHPRO()->helpers->translate( "To modify your incoming data based on your needs, you simply create a custom add_filter() call within your theme or plugin. Down below is an example on how you can do that:", $translation_ident ); ?>
<pre>add_filter( 'wpwhpro/run/actions/custom_action/return_args', 'wpwh_fire_my_custom_logic', 10, 3 );
function wpwh_fire_my_custom_logic( $return_args, $identifier, $response_body ){

	//If the identifier doesn't match, do nothing
	if( $identifier !== 'ilovewebhooks' ){
		return $return_args;
	}

	//This is how you can validate the incoming value. This field will return the value for the key user_email
	$email = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_email' );

	//Include your own logic here....

	//This is what the webhook returns back to the caller of this action (response)
	//By default, we return an array with success => true and msg -> Some Text
	return $return_args;

}</pre>
<?php echo WPWHPRO()->helpers->translate( "The custom add_filter() callback accepts three parameters, which are explained down below:", $translation_ident ); ?>
<ol>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This is what the webhook call returns as a response. You can modify it to return your own custom data.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$identifier</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This is the wpwh_identifier you may have set up within the webhook call. (We also allow to set this specific argument within the URL as &wpwh_identifier=my_identifier). Further information about this argument is available within the <strong>Special Arguments</strong> list.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$response_body</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This returns the validated payload of the incoming webhook call. You can use <code>WPWHPRO()->helpers->validate_request_value()</code> to validate single entries (See example)", $translation_ident ); ?>
    </li>
</ol>
<br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipp", $translation_ident ); ?></h4>
<?php echo WPWHPRO()->helpers->translate( "Since this webhook action requires you to still set the action as mentioned above (to let our plugin know you want to fire a custom_action), you can also set the action parameter within the webhook URL you define within your external endpoint (instead of within the body). E.g.: <code>&action=custom_action</code> - This way you can avoid modifying the webhook request payload in the first place.", $translation_ident ); ?>
<?php
            $description = ob_get_clean();

            return array(
                'action'            => 'custom_action',
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'Do whatever you like with the incoming data by defining this custom action.', $translation_ident ),
                'description'       => $description
            );

        }

        public function action_custom_action(){

            $response_body = WPWHPRO()->helpers->get_response_body();
            $return_args = array(
                'success' => true,
                'msg' => WPWHPRO()->helpers->translate("Custom action was successfully fired.", 'action-custom_action-success' )
            );
    
            $identifier = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wpwh_identifier' );
            if( empty( $identifier ) && isset( $_GET['wpwh_identifier'] ) ){
                $identifier = $_GET['wpwh_identifier'];
            }
    
            $return_args = apply_filters( 'wpwhpro/run/actions/custom_action/return_args', $return_args, $identifier, $response_body );
    
            return $return_args;
    
        }

    }

endif; // End if class_exists check.