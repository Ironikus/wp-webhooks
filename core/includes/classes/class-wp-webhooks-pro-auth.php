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

	private $auth_active = null;

	/**
	 * Init everything 
	 */
	public function __construct() {

		$this->auth_active = get_option( 'wpwhpro_activate_authentication' );
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
	 * @return boolean - True if active, false if not
	 */
	public function is_active(){

		if( ! empty( $this->auth_active ) && $this->auth_active == 'yes' ){
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Initialize the authentication table
	 *
	 * @return void
	 */
	private function setup_authentication_table(){

		if( ! $this->is_active() ){
			return;
		}

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
	public function get_auth_templates( $template = 'all' ){
		if( ! $this->is_active() ){
			return false;
		}

		if( ! is_numeric( $template ) && $template !== 'all' ){
			return false;
		}

		if( ! empty( $this->cache_authentication ) ){

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
		if( ! $this->is_active() ){
			return false;
		}

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
		if( ! $this->is_active() ){
			return false;
		}

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
		if( ! $this->is_active() ){
			return false;
		}

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
		if( ! $this->is_active() ){
			return false;
		}

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
		if( ! $this->is_active() ){
			return false;
		}

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
            <form id="ironikus-authentication-template-form">
                <table class="table wpwhpro-authentication-table form-table">
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
                            <td class="tb-settings-input">
                                <?php if( in_array( $setting['type'], array( 'text' ) ) ) : ?>
                                
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="iroikus-label-id-<?php echo $setting_name; ?>"><?php echo $setting['label']; ?></span>
                                        </div>
                                        <input type="<?php echo $setting['type']; ?>" class="form-control" id="iroikus-input-id-<?php echo $setting_name; ?>" name="<?php echo $setting_name; ?>" aria-describedby="iroikus-label-id-<?php echo $setting_name; ?>"  placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
                                    </div>

                                <?php elseif( in_array( $setting['type'], array( 'checkbox' ) ) ) : ?>
                                    <label for="iroikus-input-id-<?php echo $setting_name; ?>">
                                        <strong><?php echo $setting['label']; ?></strong>
                                    </label>
                                    <label class="switch ">
                                        <input id="iroikus-input-id-<?php echo $setting_name; ?>" class="default primary" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" <?php echo $is_checked; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                <?php elseif( $setting['type'] === 'select' && isset( $setting['choices'] ) ) : ?>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="iroikus-select-id-<?php echo $setting_name; ?>"><?php echo $setting['label']; ?></label>
                                        </div>
                                        <select id="iroikus-select-id-<?php echo $setting_name; ?>" class="custom-select" name="<?php echo $setting_name; ?><?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? '[]' : ''; ?>" <?php echo ( isset( $setting['multiple'] ) && $setting['multiple'] ) ? 'multiple' : ''; ?>>
                                            <?php foreach( $setting['choices'] as $choice_name => $choice_label ) : ?>
                                            <?php
                                                $selected = '';
                                                if( $choice_name === $value ){
                                                    $selected = 'selected="selected"';
                                                }
                                            ?>
                                            <option value="<?php echo $choice_name; ?>" <?php echo $selected; ?>><?php echo WPWHPRO()->helpers->translate( $choice_label, 'wpwhpro-page-triggers' ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <p class="description">
                                    <?php echo $setting['description']; ?>
                                </p>
                            </td>
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
	 * #### CORE AUTHENTICATONS
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
                if( $auth_data['wpwhpro_auth_api_key_add_to'] === 'both' || $auth_data['wpwhpro_auth_api_key_add_to'] === 'body' ){
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

}
