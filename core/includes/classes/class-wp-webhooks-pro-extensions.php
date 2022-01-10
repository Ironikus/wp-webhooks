<?php

/**
 * WP_Webhooks_Pro_Extensions Class
 *
 * This class contains all of the available functions
 * for our available extension
 *
 * @since 3.3.0
 */

/**
 * The api class of the plugin.
 *
 * @since 3.3.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Extensions {

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public function execute(){

		add_action( 'wp_ajax_ironikus_manage_extensions',  array( $this, 'ironikus_manage_extensions' ) );

	}

    /*
     * Manage WP Webhooks extensions
     */
	public function ironikus_manage_extensions(){
        check_ajax_referer( md5( WPWHPRO()->settings->get_page_name() ), 'ironikus_nonce' );

        $extension_slug            = isset( $_REQUEST['extension_slug'] ) ? sanitize_text_field( $_REQUEST['extension_slug'] ) : '';
        $extension_status            = isset( $_REQUEST['extension_status'] ) ? sanitize_text_field( $_REQUEST['extension_status'] ) : '';
        $extension_download            = isset( $_REQUEST['extension_download'] ) ? sanitize_text_field( $_REQUEST['extension_download'] ) : '';
        $extension_id            = isset( $_REQUEST['extension_id'] ) ? intval( $_REQUEST['extension_id'] ) : '';
        $extension_version            = isset( $_REQUEST['extension_version'] ) ? sanitize_text_field( $_REQUEST['extension_version'] ) : '';
		$response           = array( 'success' => false );

		if( empty( $extension_slug ) || empty( $extension_status ) ){
			$response['msg'] = WPWHPRO()->helpers->translate('An error occured while doing this action.', 'ajax-settings');
			return $response;
		}

		switch( $extension_status ){
			case 'activated': //runs when the "Deactivate" button was clicked
				$response['new_class'] = 'text-green';
				$response['new_status'] = 'deactivated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->deactivate_extension( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully deactivated.', 'ajax-settings');
				break;
			case 'deactivated': //runs when the "Activate" button was clicked
				$response['new_class'] = 'text-warning';
				$response['new_status'] = 'activated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->activate_extension( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully activated.', 'ajax-settings');
				break;
			case 'uninstalled': //runs when the "Install" button was clicked
				$response['new_class'] = 'text-green';
				$response['new_status'] = 'deactivated';
				$response['delete_name'] = WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-extensions' );
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->install_wpwh_extension( $extension_slug, $extension_download, $extension_id, $extension_version );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully installed.', 'ajax-settings');
				break;
			case 'update_active': //runs when the "Update" button was clicked and the previous status was active
				$response['new_class'] = 'text-warning';
				$response['new_status'] = 'activated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Deactivate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->update_wpwh_extension( $extension_slug, $extension_download, $extension_id, $extension_version );;
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully updated.', 'ajax-settings');
				break;
			case 'update_deactive': //runs when the "Update" button was clicked and the previous status was inactive
				$response['new_class'] = 'text-green';
				$response['new_status'] = 'deactivated';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Activate', 'wpwhpro-page-extensions' );
				$response['success'] = $this->update_wpwh_extension( $extension_slug, $extension_download, $extension_id, $extension_version );;
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully updated.', 'ajax-settings');
				break;
			case 'delete': //runs when the "Delete" link was clicked
				$response['new_class'] = 'text-secondary';
				$response['new_status'] = 'uninstalled';
				$response['new_label'] = WPWHPRO()->helpers->translate( 'Install', 'wpwhpro-page-extensions' );
				$response['success'] = $this->uninstall_extension( $extension_slug );
				$response['msg'] = WPWHPRO()->helpers->translate('The plugin was successfully deleted.', 'ajax-settings');
				break;
		}

        echo json_encode( $response );
		die();
    }

    public function deactivate_extension( $slug ){

		if( empty( $slug ) ){
			return false;
		}

		if ( is_plugin_active( $slug ) ) {
			deactivate_plugins( $slug );
		}

		return true;
    }

    public function activate_extension( $slug ){

		if( empty( $slug ) ){
			return false;
		}

		if ( ! WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		$activate = activate_plugin( $slug );
		if (is_null($activate)) {
			return true;
		}

		return false;
    }

	private function install_wpwh_extension( $slug, $dl, $item_id, $version ){

		if( empty( $slug ) || empty( $dl ) ){
			return false;
		}

		if ( WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		if( $dl === 'ironikus' ){
			$dl = $this->manage_extension_get_premium( $slug, $item_id, $version );
			if( empty( $dl ) ){
				return false;
			}
		}

		$check = $this->install_extension( $slug, $dl );

		return $check;
	}

	public function install_extension( $slug, $dl ){

		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-upgrader-skin.php';

		if( ! class_exists( 'Plugin_Upgrader' ) || ! class_exists( 'WP_Webhooks_Upgrader_Skin' ) ){
			return false;
		}

		wp_cache_flush();
		$skin = new WP_Webhooks_Upgrader_Skin( array( 'plugin' => $slug ) );
		$upgrader = new Plugin_Upgrader( $skin );
		$installed = $upgrader->install( $dl );
		wp_cache_flush();

		if( ! is_wp_error( $installed ) && $installed ) {
			return true;
		} else {
			return false;
		}

	}

	public function uninstall_extension( $slug ){

		if ( ! WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';

		if( ! function_exists( 'delete_plugins' ) ){
			return false;
		}

		if ( is_plugin_active( $slug ) ) {
			deactivate_plugins( $slug );
		}

		$deleted = delete_plugins( array( $slug ) );

		if( ! is_wp_error( $deleted ) && $deleted ) {
			return true;
		} else {
			return false;
		}

	 }

	private function update_wpwh_extension( $slug, $dl, $item_id, $version ){

		if( empty( $slug ) || empty( $dl ) ){
			return false;
		}

		if ( ! WPWHPRO()->helpers->is_plugin_installed( $slug ) ) {
			return false;
		}

		if( $dl === 'ironikus' ){
			$dl = $this->manage_extension_get_premium( $slug, $item_id, $version );
			if( empty( $dl ) ){
				return false;
			}
		}

		$check = $this->update_extension( $slug, $dl );

		return $check;
	 }

	 public function update_extension( $slug, $dl ){

		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once WPWH_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-upgrader-skin.php';

		if( ! class_exists( 'Plugin_Upgrader' ) || ! class_exists( 'WP_Webhooks_Upgrader_Skin' ) ){
			return false;
		}

		wp_cache_flush();
		$skin = new WP_Webhooks_Upgrader_Skin( array( 'plugin' => $slug ) );
		$upgrader = new Plugin_Upgrader( $skin );
		$updated = $upgrader->upgrade( $slug );
		wp_cache_flush();

		if( ! is_wp_error( $updated ) && $updated ) {
			return true;
		} else {
			return false;
		}

	 }

	public function manage_extension_get_premium( $slug, $item_id, $version ){

		return false;

	}

}
