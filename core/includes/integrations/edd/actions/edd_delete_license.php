<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_delete_license' ) ) :

	/**
	 * Load the edd_delete_license action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_delete_license {

        public function is_active(){

            $is_active = class_exists( 'EDD_Software_Licensing' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_delete_license-description";

			$parameter = array(
				'license_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The license id or the license key of the license you would like to delete. Please see the description for further details.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Containing the license id, as well as the license object.', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The license was successfully deleted.',
                'data' => 
                array (
                  'license_id' => '4fc336680bf576cc0298777278ceb15a',
                  'license' => 
                  array (
                    'ID' => 16,
                  ),
                ),
            );

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to delete a license for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_delete_license</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_delete_license</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_delete_license</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>license_id</strong> argument. You can set it to either the license id or the license key.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD license.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you already deleted the license and there is no license with your given id or key anymore, we will also throw an error.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "license_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts either the numeric license id or the license key that was set for the license. E.g. 4fc336680bf576cc0298777278ceb15a", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the edd_delete_license action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $license_id, $license, $return_args ){
    //run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
    <li>
        <strong>$license_id</strong> (Integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the deleted license.", $translation_ident ); ?>
    </li>
    <li>
        <strong>$license</strong> (integer)
        <br>
        <?php echo WPWHPRO()->helpers->translate( "Contains the EDD_SL_License() object of the license.", $translation_ident ); ?>
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
                'action'            => 'edd_delete_license',
                'name'              => WPWHPRO()->helpers->translate( 'Delete license', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'delete a license', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to delete a license within Easy Digital Downloads - Software Licensing.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $license_id = 0;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'license_id' => 0,
					'license' => 0,
				),
			);

			$license_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_id' );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_SL_License' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_SL_License() does not exist. The license was not renewed.', 'action-edd_delete_license-failure' );
				return $return_args;
			}

			if( empty( $license_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The license_id argument cannot be empty. The license was not renewed.', 'action-edd_delete_license-failure' );
				return $return_args;
			}
            
            $license = new EDD_SL_License( $license_id );

			$check = $license->delete();

			if( $check ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The license was successfully deleted.", 'action-edd_delete_license-success' );
				$return_args['success'] = true;
				$return_args['data']['license_id'] = $license_id;
				$return_args['data']['license'] = $license;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error deleting the license.", 'action-edd_delete_license-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $license_id, $license, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.