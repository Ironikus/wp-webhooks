<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_discount' ) ) :

	/**
	 * Load the edd_delete_discount action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_discount {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_delete_discount-description";

            $parameter = array(
				'discount_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The dicsount ID or discount code of the discount you want to delete.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Containing the discount id of the deleted discount.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The discount code was successfully deleted.',
				'data' => 
				array (
				  'discount_id' => 803,
				),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete a discount code for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_delete_discount</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_delete_discount</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_delete_discount</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>discount_id</strong> argument. Please set it to either the discount id ot the discount code you want to delete.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the deletion of the EDD discount code.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you do not have the discount id, you can also use the discount code to delete the discount.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_delete_discount</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $discount_id, $discount, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$discount_id</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the newly created discount code.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$discount</strong> (array)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "An array that contains the validated discount data we sent over to the EDD_Discounts() class", $translation_ident ); ?>
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
                'action'            => 'edd_delete_discount',
                'name'              => WPWHPRO()->helpers->translate( 'Delete discount', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'delete a discount', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to delete a dicsount code within Easy Digital Downloads.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $discount = new stdClass;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'discount_id' => 0,
				),
			);

			$discount_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'discount_id' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! function_exists( 'edd_get_discount_by_code' ) && ! function_exists( 'edd_get_discount_by' ) && ! function_exists( 'edd_remove_discount' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The functions edd_remove_discount() and edd_get_discount_by() are undefined. The discount code could not be deleted.', 'action-edd_delete_discount-failure' );
	
				return $return_args;
			}

			if( ! empty( $discount_id ) ){
				//Fetch the discount id from the code
				if( ! is_numeric( $discount_id ) ){
					$tmp_dsc_obj = edd_get_discount_by_code( $discount_id );
					if( ! empty( $tmp_dsc_obj->ID ) ){
						$discount_id = $tmp_dsc_obj->ID;
					}
				}
			}

			if( empty( $discount_id ) || ! is_numeric( $discount_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'We could not find any discount for your given value.', 'action-edd_delete_discount-failure' );
	
				return $return_args;
			}

			edd_remove_discount( $discount_id );

			$return_args['msg'] = WPWHPRO()->helpers->translate( "The discount code was successfully deleted.", 'action-edd_delete_discount-success' );
			$return_args['data']['discount_id'] = $discount_id;
			$return_args['success'] = true;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $discount_id, $discount, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.