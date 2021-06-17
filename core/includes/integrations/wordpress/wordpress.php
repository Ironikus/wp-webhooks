<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wordpress Class
 *
 * This class integrates all WordPress related features and endpoints
 *
 * @since 4.2.0
 */
class WP_Webhooks_Integrations_wordpress {

    public function is_active(){

        //Backwards compatibility for the "Manage Plugins" integration
        if( defined( 'WPWHPRO_MNGPL_PLUGIN_NAME' ) ){
            add_action( 'admin_notices', array( $this, 'mngpl_throw_admin_notices' ), 100 );
        }

        //Backwards compatibility for the "Email integration" integration
        if( defined( 'WPWH_EMAILS_PLUGIN_NAME' ) ){
            add_action( 'admin_notices', array( $this, 'wpwh_emails_throw_admin_notices' ), 100 );
        }

        //Backwards compatibility for the "Comments" integration
        if( class_exists( 'WP_Webhooks_Comments' ) ){
            add_action( 'admin_notices', array( $this, 'wpwhcomments_throw_admin_notices' ), 100 );
        }

        //Backwards compatibility for the "Manage Taxonomy Terms" integration
        if( class_exists( 'WP_Webhooks_Manage_Taxonomy_Terms' ) ){
            add_action( 'admin_notices', array( $this, 'wpwhtaxterms_throw_admin_notices' ), 100 );
        }

        //Backwards compatibility for the "Manage Taxonomy Terms" integration
        if( class_exists( 'WP_Webhooks_Pro_Remote_File_Control' ) ){
            add_action( 'admin_notices', array( $this, 'wpwhremotefc_throw_admin_notices' ), 100 );
        }

        //Backwards compatibility for the "Manage Media Files" integration
        if( class_exists( 'WP_Webhooks_Pro_Manage_Media_Files' ) ){
            add_action( 'admin_notices', array( $this, 'wpwhmanagemdf_throw_admin_notices' ), 100 );
        }

        return true;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'WordPress',
            'icon' => $integration_url . '/assets/img/icon-wordpress.svg',
        );
    }

    public function mngpl_throw_admin_notices(){
        if( current_user_can( 'manage_options' ) ){
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), WPWHPRO_MNGPL_PLUGIN_NAME, WPWHPRO()->settings->get_page_title() );
        }
	}

    public function wpwh_emails_throw_admin_notices(){
        if( current_user_can( 'manage_options' ) ){
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), WPWH_EMAILS_PLUGIN_NAME, WPWHPRO()->settings->get_page_title() );
        }
	}

    public function wpwhcomments_throw_admin_notices(){
        if( current_user_can( 'manage_options' ) ){
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), 'Comments', WPWHPRO()->settings->get_page_title() );
        }
	}

    public function wpwhtaxterms_throw_admin_notices(){
        if( current_user_can( 'manage_options' ) ){
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), 'Manage Taxonomy Terms', WPWHPRO()->settings->get_page_title() );
        }
	}

    public function wpwhremotefc_throw_admin_notices(){
        if( current_user_can( 'manage_options' ) ){
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), 'Remote File Control', WPWHPRO()->settings->get_page_title() );
        }
	}

    public function wpwhmanagemdf_throw_admin_notices(){
        if( current_user_can( 'manage_options' ) ){
            echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'To take full advantage of the <strong>%1$s</strong> integration, please deactivate it as we merged it into the core plugin of <strong>%2$s</strong>.', 'warning', false ), 'Manage Media Files', WPWHPRO()->settings->get_page_title() );
        }
	}

}
