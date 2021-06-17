<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Triggers_edd_file_downloaded' ) ) :

 /**
  * Load the edd_file_downloaded trigger
  *
  * @since 4.2.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_edd_Triggers_edd_file_downloaded {

    public function is_active(){

        $is_active = true;

        //Backwards compatibility
        if( defined( 'WPWH_EDD_NAME' ) ){
            $is_active = false;
        }

        return $is_active;
    }

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
                'hook' => 'edd_process_verified_download',
                'callback' => array( $this, 'wpwh_trigger_edd_file_downloaded' ),
                'priority' => 10,
                'arguments' => 4,
                'delayed' => false,
            ),
        );
    }

    /*
    * Register the post delete trigger as an element
    *
    * @since 1.2
    */
    public function get_details(){

        $translation_ident = "action-edd_file_downloaded-description";

        $choices = array();
        if( function_exists( 'edd_get_payment_statuses' ) ){
            $choices = edd_get_payment_statuses();

            //add our custom delete status
            $choices['wpwh_deleted'] = WPWHPRO()->helpers->translate( 'Deleted', $translation_ident );
        }

        $parameter = array(
            'file_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The file name without the file extension.', $translation_ident ) ),
            'file' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full file URL.', $translation_ident ) ),
            'email' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The email of the customer who started the download.', $translation_ident ) ),
            'product' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The product name wich contains the download.', $translation_ident ) ),
        );

        ob_start();
?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data as soon as a file is downloaded, within Easy Digital Downloads, to one or multiple given webhook URL's.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On EDD File Downloads</strong> (edd_file_downloaded) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On EDD File Downloads</strong> (edd_file_downloaded)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your recieving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For a better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you are able to recieve data on the URL once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>edd_process_verified_download</strong> hook of Easy Digital Downloads", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_action( 'edd_process_verified_download', array( $this, 'wpwh_trigger_edd_file_downloaded' ), 10, 4 );</pre>
<br><br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you don't need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can use the <strong>Send demo</strong> button to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Within the <strong>Settings</strong> link next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don't forget to save the settings once you changed them - the button is at the end of the popup.)", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can also check the response you get from the webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to wp-webhooks.com/docs" target="_blank" href="https://wp-webhooks.com/docs/article-categories/get-started/">https://wp-webhooks.com/docs/article-categories/get-started/</a>
<?php
        $description = ob_get_clean();

        $settings = array(
            'load_default_settings' => true,
        );

        return array(
            'trigger'           => 'edd_file_downloaded',
            'name'              => WPWHPRO()->helpers->translate( 'File downloaded', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires once a file download is initiated within Easy Digital Downloads.', $translation_ident ),
            'description'       => $description,
            'callback'          => 'test_edd_file_downloaded',
            'integration'       => 'edd',
        );

    }

    /**
     * Triggers once a new EDD file was downloaded
     *
     * @param  integer $customer_id   Customer ID.
     * @param  array   $args          Customer data.
     */
    public function wpwh_trigger_edd_file_downloaded( $download_id, $email, $payment_id, $args ){
        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'edd_file_downloaded' );

        $data  = array();
        $files = edd_get_download_files( $download_id );

        $data['file_name'] = $files[ $args['file_key'] ]['name'];
        $data['file']      = $files[ $args['file_key'] ]['file'];
        $data['email']     = $email;
        $data['product']   = get_the_title( $download_id );

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

            if( $webhook_url_name !== null ){
                $response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
            } else {
                $response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
            }
            
        }

        do_action( 'wpwhpro/webhooks/trigger_edd_file_downloaded', $download_id, $email, $payment_id, $args, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array(
            'file_name' => 'sample_file_name',
            'file'      => home_url( 'sample/file/url/file.zip' ),
            'email'     => 'jane@test.com',
            'product'   => 'Sample Product',
        );

        return $data;
    }

  }

endif; // End if class_exists check.