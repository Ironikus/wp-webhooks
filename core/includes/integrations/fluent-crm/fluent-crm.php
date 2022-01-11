<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_fluent_crm Class
 *
 * This class integrates all FluentCRM related features and endpoints
 *
 * @since 4.3.1
 */
class WP_Webhooks_Integrations_fluent_crm {

    public function is_active(){
        return defined( 'FLUENTCRM' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'FluentCRM',
            'icon' => $integration_url . '/assets/img/icon-fluent-crm.svg',
        );
    }

}
