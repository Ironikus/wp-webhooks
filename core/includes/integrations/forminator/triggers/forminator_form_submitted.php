<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_forminator_Triggers_forminator_form_submitted' ) ) :

 /**
  * Load the forminator_form_submitted trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_forminator_Triggers_forminator_form_submitted {

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
				'hook' => 'forminator_form_after_save_entry',
				'callback' => array( $this, 'forminator_form_after_save_entry_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-forminator_form_submitted-description";
		$validated_forms = array();
		if( class_exists( 'Forminator_API' ) ){
			$forms = Forminator_API::get_forms( null, 1, 999 );

			if ( ! empty( $forms ) ) {
				foreach ( $forms as $form ) {
					
					$form_name = $form->name;
					if( isset( $form->settings ) && is_array( $form->settings ) && isset( $form->settings['form_name'] ) ){
						$form_name = $form->settings['form_name'];
					}
						 
					$validated_forms[ $form->id ] = esc_html( $form_name );
				}
			}
		}

		$parameter = array(
			'submission' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data that was submitted within the form.', $translation_ident ) ),
			'response' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the response data that was sent back to the form caller.', $translation_ident ) ),
			'form' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data about the form definition (E.g. fields, standard text, etc.).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'forminator_form_submitted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'forminator_form_after_save_entry',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_forminator_trigger_on_forms' => array(
					'id'		  => 'wpwhpro_forminator_trigger_on_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_forms,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'forminator_form_submitted',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form is submitted within Forminator.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'forminator',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a user submits a Forminator form
	 *
	   * @param int $form_id - the form id
	 * @param array $response - the post response
	 */
	public function forminator_form_after_save_entry_callback( $form_id, $response ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'forminator_form_submitted' );

		$form = Forminator_API::get_form( $form_id );

		$payload = array(
			'submission' => $_POST,
			'response' => $response,
			'form' => $form,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				if( isset( $webhook['settings']['wpwhpro_forminator_trigger_on_forms'] ) && ! empty( $webhook['settings']['wpwhpro_forminator_trigger_on_forms'] ) ){
					if( ! in_array( $form_id, $webhook['settings']['wpwhpro_forminator_trigger_on_forms'] ) ){
						$is_valid = false;
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

		do_action( 'wpwhpro/webhooks/trigger_forminator_form_submitted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'submission' => 
			array (
			  'name-1' => 'John',
			  'email-1' => 'jon@doe.test',
			  'phone-1' => '+123456789',
			  'textarea-1' => 'This is some demo content.',
			  'referer_url' => 'https://yourdomain.test/wp-admin/post.php?post=1311&action=edit',
			  'forminator_nonce' => 'bcde0c129e',
			  '_wp_http_referer' => '/forminator/',
			  'form_id' => '1313',
			  'page_id' => '1311',
			  'form_type' => 'default',
			  'current_url' => 'https://yourdomain.test/forminator/',
			  'render_id' => '0',
			  'action' => 'forminator_submit_form_custom-forms',
			),
			'response' => 
			array (
			  'message' => 'Thank you for contacting us, we will be in touch shortly.',
			  'success' => true,
			  'behav' => 'behaviour-thankyou',
			),
			'form' => 
			array (
			  'id' => 1313,
			  'name' => 'contact-1',
			  'client_id' => NULL,
			  'fields' => 
			  array (
				0 => 
				array (
				  'slug' => 'name-1',
				  'form_id' => 'wrapper-1511347711918-1669',
				),
				1 => 
				array (
				  'slug' => 'email-1',
				  'form_id' => 'wrapper-1511347712118-1739',
				),
				2 => 
				array (
				  'slug' => 'phone-1',
				  'form_id' => 'wrapper-1311247712118-1194',
				),
				3 => 
				array (
				  'slug' => 'textarea-1',
				  'form_id' => 'wrapper-1988247712118-9871',
				),
			  ),
			  'settings' => 
			  array (
				'pagination-header' => 'nav',
				'paginationData' => 
				array (
				  'pagination-header-design' => 'show',
				  'pagination-header' => 'nav',
				),
				'formName' => 'Contact 1',
				'version' => '1.14.12.1',
				'form-border-style' => 'none',
				'form-padding' => '',
				'form-border' => '',
				'fields-style' => 'open',
				'validation' => 'on_submit',
				'akismet-protection' => true,
				'form-style' => 'default',
				'enable-ajax' => 'true',
				'autoclose' => 'true',
				'submission-indicator' => 'show',
				'indicator-label' => 'Submitting...',
				'form-type' => 'default',
				'submission-behaviour' => 'behaviour-thankyou',
				'thankyou-message' => 'Thank you for contacting us, we will be in touch shortly.',
				'submitData' => 
				array (
				  'custom-submit-text' => 'Send Message',
				  'custom-invalid-form-message' => 'Error: Your form is not valid, please fix the errors!',
				),
				'validation-inline' => true,
				'form-expire' => 'no_expire',
				'form-padding-top' => '0',
				'form-padding-right' => '0',
				'form-padding-bottom' => '0',
				'form-padding-left' => '0',
				'form-border-width' => '0',
				'form-border-radius' => '0',
				'cform-label-font-family' => 'Roboto',
				'cform-label-custom-family' => '',
				'cform-label-font-size' => '12',
				'cform-label-font-weight' => 'bold',
				'cform-title-font-family' => 'Roboto',
				'cform-title-custom-family' => '',
				'cform-title-font-size' => '45',
				'cform-title-font-weight' => 'normal',
				'cform-title-text-align' => 'left',
				'cform-subtitle-font-family' => 'Roboto',
				'cform-subtitle-custom-font' => '',
				'cform-subtitle-font-size' => '18',
				'cform-subtitle-font-weight' => 'normal',
				'cform-subtitle-text-align' => 'left',
				'cform-input-font-family' => 'Roboto',
				'cform-input-custom-font' => '',
				'cform-input-font-size' => '16',
				'cform-input-font-weight' => 'normal',
				'cform-radio-font-family' => 'Roboto',
				'cform-radio-custom-font' => '',
				'cform-radio-font-size' => '14',
				'cform-radio-font-weight' => 'normal',
				'cform-select-font-family' => 'Roboto',
				'cform-select-custom-family' => '',
				'cform-select-font-size' => '16',
				'cform-select-font-weight' => 'normal',
				'cform-multiselect-font-family' => 'Roboto',
				'cform-multiselect-custom-font' => '',
				'cform-multiselect-font-size' => '16',
				'cform-multiselect-font-weight' => 'normal',
				'cform-dropdown-font-family' => 'Roboto',
				'cform-dropdown-custom-font' => '',
				'cform-dropdown-font-size' => '16',
				'cform-dropdown-font-weight' => 'normal',
				'cform-calendar-font-family' => 'Roboto',
				'cform-calendar-custom-font' => '',
				'cform-calendar-font-size' => '13',
				'cform-calendar-font-weight' => 'normal',
				'cform-button-font-family' => 'Roboto',
				'cform-button-custom-font' => '',
				'cform-button-font-size' => '14',
				'cform-button-font-weight' => '500',
				'cform-timeline-font-family' => 'Roboto',
				'cform-timeline-custom-font' => '',
				'cform-timeline-font-size' => '12',
				'cform-timeline-font-weight' => 'normal',
				'cform-pagination-font-family' => '',
				'cform-pagination-custom-font' => '',
				'cform-pagination-font-size' => '16',
				'cform-pagination-font-weight' => 'normal',
				'payment_require_ssl' => false,
				'submission-file' => 'delete',
				'form_id' => 1313,
				'form_name' => 'Contact 1',
				'form_status' => 'draft',
			  ),
			  'notifications' => 
			  array (
				0 => 
				array (
				  'slug' => 'notification-1234-4567',
				  'label' => 'Admin Email',
				  'email-recipients' => 'default',
				  'recipients' => 'admin@yourdomain.test',
				  'email-subject' => 'New Form Entry #{submission_id} for {form_name}',
				  'email-editor' => 'You have a new website form submission: <br/> {all_fields} <br/>---<br/> This message was sent from {site_url}.',
				  'email-attachment' => 'true',
				),
			  ),
			  'raw' => 
			  array (
				'ID' => 1313,
				'post_author' => '1',
				'post_date' => '2021-07-24 17:00:51',
				'post_date_gmt' => '2021-07-24 17:00:51',
				'post_content' => '',
				'post_title' => 'contact-1',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => 'contact-1',
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2021-07-24 17:00:51',
				'post_modified_gmt' => '2021-07-24 17:00:51',
				'post_content_filtered' => '',
				'post_parent' => 0,
				'guid' => 'https://yourdomain.test/?post_type=forminator_forms&#038;p=1313',
				'menu_order' => 0,
				'post_type' => 'forminator_forms',
				'post_mime_type' => '',
				'comment_count' => '0',
				'filter' => 'raw',
			  ),
			  'status' => 'publish',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.