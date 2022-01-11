<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_subscription' ) ) :

	/**
	 * Load the edd_create_subscription action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_subscription {

        public function is_active(){

            $is_active = defined( 'EDD_RECURRING_PRODUCT_NAME' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_create_subscription-description";

			$parameter = array(
				'expiration_date'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The date for the expiration of the subscription. Recommended format: 2021-05-25 11:11:11', $translation_ident ) ),
				'profile_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe.', $translation_ident ) ),
				'download_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the download you want to connect with the subscription.', $translation_ident ) ),
				'customer_email'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer. Please see the description for further details.', $translation_ident ) ),
				'period'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The billing period of the subscription. Please see the description for further details.', $translation_ident ) ),
				'initial_amount'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The amount for the initial payment. E.g. 39.97', $translation_ident ) ),
				'recurring_amount'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The recurring amount for the subscription. E.g. 19.97', $translation_ident ) ),
				'transaction_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) This is the unique ID of the initial transaction inside of the merchant processor, such as PayPal or Stripe.', $translation_ident ) ),
				'status'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The status of the given subscription. Please see the description for further details.', $translation_ident ) ),
				'created_date'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The date of creation of the subscription. Recommended format: 2021-05-25 11:11:11', $translation_ident ) ),
				'bill_times'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) This refers to the number of times the subscription will be billed before being marked as Completed and payments stopped. Enter 0 if payments continue indefinitely.', $translation_ident ) ),
				'parent_payment_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) Use this argument to connect the subscription with an already existing payment. Otherwise, a new one is created. Please see the description for further details.', $translation_ident ) ),
				'customer_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the customer you want to connect. If it is not given, we try to fetch the user from the customer_email argument. Please see the description for further details.', $translation_ident ) ),
				'customer_first_name'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer. Please see the description for further details.', $translation_ident ) ),
				'customer_last_name'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer. Please see the description for further details.', $translation_ident ) ),
				'edd_price_option'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The variation id for a download price option. Please see the description for further details.', $translation_ident ) ),
				'gateway'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The gateway you want to use for your subscription (and maybe payment). Please see the description for further details.', $translation_ident ) ),
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

			$returns_code = array (
				'success' => true,
				'msg' => 'The subscription was successfully created.',
				'data' => 
				array (
				  'subscription_id' => '23',
				  'payment_id' => 843,
				  'customer_id' => 8,
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
$default_subscription_statuses = apply_filters( 'wpwh/descriptions/actions/edd_create_subscription/default_subscription_statuses', $default_subscription_statuses );
$beautified_subscription_statuses = json_encode( $default_subscription_statuses, JSON_PRETTY_PRINT );

$default_subscription_periods = array (
    'day' => __( 'Daily', 'edd-recurring' ),
    'week' => __( 'Weekly', 'edd-recurring' ),
    'month' => __( 'Monthly', 'edd-recurring' ),
    'quarter' => __( 'Quarterly', 'edd-recurring' ),
    'semi-year' => __( 'Semi-Yearly', 'edd-recurring' ),
    'year' => __( 'Yearly', 'edd-recurring' ),
);
$default_subscription_periods = apply_filters( 'wpwh/descriptions/actions/edd_create_subscription/default_subscription_periods', $default_subscription_periods );
$beautified_subscription_periods = json_encode( $default_subscription_periods, JSON_PRETTY_PRINT );

$default_subscription_gateways = array ();
if( function_exists( 'edd_get_payment_gateways' ) ){
	foreach( edd_get_payment_gateways() as $gwslug => $gwdata ){
		$default_subscription_gateways[ $gwslug ] = ( isset( $gwdata['admin_label'] ) ) ? $gwdata['admin_label'] : $gwdata['checkout_label'];
	}
}
$default_subscription_gateways = apply_filters( 'wpwh/descriptions/actions/edd_create_subscription/default_subscription_gateways', $default_subscription_gateways );
$beautified_subscription_gateways = json_encode( $default_subscription_gateways, JSON_PRETTY_PRINT );

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a subscription for <strong>Easy Digital Downloads - Recurring</strong> within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_create_subscription</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_create_subscription</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_create_subscription</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>expiration_date</strong> argument. Please set it to the date of expiration. Further details are available down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "The third argument you need to set is <strong>profile_id</strong>. The profile id is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe. Further details are available down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also a requirement to define the <strong>product_id</strong> argument. Please set it to the id of the download you want to connect with the subscription. Further details are available down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "The last required argument is the <strong>customer_email</strong> argument. Please set it to the email of the customer this subscription is for, or leave it empty if you want to create a new payment. Further details are available down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Please also set the <strong>period</strong> argument to the frequency you want to run the subscription it. Please see the <strong>Special Arguments</strong> section down below for further details.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD Subscription.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "Creating a subscription will also create a payment, except you define the <strong>parent_payment_id</strong> argument.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Creating the subscription will also create a customer from the given email address of the <strong>customer_email</strong> argument, except you set the <strong>customer_id</strong> argument.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "expiration_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats. Please note that in case you set the <strong>status</strong> argument to <strong>trialling</strong>, this date field will be ignored since we will calculate the expiration date based on the in the product given trial period.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "profile_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This is the unique ID of the subscription in the merchant processor, such as PayPal or Stripe. It accepts any kind of string.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_email", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts the email of the customer you create the subscription for. In case we could not find a customer with your given data, it will be created. Please note that creating a customer does not automatically create a user within your WordPress system.", $translation_ident ); ?>
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
<?php echo WPWHPRO()->helpers->translate( "Please note that in case you choose <strong>trialling</strong> as a subscription status, we will automatically apply the given trial period instead of the given expiration date from the <strong>expiration_date</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "created_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a date string what contains the date of expiration of the subscription. As a format, we recommend the SQL format (2021-05-25 11:11:11), but it also accepts other formats. Please note that in case you set the <strong>status</strong> argument to <strong>trialling</strong>, this argument will influence the expiration date of the trial perdod, which is defined within the download itself.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "parent_payment_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to connect your subscription with an already existing payment. Please note that if you set this argument, the <strong>gateway</strong> argument is ignored since the gateway will be based on the gateway of the payment you try to add. If you do not set this argument, we will create a payment automatically for you.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to connect an already existing customer with your newly created subscription. Please use the customer id and not the user id since these are different things. Please note, that in case you leave this argument empty, we will first try to find an existing customer based on your given email within the <strong>customer_email</strong> argument, and if we cannot find any customer, we will create one for you based on the given email within the <strong>customer_email</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_first_name", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add a first name to the customer in case it does not exist at that point. If we could find a customer to your given email or the cucstomer id, this argument is ignored. It is only used once a new customer is created. If it is not set, we will use the email as the default name.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_last_name", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add a last name to the customer in case it does not exist at that point. If we could find a customer to your given email or the cucstomer id, this argument is ignored. It is only used once a new customer is created. If it is not set, we will use the email as the default name.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "edd_price_option", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you work with multiple price options, please define the chosen price option for your download here. Please note, that the price option needs to be available within the download you chose for the <strong>download_id</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "gateway", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Define the gateway you want to use for this subscription. Please note that if you set the <strong>parent_payment_id</strong> argument, the gateway of the payment is used and this argument is ignored. Please use the slug of the gateway (e.g. <strong>paypal</strong>). Here is a list of all currently available gateways:", $translation_ident ); ?>
<pre><?php echo $beautified_subscription_gateways; ?></pre>
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
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_create_subscription</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 5 );
function my_custom_callback_function( $subscription_id, $subscription, $payment, $customer, $return_args ){
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
        <strong>$payment</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An object of the EDD_Payment() class with the current related payment.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$customer</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An object of the EDD_Recurring_Subscriber() class with the current related customer.", $translation_ident ); ?>
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
                'action'            => 'edd_create_subscription',
                'name'              => WPWHPRO()->helpers->translate( 'Create subscription', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'create a subscription', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to create a subscription within Easy Digital Downloads - Recurring.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $subscription_id = 0;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'subscription_id' => 0,
					'payment_id' => 0,
					'customer_id' => 0,
				),
			);

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
			$parent_payment_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_payment_id' );
			$customer_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_id' );
			$customer_email   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$customer_first_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_first_name' );
			$customer_last_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_last_name' );
			$edd_price_option   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_price_option' );
			$gateway   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway' );
			$initial_tax_rate   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_tax_rate' );
			$initial_tax   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'initial_tax' );
			$recurring_tax_rate   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_tax_rate' );
			$recurring_tax   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring_tax' );
			$notes   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'notes' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Subscription' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_Subscription() does not exist. The subscription was not created.', 'action-edd_create_subscription-failure' );
				return $return_args;
			}

			if( empty( $expiration_date ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The expiration_date argument cannot be empty. ', 'action-edd_create_subscription-failure' );
				return $return_args;
			}

			if( empty( $profile_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The profile_id argument cannot be empty. ', 'action-edd_create_subscription-failure' );
				return $return_args;
			}

			if( empty( $customer_email ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The customer_email argument cannot be empty. ', 'action-edd_create_subscription-failure' );
				return $return_args;
			}

			if( empty( $download_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The download_id argument cannot be empty. ', 'action-edd_create_subscription-failure' );
				return $return_args;
			}

			if( empty( $period ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The period argument cannot be empty. ', 'action-edd_create_subscription-failure' );
				return $return_args;
			}

			if( empty( $initial_amount ) ){
				$initial_amount = 0; //set it default to 0
			}

			if( empty( $recurring_amount ) ){
				$recurring_amount = 0; //set it default to 0
			}

			if( empty( $customer_first_name ) && empty( $customer_last_name ) ) {
				$customer_name = $customer_email;
			} else {
				$customer_name = trim( $customer_first_name . ' ' . $customer_last_name );
			}

			if( ! empty( $created_date ) ) {
				$created_date = date( 'Y-m-d ' . date( 'H:i:s', current_time( 'timestamp' ) ), strtotime( $created_date, current_time( 'timestamp' ) ) );
			} else {
				$created_date = date( 'Y-m-d H:i:s',current_time( 'timestamp' ) );
			}

			//try to fetch the customer
			if( empty( $customer_id ) ){
				$tmpcustomer = EDD()->customers->get_customer_by( 'email', $customer_email );
				if( isset( $tmpcustomer->id ) && ! empty( $tmpcustomer->id ) ) {
					$customer_id = $tmpcustomer->id;
				}
			}

			if( ! empty( $customer_id ) ) {

				$customer    = new EDD_Recurring_Subscriber( absint( $customer_id ) );
				$customer_id = $customer->id;
				$email       = $customer->email;
		
			} else {
		
				$email       = sanitize_email( $customer_email );
				$user        = get_user_by( 'email', $email );
				$user_id     = $user ? $user->ID : 0;
				$customer    = new EDD_Recurring_Subscriber;
				$customer_id = $customer->create( array( 'email' => $email, 'user_id' => $user_id, 'name' => $customer_name ) );
		
			}
		
			$customer_id = absint( $customer_id );
		
			if( ! empty( $parent_payment_id ) ) {
		
				$payment_id = absint( $parent_payment_id );
				$payment    = new EDD_Payment( $payment_id );
		
			} else {
		
				$options = array();
				if ( ! empty( $edd_price_option ) ) {
					$options['price_id'] = absint( $edd_price_option );
				}
		
				$payment = new EDD_Payment;
				$payment->add_download( absint( $download_id ), $options );
				$payment->customer_id = $customer_id;
				$payment->email       = $email;
				$payment->user_id     = $customer->user_id;
				$payment->gateway     = sanitize_text_field( $gateway );
				$payment->total       = edd_sanitize_amount( sanitize_text_field( $initial_amount ) );
				$payment->date        = $created_date;
				$payment->status      = 'pending';
				$payment->save();
				$payment->status = 'complete';
				$payment->save();

				$payment_id = absint( $payment->ID );
			}

			$sub_args = array(
				'expiration'        => date( 'Y-m-d 23:59:59', strtotime( $expiration_date, current_time( 'timestamp' ) ) ),
				'created'           => date( 'Y-m-d H:i:s', strtotime( $created_date, current_time( 'timestamp' ) ) ),
				'status'            => sanitize_text_field( $status ),
				'profile_id'        => sanitize_text_field( $profile_id ),
				'transaction_id'    => sanitize_text_field( $transaction_id ),
				'initial_amount'    => edd_sanitize_amount( sanitize_text_field( $initial_amount ) ),
				'recurring_amount'  => edd_sanitize_amount( sanitize_text_field( $recurring_amount ) ),
				'bill_times'        => absint( $bill_times ),
				'period'            => sanitize_text_field( $period ),
				'parent_payment_id' => $payment_id,
				'product_id'        => absint( $download_id ),
				'price_id'          => absint( $edd_price_option ),
				'customer_id'       => $customer_id,
			);

			//these arguments are added extra on top of the default "Add subscription function just to keep it compliant with the default EDD logic
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

			//Add trial period
			if( sanitize_text_field( $status ) === 'trialling' ){
				if( ! empty( $edd_price_option ) ){
					$trial_period = edd_recurring()->get_trial_period( $download_id, $edd_price_option );
				} else {
					$trial_period = edd_recurring()->get_trial_period( $download_id );
				}
				if( ! empty( $trial_period ) ){
					$sub_args['trial_period'] = '+' . $trial_period['quantity'] . ' ' . $trial_period['unit'];

					if( ! empty( $created_date ) ){
						$sub_args['expiration'] = date( 'Y-m-d 23:59:59', strtotime( $sub_args['trial_period'], strtotime( $created_date, current_time( 'timestamp' ) ) ) );
					} else {
						$sub_args['expiration'] = date( 'Y-m-d 23:59:59', strtotime( $sub_args['trial_period'], current_time( 'timestamp' ) ) );
					}
					
				}
			}

			$subscription = new EDD_Subscription;
			$check = $subscription->create( $sub_args );

			if( $check ){
				if( 'trialling' === $subscription->status ) {
					$customer->add_meta( 'edd_recurring_trials', $subscription->product_id );
				}

				if( ! empty( $notes ) ){
					if( WPWHPRO()->helpers->is_json( $notes ) ){
						$notes_arr = json_decode( $notes, true );
						foreach( $notes_arr as $snote ){
							$subscription->add_note( $snote );
						}
					}
				}
			
				$payment->update_meta( '_edd_subscription_payment', true );
	
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscription was successfully created.", 'action-edd_create_subscription-success' );
				$return_args['success'] = true;
				$return_args['data']['subscription_id'] = $subscription->id;
				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['customer_id'] = $customer_id;
				$subscription_id = $subscription->id;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error creating the subscription.", 'action-edd_create_subscription-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $subscription_id, $subscription, $payment, $customer, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.