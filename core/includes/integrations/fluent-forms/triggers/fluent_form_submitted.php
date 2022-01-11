<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_forms_Triggers_fluent_form_submitted' ) ) :

 /**
  * Load the fluent_form_submitted trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_forms_Triggers_fluent_form_submitted {

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
				'hook' => 'fluentform_before_form_actions_processing',
				'callback' => array( $this, 'fluentform_before_form_actions_processing_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-fluent_form_submitted-description";
		$validated_forms = array();
		if( function_exists( 'wpFluent' ) ) {
			$forms = wpFluent()->table( 'fluentform_forms' )
							   ->select( [ 'id', 'title' ] )
							   ->orderBy( 'id', 'DESC' )
							   ->get();

			if( ! empty( $forms ) ) {
				foreach ( $forms as $form ) {
					$validated_forms[ $form->id ] = esc_html( $form->title );
				}
			}
		}

		$parameter = array(
			'form_data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data that was submitted within the form.', $translation_ident ) ),
			'form' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data about the form definition (E.g. fields, standard text, etc.).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'fluent_form_submitted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluentform_before_form_actions_processing',
					'url' => 'https://fluentforms.com/docs/fluentform_before_form_actions_processing/',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_fluent_forms_trigger_on_forms' => array(
					'id'		  => 'wpwhpro_fluent_forms_trigger_on_forms',
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
			'trigger'		   => 'fluent_form_submitted',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form is submitted within Fluent Forms.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-forms',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a user submits a Fluent Forms form
	 *
	 * @param array $insertId The ID of the data inserted into the database.
	 * @param array $formData   Current form data.
	 * @param object $form   Current form.
	 */
	public function fluentform_before_form_actions_processing_callback( $insertId, $formData, $form ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'fluent_form_submitted' );

		$payload = array(
			'form_data' => $formData,
			'form' => $form,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				if( isset( $webhook['settings']['wpwhpro_fluent_forms_trigger_on_forms'] ) && ! empty( $webhook['settings']['wpwhpro_fluent_forms_trigger_on_forms'] ) ){
					if( ! in_array( $form->id, $webhook['settings']['wpwhpro_fluent_forms_trigger_on_forms'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_fluent_form_submitted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'form_data' => 
			array (
			  '__fluent_form_embded_post_id' => '1309',
			  '_fluentform_1_fluentformnonce' => 'f986529d9c',
			  '_wp_http_referer' => '/fluent-forms/',
			  'names' => 
			  array (
				'first_name' => 'Jon',
				'last_name' => 'Doe',
			  ),
			  'email' => 'jon@doe.test',
			  'subject' => 'Test Subject Line',
			  'message' => 'This is some demo content.',
			),
			'form' => 
			array (
			  'id' => '1',
			  'title' => 'Contact Form Demo',
			  'status' => 'published',
			  'appearance_settings' => NULL,
			  'form_fields' => '{"fields":[{"index":0,"element":"input_name","attributes":{"name":"names","data-type":"name-element"},"settings":{"container_class":"","admin_field_label":"Name","conditional_logics":[]},"fields":{"first_name":{"element":"input_text","attributes":{"type":"text","name":"first_name","value":"","id":"","class":"","placeholder":"First Name"},"settings":{"container_class":"","label":"First Name","help_message":"","visible":true,"validation_rules":{"required":{"value":false,"message":"This field is required"}},"conditional_logics":[]},"editor_options":{"template":"inputText"}},"middle_name":{"element":"input_text","attributes":{"type":"text","name":"middle_name","value":"","id":"","class":"","placeholder":"","required":false},"settings":{"container_class":"","label":"Middle Name","help_message":"","error_message":"","visible":false,"validation_rules":{"required":{"value":false,"message":"This field is required"}},"conditional_logics":[]},"editor_options":{"template":"inputText"}},"last_name":{"element":"input_text","attributes":{"type":"text","name":"last_name","value":"","id":"","class":"","placeholder":"Last Name","required":false},"settings":{"container_class":"","label":"Last Name","help_message":"","error_message":"","visible":true,"validation_rules":{"required":{"value":false,"message":"This field is required"}},"conditional_logics":[]},"editor_options":{"template":"inputText"}}},"editor_options":{"title":"Name Fields","element":"name-fields","icon_class":"ff-edit-name","template":"nameFields"},"uniqElKey":"el_1570866006692"},{"index":1,"element":"input_email","attributes":{"type":"email","name":"email","value":"","id":"","class":"","placeholder":"Email Address"},"settings":{"container_class":"","label":"Email","label_placement":"","help_message":"","admin_field_label":"","validation_rules":{"required":{"value":true,"message":"This field is required"},"email":{"value":true,"message":"This field must contain a valid email"}},"conditional_logics":[]},"editor_options":{"title":"Email Address","icon_class":"ff-edit-email","template":"inputText"},"uniqElKey":"el_1570866012914"},{"index":2,"element":"input_text","attributes":{"type":"text","name":"subject","value":"","class":"","placeholder":"Subject"},"settings":{"container_class":"","label":"Subject","label_placement":"","admin_field_label":"Subject","help_message":"","validation_rules":{"required":{"value":false,"message":"This field is required"}},"conditional_logics":{"type":"any","status":false,"conditions":[{"field":"","value":"","operator":""}]}},"editor_options":{"title":"Simple Text","icon_class":"ff-edit-text","template":"inputText"},"uniqElKey":"el_1570878958648"},{"index":3,"element":"textarea","attributes":{"name":"message","value":"","id":"","class":"","placeholder":"Your Message","rows":4,"cols":2},"settings":{"container_class":"","label":"Your Message","admin_field_label":"","label_placement":"","help_message":"","validation_rules":{"required":{"value":true,"message":"This field is required"}},"conditional_logics":{"type":"any","status":false,"conditions":[{"field":"","value":"","operator":""}]}},"editor_options":{"title":"Text Area","icon_class":"ff-edit-textarea","template":"inputTextarea"},"uniqElKey":"el_1570879001207"}],"submitButton":{"uniqElKey":"el_1524065200616","element":"button","attributes":{"type":"submit","class":""},"settings":{"align":"left","button_style":"default","container_class":"","help_message":"","background_color":"#409EFF","button_size":"md","color":"#ffffff","button_ui":{"type":"default","text":"Submit Form","img_url":""}},"editor_options":{"title":"Submit Button"}}}',
			  'has_payment' => '0',
			  'type' => '',
			  'conditions' => NULL,
			  'created_by' => '1',
			  'created_at' => '2021-07-24 16:01:06',
			  'updated_at' => '2021-07-24 16:01:06',
			  'settings' => 
			  array (
				'confirmation' => 
				array (
				  'redirectTo' => 'samePage',
				  'messageToShow' => 'Thank you for your message. We will get in touch with you shortly',
				  'customPage' => NULL,
				  'samePageFormBehavior' => 'hide_form',
				  'customUrl' => NULL,
				),
				'restrictions' => 
				array (
				  'limitNumberOfEntries' => 
				  array (
					'enabled' => false,
					'numberOfEntries' => NULL,
					'period' => 'total',
					'limitReachedMsg' => 'Maximum number of entries exceeded.',
				  ),
				  'scheduleForm' => 
				  array (
					'enabled' => false,
					'start' => NULL,
					'end' => NULL,
					'selectedDays' => 
					array (
					  0 => 'Monday',
					  1 => 'Tuesday',
					  2 => 'Wednesday',
					  3 => 'Thursday',
					  4 => 'Friday',
					  5 => 'Saturday',
					  6 => 'Sunday',
					),
					'pendingMsg' => 'Form submission is not started yet.',
					'expiredMsg' => 'Form submission is now closed.',
				  ),
				  'requireLogin' => 
				  array (
					'enabled' => false,
					'requireLoginMsg' => 'You must be logged in to submit the form.',
				  ),
				  'denyEmptySubmission' => 
				  array (
					'enabled' => false,
					'message' => 'Sorry, you cannot submit an empty form. Let\'s hear what you wanna say.',
				  ),
				),
				'layout' => 
				array (
				  'labelPlacement' => 'top',
				  'helpMessagePlacement' => 'with_label',
				  'errorMessagePlacement' => 'inline',
				  'cssClassName' => '',
				  'asteriskPlacement' => 'asterisk-right',
				),
				'delete_entry_on_submission' => 'no',
				'appendSurveyResult' => 
				array (
				  'enabled' => false,
				  'showLabel' => false,
				  'showCount' => false,
				),
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.