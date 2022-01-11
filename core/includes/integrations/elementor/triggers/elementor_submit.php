<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_elementor_Triggers_elementor_submit' ) ) :

 /**
  * Load the elementor_submit trigger
  *
  * @since 4.2.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_elementor_Triggers_elementor_submit {

	public function is_active(){
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	public function get_details(){

		$translation_ident = "action-elementor_submit-description";
		$validated_forms = array();
		$elementor_helpers = WPWHPRO()->integrations->get_helper( 'elementor', 'elementor_helpers' );
		$forms = $elementor_helpers->get_forms();
		foreach( $forms as $form_id => $form ){
			$form_name = $form_id;
			if( isset( $form->form_name ) ){
				$form_name = $form->form_name;
			}

			$validated_forms[ $form_id ] = $form_name;
		}

		$parameter = array(
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the submited form.', $translation_ident ) ),
			'form_type' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The type of the submitted form.', $translation_ident ) ),
			'form_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The form name.', $translation_ident ) ),
			'form_data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data that was submitted via the form.', $translation_ident ) ),
			'form_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further meta data about the form, such as page URL, submitted time, etc.', $translation_ident ) ),
			'form_files' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Files hat have been submitted along the side of the form.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'elementor_submit',
			'post_delay' => false,
			'trigger_hooks' => array(
				array( 
					'hook' => 'elementor_pro/forms/new_record',
					'url' => 'https://developers.elementor.com/forms-api/#Form_New_Record_Action',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_elementorpro_forms' => array(
					'id'		  => 'wpwhpro_elementorpro_forms',
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
			'trigger'		   => 'elementor_submit',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an "Elementor Pro" form was submitted.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'elementor',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
			'form_id' => 'e174e36',
			'form_type' => 'form',
			'form_name' => 'New Form',
			'form_data' => 
			array (
			  'name' => 'Jon Doe',
			  'email' => 'jon@doe.com',
			  'message' => 'This is some demo content.',
			),
			'form_meta' => 
			array (
			  'date' => 
			  array (
				'title' => 'Date',
				'value' => 'July 1, 2021',
			  ),
			  'time' => 
			  array (
				'title' => 'Time',
				'value' => '5:45 am',
			  ),
			  'page_url' => 
			  array (
				'title' => 'Page URL',
				'value' => 'https://yourdomain.test/elementor-915/',
			  ),
			  'user_agent' => 
			  array (
				'title' => 'User Agent',
				'value' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
			  ),
			  'remote_ip' => 
			  array (
				'title' => 'Remote IP',
				'value' => '127.0.0.1',
			  ),
			  'credit' => 
			  array (
				'title' => 'Powered by',
				'value' => 'Elementor',
			  ),
			),
			'form_files' => 
			array (
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.