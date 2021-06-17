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

    /*
    * Register the post delete trigger as an element
    *
    * @since 1.2
    */
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

      ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data, on form submission of a \"WPForms\" form, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On WPForms Submit</strong> (wpf_submit) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On WPForms Submit</strong> (wpf_submit)", $translation_ident ); ?></h4>
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
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>wpforms_process_complete</strong> hook of WPForms:", $translation_ident ); ?> 
<a title="wpforms.com" target="_blank" href="https://wpforms.com/developers/wpforms_process_complete/">https://wpforms.com/developers/wpforms_process_complete/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'wpforms_process_complete', array( $this, 'ironikus_trigger_wpf_submit' ), 20, 4 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook does not fire, by default, once the actual trigger (wpf_submit) is fired, but as soon as the WordPress <strong>shutdown</strong> hook fires. This is important since we want to allow third-party plugins to make their relevant changes before we send over the data. To deactivate this functionality, please go to our <strong>Settings</strong> and activate the <strong>Deactivate Post Trigger Delay</strong> settings item. This results in the webhooks firing straight after the initial hook is called.", $translation_ident ); ?>
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
          'wpwhpro_wpf_submit_trigger_on_forms' => array(
            'id'     => 'wpwhpro_wpf_submit_trigger_on_forms',
            'type'    => 'select',
            'multiple'  => true,
            'choices'   => $validated_forms,
            'label'    => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
            'placeholder' => '',
            'required'  => false,
            'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
          ),
        )
      );

      return array(
        'trigger'      => 'wpf_submit',
        'name'       => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
        'parameter'     => $parameter,
        'settings'     => $settings,
        'returns_code'   => $this->get_demo( array() ),
        'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a WPForms form submission.', $translation_ident ),
        'description'    => $description,
        'callback'     => 'test_wpf_submit',
        'integration'    => 'wpforms',
        'premium'    => true,
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