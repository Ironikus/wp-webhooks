<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_visit' ) ) :

	/**
	 * Load the affwp_add_visit action
	 *
	 * @since 4.2.3
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_affiliatewp_Actions_affwp_add_visit {

		function __construct(){
			$this->page_title   = WPWHPRO()->settings->get_page_title();
		}

		public function get_details(){

			$translation_ident = 'action-ironikus-affwp_add_visit-content';
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
				'affiliate_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The id or email of the related affiliate. (Optional in case user_id is set).', $translation_ident ) ),
				'ip' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The ip of the person who should be assigned to the visit. (Will be anonymized in case IP logging is disabled within AffiliateWP).', $translation_ident ) ),
				'campaign' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Set a visit campaign. This can be a identifier you created for a specific project. E.g. Summer Promotion', $translation_ident ) ),
				'context' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Some more details of where the user comes from. E.g.: cta-button', $translation_ident ) ),
				'url' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A URL the visitor arrived at.', $translation_ident ) ),
				'referrer' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A referral URL from where the visitor came from If nothing is set, it will be counted as direct traffic.', $translation_ident ) ),
				'date' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The date and time of creation of the visit. In case nothing is set, the current time is used.', $translation_ident ) ),
				'do_action'	 => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after this webhook was fired.', $translation_ident ) ),
			);

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $visit_id, $args, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$visit_id</strong> (integer)<br>
		<?php echo WPWHPRO()->helpers->translate( "The id of the newly created visit.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The data used to create the visit.", $translation_ident ); ?>
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
				'msg' => 'The visit was successfully created.',
				'data' => 
				array (
				  'visit_id' => 2,
				  'visit' => 
				  array (
					0 => 
					array (
					  'visit_id' => 2,
					  'affiliate_id' => 8,
					  'referral_id' => 0,
					  'rest_id' => '',
					  'url' => 'https://mydomain.test/custom-landing-page/',
					  'referrer' => 'https://somereferrer.test/custompath/',
					  'campaign' => 'Summer Promotion',
					  'context' => 'cta-button',
					  'ip' => '192.168.0.1',
					  'date' => '2021-04-10 11:25:33',
					),
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Add visit',
				'webhook_slug' => 'affwp_add_visit',
				'before_description' => '<p>' . WPWHPRO()->helpers->translate( 'This webhook enables you to create a new visit within AffiliateWP using a webhook endpoint. A visit is a database entry about a user that clicked on an affiliate link from a specific affiliate.', $translation_ident ) . '</p>',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the user_id argument (in case you haven't set the affiliate_id argument). Please set it to the user id or email of the user you want to connect with the referral", $translation_ident ),
					WPWHPRO()->helpers->translate( "In case you have set the affiliate_id argument, is is optional to set the user_id argument. The affiliate_id argument accepts either the affiliate id, or the user email.", $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( "To make the most out of this webhook endpoint, please take a look at the <strong>Arguments</strong> list.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'affwp_add_visit',
				'name'			  => WPWHPRO()->helpers->translate( 'Add visit', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'add a visit', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Create a visit within AffiliateWP via a webhook call.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'affiliatewp',
				'premium'		   => true,
			);

		}

	}

endif; // End if class_exists check.