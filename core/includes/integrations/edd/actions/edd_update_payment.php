<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_update_payment' ) ) :

	/**
	 * Load the edd_update_payment action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_update_payment {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_update_payment-description";

            $parameter = array(
				'payment_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the payment you want to update.', $translation_ident ) ),
				'payment_status'    => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The status of the payment. Please see the description for further details.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Within the data array, you will find further details about the response, as well as the payment id and further information.', $translation_ident ) ),
				'errors'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) An array containing all errors that might happened during the update.', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The payment was successfully updated or no changes have been made.',
                'data' => 
                array (
                  'payment_id' => 749,
                  'payment_status' => 'processing',
                ),
                'errors' => 
                array (
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

?>

<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to update a payment for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_update_payment</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_update_payment</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_update_payment</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>payment_id</strong> argument. Please set the id of the payment you want to update.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD payment.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "Since this webhook action is very versatile, it is highly recommended to check out the <strong>Special Arguments list down below</strong>.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "payment_status", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to update the status of the payment. Here is a list of the default payment statuses you can use:", $translation_ident ); ?>
<ol>
    <?php foreach( $payment_statuses as $ps_slug => $ps_name ) : ?>
        <li>
            <strong><?php echo WPWHPRO()->helpers->translate( $ps_name, $translation_ident ); ?></strong>: <?php echo $ps_slug; ?>
        </li>
    <?php endforeach; ?>
</ol>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the edd_update_payment action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $payment_id, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$payment_id</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the newly updated payment.", $translation_ident ); ?>
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
                'action'            => 'edd_update_payment',
                'name'              => WPWHPRO()->helpers->translate( 'Update payment', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'update a payment', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to update a payment within Easy Digital Downloads.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $return_args = array(
				'success' => false,
				'msg' => WPWHPRO()->helpers->translate( "The payment was successfully updated or no changes have been made.", 'action-edd_update_payment-success' ),
				'data' => array(),
				'errors' => array(),
			);

			$payment_id     = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_id' ) );
			$payment_status     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_status' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $payment_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'Payment not updated. The argument payment_id cannot be empty.', 'action-edd_update_payment-failure' );
	
				return $return_args;
			}

			$payment_exists = edd_get_payment_by( 'id', $payment_id );

			if( empty( $payment_exists ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The payment id you tried to update, could not be fetched.', 'action-edd_update_payment-failure' );
	
				return $return_args;
			}

			$return_args['data']['payment_id'] = $payment_id;
			$return_args['data']['payment_status'] = $payment_status;

			if( ! empty( $payment_status ) ){
				$updates_status = edd_update_payment_status( $payment_id, $payment_status );
				if( ! empty( $updates_status ) ){
					$return_args['success'] = true;
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "There have been partial issues with updates", 'action-edd_update_payment-success' );
					$return_args['errors'][] = WPWHPRO()->helpers->translate( "There was an issue updating the payment status.", 'action-edd_update_payment-success' );
				}
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $payment_id, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.