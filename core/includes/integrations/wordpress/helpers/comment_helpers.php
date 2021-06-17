<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Helpers_comment_helpers' ) ) :

	/**
	 * Load the comment helpers
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Helpers_comment_helpers {

		public function create_update_comment_add_meta( $comment_id ){

			$response_body = WPWHPRO()->helpers->get_response_body();

			$meta_input = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_input' );

			if( ! empty( $meta_input ) ){

				if( WPWHPRO()->helpers->is_json( $meta_input ) ){

					$post_meta_data = json_decode( $meta_input, true );
					foreach( $post_meta_data as $skey => $svalue ){

						if( ! empty( $skey ) ){
							if( $svalue == 'ironikus-delete' ){
								delete_comment_meta( $comment_id, $skey );
							} else {

								$ident = 'ironikus-serialize';
								if( is_string( $svalue ) && substr( $svalue , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $svalue ),' ' );

									if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
										$serialized_value = json_decode( $svalue );
									}

									update_comment_meta( $comment_id, $skey, $serialized_value );

								} else {
									update_comment_meta( $comment_id, $skey, maybe_unserialize( $svalue ) );
								}

							}
						}
					}

				} else {

					$post_meta_data = explode( ';', trim( $meta_input, ';' ) );
					foreach( $post_meta_data as $single_meta ){
						$single_meta_data   = explode( ',', $single_meta );
						$meta_key           = sanitize_text_field( $single_meta_data[0] );
						$meta_value         = $single_meta_data[1];

						if( ! empty( $meta_key ) ){
							if( $meta_value == 'ironikus-delete' ){
								delete_comment_meta( $comment_id, $meta_key );
							} else {

								$ident = 'ironikus-serialize';
								if( substr( $meta_value , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $meta_value ),' ' );

									if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
										$serialized_value = json_decode( $meta_value );
									}

									update_comment_meta( $comment_id, $meta_key, $serialized_value );

								} else {
									update_comment_meta( $comment_id, $meta_key, maybe_unserialize( $meta_value ) );
								}
							}
						}
					}

				}

			}

		}

	}

endif; // End if class_exists check.