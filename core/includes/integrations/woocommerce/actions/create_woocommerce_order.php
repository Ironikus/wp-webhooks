<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_create_woocommerce_order' ) ) :

	/**
	 * Load the create_woocommerce_order action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_create_woocommerce_order {

		public function get_details(){

			$translation_ident = "action-create_woocommerce_order-content";

			$parameter = array(
				'billing_address'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The billing address of the order. Please see the description for more information.', $translation_ident ) ),
				'shipping_address'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The shipping address of the order. Please see the description for more information.', $translation_ident ) ),
				'shipping_lines'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'This argument allows you to add certain shipping lines to your order. Please see the description for further details.', $translation_ident ) ),
				'add_products'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The slist with the product ids and the quantity. More information within the description.', $translation_ident ) ),
				'calculate_totals'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set it to "yes" in case you want to calculate the order total. Default "no".', $translation_ident ) ),
				'payment_complete'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set it to "yes" in case you want to set the payment to complete. You can also set a transation id instead of "yes".', $translation_ident ) ),
				'legacy_set_total'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set the legacy total amount and type. More information within the description.', $translation_ident ) ),
				'order_meta'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'You can also set custom order meta. This meta will be saved as custom values within the post meta table. More information within the description.', $translation_ident ) ),
				'order_status'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The order status you want to use for the order. Please check the description for more information.', $translation_ident ) ),
				'customer_id'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The id or the email of the customer for the order.', $translation_ident ) ),
				'customer_note'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Some text that will be displayed as the customer note.', $translation_ident ) ),
				'order_parent'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of a parent order.', $translation_ident ) ),
				'created_via'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'In identifier where the order was created from. E.g. "wp-webhooks".', $translation_ident ) ),
				'cart_hash'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A cart hash value.', $translation_ident ) ),
				'order_id'			=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A custom order id (Please note that this value may NOT be the order id of the order you currently create).', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( 'To add a shipping and/or billing address, you can separate the value from the key with a comma and each dataset with a semicolon. Here is an example of that:', $translation_ident ); ?></p>
				<pre>
first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA
				</pre>
				<p><?php echo WPWHPRO()->helpers->translate( 'The keys (e.g. first_name) are the direct identifier for the value. You can use any key that is available within Woocommerce.', $translation_ident ); ?></p>
<?php
			$parameter['billing_address']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( 'To add a shipping and/or billing address, you can separate the value from the key with a comma and each dataset with a semicolon. Here is an example of that:', $translation_ident ); ?></p>
				<pre>
first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA
				</pre>
				<p><?php echo WPWHPRO()->helpers->translate( 'The keys (e.g. first_name) are the direct identifier for the value. You can use any key that is available within Woocommerce.', $translation_ident ); ?></p>
<?php
			$parameter['shipping_address']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( 'For adding products, it is required that you know the product id. To add a product, set the product id and comma-separate the quantity of the product. To add multiple products, easily separate them with a comma. Hee is an example:', $translation_ident ); ?></p>
<pre>156,1;155,2</pre>
<?php
			$parameter['add_products']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( 'To set the legacy total, it is required to set the amount and with a ":" separated the type. It should look like that (Please define only the values without the double quotes): "123.33:total". Down below you will see a list with all deault types:', $translation_ident ); ?></p>
<pre>array( 'shipping', 'tax', 'shipping_tax', 'total', 'cart_discount', 'cart_discount_tax' )</pre>
<?php
			$parameter['legacy_set_total']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( 'You can also add custom order meta. This meta will be added to the post meta table. Here is an example on how this would look like using the simple structure (We also support json):', $translation_ident ); ?></p>
<br><br>
<pre>meta_key_1,meta_value_1;my_second_key,add_my_value</pre>
<br><br>
<?php echo WPWHPRO()->helpers->translate( 'To separate the meta from the value, you can use a comma ",". To separate multiple meta settings from each other, easily separate them with a semicolon ";" (It is not necessary to set a semicolon at the end of the last one)', $translation_ident ); ?>
<br><br>
<?php echo WPWHPRO()->helpers->translate( 'This is an example on how you can include the order meta using JSON.', $translation_ident ); ?>
<br>
<pre>{
  "meta_key_1": "This is my meta value 1",
  "another_meta_key": "This is my second meta key!",
  "another_meta_key_1": "ironikus-delete"
}</pre>
<?php
			$parameter['order_meta']['description'] = ob_get_clean();

			ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( 'The order status contains the woocommerce order status. Please also include the woocommerce order status prefix (e.g. wc-pending). Here are the default values as examples in form of an array:', $translation_ident ); ?></p>
				<pre>
$order_statuses = array(
	'wc-pending'	=> _x( 'Pending payment', 'Order status', 'woocommerce' ),
	'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
	'wc-on-hold'	=> _x( 'On hold', 'Order status', 'woocommerce' ),
	'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
	'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
	'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
	'wc-failed'	 => _x( 'Failed', 'Order status', 'woocommerce' ),
  );
				</pre>
<?php
			$parameter['order_status']['description'] = ob_get_clean();

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( 'You can also add shipping lines. To do so, you need to parse a JSON construct within the shipping_lines argument.', $translation_ident ); ?>
				<br>
				<?php echo WPWHPRO()->helpers->translate( 'Here\'s an example:', $translation_ident ); ?>
				<br>
				<pre>
[
  {
	"tax_country_code": "DE",
	"tax_state": "",
	"tax_postcode": "",
	"tax_city": "",
	"method_title": "REMOTE SHIP",
	"method_id": "",
	"price": "18.33",
	"end_date": "Dec 27, 2019 07:00PM"
  }
]
				</pre>
				<br>
				<?php echo WPWHPRO()->helpers->translate( 'For the method_id you can define an already existing id. For example: flat_rate:14', $translation_ident ); ?>
				<br>
				<?php echo WPWHPRO()->helpers->translate( 'If you want to add multiple shipping lines, you can comma separate them within the first dimension of the JSON construct.', $translation_ident ); ?>
<?php
			$parameter['shipping_lines']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The set data (inc. order id) in success, the error on failure.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'shipping_address'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The argument input of the shipping address field.', $translation_ident ) ),
				'billing_address'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The argument input of the billing address field.', $translation_ident ) ),
				'add_products'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) The input of add_products argument.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Order created successfully.',
				'data' => 
				array (
				  'meta_data' => NULL,
				  'default_args' => NULL,
				  'add_products' => NULL,
				  'billing_address' => NULL,
				  'shipping_address' => NULL,
				  'legacy_set_total' => NULL,
				  'calculate_totals' => NULL,
				  'new_order_id' => NULL,
				  'order_status' => NULL,
				),
				'shipping_address' => 'first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA
			  ',
				'billing_address' => 'first_name,Max;last_name,Mustermann;company,Demo Copmpany;email,max@mustermann.com;phone,123-456-789;address_1,Street name 12;address_2,Room 123;postcode,12345;city,Mustercity;state,CA;country,USA',
				'add_products' => '9,2',
				'default_args' => 
				array (
				),
				'meta_data' => false,
				'calculate_totals' => 'yes',
				'legacy_set_total' => false,
				'new_order_id' => 10,
				'order_status' => false,
				'shipping_lines' => '[
				{
				  "tax_country_code": "DE",
				  "tax_state": "",
				  "tax_postcode": "",
				  "tax_city": "",
				  "method_title": "REMOTE SHIP",
				  "method_id": "",
				  "price": "18.33",
				  "end_date": "Dec 27, 2019 07:00PM"
				}
			  ]',
				'pay_now_url' => 'https://yourdomain.test/?page_id=7&#038;order-pay=10&#038;pay_for_order=true&#038;key=wc_order_YahlGo76pk5k1',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Create Woocommerce order',
				'webhook_slug' => 'create_woocommerce_order',
			) );

			return array(
				'action'			=> 'create_woocommerce_order', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Create Woocommerce order', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'create a Woocommerce order', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Create a Woocommerce order on your website using webhooks.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'woocommerce',
				'premium'		  => true,
			);


		}

	}

endif; // End if class_exists check.