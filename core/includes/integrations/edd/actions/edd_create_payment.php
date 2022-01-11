<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_payment' ) ) :

	/**
	 * Load the edd_create_payment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_payment {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_create_payment-description";

            $parameter = array(
				'customer_email'       			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer you want to associate with the payment. Please see the description for further details.', $translation_ident ) ),
				'discounts'    					=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comma-separated list of discount codes. Please see the description for further details.', $translation_ident ) ),
				'gateway'    					=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of the currently used gateway. Please see the description for further details. Default empty.', $translation_ident ) ),
				'currency'    					=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The currency code of the payment. Default is your default currency. Please see the description for further details.', $translation_ident ) ),
				'parent_payment_id'    			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The payment id of a parent payment.', $translation_ident ) ),
				'payment_status'    			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The status of the payment. Default is "pending". Please see the description for further details.', $translation_ident ) ),
				'product_data'    				=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string, containing all the product data and options. Please refer to the description for examples and further details.', $translation_ident ) ),
				'edd_agree_to_terms'    		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Defines if a user agreed to the terms. Set it to "yes" to mark the user as agreed. Default: no', $translation_ident ) ),
				'edd_agree_to_privacy_policy'	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Defines if a user agreed to the privacy policy. Set it to "yes" to mark the user as agreed. Default: no', $translation_ident ) ),
				'payment_date'    				=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set a custom payment date. The format is flexible, but we recommend SQL format.', $translation_ident ) ),
				'user_id'    					=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The user id of the WordPress user. If not defined, we try to fetch the id using the customer_email.', $translation_ident ) ),
				'customer_first_name'    		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer. Please see the description for further details.', $translation_ident ) ),
				'customer_last_name'    		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer. Please see the description for further details.', $translation_ident ) ),
				'customer_country'    			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The country code of the customer.', $translation_ident ) ),
				'customer_state'    			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The state of the customer.', $translation_ident ) ),
				'customer_zip'    				=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The zip of the customer.', $translation_ident ) ),
				'send_receipt'    				=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set it to "yes" for sending out a receipt to the customer. Default "no". Please see the description for further details.', $translation_ident ) ),
				'do_action'     				=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        	=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Within the data array, you will find further details about the response, as well as the payment id and further information.', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The payment was successfully created.',
                'data' => 
                array (
                  'payment_id' => 747,
                  'payment_data' => 
                  array (
                    'purchase_key' => 'aa10bc587fb544b10c01fe13905fba74',
                    'user_email' => 'jondoe@test.test',
                    'user_info' => 
                    array (
                      'id' => 0,
                      'email' => 'jondoe@test.test',
                      'first_name' => 'Jannis',
                      'last_name' => 'Testing',
                      'discount' => false,
                      'address' => 
                      array (
                        'country' => 'AE',
                        'state' => false,
                        'zip' => false,
                      ),
                    ),
                    'gateway' => 'paypal',
                    'currency' => 'EUR',
                    'cart_details' => 
                    array (
                      0 => 
                      array (
                        'id' => 176,
                        'quantity' => 1,
                        'item_price' => 49,
                        'tax' => 5,
                        'discount' => 4,
                        'fees' => 
                        array (
                          0 => 
                          array (
                            'label' => 'Custom Fee',
                            'amount' => 10,
                            'type' => 'fee',
                            'id' => '',
                            'no_tax' => false,
                            'download_id' => 435,
                          ),
                        ),
                        'item_number' => 
                        array (
                          'options' => 
                          array (
                            'price_id' => NULL,
                          ),
                        ),
                      ),
                    ),
                    'parent' => false,
                    'status' => 'publish',
                    'post_date' => '2020-04-23 00:00:00',
                  ),
                ),
            );

			ob_start();
//load default edd statuses
$payment_statuses = array(
    'pending'   => __( 'Pending', 'easy-digital-downloads' ),
    'publish'   => __( 'Complete', 'easy-digital-downloads' ),
    'refunded'  => __( 'Refunded', 'easy-digital-downloads' ),
    'failed'    => __( 'Failed', 'easy-digital-downloads' ),
    'abandoned' => __( 'Abandoned', 'easy-digital-downloads' ),
    'revoked'   => __( 'Revoked', 'easy-digital-downloads' ),
    'processing' => __( 'Processing', 'easy-digital-downloads' )
);

if( function_exists( 'edd_get_payment_statuses' ) ){
    $payment_statuses = array_merge( $payment_statuses, edd_get_payment_statuses() );
}
$payment_statuses = apply_filters( 'wpwh/descriptions/actions/edd_create_payment/payment_statuses', $payment_statuses );

$default_cart_details = array (
    array (
      'id' => 176,
      'quantity' => 1,
      'item_price' => 49,
      'tax' => 5,
      'discount' => 4,
      'fees' => 
      array (
        array (
          'label' => 'Custom Fee',
          'amount' => 10,
          'type' => 'fee',
          'id' => '',
          'no_tax' => false,
          'download_id' => 435,
        ),
      ),
      'item_number' => 
      array (
        'options' => 
        array (
          'price_id' => NULL,
        ),
      ),
    ),
);
$default_cart_details = apply_filters( 'wpwh/descriptions/actions/edd_create_payment/default_cart_details', $default_cart_details );

$beautified_cart_details = json_encode( $default_cart_details, JSON_PRETTY_PRINT );

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a payment for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_create_payment</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_create_payment</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_create_payment</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the customer_email argument. Please set it to the email of the person you want to assign to the payment.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD payment.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "This webhook action is very versatile. Depending on your active extensions of the plugin, you will see different arguments and descriptions. This way, we can always provide you personalized features based on your active plugins.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "To run the logic, we use the EDD default function for inserting payments: edd_insert_payment() - you can therefore use all the features available for the function.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_email", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The customer email is the email address of the customer you want to associate with the payment. In case there is no existing EDD customer with this email available, EDD will create one. (An EDD customer is not the same as a WordPress user. There is no WordPRess user created by simply defining the email.) To associate a WordPress user with the EDD customer, please check out the <strong>user_id</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "discounts", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a single discount code or a comma-separated list of multiple discount codes. Down below, you will find an example on how to use multiple discount codes. <strong>Please note</strong>: This only adds the discount code to the payment, but it does not affect the pricing. If you want to apply the discounts to the payment pricing, you need to use the discount key within the <strong>product_data</strong> line item argument.", $translation_ident ); ?>
<pre>10PERCENTOFF,EASTERDISCOUNT10</pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "gateway", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The slug of the gateway you want to use. Down below, you will find further details on the available default gateways:", $translation_ident ); ?>
<ol>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "PayPal Standard", $translation_ident ); ?></strong>: paypal
    </li>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "Test Payment", $translation_ident ); ?></strong>: manual
    </li>
    <li>
        <strong><?php echo WPWHPRO()->helpers->translate( "Amazon", $translation_ident ); ?></strong>: amazon
    </li>

    <?php do_action( 'wpwh/descriptions/actions/edd_create_payment/after_gateway_items' ) ?>

</ol>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "currency", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The currency code of the currency you want to use for this payment. You can set it to e.g. <strong>EUR</strong> or <strong>USD</strong>. If you leave it empty, we use your default currency. ( edd_get_currency() )", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "payment_status", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to set a custom payment status. Down below, you will find a list of all available, default payment names and its slugs. To make this argument work, please define the slug of the status you want. If you don't define any, <strong>pending</strong> is used.", $translation_ident ); ?>
<ol>
    <?php foreach( $payment_statuses as $ps_slug => $ps_name ) : ?>
        <li>
            <strong><?php echo WPWHPRO()->helpers->translate( $ps_name, $translation_ident ); ?></strong>: <?php echo $ps_slug; ?>
        </li>
    <?php endforeach; ?>
</ol>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "product_data", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a JSON formatted String, which contains all the downloads you want to add, including further details about the pricing. Due to the complexity of the string, we explained each section of the following JSON down below. The JSON below contains a list with one product which is added to your payment details. They also determine the pricing of the payment and other information.", $translation_ident ); ?>
<pre><?php echo $beautified_cart_details; ?></pre>
<?php echo WPWHPRO()->helpers->translate( "The above JSON adds a single download to the payment. If you want to add multiple products, simply add another entry within the [] brackets. HEre are all the values explained:", $translation_ident ); ?>
<ol>

  <li>
    <strong>id</strong> (<?php echo WPWHPRO()->helpers->translate( "Required", $translation_ident ); ?>)<br>
    <?php echo WPWHPRO()->helpers->translate( "This is the download id within WordPress.", $translation_ident ); ?>
  </li>
  
  <li>
    <strong>quantity</strong> (<?php echo WPWHPRO()->helpers->translate( "Required", $translation_ident ); ?>)<br>
    <?php echo WPWHPRO()->helpers->translate( "The number of how many times this product should be added.", $translation_ident ); ?>
  </li>

  <li>
    <strong>item_price</strong> (<?php echo WPWHPRO()->helpers->translate( "Required", $translation_ident ); ?>)<br>
    <?php echo WPWHPRO()->helpers->translate( "The price of the product you want to add", $translation_ident ); ?>
  </li>

  <li>
    <strong>tax</strong> (<?php echo WPWHPRO()->helpers->translate( "Required", $translation_ident ); ?>)<br>
    <?php echo WPWHPRO()->helpers->translate( "The amount of tax that should be added to the item_price", $translation_ident ); ?>
  </li>

  <li>
    <strong>discount</strong><br>
    <?php echo WPWHPRO()->helpers->translate( "The amount of discount that should be removed from the item_price", $translation_ident ); ?>
  </li>

  <li>
    <strong>fees</strong><br>
    <?php echo WPWHPRO()->helpers->translate( "Fees are extra prices that are added on top of the product price. Usually this is set for signup fees or other prices that are not directly related with the download. The values set within the fees are all optional, but recommended to be available within the JSON.", $translation_ident ); ?>
  </li>

  <li>
    <strong>item_number</strong><br>
    <?php echo WPWHPRO()->helpers->translate( "The item number contains variation related data about the product. In case you want to add a variation, you can define the price id there.", $translation_ident ); ?>
  </li>

  <?php do_action( 'wpwh/descriptions/actions/edd_create_payment/after_cart_details_items', $default_cart_details ); ?>

</ol>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "send_receipt", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The send_receipt argument allows you to send out the receipt for the payment you just made. Please note that this logic uses the EDD default functionality. The receipt is only send based on the given payment status.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_first_name", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Please not that defining the customer first name (or last name) are only affecting the custoemr in case it doesn't exist at that point. For existing customers, the first and last name is not updated.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_last_name", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Please not that defining the customer last name (or first name) are only affecting the custoemr in case it doesn't exist at that point. For existing customers, the first and last name is not updated.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the edd_create_payment action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $payment_id, $purchase_data, $send_receipt, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$payment_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the newly created payment.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$purchase_data</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array that contins the validated payment data we sent over to the edd_insert_payment() function", $translation_ident ); ?>
    </li>
    <li>
        <strong>$send_receipt</strong> (string)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "A boolean value of wether the receipt should be sent (if applicable) or not.", $translation_ident ); ?>
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
                'action'            => 'edd_create_payment',
                'name'              => WPWHPRO()->helpers->translate( 'Create payment', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'create a payment', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to create a payment within Easy Digital Downloads.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $edd_helpers = WPWHPRO()->integrations->get_helper( 'edd', 'edd_helpers' );
            $return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$purchase_key     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'purchase_key' );
			$discounts     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'discounts' );
			$gateway     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway' );
			$parent_payment_id     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_payment_id' );
			$currency     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'currency' );
			$payment_status     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_status' );
			$product_data     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_data' );
			$edd_agree_to_terms     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_agree_to_terms' ) === 'yes' ) ? true : false;
			$edd_agree_to_privacy_policy     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'edd_agree_to_privacy_policy' ) === 'yes' ) ? true : false;
			$payment_date     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_date' );

			$user_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$customer_email     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$customer_first_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_first_name' );
			$customer_last_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_last_name' );
			$customer_country     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_country' );
			$customer_state     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_state' );
			$customer_zip     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_zip' );

			$send_receipt     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_receipt' ) === 'yes' ) ? true : false;
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) && ! empty( $customer_email ) ){
				$wp_user = get_user_by( 'email', sanitize_email( $customer_email ) );
				if ( ! empty( $wp_user ) ) {
					$user_id = $wp_user->ID;
				}
			}

			$user_info = array(
				'id'            => $user_id,
				'email'         => $customer_email,
				'first_name'    => $customer_first_name,
				'last_name'     => $customer_last_name,
				'discount'      => $discounts,
				'address'		=> array(
					'country'	=> $customer_country,
					'state'	=> $customer_state,
					'zip'	=> $customer_zip,
				)
			);

			$product_details = array();
			if( ! empty( $product_data ) && WPWHPRO()->helpers->is_json( $product_data ) ){
				$product_details = json_decode( $product_data, true );
			}

			$purchase_data = array(
				'purchase_key'  => ( ! empty( $purchase_key ) ) ? $purchase_key : strtolower( md5( uniqid() ) ),
				'user_email'    => $customer_email,
				'user_info'     => $user_info,
				'gateway'     	=> ( ! empty( $gateway ) ) ? $gateway : '',
				'currency'      => ( ! empty( $currency ) ) ? $currency : edd_get_currency(),
				'cart_details'  => $product_details,
				'parent'        => $parent_payment_id,
				'status'        => 'pending',
			);

			if ( ! empty( $payment_date ) ) {
				$purchase_data['post_date'] = date( "Y-m-d H:i:s", strtotime( $payment_date ) );
			}

			if ( ! empty( $edd_agree_to_terms ) ) {
				$purchase_data['agree_to_terms_time'] = current_time( 'timestamp' );
			}

			if ( ! empty( $edd_agree_to_privacy_policy ) ) {
				$purchase_data['agree_to_privacy_time'] = current_time( 'timestamp' );
			}

			$purchase_data = apply_filters( 'wpwh/actions/edd_create_payment/purchase_data', $purchase_data, $payment_status, $send_receipt );

			//Validate required fields
			$valid_payment_data = $edd_helpers->validate_payment_data( $purchase_data );
			if( ! $valid_payment_data['success'] ){

				$valid_payment_data['msg'] = WPWHPRO()->helpers->translate( "Your payment was not created. Please check the errors for further details.", 'action-edd_create_payment-failure' );

				return $valid_payment_data;
			}

			if( ! $send_receipt ){
				remove_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999 );

				// if we're using EDD Per Product Emails, prevent the custom email from being sent
				if ( class_exists( 'EDD_Per_Product_Emails' ) ) {
					remove_action( 'edd_complete_purchase', 'edd_ppe_trigger_purchase_receipt', 999, 1 );
				}
			}

			$payment_id = edd_insert_payment( $purchase_data );

			//Make sure the status is updated after
			if( $payment_id && ! empty( $payment_status ) && $payment_status !== 'pending' ){
				edd_update_payment_status( $payment_id, $payment_status );
			}


			if( ! $send_receipt ){
				add_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999, 3 );

				// if we're using EDD Per Product Emails, prevent the custom email from being sent
				if ( class_exists( 'EDD_Per_Product_Emails' ) ) {
					add_action( 'edd_complete_purchase', 'edd_ppe_trigger_purchase_receipt', 999, 1 );
				}
			}

			if( ! empty( $payment_id ) ){

				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['payment_data'] = $purchase_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The payment was successfully created.", 'action-edd_create_payment-success' );
				$return_args['success'] = true;

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "No payment was created.", 'action-edd_create_payment-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $payment_id, $purchase_data, $send_receipt, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.