<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_subscription' ) ) :

	/**
	 * Load the edd_delete_subscription action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_subscription {

        public function is_active(){

            $is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_delete_subscription-description";

            $parameter = array(
				'subscription_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the subscription you would like to delete.', $translation_ident ) ),
				'keep_payment_meta'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set this value to "yes" if you do not want to delet the relation of the subscription on the related payment. Default: no', $translation_ident ) ),
				'keep_list_of_trials'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set this value to "yes" to delete the list of trials of the user that are related to the given subscription id. Default: no', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Containing the new susbcription id and other arguments set during the deletion of the subscription.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The subscription was successfully deleted.',
				'data' => 
				array (
				  'subscription_id' => 21,
				  'keep_payment_meta' => false,
				  'keep_list_of_trials' => false,
				),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete an existing subscription for <strong>Easy Digital Downloads - Recurring</strong> within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_delete_subscription</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_delete_subscription</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_delete_subscription</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>subscription_id</strong> argument. Please set it to the id of the subscription you would like to delete. Further details are available down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the deletion of the EDD Subscription.", $translation_ident ); ?></>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "By default, we properly erase the subsription including the relations on the customer and payments.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "subscription_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The id of the subscription you would like to delete. Please note that the subscription needs to be existent, otherwise we will throw an error.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "keep_payment_meta", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set this value to <strong>yes</strong> to keep the payment meta (meta key on the payment: _edd_subscription_payment). Usually, it makes sense to remove this relation as well. That's why this value is deleted by default. Please only set it to <strong>yes</strong> in case you need to keep the meta key.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "keep_list_of_trials", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set this value to <strong>yes</strong> to keep the meta entry for the list of trials (meta key on the user: edd_recurring_trials). Usually, it makes sense to remove this relation as well. That's why this value is deleted by default. Please only set it to <strong>yes</strong> in case you need to keep the meta key.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_delete_subscription</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $subscription_id, $subscription, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$subscription_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the deleted subscription.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$subscription</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An object of the EDD_Subscription() class with the currently deleted subscription.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
    </li>
</ol>
<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'edd_delete_subscription',
                'name'              => WPWHPRO()->helpers->translate( 'Delete subscription', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'delete a subscription', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to delete a subscription within Easy Digital Downloads - Recurring.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription_id' => 0,
					'keep_payment_meta' => false,
					'keep_list_of_trials' => false,
				),
			);

			$subscription_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_id' ) );
			$keep_payment_meta   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_payment_meta' ) === 'yes' ) ? true : false;
			$keep_list_of_trials   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'keep_list_of_trials' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Subscription' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_Subscription() does not exist. The subscription was not deleted.', 'action-edd_delete_subscription-failure' );
				return $return_args;
			}

			$subscription = new EDD_Subscription( $subscription_id );
			if( empty( $subscription ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'Error: Invalid subscription id provided.', 'action-edd_update_subscription-failure' );
				return $return_args;
			}

			if( ! $keep_payment_meta && isset( $subscription->parent_payment_id ) ){
				delete_post_meta( $subscription->parent_payment_id, '_edd_subscription_payment' );
			}

			// Delete subscription from list of trials customer has used
			if( ! $keep_list_of_trials && isset( $subscription->product_id ) ){
				$subscription->customer->delete_meta( 'edd_recurring_trials', $subscription->product_id );
			}

			$check = $subscription->delete();

			if( $check ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscription was successfully deleted.", 'action-edd_delete_subscription-success' );
				$return_args['success'] = true;
				$return_args['data']['subscription_id'] = $subscription_id;
				$return_args['data']['keep_payment_meta'] = $keep_payment_meta;
				$return_args['data']['keep_list_of_trials'] = $keep_list_of_trials;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error deleting the subscription.", 'action-edd_delete_subscription-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $subscription_id, $subscription, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.