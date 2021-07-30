<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_newsletter Class
 *
 * This class integrates all Newsletter related features and endpoints
 *
 * @since 4.2.2
 */
class WP_Webhooks_Integrations_newsletter {

    public function is_active(){
        return class_exists('ACF');
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Newsletter',
            'icon' => $integration_url . '/assets/img/icon-newsletter.png',
        );
    }

}
