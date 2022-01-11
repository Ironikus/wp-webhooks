<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_newsletter_Triggers_news_form_submitted' ) ) :

 /**
  * Load the news_form_submitted trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_newsletter_Triggers_news_form_submitted {

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'newsletter_user_post_subscribe',
				'callback' => array( $this, 'newsletter_user_post_subscribe_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-news_form_submitted-description";
		$validated_lists = array();
		if( class_exists( 'NewsletterSubscription' ) ){
			$lists = NewsletterSubscription::instance()->get_lists();
			if( ! empty( $lists ) && is_array( $lists ) ){
				foreach( $lists as $list ){
					$validated_lists[ $list->id ] = $list->name;
				}
			}
		}

		$parameter = array(
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data about the subscribed user.', $translation_ident ) ),
			'subscribed_lists' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The lists this user has been subscribed to.', $translation_ident ) ),
			'subscription' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The subscription data about the sign up.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'news_form_submitted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'newsletter_user_post_subscribe',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_newsletter_trigger_on_list' => array(
					'id'		  => 'wpwhpro_newsletter_trigger_on_list',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_lists,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected lists', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the lists you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'news_form_submitted',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form is submitted within The Newsletter Plugin.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'newsletter',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a user signs up to a Newsletter list
	 *
	 * @param object|MemberOrder $order The data about the current order
	 */
	public function newsletter_user_post_subscribe_callback( $user ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'news_form_submitted' );
		$subscription = NewsletterSubscription::instance()->build_subscription();
		$subscribed_lists = array();
		if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
			foreach ($_REQUEST['nl'] as $list_id) {
				$subscribed_lists[ $list_id ] = NewsletterSubscription::instance()->get_list($list_id);
			}
		}

		$payload = array(
			'user' => $user,
			'subscribed_lists' => $subscribed_lists,
			'subscription' => $subscription,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				if( isset( $webhook['settings']['wpwhpro_newsletter_trigger_on_list'] ) && ! empty( $webhook['settings']['wpwhpro_newsletter_trigger_on_list'] ) ){
					$is_valid = false;

					foreach( $subscribed_lists as $single_list ){
						if( in_array( $single_list->id, $webhook['settings']['wpwhpro_newsletter_trigger_on_list'] ) ){
							$is_valid = true;
						}
					}
				}
			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_news_form_submitted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user' => 
			array (
			  'name' => 'Jon Doe',
			  'email' => 'jon@doe.test',
			  'token' => '438d89a854',
			  'language' => '',
			  'status' => 'C',
			  'id' => '8',
			  'profile' => NULL,
			  'created' => '2021-07-23 11:38:31',
			  'updated' => '1627033111',
			  'last_activity' => '0',
			  'followup_step' => '0',
			  'followup_time' => '0',
			  'followup' => '0',
			  'surname' => '',
			  'sex' => 'n',
			  'feed_time' => '0',
			  'feed' => '0',
			  'referrer' => 'widget',
			  'ip' => '127.0.0.1',
			  'wp_user_id' => '0',
			  'http_referer' => '',
			  'geo' => '0',
			  'country' => '',
			  'region' => '',
			  'city' => '',
			  'bounce_type' => '',
			  'bounce_time' => '0',
			  'unsub_email_id' => '0',
			  'unsub_time' => '0',
			  'list_1' => '1',
			  'list_2' => '1',
			  'list_3' => '0',
			  'list_4' => '0',
			  'list_5' => '0',
			  'list_6' => '0',
			  'list_7' => '0',
			  'list_8' => '0',
			  'list_9' => '0',
			  'list_10' => '0',
			  'list_11' => '0',
			  'list_12' => '0',
			  'list_13' => '0',
			  'list_14' => '0',
			  'list_15' => '0',
			  'list_16' => '0',
			  'list_17' => '0',
			  'list_18' => '0',
			  'list_19' => '0',
			  'list_20' => '0',
			  'list_21' => '0',
			  'list_22' => '0',
			  'list_23' => '0',
			  'list_24' => '0',
			  'list_25' => '0',
			  'list_26' => '0',
			  'list_27' => '0',
			  'list_28' => '0',
			  'list_29' => '0',
			  'list_30' => '0',
			  'list_31' => '0',
			  'list_32' => '0',
			  'list_33' => '0',
			  'list_34' => '0',
			  'list_35' => '0',
			  'list_36' => '0',
			  'list_37' => '0',
			  'list_38' => '0',
			  'list_39' => '0',
			  'list_40' => '0',
			  'profile_1' => '',
			  'profile_2' => '',
			  'profile_3' => '',
			  'profile_4' => '',
			  'profile_5' => '',
			  'profile_6' => '',
			  'profile_7' => '',
			  'profile_8' => '',
			  'profile_9' => '',
			  'profile_10' => '',
			  'profile_11' => '',
			  'profile_12' => '',
			  'profile_13' => '',
			  'profile_14' => '',
			  'profile_15' => '',
			  'profile_16' => '',
			  'profile_17' => '',
			  'profile_18' => '',
			  'profile_19' => '',
			  'profile_20' => '',
			  'test' => '0',
			),
			'subscribed_lists' => 
			array (
			  1 => 
			  array (
				'id' => 1,
				'name' => 'Demo list',
				'status' => 1,
				'forced' => false,
				'checked' => false,
				'show_on_subscription' => false,
				'show_on_profile' => false,
				'languages' => 
				array (
				),
			  ),
			  2 => 
			  array (
				'id' => 2,
				'name' => 'Demo list 2',
				'status' => 1,
				'forced' => false,
				'checked' => false,
				'show_on_subscription' => false,
				'show_on_profile' => false,
				'languages' => 
				array (
				),
			  ),
			),
			'subscription' => 
			array (
			  'data' => 
			  array (
				'email' => 'jon@doe.test',
				'name' => 'Jon Doe',
				'surname' => NULL,
				'sex' => NULL,
				'language' => '',
				'referrer' => 'widget',
				'http_referrer' => NULL,
				'ip' => NULL,
				'country' => NULL,
				'region' => NULL,
				'city' => NULL,
				'lists' => 
				array (
				  1 => 1,
				  2 => 1,
				),
				'profiles' => 
				array (
				),
				'http_referer' => 'https://yourdomain.test/newsletter/?nm=confirmed&nk=7-ee6e87eab6',
			  ),
			  'spamcheck' => true,
			  'optin' => 'single',
			  'if_exists' => 0,
			  'send_emails' => true,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.