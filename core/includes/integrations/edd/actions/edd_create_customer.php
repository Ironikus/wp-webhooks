<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_customer' ) ) :

	/**
	 * Load the edd_create_customer action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_customer {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_create_customer-description";

            $parameter = array(
				'customer_email'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer you want to create. In case the user already exists, we do not update it.', $translation_ident ) ),
				'customer_first_name'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name of the customer.', $translation_ident ) ),
				'customer_last_name'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name of the customer.', $translation_ident ) ),
				'additional_emails'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comma-separated list of additional email addresses. Please check the description for further details.', $translation_ident ) ),
				'attach_payments'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comma-, and doublepoint-separated list of payment ids you want to assign to the user. Please check the description for further details.', $translation_ident ) ),
				'increase_purchase_count'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) increase the purchase count for the customer.', $translation_ident ) ),
				'increase_lifetime_value'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The price you want to add to the lifetime value of the customer. Please check the description for further details.', $translation_ident ) ),
				'set_primary_email'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email you want to set as the new primary email. Default: customer_email', $translation_ident ) ),
				'customer_notes'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string containing one or multiple customer notes. Please check the description for further details.', $translation_ident ) ),
				'customer_meta'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string containing one or multiple customer meta data. Please check the description for further details.', $translation_ident ) ),
				'user_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The user id of the WordPress user you want to assign to the customer. Please read the description for further details.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-create-customer-content' ) ),
				'customer_id'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the customer', 'action-create-customer-content' ) ),
				'customer_email'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email you set within the customer_email argument.', 'action-create-customer-content' ) ),
				'additional_emails'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The additional emails you set within the additional_emails argument.', 'action-create-customer-content' ) ),
				'customer_first_name'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The first name you set within the customer_first_name argument.', 'action-create-customer-content' ) ),
				'customer_last_name'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The last name you set within the customer_last_name argument.', 'action-create-customer-content' ) ),
				'attach_payments'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The payment ids you set within the attach_payments argument.', 'action-create-customer-content' ) ),
				'increase_purchase_count'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The purchase count you set within the increase_purchase_count argument.', 'action-create-customer-content' ) ),
				'increase_lifetime_value'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The lifetime value you set within the increase_lifetime_value argument.', 'action-create-customer-content' ) ),
				'customer_notes'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The customer notes you set within the customer_notes argument.', 'action-create-customer-content' ) ),
				'customer_meta'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The customer meta you set within the customer_meta argument.', 'action-create-customer-content' ) ),
				'user_id'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The user id you set within the user_id argument.', 'action-create-customer-content' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The customer was successfully created.',
				'customer_id' => '5',
				'customer_email' => 'test@domain.com',
				'additional_emails' => 'second@domain.com,thir@domain.com',
				'customer_first_name' => 'John',
				'customer_last_name' => 'Doe',
				'attach_payments' => '747',
				'increase_purchase_count' => 2,
				'increase_lifetime_value' => '55.46',
				'customer_notes' => '["First Note 1","First Note 2"]',
				'customer_meta' => '{"meta_1": "test1","meta_2": "test2"}',
				'user_id' => 23,
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a customer for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_create_customer</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_create_customer</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_create_customer</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>customer_email</strong> argument. Please set it to the email of the person you want to add as a customer.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD customer.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "Creating a customer is not the same as creating a user. Easy Digital Downloads uses its own logic and tables for customers. Still, you can assign a user to a customer usign the <strong>user_id</strong> argument.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "In case the email you try to use, for adding the customer, already exists within the customer table, the customer won't be created.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Since this webhook action is very versatile, it is highly recommended to check out the <strong>Special Arguments list down below</strong>.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_email", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The customer email is the email address of the customer you want to associate with the newly created customer. In case there is no existing EDD customer with this email available, EDD will create one. (An EDD customer is not the same as a WordPress user. There is no WordPress user created by simply defining the email.) To associate a WordPress user with the EDD customer, please check out the <strong>user_id</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "additional_emails", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "You can add additional emails to a customer. To do that, simply comma-separate the emails within the field. The primary email address is always the <strong>customer_email</strong> argument. Here is an example:", $translation_ident ); ?>
<pre>jondoe@mydomain.com,anotheremail@domain.com</pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "attach_payments", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to connect certain payment ids to the user. To set multiple payments, please separate them with a comma. By default, it recalculates the total amount. If you do not want that, add <strong>:no_update_stats</strong> after the payment id. Here is an example:", $translation_ident ); ?>
<pre>125,365,444:no_update_stats,777</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above asigns the payment ids 125, 365, 444, 777 to the customer. It also assigns the payment id 444, but it does not update the statistics.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "increase_purchase_count", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This field accepts a number, which is added on top of the existing purchase count. If you are going to add three payments for a new customer, and you set this value to 1, your total purchase count will show 4.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "increase_lifetime_value", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This field accepts a decimalnumber, which is added on top of the existing lifetime value. If you are going to add one payment with a price of 20$ for a new customer, and you set this value to 5$, the total lifetime value will show 25$.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_notes", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to add one or multiple customer notes to the customer. This value accepts a JSON, containing one customer note per line. Here is an example:", $translation_ident ); ?>
<pre>[
  "First Note 1",
  "First Note 2"
]</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above adds two notes.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_meta", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add one or multiple customer meta values to your newly created customer, using a JSON string. Easy Digital Downloads uses a custom table for these meta values. Here are some examples on how you can use it:", $translation_ident ); ?>
<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><?php echo WPWHPRO()->helpers->translate( "Add/update meta values", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This JSON shows you how to add simple meta values for your customer.", $translation_ident ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "test2"
}</pre>
        <?php echo WPWHPRO()->helpers->translate( "The key is always the customer meta key. On the right, you always have the value for the customer meta value. In this example, we add two meta values to the customer meta. In case a meta key already exists, it will be updated.", $translation_ident ); ?>
    </li>
    <li class="list-group-item">
        <strong><?php echo WPWHPRO()->helpers->translate( "Delete meta values", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "You can also delete existing meta key by setting the value to <strong>ironikus-delete</strong>. This way, the meta will be removed. Here is an example:", $translation_ident ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "ironikus-delete"
}</pre>
        <?php echo WPWHPRO()->helpers->translate( "The example above will add the meta key <strong>meta_1</strong> with the value <strong>test1</strong> and it deletes the meta key <strong>meta_2</strong> including its value.", $translation_ident ); ?>
    </li>
    <li class="list-group-item">
        <strong><?php echo WPWHPRO()->helpers->translate( "Add/update/remove serialized meta values", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Sometimes, it is necessary to add serialized arrays to your data. Using the json below, you can do exactly that. You can use a simple JSON string as the meta value and we automatically convert it to a serialized array once you place the identifier <strong>ironikus-serialize</strong> in front of it. Here is an example:", $translation_ident ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "ironikus-serialize{\"test_key\":\"wow\",\"testval\":\"new\"}"
}</pre>
        <?php echo WPWHPRO()->helpers->translate( "This example adds a simple meta with <strong>meta_1</strong> as the key and <strong>test1</strong> as the value. The second meta value contains a json value with the identifier <strong>ironikus-serialize</strong> in the front. Once this value is saved to the database, it gets turned into a serialized array. In this example, it would look as followed: ", $translation_ident ); ?>
        <pre>a:2:{s:8:"test_key";s:3:"wow";s:7:"testval";s:3:"new";}</pre>
    </li>
</ul>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "user_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to assign a user to the Easy Digital Downloads customer. In case the user id is not defined, we will automatically try to match the primary email with a WordPress user.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_create_customer</strong> action was fired (It also fires if the user was not successfully created, but you can check if the user id is set or not to determine if it worked).", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $customer_id, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$customer_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The customer id of the newly created customer. 0 or false if something went wrong.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$return_args</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller. This includes all of in the request set data.", $translation_ident ); ?>
    </li>
</ol>
<?php
			$description = ob_get_clean();

            return array(
                'action'            => 'edd_create_customer',
                'name'              => WPWHPRO()->helpers->translate( 'Create customer', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'create a customer', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to create a customer within Easy Digital Downloads.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $customer_id = 0;
			$customer = new stdClass;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'customer_id' => 0,
				'customer_email' => '',
				'additional_emails' => '',
				'customer_first_name' => '',
				'customer_last_name' => '',
				'attach_payments' => '',
				'increase_purchase_count' => '',
				'increase_lifetime_value' => '',
				'customer_notes' => '',
				'customer_meta' => '',
				'user_id' => '',
			);

			$customer_email     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_email' );
			$customer_first_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_first_name' );
			$customer_last_name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_last_name' );
			$additional_emails     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'additional_emails' );
			$attach_payments     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'attach_payments' );
			$increase_purchase_count     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'increase_purchase_count' ) );
			$user_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$increase_lifetime_value     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'increase_lifetime_value' );
			$set_primary_email     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'set_primary_email' );
			$customer_notes     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_notes' );
			$customer_meta     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_meta' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Customer' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_Customer() is undefined. The user could not be created.', 'action-edd_create_customer-failure' );
	
				return $return_args;
			}

			if ( ! empty( $customer_email ) ) {
				$customer = new EDD_Customer( $customer_email );
			}

			if( empty( $customer->id ) ){

				if( empty( $customer_first_name ) && empty( $customer_last_name ) ) {
					$name = $customer_email;
				} else {
					$name = trim( $customer_first_name . ' ' . $customer_last_name );
				}
	
				$customer_data = array(
					'name'        => $name,
					'email'       => $customer_email
				);

				//tro to match a WordPress user with an email
				if( empty( $user_id ) && ! empty( $customer_email ) && is_email( $customer_email ) ){
					$wp_user = get_user_by( 'email', sanitize_email( $customer_email ) );
					if ( ! empty( $wp_user ) ) {
						$user_id = $wp_user->ID;
					}
				}

				if( ! empty( $user_id ) ){
					$customer_data['user_id'] = $user_id;
				}
	
				$customer_id = $customer->create( $customer_data );
				
				if( ! empty( $customer_id ) ){

					if( ! empty( $additional_emails ) ){
						$email_arr = explode( ',', $additional_emails );
						if( is_array( $email_arr ) ){
							foreach( $email_arr as $semail ){
								if( is_email( $semail ) ){
									$customer->add_email( $semail );
								}
							}
						}
					}

					if( ! empty( $set_primary_email ) && is_email( $set_primary_email ) ){
						$customer->set_primary_email( $set_primary_email );
					}

					if( ! empty( $attach_payments ) ){
						$payments_arr = explode( ',', $attach_payments );
						if( is_array( $payments_arr ) ){
							foreach( $payments_arr as $spayment ){
								$spayment_settings = explode( ':', $spayment );
								if( in_array( 'no_update_stats', $spayment_settings ) ){
									$customer->attach_payment( intval( $spayment_settings[0] ), false );
								} else {
									$customer->attach_payment( intval( $spayment_settings[0] ) );
								}
							}
						}
					}

					if( ! empty( $increase_purchase_count ) && is_numeric( $increase_purchase_count ) ){
						$customer->increase_purchase_count( $increase_purchase_count );
					}

					if( ! empty( $increase_lifetime_value ) && is_numeric( $increase_lifetime_value ) ){
						$customer->increase_value( $increase_lifetime_value );
					}

					if( ! empty( $customer_notes ) ){
						if( WPWHPRO()->helpers->is_json( $customer_notes ) ){
							$customer_notes_arr = json_decode( $customer_notes, true );
							foreach( $customer_notes_arr as $snote ){
								$customer->add_note( $snote );
							}
						}
					}

					if( ! empty( $customer_meta ) ){
						if( WPWHPRO()->helpers->is_json( $customer_meta ) ){
							$customer_meta_arr = json_decode( $customer_meta, true );
							foreach( $customer_meta_arr as $skey => $sval ){

								if( ! empty( $skey ) ){
									if( $sval == 'ironikus-delete' ){
										$customer->delete_meta( $skey );
									} else {
										$ident = 'ironikus-serialize';
										if( is_string( $sval ) && substr( $sval , 0, strlen( $ident ) ) === $ident ){
											$serialized_value = trim( str_replace( $ident, '', $sval ),' ' );

											if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
												$serialized_value = json_decode( $serialized_value );
											}

											$customer->update_meta( $skey, $serialized_value );

										} else {
											$customer->update_meta( $skey, maybe_unserialize( $sval ) );
										}
									}
								}
							}
						}
					}

					$return_args['customer_id'] = $customer_id;
					$return_args['customer_email'] = $customer_email;
					$return_args['additional_emails'] = $additional_emails;
					$return_args['customer_first_name'] = $customer_first_name;
					$return_args['customer_last_name'] = $customer_last_name;
					$return_args['attach_payments'] = $attach_payments;
					$return_args['increase_purchase_count'] = $increase_purchase_count;
					$return_args['increase_lifetime_value'] = $increase_lifetime_value;
					$return_args['customer_notes'] = $customer_notes;
					$return_args['customer_meta'] = $customer_meta;
					$return_args['user_id'] = $user_id;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The customer was successfully created.", 'action-edd_create_customer-success' );
					$return_args['success'] = true;
				} else {
					$return_args['customer_id'] = $customer_id;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured creating the user.", 'action-edd_create_customer-success' );
				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not create the customer. Please set the user_id or the customer_email.", 'action-edd_create_customer-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $customer_id, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.