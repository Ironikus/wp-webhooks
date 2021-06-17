<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Helpers_plugin_helpers' ) ) :

	/**
	 * Load the create_user action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Helpers_plugin_helpers {

		/**
          * Installs a plugin
          *
          * @param string $slug
          * @param string $download
          * @param array $args
          * @return bool
          */
          public function install( $slug, $download, $args = array() ){

            if( empty( $slug ) || empty( $download ) ){
                return false;
            }

            wp_cache_flush();

            if( ! isset( $args[ 'slug' ] ) ){
                $args[ 'slug' ] = $slug;
            }

            $upgrader = $this->get_upgrader( $args );
            $installed = $upgrader->install( $download );

            wp_cache_flush();

            if( ! is_wp_error( $installed ) && $installed ) {
                return true;
            } else {
                return false;
            }

        }

        /**
         * Activates a plugin
         *
         * @param string $slug
         * @return bool
         */
        public function activate( $slug ){

            if( empty( $slug ) ){
                return false;
            }
    
            if ( ! $this->is_installed( $slug ) ) {
                return false;
            }

            if ( $this->is_plugin_active( $slug ) ) {
                return true;   
            }

            if( ! function_exists( 'activate_plugin' ) ){
                $this->load_plugin_dependencies();
            }
    
            $activate = activate_plugin( $slug );
            if( is_null( $activate ) ) {
                return true;
            }
    
            return false;
        }

        /**
         * Deactivates a plugin
         *
         * @param string $slug
         * @return bool
         */
        public function deactivate( $slug ){

            if( empty( $slug ) ){
                return false;
            }

            if ( ! $this->is_installed( $slug ) ) {
                return false;
            }

            if ( ! $this->is_plugin_active( $slug ) ) {
                return true;    
            }

            if( ! function_exists( 'is_plugin_active' ) || ! function_exists( 'deactivate_plugins' ) ){
                $this->load_plugin_dependencies();
            }
    
            if( is_plugin_active( $slug ) ) {
                deactivate_plugins( $slug );
                return true;
            }
    
            return false;
         }

         /**
          * Updates a plugin
          *
          * @param string $slug
          * @param array $args
          * @return bool
          */
         public function update( $slug, $args = array() ){

            if( empty( $slug ) ){
                return false;
            }

            if ( ! $this->is_installed( $slug ) ) {
                return false;
            }

            if( ! isset( $args[ 'slug' ] ) ){
                $args[ 'slug' ] = $slug;
            }
            
            wp_cache_flush();

            $upgrader = $this->get_upgrader( $args );
            $updated = $upgrader->upgrade( $slug );

            wp_cache_flush();
    
            if( ! is_wp_error( $updated ) && $updated ) {
                return true;
            } else {
                return false;
            }
    
         }

         /**
          * Deletes a plugin
          *
          * @param string $slug
          * @param array $args
          * @return bool
          */
         public function uninstall( $slug, $args = array() ){

            if( empty( $slug ) ){
                return false;
            }

            $this->load_plugin_dependencies();

            if ( ! $this->is_installed( $slug ) ) {
                return false;
            }
    
            if( isset( $args['force_delete'] ) && $args['force_delete'] ){
                if ( $this->is_plugin_active( $slug ) ) {
                    deactivate_plugins( $slug );    
                }
            }
            
            $deleted = delete_plugins( array( $slug ) );
    
            if( ! is_wp_error( $deleted ) && $deleted ) {
                return true;
            } else {
                return false;
            }
    
         }

         /**
          * Checks if a plugin is installed
          *
          * @param string $slug
          * @return boolean
          */
        public function is_installed( $slug ){

            if( empty( $slug ) ){
                return false;
            }
            
            if( ! function_exists( 'get_plugins' ) ){
                $this->load_plugin_dependencies();
            }
            
            $all_plugins = get_plugins();

            if( ! empty( $all_plugins[ $slug ] ) ){
                return true;
            } else {
                return false;
            }
        }

        /**
         * Checks if a plugin is active
         *
         * @param string $slug
         * @return boolean
         */
        public function is_plugin_active( $slug ){

            if( empty( $slug ) ){
                return false;
            }
            
            if( ! function_exists( 'is_plugin_active' ) ){
                $this->load_plugin_dependencies();
            }

            if( is_plugin_active( $slug ) ){
                return true;
            } else {
                return false;
            }
        }

        /**
         * ######################
         * ###
         * #### CORE FUNCTIONS
         * ###
         * ######################
         */

         /**
          * Load the necessary plugin dependencies
          *
          * @return void
          */
        public function load_plugin_dependencies(){

            @include_once ABSPATH . 'wp-admin/includes/plugin.php';
            @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            @include_once ABSPATH . 'wp-admin/includes/file.php';
            @include_once ABSPATH . 'wp-admin/includes/misc.php';

            $integration_folder = WPWHPRO()->integrations->get_integrations_folder();
            @include_once $integration_folder . '/wordpress/misc/plugin_upgrader_skin.php';

        }

        /**
         * Load the upgrader class with further settings
         *
         * @param array $args
         * @return class
         */
        public function get_upgrader( $args = array() ){

            if( ! class_exists( 'Plugin_Upgrader' ) ){
                $this->load_plugin_dependencies();
            }

            if( isset( $args['prevent_outputs'] ) && $args['prevent_outputs'] ){
                
                $skin = new WP_Webhooks_Plugin_Manager_Manager_Skin( array( 'plugin' => $args['slug'] ) );
                return new Plugin_Upgrader( $skin );

            } else {
                return new Plugin_Upgrader();
            }

        }

	}

endif; // End if class_exists check.