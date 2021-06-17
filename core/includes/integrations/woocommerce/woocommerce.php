<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_woocommerce Class
 *
 * This class integrates all Woocommerce related features and endpoints
 *
 * @since 4.2.0
 */
class WP_Webhooks_Integrations_woocommerce {

    public function is_active(){

        $is_active = class_exists( 'WooCommerce' );

        //Backwards compatibility
        if( class_exists( 'WP_Webhooks_Pro_Woocommerce' ) ){
            $is_active = false;
            add_action( 'admin_notices', array( $this, 'throw_admin_notices' ), 100 );
        }

        return $is_active;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Woocommerce',
            'icon' => $integration_url . '/assets/img/icon-woocommerce.svg',
        );
    }

    public function throw_admin_notices(){

        if( current_user_can( 'manage_options' ) ){
            $details = $this->get_details();
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%2$s %1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), $details['name'], WPWHPRO()->settings->get_page_title() );
        }

	}

}
