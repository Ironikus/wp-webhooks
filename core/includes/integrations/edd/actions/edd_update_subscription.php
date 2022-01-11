<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_update_subscription' ) ) :

	/**
	 * Load the edd_update_subscription action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_update_subscription {

        public function is_active(){

            $is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_update_subscription-description";

			$parameter = array(
				'subscription_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the subscription you would like to update.', $translation_ident ) ),
				'expiration_date'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date for the expiration of the subscription. Recommended format: 2021-05-25 11:11:11', $translation_ident ) ),
				'profile_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe.', $translation_ident ) ),
				'download_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the download you want to connect with the subscription.', $translation_ident ) ),
				'customer_email'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer in case you do not have the customer id. Please see the description for further details.', $translation_ident ) ),
				'period'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The billing period of the subscription. Please see the description for further details.', $translation_ident ) ),
				'initial_amount'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The amount for the initial payment. E.g. 39.97', $translation_ident ) ),
				'recurring_amount'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The recurring amount for the subscription. E.g. 19.97', $translation_ident ) ),
				'transaction_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) This is the unique ID of the initial transaction inside of the merchant processor, such as PayPal or Stripe.', $translation_ident ) ),
				'status'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The status of the given subscription. Please see the description for further details.', $translation_ident ) ),
				'created_date'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date of creation of the subscription. Recommended format: 2021-05-25 11:11:11', $translation_ident ) ),
				'bill_times'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) This refers to the number of times the subscription will be billed before being marked as Completed and payments stopped. Enter 0 if payments continue indefinitely.', $translation_ident ) ),
				'parent_payment_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) Use this argument to connect the subscription with an already existing payment. Please see the description for further details.', $translation_ident ) ),
				'customer_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the customer you want to connect. If it is not given, we try to fetch the user from the customer_email argument. Please see the description for further details.', $translation_ident ) ),
				'edd_price_option'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The variation id for a download price option. Please see the description for further details.', $translation_ident ) ),
				'initial_tax_rate'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The percentage for your initial tax rate. Please see the description for further details.', $translation_ident ) ),
				'initial_tax'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The amount of tax for your initial tax amount. Please see the description for further details.', $translation_ident ) ),
				'recurring_tax_rate'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The percentage for your recurring tax rate. Please see the description for further details.', $translation_ident ) ),
				'recurring_tax'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The amount of tax for your recurring tax amount. Please see the description for further details.', $translation_ident ) ),
				'notes'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string containing one or multiple subscription notes. Please check the description for further details.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Containing the new susbcription id, the payment id, customer id, as well as further details about the subscription.', $translation_ident ) ),
			);

			$returns_code = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription_id' => 0,
					'payment_id' => 0,
					'customer_id' => 0,
				),
			);

			ob_start();
            $default_subscription_statuses = array (
                'pending' => __( 'Pending', 'edd-recurring' ),
                'active' => __( 'Active', 'edd-recurring' ),
                'cancelled' => __( 'Cancelled', 'edd-recurring' ),
                'expired' => __( 'Expired', 'edd-recurring' ),
                'trialling' => __( 'Trialling', 'edd-recurring' ),
                'failing' => __( 'Failing', 'edd-recurring' ),
                'completed' => __( 'Completed', 'edd-recurring' ),
            );
            $default_subscription_statuses = apply_filters( 'wpwh/descriptions/actions/edd_update_subscription/default_subscription_statuses', $default_subscription_statuses );
            $beautified_subscription_statuses = json_encode( $default_subscription_statuses, JSON_PRETTY_PRINT );
            
            $default_subscription_periods = array (
                'day' => __( 'Daily', 'edd-recurring' ),
                'week' => __( 'Weekly', 'edd-recurring' ),
                'month' => __( 'Monthly', 'edd-recurring' ),
                'quarter' => __( 'Quarterly', 'edd-recurring' ),
                'semi-year' => __( 'Semi-Yearly', 'edd-recurring' ),
                'year' => __( 'Yearly', 'edd-recurring' ),
            );
            $default_subscription_periods = apply_filters( 'wpwh/descriptions/actions/edd_update_subscription/default_subscription_periods', $default_subscription_periods );
            $beautified_subscription_periods = json_encode( $default_subscription_periods, JSON_PRETTY_PRINT );
            
?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to update an existing subscription for <strong>Easy Digital Downloads - Recurring</strong> within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_update_subscription</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_update_subscription</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_update_subscription</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>subscription_id</strong> argument. Please set it to the id of the subscription you would like to update. Further details are available down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the update process of the EDD Subscription code.", $translation_ident ); ?></>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you would like to update the customer but you do not have the customer id, simply provide the customer email within the <strong>customer_email</strong> argument and we will fetch the customer automatically.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "subscription_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The id of the subscription you would like to update. Please note that the subscription needs to be existent, otherwise we will throw an error.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "expiration_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "profile_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe. It accepts any kind of string.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_email", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the email of the customer you would like to set for the susbcription. You can set this argument in case you do not have the customer id of the customer available.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "period", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This is the frequency of the renewals for the subscription. Down below, you will find a list with all of the default subscription periods. Please use the slug as a value (e.g. <strong>month</strong>).", $translation_ident ); ?>
<pre><?php echo $beautified_subscription_periods;  ?></pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "transaction_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This is the unique ID of the initial transaction inside of the merchant processor, such as PayPal or Stripe. The argument accepts any kind of string.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "status", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to customize the status of the subscription. Please use the slug of the status as a value (e.g. <strong>completed</strong>). Down below, you will find a list with all available default statuses:", $translation_ident ); ?>
<pre><?php echo $beautified_subscription_statuses; ?></pre>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "created_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "parent_payment_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to connect your subscription with an already existing payment.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to connect an already existing customer with your subscription. Please use the customer id and not the user id since these are different things. Please note, that in case you leave this argument empty, we will first try to find an existing customer based on your given email within the <strong>customer_email</strong> argument, and if we found a customer with it, we will map the customer id automatically.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "edd_price_option", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you work with multiple price options, please define the chosen price option for your download here. Please note, that the price option needs to be available within the download you chose for the <strong>download_id</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "initial_tax_rate", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the percentage of tax that is included within your initial price. E.g.: In case you add 20, it is interpreted as 20% tax.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "initial_tax", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the amount of tax for the initial payment. E.g.: In case your tax is 13.54$, simply add 13.54", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "recurring_tax_rate", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the percentage of tax that is included within your recurring price. E.g.: In case you add 20, it is interpreted as 20% tax.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "recurring_tax", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the amount of tax for the recurring payment. E.g.: In case your tax is 13.54$, simply add 13.54", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "notes", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to add one or multiple subscription notes to the subscription. This value accepts a JSON, containing one subscription note per line. Here is an example:", $translation_ident ); ?>
<pre>[
    "First Note 1",
    "First Note 2"
]</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above adds two notes.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_update_subscription</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $subscription_id, $subscription, $sub_args, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$subscription_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the newly created subscription.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$subscription</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An object of the EDD_Subscription() class with the current subscription.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$sub_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array containing all the susbcription arguments that we are sending over to the EDD_Subscription()->update() function.", $translation_ident ); ?>
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
                'action'            => 'edd_update_subscription',
                'name'              => WPWHPRO()->helpers->translate( 'Update subscription', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'update a subscription', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to update a subscription within Easy Digital Downloads - Recurring.', $translation_ident ),
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
					'payment_id' => 0,
					'customer_id' => 0,
				),
			);

			$subscription_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_id' ) );
			$expiration_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$profile_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'profile_id' );
			$initial_amount   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_amount' );
			$recurring_amount   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_amount' );
			$download_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' );
			$transaction_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'transaction_id' );
			$status   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$created_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'created_date' );
			$bill_times   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'bill_times' );
			$period   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'period' );
			$parent_payment_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_payment_id' ) );
			$customer_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_id' ) );
			$customer_email   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$edd_price_option   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_price_option' );
			$initial_tax_rate   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_tax_rate' );
			$initial_tax   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_tax' );
			$recurring_tax_rate   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_tax_rate' );
			$recurring_tax   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_tax' );
			$notes   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'notes' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Subscription' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_Subscription() does not exist. The subscription was not created.', 'action-edd_update_subscription-failure' );
				return $return_args;
			}

			if( empty( $subscription_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The subscription_id argument cannot be empty. ', 'action-edd_update_subscription-failure' );
				return $return_args;
			}

			$subscription = new EDD_Subscription( $subscription_id );
			if( empty( $subscription ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'Error: Invalid subscription id provided.', 'action-edd_update_subscription-failure' );
				return $return_args;
			}

			//try to fetch the customer
			if( empty( $customer_id ) ){
				if( ! empty( $customer_email ) ){
					$tmpcustomer = EDD()->customers->get_customer_by( 'email', $customer_email );
					if( isset( $tmpcustomer->id ) && ! empty( $tmpcustomer->id ) ) {
						$customer_id = $tmpcustomer->id;
					}
				}
			} else {
				$tmpcustomer = EDD()->customers->get_customer_by( 'id', $customer_id );
				if( isset( $tmpcustomer->id ) && ! empty( $tmpcustomer->id ) ) {
					$customer_id = $tmpcustomer->id;
				}
			}

			$sub_args = array();
			
			if( $expiration_date ){
				$sub_args['expiration'] = date( 'Y-m-d H:i:s', strtotime( $expiration_date, current_time( 'timestamp' ) ) );
			}
			
			if( $created_date ){
				$sub_args['created'] = date( 'Y-m-d H:i:s', strtotime( $created_date, current_time( 'timestamp' ) ) );
			}
			
			if( $status ){
				$sub_args['status'] = sanitize_text_field( $status );
			}
			
			if( $profile_id ){
				$sub_args['profile_id'] = sanitize_text_field( $profile_id );
			}
			
			if( $transaction_id ){
				$sub_args['transaction_id'] = sanitize_text_field( $transaction_id );
			}
			
			if( $initial_amount ){
				$sub_args['initial_amount'] = edd_sanitize_amount( sanitize_text_field( $initial_amount ) );
			}
			
			if( $recurring_amount ){
				$sub_args['recurring_amount'] = edd_sanitize_amount( sanitize_text_field( $recurring_amount ) );
			}
			
			if( $bill_times ){
				$sub_args['bill_times'] = absint( $bill_times );
			}
			
			if( $period ){
				$sub_args['period'] = sanitize_text_field( $period );
			}
			
			if( $parent_payment_id ){
				$sub_args['parent_payment_id'] = $parent_payment_id;
			}
			
			if( $download_id ){
				$sub_args['product_id'] = absint( $download_id );
			}
			
			if( $edd_price_option ){
				$sub_args['price_id'] = absint( $edd_price_option );
			}
			
			if( $customer_id ){
				$sub_args['customer_id'] = $customer_id;
			}
			
			if( $initial_tax_rate ){
				$sub_args['initial_tax_rate'] = edd_sanitize_amount( (float) $initial_tax_rate / 100 );
			}

			if( $initial_tax ){
				$sub_args['initial_tax'] = edd_sanitize_amount( $initial_tax );
			}

			if( $recurring_tax_rate ){
				$sub_args['recurring_tax_rate'] = edd_sanitize_amount( (float) $recurring_tax_rate / 100 );
			}

			if( $recurring_tax ){
				$sub_args['recurring_tax'] = edd_sanitize_amount( $recurring_tax );
			}

			$check = $subscription->update( $sub_args );

			if( $check ){

				if( ! empty( $notes ) ){
					if( WPWHPRO()->helpers->is_json( $notes ) ){
						$notes_arr = json_decode( $notes, true );
						foreach( $notes_arr as $snote ){
							$subscription->add_note( $snote );
						}
					}
				}
	
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscription was successfully updated.", 'action-edd_update_subscription-success' );
				$return_args['success'] = true;
				$return_args['data']['subscription_id'] = $subscription->id;
				$return_args['data']['subscription_arguments'] = $sub_args;
				$subscription_id = $subscription->id;
			} else {
				if( empty( $sub_args ) ){
					$return_args['msg'] = WPWHPRO()->helpers->translate( "Error updating the subscription. No arguments/values for an update given.", 'action-edd_update_subscription-success' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "Error updating the subscription.", 'action-edd_update_subscription-success' );
				}
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $subscription_id, $subscription, $sub_args, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.