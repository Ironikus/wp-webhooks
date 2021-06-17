<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_contactform7_Helpers_form_helpers' ) ) :

	class WP_Webhooks_Integrations_contactform7_Helpers_form_helpers {

		public function wpwhpro_cf7_create_upload_protection_files( $upload_path ) {
	
			// Top level .htaccess file
			$rules = $this->get_htaccess_rules();
			if ( $this->htaccess_exists() ) {
				$contents = @file_get_contents( $upload_path . '/.htaccess' );
				if ( $contents !== $rules || ! $contents ) {
					// Update the .htaccess rules if they don't match
					@file_put_contents( $upload_path . '/.htaccess', $rules );
				}
			} elseif( wp_is_writable( $upload_path ) ) {
				// Create the file if it doesn't exist
				@file_put_contents( $upload_path . '/.htaccess', $rules );
			}
	
			// Top level blank index.php
			$this->create_index_php( $upload_path );
	
			// Now place index.php files in all sub folders
			$folders = $this->plpl_scan_folders( $upload_path );
			foreach ( $folders as $folder ) {
				// Create index.php, if it doesn't exist
				$this->create_index_php( $folder );
			}
		}

		public function wpwhpro_cf7_clear_preserved_files(){
			$preserved_files = $this->get_preserved_files();
			$update = false;
			$current_stamp = time();

			foreach( $preserved_files as $preserved_files_key => $single_file ){
				if( $single_file['time_to_delete'] < $current_stamp ){

					if( file_exists( $single_file['file_path'] ) ){
						wp_delete_file( $single_file['file_path'] );
						unset( $preserved_files[ $preserved_files_key ] );
						$update = true;
					}
					
				}
			}

			if( $update ){
				$this->update_preserved_files( $preserved_files );
			}
		}

        public function get_preserved_files(){
			$preserved_files = get_transient( 'wpwhcf7_preserved_files' );

			if( empty( $preserved_files ) ){
				$preserved_files = array();
			}

			return apply_filters( 'wpcf7_get_preserved_files', $preserved_files );
		 }

		 public function update_preserved_files( $preserved_files ){
			$success = set_transient( 'wpwhcf7_preserved_files', $preserved_files );
			return $success;
		 }

		 public function create_index_php( $folder ){
			if ( ! file_exists( $folder . '/index.php' ) && wp_is_writable( $folder ) ) {
				@file_put_contents( $folder . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
			}
		 }

		public function get_upload_dir( $create = true, $sub_dir = null ) {
			$wp_upload_dir = wp_upload_dir();
			$folder_name = apply_filters( 'wpcf7_upload_file_folder_name', 'wpwhcf7' );

			if( $create ){
				$create_files = false;
				if( ! is_dir( $wp_upload_dir['basedir'] . '/' . $folder_name ) ){
					$create_files = true;
				}

				wp_mkdir_p( $wp_upload_dir['basedir'] . '/' . $folder_name );

				if( $create_files ){
					$this->wpwhpro_cf7_create_upload_protection_files( $wp_upload_dir['basedir'] . '/' . $folder_name );
				}
			}
			
			$path = $wp_upload_dir['basedir'] . '/' . $folder_name;

			if( ! empty( $sub_dir ) ){
				if( $create ){
					wp_mkdir_p( $path . '/' . $sub_dir );
					$this->create_index_php( $path . '/' . $sub_dir );
				}
				
				$path = $path . '/' . $sub_dir;
			}
		
			return $path;
		}

		public function htaccess_exists() {
			$upload_path = $this->get_upload_dir();
		
			return file_exists( $upload_path . '/.htaccess' );
		}

		public function get_htaccess_rules() {

			$rules = "Options -Indexes\n";
			$rules .= "deny from all\n";
			
			return $rules;
		}

		function plpl_scan_folders( $path = '', $return = array() ) {
			$path = $path == ''? dirname( __FILE__ ) : $path;
			$lists = @scandir( $path );
		
			if ( ! empty( $lists ) ) {
				foreach ( $lists as $f ) {
					if ( is_dir( $path . DIRECTORY_SEPARATOR . $f ) && $f != "." && $f != ".." ) {
						if ( ! in_array( $path . DIRECTORY_SEPARATOR . $f, $return ) )
							$return[] = trailingslashit( $path . DIRECTORY_SEPARATOR . $f );
		
						$this->plpl_scan_folders( $path . DIRECTORY_SEPARATOR . $f, $return);
					}
				}
			}
		
			return $return;
		}

		/**
		 * Validate the form data into an array we can send to Zapier
		 *
		 * @since 1.0.0
		 * @param object $contact_form - ContactForm Obj
		 */
		public function get_contact_form_data( $contact_form ) {
			$data = array();
			$form_tags = $contact_form->scan_form_tags();
			$submission = WPCF7_Submission::get_instance();
			$uploaded_files = $submission->uploaded_files();

			foreach ( $form_tags as $stag ) {

				if ( empty( $stag->name ) ){
					continue;
                }

				$pipes = $stag->pipes;
				$value = ( ! empty( $_POST[ $stag->name ] ) ) ? $_POST[ $stag->name ] : '';
				$value = ( is_array( $value ) ) ? array_map( 'stripslashes', $value ) : stripslashes( $value );
				$payload_key = $stag->name;
				$form_key = $stag->get_option( 'wpwhkey' );

				if( ! empty( $form_key ) && is_array( $form_key ) && ! empty( $form_key[0] ) ){
					$payload_key = $form_key[0];
				}

				if ( is_array( $uploaded_files ) && ! empty( $uploaded_files[ $stag->name ] ) ) {
					$file_name = wp_basename( $uploaded_files[ $stag->name ] );
					$value = array(
						'file_name' => $file_name,
						'file_url' => str_replace( ABSPATH, trim( home_url(), '/' ) . '/', $uploaded_files[ $stag->name ] ),
						'absolute_path' => $uploaded_files[ $stag->name ],
					);
				} else {
					if ( defined( 'WPCF7_USE_PIPE' ) && WPCF7_USE_PIPE && $pipes instanceof WPCF7_Pipes && ! $pipes->zero() ) {
						if ( is_array( $value) ) {
							$new_value = array();
	
							foreach ( $value as $svalue ) {
								$new_value[] = $pipes->do_pipe( wp_unslash( $svalue ) );
							}
	
							$value = $new_value;
						} else {
							$value = $pipes->do_pipe( wp_unslash( $value ) );
						}
					}
				}

				$data[ $payload_key ] = $value;
			}

			return $data;
		}


		public function validate_special_mail_tags( $cf ) {
			$return = array();

			if( empty( $cf ) ){
				return $return;
			}

			$tags_data = explode( ',', $cf );
			if( ! empty( $tags_data ) && is_array( $tags_data ) ){
				foreach( $tags_data as $stag ){
					$stag_data = explode( ':', $stag );
					$mail_tag = new WPCF7_MailTag( '[' . $stag . ']', $stag, '' );

					if( isset( $stag_data[0] ) ){
						$special_tag_name = $stag_data[0];
						$special_tag_key = $stag_data[0];

						if( isset( $stag_data[1] ) ){
							$special_tag_key = $stag_data[1];
						}

						$return[ $special_tag_key ] = apply_filters( 'wpcf7_special_mail_tags', '', $special_tag_name, false, $mail_tag );
					}
				}
			}
			
			return $return;
		}

    }

endif; // End if class_exists check.