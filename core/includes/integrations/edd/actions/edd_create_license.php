<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_license' ) ) :

	/**
	 * Load the edd_create_license action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_license {

        public function is_active(){

            $is_active = class_exists( 'EDD_Software_Licensing' );

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_create_license-description";

			$parameter = array(
				'download_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the download you want to associate with the license. Please see the description for further details.', $translation_ident ) ),
				'payment_id'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the payment you want to associate with the license. Please see the description for further details.', $translation_ident ) ),
				'price_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) In case you work with multiple pricing options (variations) within the same product, please set the pricing id here. Please see the description for further details.', $translation_ident ) ),
				'cart_index'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The numerical index in the cart items array of the product the license key is associated with. Please see the description for further details.', $translation_ident ) ),
				'existing_license_ids'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string of existing license ids. Please see the description for further information.', $translation_ident ) ),
				'parent_license_id'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) Set the parent id of this license in case you want to use this license as a child license. Please see the description for further details.', $translation_ident ) ),
				'activation_limit'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) A number representing the amount of possible activations at the same time. set it to 0 for unlimited activations. Please see the description for further details.', $translation_ident ) ),
				'license_length'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The length of the license key.', $translation_ident ) ),
				'expiration_date'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) In case you want to customize the expiration date, you can define the date here. Otherwise it will be calculated based on the added product. Please see the description for further details.', $translation_ident ) ),
				'is_lifetime'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set this value to "yes" to mark the license as a lifetime license. Default: no', $translation_ident ) ),
				'manage_sites'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string containing one or multiple site urls. Please see the description for further details.', $translation_ident ) ),
				'logs'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string containing one or multiple logs. Please see the description for further details.', $translation_ident ) ),
				'license_meta'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A JSON formatted string containing one or multiple meta values. Please see the description for further details.', $translation_ident ) ),
				'license_action'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Do additional, native actions using the license. Please see the description for further details.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Containing the new license id and other arguments set during the creation of the license.', $translation_ident ) ),
			);

			$returns_code = array (
                'success' => true,
                'msg' => 'The license was successfully created.',
                'data' => 
                array (
                  'license_id' => 17,
                  'download_id' => 285,
                  'payment_id' => 843,
                  'price_id' => '2',
                  'cart_index' => 0,
                  'license_options' => 
                  array (
                    'activation_limit' => 0,
                    'license_length' => '32',
                    'expiration_date' => 1621654140,
                    'is_lifetime' => true,
                  ),
                  'license_meta' => '{
                "meta_1": "test1",
                "meta_2": "ironikus-serialize{\\"test_key\\":\\"wow\\",\\"testval\\":\\"new\\"}"
              }',
                  'logs' => '[
                {
                  "title": "Log 1",
                  "message": "This is my description for log 1"
                },
                {
                  "title": "Log 2",
                  "message": "This is my description for log 2",
                  "type": null
                }
              ]',
                ),
        );

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a license for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_create_license</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_create_license</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_create_license</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>download_id</strong> argument. Please set it to the download id you want to connect with the license.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Another required argument is the <strong>payment_id</strong> argument. Please set it to the payment id you want to connect the license with.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD license.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "This webhook creates the license by default with the status <strong>inactive</strong>, which will automatically switch to <strong>active</strong> once the user activates his first site.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Please note that the download you would like to connect, must have licensing activated within the product. Otherwise we throw an error.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "download_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The download id of the download (product) you need to relate with the license. Please note that the product needs to have licensing activated. We will use this download to fetch certain information as expiration, pricing, bundles, etc.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "payment_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The payment id of the payment you need to relate with the license. It will be used to assign the payment with the newly created license. EDD also uses this argument to assign the user to the license, as well as the customer.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "price_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you work with pricing options (variations) for your download, please set the pricing id of the variation price you want to use here. The pricing id is called <strong>Download file ID</strong> on the edit-download page.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "cart_index", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The identifier of the given download within the cart array. You can use this argument to associate the license with a specifc product wiithin the payment.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "existing_license_ids", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to add one or multiple, existing licenses to the subscription using the license id. This value accepts a JSON, containing one license id per line. Here is an example:", $translation_ident ); ?>
<pre>[
  342,
  365
]</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above adds two licenses.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "<strong>Please note</strong>: Defining ids within this argument causes the added licenses to bbe added as child-licenses (the parent will be set to this license).", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "parent_license_id", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to set a parent license for the newly created license.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "activation_limit", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you would like to customize the licensing slots for your license (the amount of wesbites that can be added), you can use this argument. Please set it to e.g. 20 to allow 20 licensing slots. If you leave it empty, the values re fetched accordingly from the given download. If you set this argument to 0, the license will contain unlimited license slots.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "license_length", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The length of the license key itself. In case you set this argument to 64, the license key will look as followed:", $translation_ident ); ?>
<pre>d96ef9c6e8d4259c11bf5f7bad4f6d67232daddee75cced747de0aed7d2d6c99</pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "expiration_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "You can use this argument to customize the expiration date. It allows you to set most kind of date formats, but we suggest you using the SQL format: 2021-05-25 11:11:11", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "is_lifetime", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set the value to <strong>yes</strong> to never expire this license. Default: <strong>no</strong>. Please note that setting this argument to <strong>yes</strong> will ignore the expiration date.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "manage_sites", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to add and/or remove sites on a license. It accepts a JSON formatted string containg the site URLs. Here is an example:", $translation_ident ); ?>
<pre>[
  "https://demo.com",
  "https://demo.demo",
  "remove:https://demo3.demo"
]</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above adds two new site URLs. It also removes one site URL. To remove a site URL, please place <strong>remove:</strong> in front of the site URL.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "logs", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Use this argument to add one or multiple log entries to the license. This value accepts a JSON formated string. Here is an example:", $translation_ident ); ?>
<pre>[
  {
    "title": "Log 1",
    "message": "This is my description for log 1"
  },
  {
    "title": "Log 2",
    "message": "This is my description for log 2",
    "type": null
  }
]</pre>
<?php echo WPWHPRO()->helpers->translate( "The example above adds two logs. The <strong>type</strong> key can contain a single term slug, single term id, or array of either term slugs or ids. For further details on the type key, please check out the \$terms variable within the wp_set_object_terms() function:", $translation_ident ); ?>
<a href="https://developer.wordpress.org/reference/functions/wp_set_object_terms/" target="_blank">https://developer.wordpress.org/reference/functions/wp_set_object_terms/</a>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "license_meta_arr", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add/update or remove one or multiple license meta values to your newly created license, using a JSON string. Easy Digital Downloads uses a custom table for these meta values. Here are some examples on how you can use it:", $translation_ident ); ?>
<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><?php echo WPWHPRO()->helpers->translate( "Add/update meta values", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This JSON shows you how to add simple meta values for your license.", $translation_ident ); ?>
        <pre>{
  "meta_1": "test1",
  "meta_2": "test2"
}</pre>
        <?php echo WPWHPRO()->helpers->translate( "The key is always the license meta key. On the right, you always have the value for the license meta value. In this example, we add two meta values to the license meta. In case a meta key already exists, it will be updated.", $translation_ident ); ?>
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

<h5><?php echo WPWHPRO()->helpers->translate( "license_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to fire further native features of the licensing class. Please find further details down below:", $translation_ident ); ?>
<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><?php echo WPWHPRO()->helpers->translate( "Enable licenses", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This value allows you to enable the license and all of its child licenses. It does it by checking on the activation count and if some sites are active, it will set the license to <strong>active</strong>, otherwise it will set it to <strong>inactive</strong>.", $translation_ident ); ?>
        <pre>enable</pre>
    </li>
    <li class="list-group-item">
        <strong><?php echo WPWHPRO()->helpers->translate( "Disable licenses", $translation_ident ); ?></strong>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "This value allows you to disable the license and all of its child licenses. It will set the license to <strong>disabled</strong>.", $translation_ident ); ?>
        <pre>disable</pre>
    </li>
</ul>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the edd_create_license action was fired.", $translation_ident ); ?>
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
        <?php echo WPWHPRO()->helpers->translate( "Contains the id of the newly created license.", $translation_ident ); ?>
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
                'action'            => 'edd_create_license',
                'name'              => WPWHPRO()->helpers->translate( 'Create license', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'create a license', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to create a license within Easy Digital Downloads - Software Licensing.', $translation_ident ),
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
					'download_id' => 0,
					'payment_id' => 0,
					'price_id' => false,
					'cart_index' => 0,
					'license_options' => array(),
					'license_meta' => array(),
				),
			);

			$download_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_id' ) );
			$payment_id   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'payment_id' ) );
			$price_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'price_id' );
			$cart_index   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cart_index' ) );
			$existing_license_ids   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'existing_license_ids' );
			$parent_license_id   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent_license_id' );
			$activation_limit   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'activation_limit' ) );
			$license_length   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_length' );
			$expiration_date   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$is_lifetime   = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_lifetime' ) === 'yes' ) ? true : false;
			$manage_sites   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_sites' );
			$logs   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'logs' );
			$license_meta   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_meta' );
			$license_action   = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'license_action' ) );
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_SL_License' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_SL_License() does not exist. The license was not created.', 'action-edd_create_license-failure' );
				return $return_args;
			}

			if( ! class_exists( 'EDD_SL_Download' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_SL_Download() does not exist. The license was not created.', 'action-edd_create_license-failure' );
				return $return_args;
			}

			if( empty( $download_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The download_id argument cannot be empty. The license was not created.', 'action-edd_create_license-failure' );
				return $return_args;
			}

			if( empty( $payment_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The payment_id argument cannot be empty. The license was not created.', 'action-edd_create_license-failure' );
				return $return_args;
            }
            
            $purchased_download   = new EDD_SL_Download( $download_id );
            if( ! $purchased_download->licensing_enabled() ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( 'The download given within the download_id argument has no licensing activated within the product. The license was not created.', 'action-edd_create_license-failure' );
				return $return_args;
            }

            $license = new EDD_SL_License();
            
            if( empty( $price_id ) ){
                $price_id = false;
            }

            $license_options = array();

            if( ! empty( $existing_license_ids ) ){
                if( WPWHPRO()->helpers->is_json( $existing_license_ids ) ){
                    $existing_license_ids_arr = json_decode( $existing_license_ids, true );
                    if( is_array( $existing_license_ids_arr ) && ! empty( $existing_license_ids_arr ) ){
                        $license_options['existing_license_ids'] = $existing_license_ids_arr;
                    }
                }
            }

            if( ! empty( $parent_license_id ) ){
                $license_options['parent_license_id'] = $parent_license_id;
            }

            if( ! empty( $activation_limit ) || $activation_limit === 0 ){
                $license_options['activation_limit'] = $activation_limit;
            }

            if( ! empty( $license_length ) ){
                $license_options['license_length'] = $license_length;
            }

            if( ! empty( $expiration_date ) ){
                $license_options['expiration_date'] = strtotime( $expiration_date );
            }

            if( ! empty( $is_lifetime ) ){
                $license_options['is_lifetime'] = $is_lifetime;
            }

			$check = $license->create( $download_id, $payment_id, $price_id, $cart_index, $license_options );

			if( $check ){

                //Make sure we set again the activation limit since by default it was not set properly
                if( ! empty( $activation_limit ) || $activation_limit === 0 ){
                    $license->update_meta( '_edd_sl_limit', $activation_limit );
                }

                if( ! empty( $logs ) ){
					if( WPWHPRO()->helpers->is_json( $logs ) ){
						$logs_arr = json_decode( $logs, true );
						foreach( $logs_arr as $slog ){

                            $title = WPWHPRO()->settings->get_page_title();
                            if( isset( $slog['title'] ) && ! empty( $slog['title'] ) ){
                                $title = $slog['title'];
                            }

                            $message = '';
                            if( isset( $slog['message'] ) && ! empty( $slog['message'] ) ){
                                $message = $slog['message'];
                            }

                            $type = null;
                            if( isset( $slog['type'] ) && ! empty( $slog['type'] ) ){
                                $type = $slog['type'];
                            }

							$license->add_log( $title, $message, $type );
						}
					}
				}

                if( ! empty( $manage_sites ) ){
                    if( WPWHPRO()->helpers->is_json( $manage_sites ) ){
                        $manage_sites_arr = json_decode( $manage_sites, true );
                        foreach( $manage_sites_arr as $site ){

                            $ident = 'remove:';
                            if( is_string( $site ) && substr( $site , 0, strlen( $ident ) ) === $ident ){
                                $saction = 'remove';
                                $site = str_replace( $ident, '', $site );
                            } else {
                                $saction = 'add';
                            }

                            switch( $saction ){
                                case 'remove':
                                    $license->remove_site( $site );
                                break;
                                case 'add':
                                default: 
                                    $license->add_site( $site );
                                break;
                            }
                        }
                    }
                }

                if( ! empty( $license_meta ) ){
                    if( WPWHPRO()->helpers->is_json( $license_meta ) ){
                        $license_meta_arr = json_decode( $license_meta, true );
                        foreach( $license_meta_arr as $skey => $sval ){

                            if( ! empty( $skey ) ){
                                if( $sval == 'ironikus-delete' ){
                                    $license->delete_meta( $skey );
                                } else {
                                    $ident = 'ironikus-serialize';
                                    if( is_string( $sval ) && substr( $sval , 0, strlen( $ident ) ) === $ident ){
                                        $serialized_value = trim( str_replace( $ident, '', $sval ),' ' );

                                        if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
                                            $serialized_value = json_decode( $serialized_value );
                                        }

                                        $license->update_meta( $skey, $serialized_value );

                                    } else {
                                        $license->update_meta( $skey, maybe_unserialize( $sval ) );
                                    }
                                }
                            }
                        }
                    }
				}
				
				if( ! empty( $license_action ) ){
					switch( $license_action ){
						case 'enable':
							$license->enable();
						break;
						case 'disable':
							$license->disable();
						break;
					}
				}

                $license_id = $license->ID;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The license was successfully created.", 'action-edd_create_license-success' );
				$return_args['success'] = true;
				$return_args['data']['license_id'] = $license->ID;
				$return_args['data']['license_key'] = $license->get_license_key();
				$return_args['data']['download_id'] = $download_id;
				$return_args['data']['payment_id'] = $payment_id;
				$return_args['data']['price_id'] = $price_id;
				$return_args['data']['cart_index'] = $cart_index;
				$return_args['data']['license_options'] = $license_options;
				$return_args['data']['license_meta'] = $license_meta;
				$return_args['data']['logs'] = $logs;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error creating the license.", 'action-edd_create_license-success' );
			}
		
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $license_id, $license, $return_args );
			}

			return $return_args;
    
        }

    }

endif; // End if class_exists check.