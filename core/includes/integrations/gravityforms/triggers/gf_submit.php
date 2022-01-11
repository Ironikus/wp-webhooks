<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_gravityforms_Triggers_gf_submit' ) ) :

	/**
	 * Load the gf_submit trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_gravityforms_Triggers_gf_submit {

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
                    'hook' => 'gform_after_submission',
                    'callback' => array( $this, 'ironikus_trigger_gf_submit' ),
                    'priority' => 10,
                    'arguments' => 2,
					'delayed' => true,
                ),
            );

		}

		public function get_details(){

			$translation_ident = "trigger-gf_submit-description";

			$validated_forms = array();
			if( class_exists( 'GFFormsModel' ) ){
				$forms = GFFormsModel::get_forms();

				foreach ( $forms as $form ) {
					$validated_forms[ $form->id ] = esc_html( $form->title );
				}
			}

			$parameter = array(
				'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the form that was currently submitted.', $translation_ident ) ),
				'lead' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full data that was submitted within the form.', $translation_ident ) ),
				'form' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full form data, including field definitions, etc.', $translation_ident ) ),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Form submitted',
				'webhook_slug' => 'gf_submit',
				'post_delay' => true,
				'trigger_hooks' => array(
					array( 
						'hook' => 'gform_after_submission',
						'url' => 'https://docs.gravityforms.com/gform_after_submission/',
					),
				)
			) );

			$settings = array(
				'load_default_settings' => true,
				'data' => array(
					'wpwhpro_gf_submit_trigger_on_forms' => array(
						'id'		  => 'wpwhpro_gf_submit_trigger_on_forms',
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
				'trigger'		   => 'gf_submit',
				'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
				'parameter'		 => $parameter,
				'settings'		  => $settings,
				'returns_code'	  => $this->get_demo( array() ),
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a Gravity Form submission.', $translation_ident ),
				'description'	   => $description,
				'callback'		  => 'test_gf_submit',
				'integration'	   => 'gravityforms',
				'premium' => false,
			);

		}

		public function ironikus_trigger_gf_submit( $lead, $form ){

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'gf_submit' );
			$form_id = ( is_array( $form ) && isset( $form['id'] ) ) ? $form['id'] : 0;
			$data_array = array(
				'form_id' => $form_id,
				'lead'	  => $lead,
				'form' => $form,
			);
			$response_data = array();

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_gf_submit_trigger_on_forms' && ! empty( $settings_data ) ){
							if( ! in_array( $form_id, $settings_data ) ){
								$is_valid = false;
							}
						}

					}
				}

				if( $is_valid ) {
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_gf_submit', $data_array, $response_data );
		}

		/*
		* Register the demo post delete trigger callback
		*
		* @since 1.2
		*/
		public function get_demo( $options = array() ) {

			$data = array (
				'form_id' => 1,
				'lead' => 
				array (
				  1 => 'Some single line text',
				  2 => 'This is some paragrap text
			  With breaks.',
				  3 => 'Second Choice',
				  4 => '["Second Choice","Third Choice"]',
				  5 => '123',
				  7 => 'Second Choice',
				  8 => '',
				  'id' => '12',
				  'status' => 'active',
				  'form_id' => '1',
				  'ip' => '127.0.0.1',
				  'source_url' => 'https://yourdomain.com/your-url-path',
				  'currency' => 'USD',
				  'post_id' => NULL,
				  'date_created' => '2021-05-27 07:00:51',
				  'date_updated' => '2021-05-27 07:00:51',
				  'is_starred' => 0,
				  'is_read' => 0,
				  'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36',
				  'payment_status' => NULL,
				  'payment_date' => NULL,
				  'payment_amount' => NULL,
				  'payment_method' => '',
				  'transaction_id' => NULL,
				  'is_fulfilled' => NULL,
				  'created_by' => '1',
				  'transaction_type' => NULL,
				  '6.1' => 'First Choice',
				  '6.2' => 'Second Choice',
				  '6.3' => '',
				),
				'form' => 
				array (
				  'title' => 'Test form',
				  'description' => 'test webhooks',
				  'labelPlacement' => 'top_label',
				  'descriptionPlacement' => 'below',
				  'button' => 
				  array (
					'type' => 'text',
					'text' => 'Submit',
					'imageUrl' => '',
				  ),
				  'fields' => 
				  array (
					0 => 
					array (
					  'type' => 'text',
					  'id' => 1,
					  'label' => 'Single Text Field',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'choices' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'enablePasswordInput' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'enableCopyValuesOption' => '',
					  'enablePrice' => '',
					  'is_field_hidden' => '',
					  'displayOnly' => '',
					  'form_id' => '',
					),
					1 => 
					array (
					  'type' => 'textarea',
					  'id' => 2,
					  'label' => 'Paragraph Text',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'choices' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'form_id' => '',
					  'useRichTextEditor' => false,
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					  'enablePrice' => '',
					),
					2 => 
					array (
					  'type' => 'select',
					  'id' => 3,
					  'label' => 'Dropdown',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'choices' => 
					  array (
						0 => 
						array (
						  'text' => 'First Choice',
						  'value' => 'First Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						1 => 
						array (
						  'text' => 'Second Choice',
						  'value' => 'Second Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						2 => 
						array (
						  'text' => 'Third Choice',
						  'value' => 'Third Choice',
						  'isSelected' => false,
						  'price' => '',
						),
					  ),
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'enablePrice' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					  'enableEnhancedUI' => '',
					),
					3 => 
					array (
					  'type' => 'multiselect',
					  'id' => 4,
					  'label' => 'Multi Select',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'storageType' => 'json',
					  'inputs' => NULL,
					  'choices' => 
					  array (
						0 => 
						array (
						  'text' => 'First Choice',
						  'value' => 'First Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						1 => 
						array (
						  'text' => 'Second Choice',
						  'value' => 'Second Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						2 => 
						array (
						  'text' => 'Third Choice',
						  'value' => 'Third Choice',
						  'isSelected' => false,
						  'price' => '',
						),
					  ),
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'conditionalLogic' => '',
					  'enableEnhancedUI' => false,
					  'productField' => '',
					  'multiSelectSize' => '',
					  'enablePrice' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					),
					4 => 
					array (
					  'type' => 'number',
					  'id' => 5,
					  'label' => 'Number',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'numberFormat' => 'decimal_dot',
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'choices' => '',
					  'conditionalLogic' => '',
					  'enableCalculation' => false,
					  'rangeMin' => '',
					  'rangeMax' => '',
					  'productField' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					  'enablePrice' => '',
					),
					5 => 
					array (
					  'type' => 'checkbox',
					  'id' => 6,
					  'label' => 'Checkboxes',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'choices' => 
					  array (
						0 => 
						array (
						  'text' => 'First Choice',
						  'value' => 'First Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						1 => 
						array (
						  'text' => 'Second Choice',
						  'value' => 'Second Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						2 => 
						array (
						  'text' => 'Third Choice',
						  'value' => 'Third Choice',
						  'isSelected' => false,
						  'price' => '',
						),
					  ),
					  'inputs' => 
					  array (
						0 => 
						array (
						  'id' => '6.1',
						  'label' => 'First Choice',
						  'name' => '',
						),
						1 => 
						array (
						  'id' => '6.2',
						  'label' => 'Second Choice',
						  'name' => '',
						),
						2 => 
						array (
						  'id' => '6.3',
						  'label' => 'Third Choice',
						  'name' => '',
						),
					  ),
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'enableSelectAll' => '',
					  'enablePrice' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					),
					6 => 
					array (
					  'type' => 'radio',
					  'id' => 7,
					  'label' => 'Radio Buttons',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'choices' => 
					  array (
						0 => 
						array (
						  'text' => 'First Choice',
						  'value' => 'First Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						1 => 
						array (
						  'text' => 'Second Choice',
						  'value' => 'Second Choice',
						  'isSelected' => false,
						  'price' => '',
						),
						2 => 
						array (
						  'text' => 'Third Choice',
						  'value' => 'Third Choice',
						  'isSelected' => false,
						  'price' => '',
						),
					  ),
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'enableOtherChoice' => '',
					  'enablePrice' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					),
					7 => 
					array (
					  'type' => 'hidden',
					  'id' => 8,
					  'label' => 'Hidden Field',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'choices' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'displayOnly' => '',
					  'enableCopyValuesOption' => '',
					  'enablePrice' => '',
					  'form_id' => '',
					),
					8 => 
					array (
					  'type' => 'html',
					  'id' => 9,
					  'label' => 'HTML Block',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'displayOnly' => true,
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'choices' => '',
					  'conditionalLogic' => '',
					  'content' => '<span>This is a HTML block</span>',
					  'productField' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'form_id' => '',
					),
					9 => 
					array (
					  'type' => 'section',
					  'id' => 10,
					  'label' => 'Section Break',
					  'adminLabel' => '',
					  'isRequired' => false,
					  'size' => 'medium',
					  'errorMessage' => '',
					  'visibility' => 'visible',
					  'inputs' => NULL,
					  'displayOnly' => true,
					  'formId' => 1,
					  'description' => '',
					  'allowsPrepopulate' => false,
					  'inputMask' => false,
					  'inputMaskValue' => '',
					  'inputMaskIsCustom' => false,
					  'maxLength' => '',
					  'inputType' => '',
					  'labelPlacement' => '',
					  'descriptionPlacement' => '',
					  'subLabelPlacement' => '',
					  'placeholder' => '',
					  'cssClass' => '',
					  'inputName' => '',
					  'noDuplicates' => false,
					  'defaultValue' => '',
					  'choices' => '',
					  'conditionalLogic' => '',
					  'productField' => '',
					  'multipleFiles' => false,
					  'maxFiles' => '',
					  'calculationFormula' => '',
					  'calculationRounding' => '',
					  'enableCalculation' => '',
					  'disableQuantity' => false,
					  'displayAllCategories' => false,
					  'useRichTextEditor' => false,
					  'pageNumber' => 1,
					  'fields' => '',
					  'failed_validation' => false,
					  'validation_message' => '',
					  'form_id' => '',
					),
				  ),
				  'version' => '2.4.18.4',
				  'id' => 1,
				  'nextFieldId' => 11,
				  'useCurrentUserAsAuthor' => true,
				  'postContentTemplateEnabled' => false,
				  'postTitleTemplateEnabled' => false,
				  'postTitleTemplate' => '',
				  'postContentTemplate' => '',
				  'lastPageButton' => NULL,
				  'pagination' => NULL,
				  'firstPageCssClass' => NULL,
				  'notifications' => 
				  array (
					'60ad29708aec4' => 
					array (
					  'id' => '60ad29708aec4',
					  'isActive' => true,
					  'to' => '{admin_email}',
					  'name' => 'Admin Notification',
					  'event' => 'form_submission',
					  'toType' => 'email',
					  'subject' => 'New submission from {form_title}',
					  'message' => '{all_fields}',
					),
				  ),
				  'confirmations' => 
				  array (
					'60ad29708c2d2' => 
					array (
					  'id' => '60ad29708c2d2',
					  'name' => 'Default Confirmation',
					  'isDefault' => true,
					  'type' => 'message',
					  'message' => 'Thanks for contacting us! We will get in touch with you shortly.',
					  'url' => '',
					  'pageId' => '',
					  'queryString' => '',
					),
				  ),
				  'is_active' => '1',
				  'date_created' => '2021-05-25 16:44:32',
				  'is_trash' => '0',
				  'confirmation' => 
				  array (
					'id' => '60ad29708c2d2',
					'name' => 'Default Confirmation',
					'isDefault' => true,
					'type' => 'message',
					'message' => 'Thanks for contacting us! We will get in touch with you shortly.',
					'url' => '',
					'pageId' => '',
					'queryString' => '',
				  ),
				),
			  );

			return $data;
		}

	}

endif; // End if class_exists check.