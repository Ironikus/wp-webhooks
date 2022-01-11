<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_ninjaforms_Triggers_ninjaforms_submit' ) ) :

 /**
  * Load the ninjaforms_submit trigger
  *
  * @since 4.2.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_ninjaforms_Triggers_ninjaforms_submit {

  /**
   * Register the actual functionality of the webhook
   *
   * @return array The registered callbacks
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'ninja_forms_after_submission',
				'callback' => array( $this, 'wpwh_trigger_ninjaforms_submit' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-ninjaforms_submit-description";

		$validated_forms = array();
		if( class_exists( 'Ninja_Forms' ) ){
			$forms = Ninja_Forms()->form()->get_forms();

			if ( ! empty( $forms ) ) {
				foreach ( $forms as $form ) {
					$validated_forms[ $form->get_id() ] = esc_html( $form->get_setting( 'title' ) );
				}
			}
		}

		$parameter = array(
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) ID of the form that was submitted.', $translation_ident ) ),
			'actions' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further information about what happened after the form submission.', $translation_ident ) ),
			'form_submit_data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data that was submitted via the form.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'ninjaforms_submit',
			'post_delay' => false,
			'trigger_hooks' => array(
				array( 
					'hook' => 'ninja_forms_after_submission',
					'url' => 'https://developer.ninjaforms.com/codex/submission-processing-hooks/',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_ninja_forms' => array(
					'id'		  => 'wpwhpro_ninja_forms',
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
			'trigger'		   => 'ninjaforms_submit',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an "Ninja Forms" form was submitted.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'ninjaforms',
		);

	}

	/**
	 * Triggers once a new Ninja Forms form was submitted
	 *
	 * @param  $form The Ninja Forms form
	 */
	public function wpwh_trigger_ninjaforms_submit( $form ){

		$form_id = 0;
		if( isset( $form['form_id'] ) ){
			$form_id = $form['form_id'];
		}

		$actions = null;
		if( isset( $form['actions'] ) ){
			$actions = $form['actions'];
		}

		$form_submit_data = array();
		if( isset( $form['fields'] ) ){
			foreach ( $form['fields'] as $field ) {
				$form_submit_data[ $field['id'] ] = array(
					'field_id' => $field['id'],
					'key' => $field['key'],
					'value' => $field['value'],
					'label' => $field['label'],
				);
			}
		}

		$payload = array(
			'form_id' => $form_id,
			'actions' => $actions,
			'form_submit_data' => $form_submit_data,
		);

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ninjaforms_submit' );
		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				if( isset( $webhook['settings']['wpwhpro_ninja_forms'] ) && ! empty( $webhook['settings']['wpwhpro_ninja_forms'] ) ){
					if( ! in_array( $form_id, $webhook['settings']['wpwhpro_ninja_forms'] ) ){
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

		do_action( 'wpwhpro/integrations/ninjaforms/triggers/ninjaforms_submit', $payload, $form );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'form_id' => '1',
			'actions' => 
			array (
			  'save' => 
			  array (
				'hidden' => 
				array (
				  0 => 'submit',
				),
				'sub_id' => 1235,
			  ),
			  'email' => 
			  array (
				'to' => 'admin@domain.test',
				'headers' => 
				array (
				  0 => 'Content-Type: text/html',
				  1 => 'charset=UTF-8',
				  2 => 'X-Ninja-Forms:ninja-forms',
				  3 => 'From: Admin <admin@domain.test>',
				  4 => 'Reply-to: jondoe@domain.test <jondoe@domain.test>',
				),
				'attachments' => 
				array (
				),
				'sent' => true,
			  ),
			  'success_message' => '<p>Form submittedted successfully.</p>
		  <p>A confirmation email was sent to jondoe@domain.test.</p>
		  ',
			),
			'form_submit_data' => 
			array (
			  1 => 
			  array (
				'field_id' => 1,
				'key' => 'name',
				'value' => 'Jon Doe',
				'label' => 'Name',
			  ),
			  2 => 
			  array (
				'field_id' => 2,
				'key' => 'email',
				'value' => 'jondoe@domain.test',
				'label' => 'Email',
			  ),
			  3 => 
			  array (
				'field_id' => 3,
				'key' => 'message',
				'value' => 'This is a sample message.',
				'label' => 'Message',
			  ),
			  4 => 
			  array (
				'field_id' => 4,
				'key' => 'submit',
				'value' => '',
				'label' => 'Submit',
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.