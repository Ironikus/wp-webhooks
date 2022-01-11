<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wpforms_Triggers_wpf_submit' ) ) :

 /**
  * Load the wpf_submit trigger
  *
  * @since 4.1.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wpforms_Triggers_wpf_submit {

	public function get_details(){

	  $translation_ident = "trigger-wpf_submit-description";

	  $validated_forms = array();
	  if( class_exists( 'WPForms_Form_Handler' ) ){
	   $forms_object = new WPForms_Form_Handler();

	   $forms = $forms_object->get( '', array(
		'orderby' => 'title'
	   ) );
	
	   if ( ! empty( $forms ) ) {
		foreach ( $forms as $form ) {
		 $validated_forms[ $form->ID ] = esc_html( $form->post_title );
		}
	   }
	  }

	  $parameter = array(
		'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the form that was currently submitted.', $translation_ident ) ),
		'entry_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the current form submission.', $translation_ident ) ),
		'entry' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full data that was submitted within the form.', $translation_ident ) ),
		'fields' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full form data, including field definitions, etc.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'wpf_submit',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpforms_process_complete',
					'url' => 'https://wpforms.com/developers/wpforms_process_complete/',
				),
			)
		) );

	  	$settings = array(
		'load_default_settings' => true,
		'data' => array(
		  'wpwhpro_wpf_submit_trigger_on_forms' => array(
			'id'	 => 'wpwhpro_wpf_submit_trigger_on_forms',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => $validated_forms,
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
		  ),
		)
	  );

	  return array(
		'trigger'	  => 'wpf_submit',
		'name'	   => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a WPForms form submission.', $translation_ident ),
		'description'	=> $description,
		'callback'	 => 'test_wpf_submit',
		'integration'	=> 'wpforms',
		'premium'	=> true,
	  );

	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

	  $data = array (
		'form_id' => '717',
		'entry_id' => 2,
		'entry' => 
		array (
		  'fields' => 
		  array (
			0 => 
			array (
			  'first' => 'Jon',
			  'last' => 'Doe',
			),
			1 => 'demo@email.test',
			2 => '(123) 456-7890',
			3 => 
			array (
			  'address1' => 'Demo  Street',
			  'address2' => '',
			  'city' => 'Demo City',
			  'state' => 'AL',
			  'postal' => '12345',
			),
			4 => '2',
			5 => '$ 20.00',
			6 => 'This is a demo message',
		  ),
		  'hp' => '',
		  'id' => '717',
		  'author' => '1',
		  'submit' => 'wpforms-submit',
		),
		'fields' => 
		array (
		  0 => 
		  array (
			'name' => 'Name',
			'value' => 'Jon Doe',
			'id' => 0,
			'type' => 'name',
			'first' => 'Jon',
			'middle' => '',
			'last' => 'Doe',
		  ),
		  1 => 
		  array (
			'name' => 'Email',
			'value' => 'demo@email.test',
			'id' => 1,
			'type' => 'email',
		  ),
		  2 => 
		  array (
			'name' => 'Phone',
			'value' => '(123) 456-7890',
			'id' => 2,
			'type' => 'phone',
		  ),
		  3 => 
		  array (
			'name' => 'Address',
			'value' => 'Demo  Street
	  Demo City, AL
	  12345',
			'id' => 3,
			'type' => 'address',
			'address1' => 'Demo Street',
			'address2' => '',
			'city' => 'Demo City',
			'state' => 'AL',
			'postal' => '12345',
			'country' => '',
		  ),
		  4 => 
		  array (
			'name' => 'Available Items',
			'value' => 'Second Item - &#36; 20.00',
			'value_choice' => 'Second Item',
			'value_raw' => '2',
			'amount' => '20.00',
			'amount_raw' => '20.00',
			'currency' => 'USD',
			'image' => '',
			'id' => 4,
			'type' => 'payment-multiple',
		  ),
		  5 => 
		  array (
			'name' => 'Total Amount',
			'value' => '&#36; 20.00',
			'amount' => '20.00',
			'amount_raw' => '20.00',
			'id' => 5,
			'type' => 'payment-total',
		  ),
		  6 => 
		  array (
			'name' => 'Comment or Message',
			'value' => 'This is a demo message',
			'id' => 6,
			'type' => 'textarea',
		  ),
		),
	  );

	  return $data;
	}

  }

endif; // End if class_exists check.