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

		/*
		* Register the post delete trigger as an element
		*
		* @since 1.2
		*/
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

			ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on form submission of a \"Gravity Form\" form, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On Gravity Form Submit</strong> (gf_submit) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On Gravity Form Submit</strong> (gf_submit)", $translation_ident ); ?></h4>
<ol>
	<li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you can receive data on the URL once the trigger fires.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>gform_after_submission</strong> hook of Gravity Forms:", $translation_ident ); ?> 
<a title="docs.gravityforms.com" target="_blank" href="https://docs.gravityforms.com/gform_after_submission/">https://docs.gravityforms.com/gform_after_submission/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'gform_after_submission', array( $this, 'ironikus_trigger_gf_submit' ), 20, 2 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (gf_submit) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
<br><br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
	<li><?php echo WPWHPRO()->helpers->translate( "In case you don't need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "You can use the <strong>Send demo</strong> button to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Within the <strong>Settings</strong> link next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don't forget to save the settings once you changed them - the button is at the end of the popup.)", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "You can also check the response you get from the demo webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
			$description = ob_get_clean();

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