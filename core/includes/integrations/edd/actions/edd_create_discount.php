<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Actions_edd_create_discount' ) ) :

	/**
	 * Load the edd_create_discount action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_edd_Actions_edd_create_discount {

        public function is_active(){

            $is_active = true;

            //Backwards compatibility for the "Easy Digital Downloads" integration
            if( defined( 'WPWH_EDD_NAME' ) ){
                $is_active = false;
            }

            return $is_active;
        }

        public function get_details(){

            $translation_ident = "action-edd_create_discount-description";

            $parameter = array(
				'code'       => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The dicsount code you would like to set for this dicsount. Only alphanumeric characters are allowed.', $translation_ident ) ),
				'name'       => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The name to identify the discount code.', $translation_ident ) ),
				'status'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The status of the discount code. Default: active', $translation_ident ) ),
				'current_uses'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) A number that tells how many times the coupon code has been already used.', $translation_ident ) ),
				'max_uses'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number of how often the discount code can be used in total.', $translation_ident ) ),
				'amount'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The amount of the discount code. If chosen percent, use an interger, for an amount, use float. More info is within the description.', $translation_ident ) ),
				'start_date'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The start date of the availability of the discount code. More info is within the description.', $translation_ident ) ),
				'expiration_date'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The end date of the availability of the discount code. More info is within the description.', $translation_ident ) ),
				'type'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The type of the discount code. Default: percent. More info is within the description.', $translation_ident ) ),
				'min_price'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The minimum price that needs to be reached to use the discount code. More info is within the description.', $translation_ident ) ),
				'product_requirement'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list of download IDs that are required to apply the discount code. More info is within the description.', $translation_ident ) ),
				'product_condition'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A string containing further conditions on when the discount code can be applied. More info is within the description.', $translation_ident ) ),
				'excluded_products'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A comma-separated list, containing all the products that are excluded from the discount code. More info is within the description.', $translation_ident ) ),
				'is_not_global'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set this argument to "yes" if you do not want to apply the discount code globally to all products. Default: no. More info is within the description.', $translation_ident ) ),
				'is_single_use'     => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Set this argument to "yes" if you want to limit this discount code to only a single use per customer. Default: no. More info is within the description.', $translation_ident ) ),
				'do_action'     => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More info is within the description.', $translation_ident ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'        => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Containing all of the predefined data of the webhook, as well as the discount id in case it was successfully created.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The discount code was successfully created.',
				'data' => 
				array (
				  'code' => 'erthsashtsw',
				  'name' => 'Demo Discount Code',
				  'status' => 'inactive',
				  'uses' => '5',
				  'max' => '10',
				  'amount' => '11.10',
				  'start' => '05/23/2020 00:00:00',
				  'expiration' => '06/27/2020 23:59:59',
				  'type' => 'flat',
				  'min_price' => '22',
				  'products' => 
				  array (
					0 => '176',
					1 => '772',
				  ),
				  'product_condition' => 'any',
				  'excluded-products' => 
				  array (
					0 => '774',
				  ),
				  'not_global' => true,
				  'use_once' => true,
				  'discount_id' => 805,
				),
			);

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook action is used to create a discount code for Easy Digital Downloads within your WordPress system via a webhook call.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "The description is uniquely made for the <strong>edd_create_discount</strong> webhook action.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "In case you want to first understand how to setup webhook actions in general, please check out the following manuals:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>edd_create_discount</strong>", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "The first argument you need to set within your webhook action request is the <strong>action</strong> argument. This argument is always required. Please set it to <strong>edd_create_discount</strong>.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "It is also required to set the <strong>code</strong> argument. Please set it to the discount code you want to apply for future orders.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "All the other arguments are optional and just extend the creation of the EDD discount code.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "By changing the <strong>type</strong> argument, you can switch between flat or percentage based discounts. More details are down below within the <strong>Special Arguments</strong> list.", $translation_ident ); ?></li>
</ol>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Special Arguments", $translation_ident ); ?></h4>
<br>

<h5><?php echo WPWHPRO()->helpers->translate( "status", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Defines the status in which you want to create the discount code with. Possible values are <strong>active</strong> and <strong>inactive</strong>. By default, this value is set to <strong>active</strong>.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "current_uses", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a number that defines how often this discount code has been already used. Usually, you do not need to define this argument for creating a discount code.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "max_uses", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument defines the maximal number on how often this discount code can be applied. Set it to <strong>0</strong> for unlimited uses.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "amount", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The amount argument accepts different values, based on the type you set. By default, you can set this value to the number of percents you want to discount the order. E.g.: <strong>10</strong> will be represented as ten percent. If the <strong>type</strong> argument is set to <strong>flat</strong>, it would discount 10$ (or the currency you choose for your shop).", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "start_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set the date you want this discount code do become active. We recommend using the SQL format: <strong>2020-03-10 17:16:18</strong>. This arguments also accepts other formats - if you have no chance of changing the date format, its the best if you simply give it a try.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "expiration_date", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set the date you want this discount code do become inactive. We recommend using the SQL format: <strong>2020-03-10 17:16:18</strong>. This arguments also accepts other formats - if you have no chance of changing the date format, its the best if you simply give it a try.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "type", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "This argument defines the type of the discount code. If you want to use a percentage, set this argument to <strong>percent</strong>. If you would like to use a flat amount, please set it to <strong>flat</strong>. Based on the given value, you might also want to adjust the <strong>amount</strong> argument.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "min_price", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set a minimum price that needs to be reached for a purchase to actually apply this discount code. Please write the price in the following format: 19.99", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "product_requirement", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you want to limit the discount code to only certain downloads, this argument is made for you. Simply separate the download IDs that are required by a comma. Here is an example:", $translation_ident ); ?>
<pre>123,443</pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "product_condition", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you set this argument to <strong>all</strong>, it is required to have all downloads from the <strong>product_requirement</strong> argument within the cart before the coupon will be applied. If you set the argument to <strong>any</strong>, only one of the products mentioned within the <strong>product_requirement</strong> argument have to be within the cart.", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "excluded_products", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "In case you want to limit certain downloads from applying this coupon code to, this argument is made for you. Simply comma-separate the download IDs that the coupon code should ignore. Here is an example:", $translation_ident ); ?>
<pre>32,786</pre>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "is_not_global", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to <strong>yes</strong> in case you do not want to apply the discount code globally on the whole order. If you set this argument to <strong>yes</strong>, it will only be applied to the downloads you defined within the <strong>product_requirement</strong> argument. Default: <strong>no</strong>", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "is_single_use", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to <strong>yes</strong> in case you want to limit the use of this discount code to only one time per customer. Default: <strong>no</strong>", $translation_ident ); ?>
<br>
<hr>

<h5><?php echo WPWHPRO()->helpers->translate( "do_action", $translation_ident ); ?></h5>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>edd_create_discount</strong> action was fired.", $translation_ident ); ?>
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
                'action'            => 'edd_create_discount',
                'name'              => WPWHPRO()->helpers->translate( 'Create discount', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'create a discount', $translation_ident ),
                'parameter'         => $parameter,
                'returns'           => $returns,
                'returns_code'      => $returns_code,
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to create a dicsount code within Easy Digital Downloads.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'edd',
                'premium' 			=> false,
            );

        }

        public function execute( $return_data, $response_body ){

            $discount_id = 0;
			$discount = new stdClass;
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'code'              => '',
					'name'              => '',
					'status'            => 'active',
					'current_uses'		=> '',
					'max_uses'          => '',
					'amount'            => '',
					'start_date'             => '',
					'expiration_date'        => '',
					'type'              => '',
					'min_price'         => '',
					'product_requirement'      => array(),
					'product_condition' => '',
					'excluded_products' => array(),
					'is_not_global'     => false,
					'is_single_use'     => false,
				),
			);

			$code   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'code' );
			$name     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$status     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$current_uses     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'current_uses' );
			$max_uses     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'max_uses' );
			$amount     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'amount' );
			$start_date     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'start_date' );
			$expiration_date     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$type     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'type' );
			$min_price     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'min_price' );
			$product_requirement     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_requirement' );
			$product_condition     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_condition' );
			$excluded_products     = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'excluded_products' );
			$is_not_global     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_not_global' ) === 'yes' ) ? true : false;
			$is_single_use     = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_single_use' ) === 'yes' ) ? true : false;
			
			$do_action          = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( ! class_exists( 'EDD_Discount' ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The class EDD_Discount() is undefined. The discount code could not be created.', 'action-edd_create_discount-failure' );
	
				return $return_args;
			}

			if( empty( $code ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'No code given. The argument code cannot be empty.', 'action-edd_create_discount-failure' );
	
				return $return_args;
			}

			$discount = new EDD_Discount();
			$discount_args = array(
				'code' => $code
			);

			if( ! empty( $name ) ){
				$discount_args['name'] = $name;
			}

			if( ! empty( $status ) ){
				$discount_args['status'] = $status;
			}

			if( ! empty( $current_uses ) ){
				$discount_args['uses'] = $current_uses;
			}

			if( ! empty( $max_uses ) ){
				$discount_args['max'] = $max_uses;
			}

			if( ! empty( $amount ) ){
				$discount_args['amount'] = $amount;
			}

			if( ! empty( $start_date ) ){
				$discount_args['start'] = $start_date;
			}

			if( ! empty( $expiration_date ) ){
				$discount_args['expiration'] = $expiration_date;
			}

			if( ! empty( $type ) ){
				$discount_args['type'] = $type;
			}

			if( ! empty( $min_price ) ){
				$discount_args['min_price'] = $min_price;
			}

			if( ! empty( $product_requirement ) ){
				$product_requirement = explode( ',', trim( $product_requirement, ',' ) );
				$discount_args['products'] = $product_requirement;
			}

			if( ! empty( $product_condition ) ){
				$discount_args['product_condition'] = $product_condition;
			}

			if( ! empty( $excluded_products ) ){
				$excluded_products = explode( ',', trim( $excluded_products, ',' ) );
				$discount_args['excluded-products'] = $excluded_products;
			}

			if( ! empty( $is_not_global ) ){
				$discount_args['not_global'] = $is_not_global;
			}

			if( ! empty( $is_single_use ) ){
				$discount_args['use_once'] = $is_single_use;
			}

			$discount_args = apply_filters( 'wpwh/actions/edd_create_discount/filter_discount_arguments', $discount_args );

			$discount_id = $discount->add( $discount_args );
			
			//fallback since the ID is not directly available within the class
			if( ! empty( $discount_id ) && is_numeric( $discount_id ) ){
				$discount = new EDD_Discount( $discount_id );
			}

			if ( empty( $discount ) || empty( $discount->ID ) ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( 'The discount code was not created.', 'action-edd_create_discount-failure' );
				return $return_args;
			}

			$return_args['data'] = $discount_args;
			$return_args['data']['discount_id'] = $discount_id;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The discount code was successfully created.", 'action-edd_create_discount-success' );
			$return_args['success'] = true;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $discount_id, $discount, $return_args );
			}

			return $return_args;
            
        }

    }

endif; // End if class_exists check.