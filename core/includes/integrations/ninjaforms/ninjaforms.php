<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_ninjaforms Class
 *
 * This class integrates all Ninja Forms related features and endpoints
 *
 * @since 4.2.1
 */
class WP_Webhooks_Integrations_ninjaforms {

    public function is_active(){
        return class_exists( 'Ninja_Forms' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Ninja Forms',
            'icon' => $integration_url . '/assets/img/icon-ninjaforms.png',
        );
    }

}
