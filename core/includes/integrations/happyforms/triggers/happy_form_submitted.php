<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_happyforms_Triggers_happy_form_submitted' ) ) :

 /**
  * Load the happy_form_submitted trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_happyforms_Triggers_happy_form_submitted {

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
				'hook' => 'happyforms_submission_success',
				'callback' => array( $this, 'happyforms_submission_success_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-happy_form_submitted-description";
		$validated_forms = array();
		if( function_exists( 'happyforms_get_form_controller' ) ){
			$form_controller = happyforms_get_form_controller();
			$forms = $form_controller->do_get();

			if( ! empty( $forms ) ) {
				foreach( $forms as $form ) {
					$validated_forms[ $form["ID"] ] = $form["post_title"];
				}
			}
		}

		$parameter = array(
			'submission' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data that was submitted within the form.', $translation_ident ) ),
			'form' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The data about the form definition (E.g. fields, standard text, etc.).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'happy_form_submitted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'happyforms_submission_success',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_happyforms_trigger_on_forms' => array(
					'id'		  => 'wpwhpro_happyforms_trigger_on_forms',
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
			'trigger'		   => 'happy_form_submitted',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form is submitted within HappyForms.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'happyforms',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a user submits a HappyForms form
	 *
	 * @param array $submission Submission data.
	 * @param array $form   Current form data.
	 */
	public function happyforms_submission_success_callback( $submission, $form ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'happy_form_submitted' );

		$payload = array(
			'submission' => $submission,
			'form' => $form,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				if( isset( $webhook['settings']['wpwhpro_happyforms_trigger_on_forms'] ) && ! empty( $webhook['settings']['wpwhpro_happyforms_trigger_on_forms'] ) ){
					if( ! in_array( $form['ID'], $webhook['settings']['wpwhpro_happyforms_trigger_on_forms'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_happy_form_submitted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'submission' => 
			array (
			  'single_line_text_1' => 'Demo Text Value',
			  'radio_2' => 'Choice 3',
			  'email_3' => 'demoemail@domain.test',
			),
			'form' => 
			array (
			  'ID' => 1306,
			  'post_author' => '1',
			  'post_date' => '2021-07-24 15:16:09',
			  'post_date_gmt' => '2021-07-24 15:16:09',
			  'post_content' => '',
			  'post_title' => 'Second Test Form',
			  'post_excerpt' => '',
			  'post_status' => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_password' => '',
			  'post_name' => '1306',
			  'to_ping' => '',
			  'pinged' => '',
			  'post_modified' => '2021-07-24 15:16:09',
			  'post_modified_gmt' => '2021-07-24 15:16:09',
			  'post_content_filtered' => '',
			  'post_parent' => 0,
			  'guid' => 'https://yourdomain.test/?happyform=1306',
			  'menu_order' => 0,
			  'post_type' => 'happyform',
			  'post_mime_type' => '',
			  'comment_count' => '0',
			  'filter' => 'raw',
			  'ancestors' => 
			  array (
			  ),
			  'page_template' => '',
			  'post_category' => 
			  array (
			  ),
			  'tags_input' => 
			  array (
			  ),
			  'layout' => 
			  array (
				0 => 'single_line_text_1',
				1 => 'radio_2',
				2 => 'email_3',
			  ),
			  'parts' => 
			  array (
				0 => 
				array (
				  'type' => 'single_line_text',
				  'label' => 'Simple Text Field',
				  'label_placement' => 'show',
				  'description' => '',
				  'description_mode' => '',
				  'placeholder' => '',
				  'prefix' => '',
				  'suffix' => '',
				  'width' => 'full',
				  'css_class' => '',
				  'required' => 1,
				  'use_as_subject' => '',
				  'default_value' => '',
				  'id' => 'single_line_text_1',
				),
				1 => 
				array (
				  'type' => 'radio',
				  'label' => 'Single Choice Field',
				  'label_placement' => 'show',
				  'description' => '',
				  'description_mode' => '',
				  'width' => 'full',
				  'css_class' => '',
				  'display_type' => 'block',
				  'required' => 1,
				  'options' => 
				  array (
					0 => 
					array (
					  'is_default' => '',
					  'label' => 'Choice 1',
					  'description' => '',
					  'id' => 'radio_2_option_1627139744801',
					),
					1 => 
					array (
					  'is_default' => '',
					  'label' => 'Choice 2',
					  'description' => '',
					  'id' => 'radio_2_option_1627139750528',
					),
					2 => 
					array (
					  'is_default' => '',
					  'label' => 'Choice 3',
					  'description' => '',
					  'id' => 'radio_2_option_1627139754634',
					),
				  ),
				  'options_width' => 'auto',
				  'id' => 'radio_2',
				),
				2 => 
				array (
				  'type' => 'email',
				  'label' => 'Email Address',
				  'label_placement' => 'show',
				  'description' => '',
				  'description_mode' => '',
				  'placeholder' => '',
				  'prefix' => '',
				  'suffix' => '',
				  'width' => 'full',
				  'css_class' => '',
				  'required' => 1,
				  'default_value' => '',
				  'id' => 'email_3',
				),
			  ),
			  'form_direction' => '',
			  'form_width' => 0,
			  'form_padding' => '',
			  'form_hide_on_submit' => '',
			  'form_title' => '',
			  'form_title_alignment' => '',
			  'form_title_font_size' => '32',
			  'part_border' => '',
			  'part_border_location' => '',
			  'part_border_radius' => '',
			  'part_outer_padding' => '',
			  'part_inner_padding' => '',
			  'part_toggle_placeholders' => '',
			  'part_title_alignment' => '',
			  'part_title_font_size' => '16',
			  'part_title_font_weight' => '',
			  'part_title_label_placement' => 'above',
			  'part_description_alignment' => '',
			  'part_description_font_size' => '12',
			  'part_description_mode' => '',
			  'part_value_alignment' => '',
			  'part_value_font_size' => '16',
			  'submit_button_border' => '',
			  'submit_button_border_radius' => '',
			  'submit_button_width' => '',
			  'submit_button_padding' => '',
			  'submit_button_font_size' => '16',
			  'submit_button_font_weight' => '',
			  'submit_button_alignment' => '',
			  'submit_button_part_of_last_input' => '',
			  'color_primary' => '#000000',
			  'color_success_notice' => '#ebf9f0',
			  'color_success_notice_text' => '#1eb452',
			  'color_error' => '#f23000',
			  'color_error_notice' => '#ffeeea',
			  'color_error_notice_text' => '#f23000',
			  'color_part_title' => '#000000',
			  'color_part_text' => '#000000',
			  'color_part_placeholder' => '#888888',
			  'color_part_description' => '#454545',
			  'color_part_border' => '#dbdbdb',
			  'color_part_border_focus' => '#7aa4ff',
			  'color_part_background' => '#ffffff',
			  'color_part_background_focus' => '#ffffff',
			  'color_submit_background' => '#000000',
			  'color_submit_background_hover' => '#000000',
			  'color_submit_border' => 'transparent',
			  'color_submit_text' => '#ffffff',
			  'color_submit_text_hover' => '#ffffff',
			  'color_rating_star' => '#cccccc',
			  'color_rating_star_hover' => '#000000',
			  'color_table_row_odd' => '#fcfcfc',
			  'color_table_row_even' => '#efefef',
			  'color_table_row_odd_text' => '#000000',
			  'color_table_row_even_text' => '#000000',
			  'color_divider_hr' => '#cccccc',
			  'color_choice_checkmark_bg' => '#ffffff',
			  'color_choice_checkmark_bg_focus' => '#000000',
			  'color_choice_checkmark_color' => '#ffffff',
			  'color_dropdown_item_bg' => '#ffffff',
			  'color_dropdown_item_text' => '#000000',
			  'color_dropdown_item_bg_hover' => '#f4f4f5',
			  'color_dropdown_item_text_hover' => '#000000',
			  'notices_position' => '',
			  'additional_css' => '',
			  'confirm_submission' => 'success_message_hide_form',
			  'redirect_on_complete' => '',
			  'redirect_url' => '',
			  'redirect_blank' => '',
			  'spam_prevention' => '1',
			  'form_expiration_datetime' => '2021-07-31 15:14:50',
			  'save_entries' => '1',
			  'captcha' => '1',
			  'captcha_site_key' => '',
			  'captcha_secret_key' => '',
			  'captcha_label' => 'Validate your submission',
			  'preview_before_submit' => '',
			  'use_html_id' => '',
			  'html_id' => '',
			  'disable_submit_until_valid' => '',
			  'add_submit_button_class' => '',
			  'submit_button_html_class' => '',
			  'receive_email_alerts' => '1',
			  'email_recipient' => 'admin@yourdomain.test',
			  'email_bccs' => '',
			  'email_mark_and_reply' => '',
			  'alert_email_from_name' => 'yourdomain',
			  'alert_email_subject' => 'You received a new message',
			  'send_confirmation_email' => '1',
			  'confirmation_email_sender_address' => 'admin@yourdomain.test',
			  'confirmation_email_reply_to' => 'admin@yourdomain.test',
			  'confirmation_email_from_name' => 'yourdomain',
			  'confirmation_email_subject' => 'We received your message',
			  'confirmation_email_content' => 'Your message has been successfully sent. We appreciate you contacting us and we’ll be in touch soon.',
			  'confirmation_email_include_values' => '',
			  'words_label_min' => 'Min words',
			  'words_label_max' => 'Max words',
			  'characters_label_min' => 'Min characters',
			  'characters_label_max' => 'Max characters',
			  'no_results_label' => 'Nothing found',
			  'number_min_invalid' => 'Oops. This number isn\'t big enough.',
			  'number_max_invalid' => 'Oops. This number is too big.',
			  'optional_part_label' => '(optional)',
			  'required_field_label' => '',
			  'field_invalid' => 'Oops. Looks like there\'s a mistake here.',
			  'field_empty' => 'Oops. Please answer this question.',
			  'no_selection' => 'Oops. Please make a selection.',
			  'message_too_long' => 'Oops. This answer is too long.',
			  'message_too_short' => 'Oops. This answer isn\'t long enough.',
			  'per_form_validation_msg' => '1',
			  'confirmation_message' => 'Thank you. Your reply has been sent.',
			  'error_message' => 'Bummer. We can&#039;t submit your reply. Please check for mistakes.',
			  'submit_button_label' => 'Send',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.