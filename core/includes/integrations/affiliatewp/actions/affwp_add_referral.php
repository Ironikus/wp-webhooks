<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_referral' ) ) :

	/**
	 * Load the affwp_add_referral action
	 *
	 * @since 4.2.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_referral {

		function __construct(){
			$this->page_title   = WPWHPRO()->settings->get_page_title();
		}

		public function get_details(){

			$translation_ident = 'action-ironikus-affwp_add_referral-content';
			$third_party_integrations = array();
			$validated_types = array();
			$validated_statuses = array();

			if( function_exists( 'affiliate_wp' ) ){
				$third_party_integrations = affiliate_wp()->integrations->get_integrations();
			}

			if( function_exists( 'affiliate_wp' ) ){
				foreach ( affiliate_wp()->referrals->types_registry->get_types() as $type_slug => $type ) {
					$validated_types[ $type_slug ] = ( isset( $type['label'] ) && ! empty( $type['label'] ) ) ? sanitize_text_field( $type['label'] ) : $type_slug;
				}
			}

			if( function_exists( 'affwp_get_referral_statuses' ) ){
				$validated_statuses = affwp_get_referral_statuses();
			}

			$parameter = array(
				'user_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The id or email of the related user. (Optional in case affiliate_id is set).', $translation_ident ) ),
				'affiliate_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The id of the related affiliate. (Optional in case user_id is set).', $translation_ident ) ),
				'amount' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The amount that is paid to the affiliate.', $translation_ident ) ),
				'description' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A description for this referral.', $translation_ident ) ),
				'reference' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A reference for this referral. Usually this would be the transaction ID of the associated purchase.', $translation_ident ) ),
				'parent_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'An id of a different referral you want to associate as a parent.', $translation_ident ) ),
				'currency' => array( 'short_description' => WPWHPRO()->helpers->translate( 'An custom currency code such as EUR. Please note that the currency will only have effect it is is selected within the settings of AffiliateWP.', $translation_ident ) ),
				'campaign' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set a referral campaign. This can be a referral you created for a specific project. E.g. Summer Promotion', $translation_ident ) ),
				'context' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The context usually is the slug of the payment provider. E.g. fastspring or paypal. You can also use the third-party integration.', $translation_ident ) ),
				'custom' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Add any kind of data to your referral.', $translation_ident ) ),
				'date' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The date and time of creation of the referral. In case nothing is set, the current time is used.', $translation_ident ) ),
				'type' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The referral type. E.g.: sale', $translation_ident ) ),
				'products' => array( 'short_description' => WPWHPRO()->helpers->translate( 'In case you use a third-party integration, you can also relate specific products to your referral.', $translation_ident ) ),
				'status' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The status of your current referral. E.g.: unpaid', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after this webhook was fired.', $translation_ident ) ),
			);

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The reference is usually the transaction id of the payment provider. In case you use a third-party plugin, you can also add the order id here. In case of Easy Digital Downloads, this might be the payment id. E.g.: 1344", $translation_ident ); ?>
			<?php
			$parameter['reference']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "As a contect, you can also use the third-party integration slug. Down below you will find a list of all integrated third-party integrations for AffiliateWP. If you want to, for example, create the referral for the Easy Digital Downloads integration, set the context to <strong>edd</strong>.", $translation_ident ); ?>
<ol>
	<?php foreach( $third_party_integrations as $slug => $name ) : ?>
	<li>
		<?php echo sanitize_text_field( $name ); ?>: <strong><?php echo sanitize_text_field( $slug ); ?></strong>
	</li>
	<?php endforeach; ?>
</ol>
			<?php
			$parameter['context']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The type determines what kind of affiliation this referral is. In case it was a sale, you can use <strong>sale</strong> - for an opt in, you can use <strong>opt-in</strong>. Down below you will find a full list of all types.", $translation_ident ); ?>
<ol>
	<?php foreach( $validated_types as $slug => $name ) : ?>
	<li>
		<?php echo sanitize_text_field( $name ); ?>: <strong><?php echo sanitize_text_field( $slug ); ?></strong>
	</li>
	<?php endforeach; ?>
</ol>
			<?php
			$parameter['type']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "In case of a referral from Woocommerce or Easy Digital Downloads (or any other third-party integration), it is possible to relate products to your given order. This argument accepts a JSON formatted value containing one or multiple products. Down below you will find an example.", $translation_ident ); ?>
<pre>
[
   {
      "name":"Demo Article",
      "id":285,
      "price":39,
      "referral_amount":"3.9"
   }
]
</pre>
<?php echo WPWHPRO()->helpers->translate( "To give you an explanation about each value, please refer to the lsit down below. Every data within the curly brackets {} refers to one product.", $translation_ident ); ?>
<ol>
	<li>
		<strong>name</strong>: <?php echo WPWHPRO()->helpers->translate( "The name refers to the name of the added product.", $translation_ident ); ?>
	</li>
	<li>
		<strong>id</strong>: <?php echo WPWHPRO()->helpers->translate( "The id refers to the id of the added product within the third-party integration.", $translation_ident ); ?>
	</li>
	<li>
		<strong>price</strong>: <?php echo WPWHPRO()->helpers->translate( "The price refers to the price of the added product within the third-party integrations currency.", $translation_ident ); ?>
	</li>
	<li>
		<strong>referral_amount</strong>: <?php echo WPWHPRO()->helpers->translate( "This is the amount your affiliate gets paid for that specific product referral.", $translation_ident ); ?>
	</li>
</ol>
			<?php
			$parameter['products']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "You can also customize the status of the given referral. If you want to mark a referral as paid, simple set the status to <strong>paid</strong>. A list of all possible statuses is down below.", $translation_ident ); ?>
<ol>
	<?php foreach( $validated_statuses as $slug => $name ) : ?>
	<li>
		<?php echo sanitize_text_field( $name ); ?>: <strong><?php echo sanitize_text_field( $slug ); ?></strong>
	</li>
	<?php endforeach; ?>
</ol>
			<?php
			$parameter['status']['description'] = ob_get_clean();

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $referral_id, $args, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$referral_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The id of the newly created referral.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The data used to create the referral.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data in relation the current webhook action.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'Referral was successfully created.',
				'data' => 
				array (
				  'referral_id' => 15,
				  'referral' => 
				  array (
					'referral_id' => 15,
					'affiliate_id' => 8,
					'visit_id' => 2,
					'rest_id' => '',
					'customer_id' => '0',
					'parent_id' => 0,
					'description' => 'This is a demo description',
					'status' => 'paid',
					'amount' => '18.00',
					'currency' => 'eur',
					'custom' => 'Some custom information',
					'context' => 'edd',
					'campaign' => 'Demo Campaign',
					'reference' => '1344',
					'products' => 
					array (
					  0 => 
					  array (
						'name' => 'Demo Article',
						'id' => 285,
						'price' => 39,
						'referral_amount' => '3.9',
					  ),
					),
					'date' => '2021-05-12 14:10:23',
					'type' => 'sale',
					'payout_id' => '0',
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Add referral',
				'webhook_slug' => 'affwp_add_referral',
				'before_description' => '<p>' . WPWHPRO()->helpers->translate( 'This webhook enables you to create a new referral within AffiliateWP using a webhook endpoint.', $translation_ident ) . '</p>',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the user_id argument (in case you haven't set the affiliate_id argument). Please set it to the user id or email of the user you want to connect with the referral", $translation_ident ),
					WPWHPRO()->helpers->translate( "In case you have set the affiliate_id argument, is is optional to set the user_id argument.", $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( "To make the most out of this webhook endpoint, please take a look at the <strong>Arguments</strong> list.", $translation_ident ),
					WPWHPRO()->helpers->translate( "This endpoint also supports all of the, by AffiliateWP integrated, third-party integrations. For further details, please take a look at the <strong>context</strong> argument.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'affwp_add_referral',
				'name'			  => WPWHPRO()->helpers->translate( 'Add referral', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'add a referral', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Create a referral within AffiliateWP via a webhook call.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'affiliatewp',
				'premium'		   => true,
			);

		}

	}

endif; // End if class_exists check.