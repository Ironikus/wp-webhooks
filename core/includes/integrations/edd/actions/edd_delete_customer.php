<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_customer' ) ) :

	/**
	 * Load the edd_delete_customer action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_customer {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_delete_customer-description";

            $parameter = array(
				'customer_value'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The actual value you want to use to determine the customer. In case you havent set the get_customer_by argument or you set it to email, place the customer email in here.', $translation_ident ) ),
				'get_customer_by'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The type of value you want to use to fetch the customer from the database. Possible values: email, customer_id, user_id. Default: email', $translation_ident ) ),
				'delete_records'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to "yes" if you want to delete all of the customer records (payments) from the database. More info is within the description.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'customer_id'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the customer', $translation_ident ) ),
				'get_customer_by'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The type of value you want to use to fetch the customer from the database. Possible values: email, customer_id, user_id. Default: email', $translation_ident ) ),
				'customer_value'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The additional emails you set within the additional_emails argument.', $translation_ident ) ),
				'delete_records'        => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to "yes" if you want to delete all of the customer records (payments) from the database.', $translation_ident ) ),
				'customer_data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The Data from the EDD_Customer class.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The customer was successfully deleted.',
				'customer_id' => '5',
				'get_customer_by' => 'email',
				'customer_value' => 'jondoe@domain.test',
				'delete_records' => false,
				'customer_data' => 
				array (
				),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete a customer for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_delete_customer</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_delete_customer</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_delete_customer</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "As a second argument, you need to set the actual data you want to use for fetching the user. If you have chosen nothing or <strong>email</strong> for the <strong>get_customer_by</strong> argument, you need to include the customers email address here.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the update process of the EDD customer.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "Deleting a customer is not the same as deleting a user. Easy Digital Downloads uses its own logic and tables for customers.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can also delete all related payment records assigned to a customer. To do that, simply set the <strong>delete_records</strong> argument to <strong>yes</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Since this webhook action is very versatile, it is highly recommended to check out the <strong>Special Arguments list down below</strong>.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "customer_value", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The value we use to determine the customer. In case you haven't set the <strong>get_user_by</strong> argument or you have set it to email, please include the customer email in here. If you have chosen the <strong>customer_id</strong>, please include the customer id and in case you set <strong>user_id</strong>, please include the user id.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "get_customer_by", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Customize the default way we use to fetch the customer from the backend. Possible values are <strong>email</strong> (Default), <strong>customer_id</strong> or <strong>user_id</strong>.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "delete_records", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to delete payments assigned to a customer. In case you haven't set it to <strong>yes</strong>, we only remove the user correlation to the payment.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_delete_customer</strong> action was fired (It also fires if the customer was not successfully deleted, but you can check if the user id is set or not to determine if it worked).", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $customer_id, $customer, $return_args ){
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
        <strong>$customer</strong> (object)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "The customer object from the EDD EDD_Customer class.", $translation_ident ); ?>
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
                'action'            => 'edd_delete_customer',
                'name'              => WPWHPRO()->helpers->translate( 'Delete customer', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'delete a customer', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to delete a customer within Easy Digital Downloads.', $translation_ident ),
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
				'get_customer_by' => '',
				'customer_value' => '',
				'delete_records' => '',
				'customer_data' => '',
			);

			$get_customer_by   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'get_customer_by' );
			$customer_value     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'customer_value' );
			$delete_records     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'delete_records' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Customer' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_Customer() is undefined. The user could not be deleted.', 'action-edd_delete_customer-failure' );
	
				return $return_args;
			}

			if( empty( $customer_value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'User not deleted. The argument customer_value cannot be empty.', 'action-edd_delete_customer-failure' );
	
				return $return_args;
			}

			switch( $get_customer_by ){
				case 'customer_id':
					$customer = new EDD_Customer( intval( $customer_value ) );
				break;
				case 'user_id':
					$customer = new EDD_Customer( intval( $customer_value ), true );
				break;
				case 'email':
				default:
					$customer = new EDD_Customer( $customer_value );
				break;
			}

			if ( empty( $customer ) || empty( $customer->id ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The user you tried to delete does not exist.', 'action-edd_delete_customer-failure' );
				return $return_args;
			}

			$customer_id = $customer->id;
			do_action( 'edd_pre_delete_customer', $customer_id, true, $delete_records ); //confirm is always true

			$payments_array = explode( ',', $customer->payment_ids );
			$success        = EDD()->customers->delete( $customer_id );

			if ( $success ) {

				if ( $delete_records ) {

					// Remove all payments, logs, etc
					foreach ( $payments_array as $payment_id ) {
						edd_delete_purchase( $payment_id, false, true );
					}

				} else {

					// Just set the payments to customer_id of 0
					foreach ( $payments_array as $payment_id ) {
						edd_update_payment_meta( $payment_id, '_edd_payment_customer_id', 0 );
					}

				}

				$return_args['customer_id'] = $customer_id;
				$return_args['get_customer_by'] = $get_customer_by;
				$return_args['customer_value'] = $customer_value;
				$return_args['delete_records'] = $delete_records;
				$return_args['customer_data'] = $customer;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The customer was successfully deleted.", 'action-edd_delete_customer-success' );
				$return_args['success'] = true;

			} else {

				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error deleting the customer. (EDD error)", 'action-edd_delete_customer-success' );

			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $customer_id, $customer, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.