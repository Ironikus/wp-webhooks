<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_elementor Class
 *
 * This class integrates all Elementor related features and endpoints
 *
 * @since 4.2.1
 */
class WP_Webhooks_Integrations_elementor {

    public function is_active(){
        return defined( 'ELEMENTOR_PRO_VERSION' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Elementor',
            'icon' => $integration_url . '/assets/img/icon-elementor.png',
        );
    }

}
