<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Helpers_wc_helpers' ) ) :

	/**
	 * Load the Woocommerce helpers
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Helpers_wc_helpers {

		/**
		 * Get all Woocommerce webhook API versions
		 *
		 * @return array A list of the available types 
		 */
		public function get_wc_api_versions(){

			$versions = array();

			if( function_exists( 'wc_get_webhook_rest_api_versions' ) ){
				$versions = wc_get_webhook_rest_api_versions();
			} else {
				$versions = array(
					'wp_api_v1',
					'wp_api_v2',
					'wp_api_v3',
				);
			}

			$validated_versions = array();
			foreach( $versions as $version ){
				$validated_versions[ $version ] = esc_html( sprintf( WPWHPRO()->helpers->translate( 'WP REST API v%d', 'trigger-wc_helpers-get_types' ), str_replace( 'wp_api_v', '', $version ) ) );
			}

			return apply_filters( 'wpwhpro/webhooks/wc_helpers/get_wc_api_versions', $validated_versions );
		}

		/**
		 * Get an array of assigned taxonomies for a given post
		 *
		 * @param int $post_id
		 * @since 4.3.3
		 * @return array
		 */
		public function get_validated_taxonomies( $post_id ){
			
			$tax_output = array();

			if( ! empty( $post_id ) ){
				$tax_output = array();
                $taxonomies = get_taxonomies( array(),'names' );
                if( ! empty( $taxonomies ) ){
                    $tax_terms = wp_get_post_terms( $post_id, $taxonomies );
                    foreach( $tax_terms as $sk => $sv ){

                        if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
                            continue;
                        }

                        if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
                            $tax_output[ $sv->taxonomy ] = array();
                        }

                        if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
                            $tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
                        }

                        $tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

                    }
                }
			}

			return $tax_output;
		}

	}

endif; // End if class_exists check.