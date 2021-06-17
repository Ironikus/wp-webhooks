<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_edd_Helpers_edd_helpers' ) ) :

	class WP_Webhooks_Integrations_edd_Helpers_edd_helpers {

		/**
		 * Update the post (download) meta
		 *
		 * @param int $post_id - the post id
		 * @return void
		 */
		public function edd_create_update_download_add_meta( $post_id ){

			$response_body 				= WPWHPRO()->helpers->get_response_body();

			$meta_input 				= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_input' );
			$edd_price      			= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'price' );//float
			$is_variable_pricing      	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'is_variable_pricing' ) === 'yes' ) ? 1 : 0;//integer
			$edd_variable_prices      	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'variable_prices' );//json string
			$default_price_id      		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'default_price_id' );//integer
			$edd_download_files      	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_files' );//json string
			$edd_bundled_products      	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'bundled_products' );//json string
			$bundled_products_conditions= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'bundled_products_conditions' );//json string
			$hide_purchase_link      	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'hide_purchase_link' ) === 'yes' ) ? 'on': 'off';
			$download_limit      		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'download_limit' ) );

			//START EDD
			if( ! empty( $edd_price ) && is_numeric( $edd_price ) ){
				update_post_meta( $post_id, 'edd_price', $edd_price );
			}

			if( ! empty( $edd_variable_prices ) && WPWHPRO()->helpers->is_json( $edd_variable_prices ) ){
				$edd_variable_prices = json_decode( $edd_variable_prices, true );
				update_post_meta( $post_id, 'edd_variable_prices', $edd_variable_prices );
			}

			if( ! empty( $edd_download_files ) && WPWHPRO()->helpers->is_json( $edd_download_files ) ){
				$edd_download_files = json_decode( $edd_download_files, true );
				update_post_meta( $post_id, 'edd_download_files', $edd_download_files );
			}

			if( ! empty( $edd_bundled_products ) && WPWHPRO()->helpers->is_json( $edd_bundled_products ) ){
				$edd_bundled_products = json_decode( $edd_bundled_products, true );
				update_post_meta( $post_id, '_edd_bundled_products', $edd_bundled_products );
			}

			if( ! empty( $bundled_products_conditions ) && WPWHPRO()->helpers->is_json( $bundled_products_conditions ) ){
				$bundled_products_conditions = json_decode( $bundled_products_conditions, true );
				update_post_meta( $post_id, '_edd_bundled_products_conditions', $bundled_products_conditions );
			}

			if( ! empty( $is_variable_pricing ) && is_numeric( $is_variable_pricing ) ){
				update_post_meta( $post_id, '_variable_pricing', intval( $is_variable_pricing ) );
			}

			if( ! empty( $default_price_id ) && is_numeric( $default_price_id ) ){
				update_post_meta( $post_id, '_edd_default_price_id', intval( $default_price_id ) );
			}

			if( ! empty( $hide_purchase_link ) && $hide_purchase_link === 'on' ){
				update_post_meta( $post_id, '_edd_hide_purchase_link', $hide_purchase_link );
			}

			if( ! empty( $download_limit ) && is_numeric( $download_limit ) ){
				update_post_meta( $post_id, '_edd_download_limit', $download_limit );
			}
			//END EDD

			if( ! empty( $meta_input ) ){
				
				if( WPWHPRO()->helpers->is_json( $meta_input ) ){

					$post_meta_data = json_decode( $meta_input, true );
					foreach( $post_meta_data as $skey => $svalue ){
						if( ! empty( $skey ) ){
							if( $svalue == 'ironikus-delete' ){
								delete_post_meta( $post_id, $skey );
							} else {

								$ident = 'ironikus-serialize';
								if( is_string( $svalue ) && substr( $svalue , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $svalue ),' ' );

									if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
										$serialized_value = json_decode( $serialized_value );
									}

									update_post_meta( $post_id, $skey, $serialized_value );

								} else {
									update_post_meta( $post_id, $skey, maybe_unserialize( $svalue ) );
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
								delete_post_meta( $post_id, $meta_key );
							} else {

								$ident = 'ironikus-serialize';
								if( substr( $meta_value , 0, strlen( $ident ) ) === $ident ){
									$serialized_value = trim( str_replace( $ident, '', $meta_value ),' ' );

									if( WPWHPRO()->helpers->is_json( $serialized_value ) ){
										$serialized_value = json_decode( $serialized_value );
									}

									update_post_meta( $post_id, $meta_key, $serialized_value );

								} else {
									update_post_meta( $post_id, $meta_key, maybe_unserialize( $meta_value ) );
								}
							}
						}
					}

				}

			}

		}

		public function validate_payment_data( $payment_data ){

			$response = array(
				'success' => true,
				'errors' => array(),
			);

			if ( empty( $payment_data ) ) {
				$response['errors'][] = WPWHPRO()->helpers->translate("The payment data cannot be empty.", 'action-edd_helpers-validate-failure' );
				$response['success'] = false;
			}

			if ( empty( $payment_data['user_info']['email'] ) ) {
				$response['errors'][] = WPWHPRO()->helpers->translate("The argument user_email cannot be empty.", 'action-edd_helpers-validate-failure' );
				$response['success'] = false;
			}

			if( ! empty( $payment_data['cart_details'] ) ){
				foreach( $payment_data['cart_details'] as $item ){

					if ( ! isset( $item['id'] ) ) {
						$response['errors'][] = WPWHPRO()->helpers->translate("The item argument id cannot be empty. Please set it to the download id.", 'action-edd_helpers-validate-failure' );
						$response['success'] = false;
					}

					if ( ! isset( $item['quantity'] ) ) {
						$response['errors'][] = WPWHPRO()->helpers->translate("The item argument quantity cannot be empty.", 'action-edd_helpers-validate-failure' );
						$response['success'] = false;
					}

					if ( ! isset( $item['tax'] ) ) {
						$response['errors'][] = WPWHPRO()->helpers->translate("The item argument tax cannot be empty.", 'action-edd_helpers-validate-failure' );
						$response['success'] = false;
					}

				}
			}
			
			return $response;

		}

		/**
         * Get relevant data for a given license ID.
         *
         * @param  integer $license_id License post ID.
         * @return array               License data.
         */
        public function edd_get_license_data( $license_id = 0, $download_id = 0, $payment_id = 0 ) {

            $license = edd_software_licensing()->get_license( $license_id );

            // The license ID supplied didn't give us a valid license, no data to return.
            if ( false === $license ) {
                return array();
            }

            if ( empty( $download_id ) ) {

                $download_id = $license->download_id;

            }

            if ( empty( $payment_id ) ) {

                $payment_id = $license->payment_id;

            }

            $customer_id = edd_get_payment_customer_id( $payment_id );

            if( empty( $customer_id ) ) {

                $user_info       = edd_get_payment_meta_user_info( $payment_id );
                $customer        = new stdClass;
                $customer->email = edd_get_payment_user_email( $payment_id );
                $customer->name  = $user_info['first_name'];

            } else {

                $customer = new EDD_Customer( $customer_id );

            }

            if( $license->is_lifetime ) {
                $expiration = 'never';
            } else {
                $expiration = $license->expiration;
                $expiration = date( 'Y-n-d H:i:s', $expiration );
            }

            $download = method_exists( $license, 'get_download' ) ? $license->get_download() : new EDD_SL_Download( $download_id );


            $license_data = array(
                'ID'               => $license->ID,
                'key'              => $license->key,
                'customer_email'   => $customer->email,
                'customer_name'    => $customer->name,
                'product_id'       => $download_id,
                'product_name'     => $download->get_name(),
                'activation_limit' => $license->activation_limit,
                'activation_count' => $license->activation_count,
                'activated_urls'   => implode( ',', $license->sites ),
                'expiration'       => $expiration,
                'is_lifetime'      => $license->is_lifetime ? '1' : '0',
                'status'           => $license->status,
            );

            return $license_data;
        }

		/**
		 * Get relevant data for a given complete order.
		 *
		 * @param  integer $payment_id Payment post ID.
		 * @return array               Order data.
		 */
		function wpwh_get_edd_order_data( $payment_id = 0 ) {

			if( ! function_exists( 'edd_get_payment_meta_user_info' ) ){
				return false;
			}

			$user_info                      = edd_get_payment_meta_user_info( $payment_id );
			$order_data                     = array();
			$order_data['ID']               = $payment_id;
			$order_data['key']              = edd_get_payment_key( $payment_id );
			$order_data['subtotal']         = edd_get_payment_subtotal( $payment_id );
			$order_data['tax']              = edd_get_payment_tax( $payment_id );
			$order_data['fees']             = edd_get_payment_fees( $payment_id );
			$order_data['total']            = edd_get_payment_amount( $payment_id );
			$order_data['gateway']          = edd_get_payment_gateway( $payment_id );
			$order_data['email']            = edd_get_payment_user_email( $payment_id );
			$order_data['date']             = get_the_time( 'Y-m-d H:i:s', $payment_id );
			$order_data['products']         = $this->wpwh_edd_get_order_products( $payment_id );
			$order_data['discount_codes']   = $user_info['discount'];
			$order_data['first_name']       = $user_info['first_name'];
			$order_data['last_name']        = $user_info['last_name'];
			$order_data['transaction_id']   = edd_get_payment_transaction_id( $payment_id );
			$order_data['billing_address']  = ! empty( $user_info['address'] ) ? $user_info['address'] : array( 'line1' => '', 'line2' => '', 'city' => '', 'country' => '', 'state' => '', 'zip' => '' );
			$order_data['shipping_address'] = ! empty( $user_info['shipping_info'] ) ? $user_info['shipping_info'] : array( 'address' => '', 'address2' => '', 'city' => '', 'country' => '', 'state' => '', 'zip' => '' );
			$order_data['metadata']         = $this->wpwh_edd_get_order_metadata( $payment_id );

			return $order_data;
		}

		/**
		 * Get ordered products for a given order.
		 *
		 * @param  integer $payment_id Payment post ID.
		 * @return array               Ordered products.
		 */
		function wpwh_edd_get_order_products( $payment_id = 0 ) {

			$cart_items = edd_get_payment_meta_cart_details( $payment_id );
			$products   = array();

			foreach ( $cart_items as $key => $item ) {

				$price_name = '';
				if ( isset( $cart_items[ $key ]['item_number'] ) ) {
					$price_options  = $cart_items[ $key ]['item_number']['options'];
					if ( isset( $price_options['price_id'] ) ) {
						$price_name = edd_get_price_option_name( $item['id'], $price_options['price_id'], $payment_id );
					}
				}

				$products[ $key ]['Product']   = $item['name'];
				$products[ $key ]['Subtotal']  = $item['subtotal'];
				$products[ $key ]['Tax']       = $item['tax'];
				$products[ $key ]['Discount']  = $item['discount'];
				$products[ $key ]['Price']     = $item['price'];
				$products[ $key ]['PriceName'] = $price_name;
				$products[ $key ]['Quantity']  = $item['quantity'];
			}

			return $products;
		}

		/**
		 * Retrieve an array of all custom metadata on a payment
		 *
		 * @param  integer $payment_id Payment post ID.
		 * @return array               Metadata
		 */
		function wpwh_edd_get_order_metadata( $payment_id = 0 ) {

			$ignore = array(
				'_edd_payment_gateway',
				'_edd_payment_mode',
				'_edd_payment_transaction_id',
				'_edd_payment_user_ip',
				'_edd_payment_customer_id',
				'_edd_payment_user_id',
				'_edd_payment_user_email',
				'_edd_payment_purchase_key',
				'_edd_payment_number',
				'_edd_completed_date',
				'_edd_payment_unlimited_downloads',
				'_edd_payment_total',
				'_edd_payment_tax',
				'_edd_payment_meta',
				'user_info',
				'cart_details',
				'downloads',
				'fees',
				'currency',
				'address'
			);

			$metadata = get_post_custom( $payment_id );
			foreach( $metadata as $key => $value ) {

				if( in_array( $key, $ignore ) ) {

					if( '_edd_payment_meta' == $key ) {

						// Look for custom values added to _edd_payment_meta
						foreach( $value as $inner_key => $inner_value ) {

							if( ! in_array( $inner_key, $ignore ) ) {

								$metadata[ $inner_key ] = $inner_value;

							}

						}

					}

					unset( $metadata[ $key ] );
				}

			}

			return $metadata;

		}

    }

endif; // End if class_exists check.