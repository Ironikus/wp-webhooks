<?php

/**
 * WP_Webhooks_Pro_Authentication Class
 *
 * This class contains all of the available authentication functions
 *
 * @since 3.0.0
 */

/**
 * The authentication class of the plugin.
 *
 * @since 3.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Authentication {

	/**
	 * Init everything
	 */
	public function __construct() {
        $this->authentication_table_data = WPWHPRO()->settings->get_authentication_table_data();
        $this->auth_methods = WPWHPRO()->settings->get_authentication_methods();
		$this->cache_table_exists = null;
		$this->cache_authentication = array();
		$this->cache_authentication_count = 0;
        $this->setup_authentication_table();

	}

	/**
	 * Wether the authentication feature is active or not
	 *
	 * Authentication will now be active by default
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active(){
		return true;
	}

	/**
	 * Initialize the authentication table
	 *
	 * @return void
	 */
	private function setup_authentication_table(){

		if( $this->cache_table_exists !== null ){
			return $this->cache_table_exists;
		}

		if( ! WPWHPRO()->sql->table_exists( $this->authentication_table_data['table_name'] ) ){
			WPWHPRO()->sql->run_dbdelta( $this->authentication_table_data['sql_create_table'] );
			$this->cache_table_exists = true;
		}

	}

	/**
	 * Get the data authentication template/S
	 *
	 * @param string $template
	 * @return array - an array of the authentication settings
	 */
	public function get_auth_templates( $template = 'all', $cached = true ){

		if( ! is_numeric( $template ) && $template !== 'all' ){
			return false;
		}

		if( ! empty( $this->cache_authentication ) && $cached ){

			if( $template !== 'all' ){
				if( isset( $this->cache_authentication[ $template ] ) ){
					return $this->cache_authentication[ $template ];
				} else {
					return false;
				}
			} else {
				return $this->cache_authentication;
			}

		}

		$this->setup_authentication_table();

		$sql = 'SELECT * FROM {prefix}' . $this->authentication_table_data['table_name'] . ' ORDER BY name ASC;';

		$data = WPWHPRO()->sql->run($sql);

		$validated_data = array();
		if( ! empty( $data ) && is_array( $data ) ){
			foreach( $data as $single ){
				if( ! empty( $single->id ) ){
					$validated_data[ $single->id ] = $single;
				}
			}
		}

		$this->cache_authentication = $validated_data;

		if( $template !== 'all' ){
			if( isset( $this->cache_authentication[ $template ] ) ){
				return $this->cache_authentication[ $template ];
			} else {
				return false;
			}
		} else {
			return $this->cache_authentication;
		}
    }

    /**
	 * Helper function to flatten authentication specific data
	 *
	 * @param mixed $data - the data value that needs to be flattened
	 * @return mixed - the flattened value
	 */
	public function flatten_authentication_data( $data ){
		$flattened = array();

		foreach( $data as $id => $sdata ){
			$flattened[ $id ] = $sdata->name;
		}

		return $flattened;
	}

	/**
	 * Delete a authentication template
	 *
	 * @param ind $id - the id of the authentication template
	 * @return bool - True if deletion was succesful, false if not
	 */
	public function delete_authentication_template( $id ){

		$id = intval( $id );

		if( ! $this->get_auth_templates( $id ) ){
			return false;
		}

		$sql = 'DELETE FROM {prefix}' . $this->authentication_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Get a global count of all authentication templates
	 *
	 * @return mixed - int if count is available, false if not
	 */
	public function get_authentication_count(){

		if( ! empty( $this->cache_authentication_count ) ){
			return intval( $this->cache_authentication_count );
		}

		$this->setup_authentication_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->authentication_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if( is_array( $data ) && ! empty( $data ) ){
			$this->cache_authentication_count = $data;
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}

	/**
	 * Add a authentication template
	 *
	 * @param string $name - the name of the authentication template
	 * @return bool - True if the creation was successful, false if not
	 */
	public function add_template( $name, $auth_type ){

		$sql_vals = array(
			'name' => $name,
			'auth_type' => $auth_type,
			'log_time' => date( 'Y-m-d H:i:s' )
		);

		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->authentication_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Update an existing authentication template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_template( $id, $data ){

		$id = intval( $id );

		if( ! $this->get_auth_templates( $id ) ){
			return false;
		}

		$sql_vals = array();

		if( isset( $data['name'] ) ){
			$sql_vals['name'] = sanitize_title( $data['name'] );
		}

		if( isset( $data['template'] ) ){
			$sql_vals['template'] = base64_encode( $data['template'] );
		}

		if( empty( $sql_vals ) ){
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ){

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->authentication_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Delete the whole authentication table
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_table(){

		$check = true;
		if( WPWHPRO()->sql->table_exists( $this->authentication_table_data['table_name'] ) ){
			$check = WPWHPRO()->sql->run( $this->authentication_table_data['sql_drop_table'] );
		}

		return $check;
    }

    public function get_html_fields_form( $current_method, $template_json ){

        $return = '';

        if( empty( $template_json ) || ! WPWHPRO()->helpers->is_json( $template_json ) ){
            $template_json = json_encode( array() );
        }

        $template_data = json_decode( $template_json, true );
        if( empty( $template_data ) ){
            $template_data = array();
        }

        if( ! empty( $current_method ) && isset( $this->auth_methods[ $current_method ] ) ){

            ob_start();
            ?>
            <form id="wpwh-authentication-template-form">
                <table class="wpwh-table wpwh-table--no-style wpwhpro-authentication-table form-table">
                    <tbody>

                    <?php foreach( $this->auth_methods[ $current_method ]['fields'] as $setting_name => $setting ) :

                        if( ! isset( $setting['value'] ) ){
                            $setting['value'] = $setting['default_value'];
                        }

                        //Map settings values
                        if( isset( $template_data[ $setting_name ] ) ){
                            $setting['value'] = $template_data[ $setting_name ];
                        }

                        $is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
                        $value = ( $setting['type'] != 'checkbox' && isset( $setting['value'] ) ) ? $setting['value'] : '1';
                        $placeholder = ( $setting['type'] != 'checkbox' && isset( $setting['placeholder'] ) ) ? $setting['placeholder'] : '';

                        ?>
                        <tr valign="top">
                            <td>
                                <label class="wpwh-form-label pt-2" for="iroikus-label-id-<?php echo $setting_name; ?>">
                                    <strong><?php echo $setting['label']; ?></strong>
                                </label>
                            </td>
                            <td>
                                <?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>

                                    <input type="<?php echo $setting['type']; ?>" class="wpwh-form-input wpwh-w-100" id="iroikus-input-id-<?php echo $setting_name; ?>" name="<?php echo $setting_name; ?>" aria-describedby="iroikus-label-id-<?php echo $setting_name; ?>"  placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />

                                <?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
                                    <label class="wpwh-form-label" for="iroikus-input-id-<?php echo $setting_name; ?>">
                                        <strong><?php echo $setting['label']; ?></strong>
                                    </label>
                                    <div class="wpwh-toggle wpwh-toggle--on-off">
                                        <input class="wpwh-toggle__input" id="iroikus-input-id-<?php echo $setting_name; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> >
                                        <label class="wpwh-toggle__btn" for="iroikus-input-id-<?php echo $setting_name; ?>"></label>
                                    </div>
                                <?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
                                    <select id="iroikus-select-id-<?php echo $setting_name; ?>" class="wpwh-form-input wpwh-w-100" name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>>
                                        <?php foreach( $setting['choices'] as $choice_name => $choice_label ) : ?>
                                        <?php
                                            $selected = '';
                                            if( $choice_name === $value ){
                                                $selected = 'selected="selected"';
                                            }
                                        ?>
                                        <option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo WPWHPRO()->helpers->translate( $choice_label, 'wpwhpro-page-authentication' ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $setting['description']; ?></td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </form>
            <?php
            $return .= ob_get_clean();

        }

        return $return;
    }

    /**
	 * ######################
	 * ###
	 * #### CORE TRIGGER AUTHENTICATONS
	 * ###
	 * ######################
	 */

    public function validate_http_api_key_body( $body, $auth_data ){

        if(
            ! isset( $auth_data['wpwhpro_auth_api_key_add_to'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_key'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_value'] )
        ){
            return $body;
        }

        if( is_array( $body ) && $auth_data['wpwhpro_auth_api_key_add_to'] === 'both' || $auth_data['wpwhpro_auth_api_key_add_to'] === 'body' ){
            $body[ $auth_data['wpwhpro_auth_api_key_key'] ] = $auth_data['wpwhpro_auth_api_key_value'];
        }

        return $body;
    }

    public function validate_http_api_key_header( $http_args, $auth_data ){

        if(
            ! isset( $auth_data['wpwhpro_auth_api_key_add_to'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_key'] )
            || ! isset( $auth_data['wpwhpro_auth_api_key_value'] )
        ){
            return $http_args;
        }

        if( is_array( $http_args ) ){
            if( isset( $http_args['headers'] ) ){
                if( $auth_data['wpwhpro_auth_api_key_add_to'] === 'both' || $auth_data['wpwhpro_auth_api_key_add_to'] === 'header' ){
                    $http_args['headers'][ $auth_data['wpwhpro_auth_api_key_key'] ] = $auth_data['wpwhpro_auth_api_key_value'];
                }
            }
        }

        return $http_args;
    }

    public function validate_http_bearer_token_header( $http_args, $auth_data ){

        if( ! isset( $auth_data['wpwhpro_auth_bearer_token_token'] ) ){
            return $http_args;
        }

        if( is_array( $http_args ) ){
            if( isset( $http_args['headers'] ) ){
                if( ! empty( $auth_data['wpwhpro_auth_bearer_token_token'] ) ){
                    $http_args['headers']['Authorization'] = 'Bearer ' . $auth_data['wpwhpro_auth_bearer_token_token'];
                }
            }
        }

        return $http_args;
    }

    public function validate_http_basic_auth_header( $http_args, $auth_data ){

        if(
            ! isset( $auth_data['wpwhpro_auth_basic_auth_username'] )
            || ! isset( $auth_data['wpwhpro_auth_basic_auth_password'] )
        ){
            return $http_args;
        }

        if( is_array( $http_args ) ){
            if( isset( $http_args['headers'] ) ){
                $http_args['headers']['Authorization'] = 'Basic ' . base64_encode( $auth_data['wpwhpro_auth_basic_auth_username'] . ':' . $auth_data['wpwhpro_auth_basic_auth_password'] );
            }
        }

        return $http_args;
    }
	
	/**
	 * ######################
	 * ###
	 * #### CORE ACTION AUTHENTICATONS
	 * ###
	 * ######################
	 */

	 public function verify_incoming_request( $settings_data ){
		 $return = array(
			 'success' => false
		 );

		$template = WPWHPRO()->auth->get_auth_templates( $settings_data );
		if( ! empty( $template ) && ! empty( $template->template ) && ! empty( $template->auth_type ) ){
			$sub_template_data = base64_decode( $template->template );
			if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
				$template_data = json_decode( $sub_template_data, true );
				if( ! empty( $template_data ) ){

					switch( $template->auth_type ){
						case 'api_key':
							$return = $this->action_validate_api_key( $template_data );
							break;
						case 'basic_auth':
							$return = $this->action_validate_basic_auth( $template_data );
							break;
					}

				}
			}
		}

		return $return;
	}

	public function action_validate_api_key( $template_data ){
		$return = array(
			'success' => false
		);
		$response_body = WPWHPRO()->helpers->get_response_body();
		$auth_api_key = $template_data['wpwhpro_auth_api_key_key'];
		$auth_api_val = $template_data['wpwhpro_auth_api_key_value'];
		$auth_api_pos = $template_data['wpwhpro_auth_api_key_add_to'];

		switch( $auth_api_pos ){
			case 'header':

				$header_value = WPWHPRO()->helpers->validate_server_header( $auth_api_key );
				
				if( $header_value !== NULL ){
					if( $header_value === $auth_api_val ){
						$return['success'] = true;
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication was successful.', 'wpwhpro-page-authentication' );
					} else {
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. API Key not valid.', 'wpwhpro-page-authentication' );
					}
				} else {
					$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. No API Key found.', 'wpwhpro-page-authentication' );
				}
				
				break;
			case 'body':

				$live_body_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], $auth_api_key );
				if( ! empty( $live_body_key ) ){
					if( $live_body_key === $auth_api_val ){
						$return['success'] = true;
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication was successful.', 'wpwhpro-page-authentication' );
					} else {
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. API Key not valid.', 'wpwhpro-page-authentication' );
					}
				} else {
					$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. No API Key found.', 'wpwhpro-page-authentication' );
				}

				break;
			case 'both':

				$live_body_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], $auth_api_key );
				$header_value = WPWHPRO()->helpers->validate_server_header( $auth_api_key );
				if( $header_value !== NULL && ! empty( $live_body_key ) ){
					if( $header_value === $auth_api_val && $live_body_key === $auth_api_val ){
						$return['success'] = true;
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication was successful.', 'wpwhpro-page-authentication' );
					} else {
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. API Key not valid.', 'wpwhpro-page-authentication' );
					}
				} else {
					$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. No API Keys found.', 'wpwhpro-page-authentication' );
				}
				
				break;
		}

		return $return;
	}

	public function action_validate_basic_auth( $template_data ){
		$return = array(
			'success' => false
		);
		
		//User validation
		if( isset( $_SERVER['PHP_AUTH_USER'] ) && ! empty( $_SERVER['PHP_AUTH_USER'] ) ){
			if( $_SERVER['PHP_AUTH_USER'] === $template_data['wpwhpro_auth_basic_auth_username'] ){

				//Password validation
				if( isset( $_SERVER['PHP_AUTH_PW'] ) && ! empty( $_SERVER['PHP_AUTH_PW'] ) ){
					if( $_SERVER['PHP_AUTH_PW'] === $template_data['wpwhpro_auth_basic_auth_password'] ){
						$return['success'] = true;
						$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication was successful.', 'wpwhpro-page-authentication' );
					} else {
						$return['msg'] = WPWHPRO()->helpers->translate( 'Wrong username or password.', 'wpwhpro-page-authentication' );
					}
				} else {
					$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. No auth password given.', 'wpwhpro-page-authentication' );
				}
				
			} else {
				$return['msg'] = WPWHPRO()->helpers->translate( 'Wrong username or password.', 'wpwhpro-page-authentication' );
			}
		} else {
			$return['msg'] = WPWHPRO()->helpers->translate( 'Authentication denied. No auth user given.', 'wpwhpro-page-authentication' );
		}

		return $return;
	}

}
