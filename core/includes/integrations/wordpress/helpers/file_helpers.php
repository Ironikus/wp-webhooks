<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Helpers_file_helpers' ) ) :

	/**
	 * Load the file helpers
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Helpers_file_helpers {

		/**
         * Get the WordPress root folder
		 *
		 * @return bool|string
		 */
		public function root_folder(){
		    if( ! defined( 'WP_CONTENT_DIR' ) ){
		        return false;
            }

			$content_dir = WP_CONTENT_DIR;
			$custom_folder = ( defined( 'WP_CONTENT_FOLDERNAME' ) && WP_CONTENT_FOLDERNAME ) ? WP_CONTENT_FOLDERNAME : 'wp-content'; //In case a custom folder name is used

			//validate Root Folder
			$root_dir = substr($content_dir, 0, -1 * strlen( $custom_folder ));

		    return rtrim( $root_dir, '/' );
        }

		/**
         * Validate the path yb applying the WordPress root folder
         *
         * For security reasons we just allow files within the
         * WordPress folder and its sub folders (bby default)
         *
		 * @param $path - a relative path with or without a file
		 *
		 * @return bool|string
		 */
		public function validate_path( $path ){
		    $root_folder = $this->root_folder();
		    if( empty( $root_folder ) ){
		        return false;
            }

			if( strpos( $path, ABSPATH ) !== FALSE ){
				$path = str_replace( ABSPATH, '', $path );
			}

            //Backwards compatibility
            $validate = true;
            $check_1 = add_filter( 'wpwhpro/remote_file_control/validate_path', true, $path );
            $check_2 = add_filter( 'wpwhpro/manage_media_files/validate_path', true, $path );
            if( $check_1 === $check_2 && empty( $check_1 ) ){
                $validate = false;
            }

            //todo - add to docs
            if( $validate ){
	            $path = $root_folder . '/' . ltrim( $path, '/' );
            } else {
	            $path = ltrim( $path, '/' );
            }

		    return $path;
        }

		/**
		 * Deletes a directory including all its data
		 *
		 * @param $dir - path of the directory
		 * @return bool - wether the dir is deleted or not
		 */
		public function delete_folder( $dir ) {

		    //We never allow to delete the root folder.
            if( empty( $dir ) || ! class_exists( 'RecursiveDirectoryIterator' ) || ! class_exists( 'RecursiveIteratorIterator' ) ){
                return false;
            }

		    $dir = $this->validate_path( $dir );

			if( ! is_dir($dir) ){
				return false;
			}

			$it = new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS );
			$files = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::CHILD_FIRST );
			foreach( $files as $file ) {
				if( $file->isDir() ){
					rmdir( $file->getRealPath() );
				} else {
					unlink( $file->getRealPath() );
				}
			}

			rmdir( $dir );

			if( ! file_exists( $dir ) ){
				return true;
			} else {
				return false;
			}
		}

		public function delete_file( $file ){

			$file = $this->validate_path( $file );

			if( file_exists( $file ) ){
				$check = unlink( $file );
				if( $check ){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}

		}

		/**
		 * Creates a folder
		 *
		 * @param $path
		 * @return bool
		 */
		public function create_folder( $path, $mode = 0777, $recursive = false ){

		    $return = false;
			$path = $this->validate_path( $path );

			if( empty( $path ) )
				return $return;

			if( ! is_dir( $path ) ){
				$return = mkdir( $path, $mode, $recursive );
			}

			return $return;
		}

		/**
		 * Create a file
		 *
		 * @param $file - Path and name of file
		 * @param string $text - the file content
		 * @param string $mode
		 * @return bool - true or false if file is created
		 */
		public function create_file( $file, $text = '', $mode = 'w' ){

			$file = $this->validate_path( $file );

			$myfile = fopen( $file, $mode );
			fwrite( $myfile, $text );
			fclose( $myfile );

			if( file_exists( $file ) ){
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Create a file
		 *
		 * @param $file - Path and name of file
		 * @param string $text - the file content
		 * @param string $mode
		 * @return bool - true or false if file is created
		 */
		public function rename_file_or_folder( $source, $target = '' ){

			$source = $this->validate_path( $source );
			$target = $this->validate_path( $target );

			if( empty( $source ) || empty( $target ) ){
			    return false;
            }

            if( rename( $source, $target ) ){
                return true;
            } else {
                return false;
            }

		}

		/**
		 * Copies a file within the wordpress filesystem
		 *
		 * @param $source - Path of source file
		 * @param $target - Path of target file (With name)
		 * @return bool - If the file was copied or not
		 */
		public function copy_file( $source, $target ){

		    if( strpos( $source, '://' ) === FALSE ){
			    $source = $this->validate_path( $source );
            }

			$target = $this->validate_path( $target );

			if( empty( $source ) || empty( $target ) )
				return false;

			$is_copied = false;

			if( copy( $source, $target ) ){
				$is_copied = true;
			}

			return $is_copied;
		}

		/**
		 * Recursively copy files and folders from one directory to another
		 *
		 * @param String $source - Source of files being moved
		 * @param String $target - Destination of files being moved
         * @return array - an array with debug information and success messages
		 */
		public function copy_folder( $source, $target, $mode = 0777, $recursive = false ){
			$return = array(
				'success' => false,
				'data' => array()
			);

			$source = $this->validate_path( $source );
			$target = $this->validate_path( $target );
	
			if( ! is_dir( $source ) ){
			    return $return;
			}

			if( ! is_dir( $target ) ) {
				$check_create = mkdir( $target, $mode, $recursive );
				if( ! $check_create ) {
					return $return;
				} else {

					if( ! isset( $return['data']['folder'] ) ){
						$return['data']['folder'] = array();
					}

					$return['data']['folder'][] = array(
						'success' => true,
						'data' => $check_create,
						'source' => $source,
						'destination' => $target,

					);
				}
			}

			// Open the source directory to read in files
			$i = new DirectoryIterator( $source );
			foreach( $i as $f ) {
				if( $f->isFile() ) {
					if( ! isset( $return['data']['file'] ) ){
						$return['data']['file'] = array();
					}

					$check = copy( $f->getRealPath() , "$target/" . $f->getFilename() );
					$return['success'] = true;
					$return['data']['file'][] = array(
                        'success' => $check,
                        'source' => $f->getRealPath(),
                        'destination' => "$target/" . $f->getFilename(),
                    );
				} elseif( ! $f->isDot() && $f->isDir() ) {	
					if( ! isset( $return['data']['folder'] ) ){
						$return['data']['folder'] = array();
					}

					$check_create = mkdir( "$target/$f", $mode, $recursive );
					$return['data']['folder'][] = array(
						'success' => ( ! empty( $check_create ) ) ? true : false,
						'childs' => call_user_func( array( $this, 'copy_folder' ), $f->getRealPath(), "$target/$f", $mode, $recursive ),
						'source' => $f->getRealPath(),
                        'destination' => "$target/$f",
					);
					
				}
			}

			return $return;
		}

		/**
		 * Moves a file within the wordpress filesystem
		 *
		 * @param $source - Path of source file
		 * @param $target - Path of target file (With name)
		 * @return array - If the file was moved or not
		 */
		public function move_file( $source, $target ){

		    $return = array(
                'success' => false,
            );

			if( is_dir( $source ) ){
				return $return;
			}

			if( strpos( $source, '://' ) === FALSE ){
				$source = $this->validate_path( $source );
			}

			$target = $this->validate_path( $target );

			if( empty( $source ) || empty( $target ) )
				return $return;

			//Use copy since rename doesn't move files larger than 4GB on PHP 5.+
			if( copy( $source, $target ) ) {
				$return['success'] = true;
				$return['origin_delete'] = unlink( $source );
            }

			return $return;
		}

		/**
		 * Recursively move files from one directory to another
		 *
		 * @param String $source – Source of files being moved
		 * @param String $target – Destination of files being moved
		 */
		function move_folder( $source, $target, $mode = 0777, $recursive = false ){
			$return = array(
				'success' => false,
				'data' => array()
			);

			if( ! is_dir( $source ) ){
			    return $return;
			}

			if( ! is_dir( $target ) ) {
				if( ! mkdir( $target, $mode, $recursive ) ) {
					return $return;
				}
			}

			$allsuccessful = true;
			$di = new DirectoryIterator( $source );
			foreach( $di as $f ) {
				if( $f->isFile() ) {
					$check = rename( $f->getRealPath(), "$target/" . $f->getFilename() );

					if( ! $check ){
						$allsuccessful = false;
                    }

					$return['data']['file'][] = array(
						'success' => $check,
						'source' => $f->getRealPath(),
						'destination' => "$target/" . $f->getFilename(),

					);
                } elseif( ! $f->isDot() && $f->isDir() ) {

				    if( !  is_dir( "$target/$f" ) ){
					    $create_new_folder = mkdir( "$target/$f", $mode, $recursive );
                    } else {
					    $create_new_folder = true;
                    }

                    $delete_old_folder = false;
				    if( $create_new_folder ){
					    $delete_old_folder = rmdir( $f->getRealPath() );
                    }

                    if( ! $delete_old_folder || ! $create_new_folder ){
	                    $allsuccessful = false;
                    }

					$data = call_user_func( array( $this, 'move_folder' ), $f->getRealPath(), "$target/$f" );
					$return['data']['folder'][] = array(
						'data' => array(
                            'folder_data' => $data,
                            'new_folder_create' => $create_new_folder,
                            'old_folder_delete' => $delete_old_folder
                        )
					);

                }
			}

			if( $allsuccessful ){
				$return['success'] = rmdir( $source );
            }

			return $return;
		}

	}

endif; // End if class_exists check.