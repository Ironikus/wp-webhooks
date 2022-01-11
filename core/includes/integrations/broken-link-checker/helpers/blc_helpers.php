<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_broken_link_checker_Helpers_blc_helpers' ) ) :

	/**
	 * Load the Broken Link Checker helpers
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_broken_link_checker_Helpers_blc_helpers {

		public function get_notification_payload( $links ) {

			$payload = array();
			
			$payload['msg'] = sprintf(
				__( '[%s] Broken links detected', 'broken-link-checker' ),
				html_entity_decode( get_option( 'blogname' ), ENT_QUOTES )
			);

			$payload['link_count'] = count( $links );
			$payload['admin_url'] = admin_url( 'tools.php?page=view-broken-links' );

			$instances = array();
			foreach( $links as $link ){
				$instances = array_merge( $instances, $link->get_instances() );
			}

			$payload['links'] = array();
			
			foreach ( $instances as $instance ) { 

				$container = $instance->get_container();

				$edit_url = '';
				if( ! empty( $container ) ){
					$edit_url = $container->get_edit_url();
				}

				$payload['links'][] = array(
					'text' => $instance->ui_get_link_text( 'email' ),
					'url' => htmlentities( $instance->get_url() ),
					'src' => $edit_url,
				);

			}

			return apply_filters( 'wpwhpro/webhooks/blc_helpers/get_notification_payload', $payload );
		}

	}

endif; // End if class_exists check.