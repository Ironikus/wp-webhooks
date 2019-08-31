<?php

/**
 * WP_Webhooks_Pro_Helpers Class
 *
 * This class contains all of the available helper functions
 *
 * @since 1.0.0
 */

/**
 * The helpers of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Helpers {

	/**
     * Variable to check if translations
     * are active or not
     *
	 * @var bool - False as default
	 */
    private $activate_translations = false;

	/**
     * The current content of the incoming response
     *
	 * @var mixed - the current content
	 */
	private $incoming_content = false;

	/**
	 * WP_Webhooks_Pro_Helpers constructor.
	 */
    public function __construct() {
		$this->activate_translations = ( get_option( 'wpwhpro_activate_translations' ) == 'yes' ) ? true : false;
    }

	/**
	 * Translate custom Strings
	 *
	 * @param $string - The language string
	 * @param null $cname - If no custom name is set, return the default one
	 * @return string - The translated language string
	 */
	public function translate( $string, $cname = null, $prefix = null ){

		/**
		 * Filter to control the translation and optimize
		 * them to a specific output
		 */
		$trigger = apply_filters( 'wpwhpro/helpers/control_translations', $this->activate_translations, $string, $cname );
		if( empty( $trigger ) ){
			return $string;
		}

		if( empty( $string ) ){
			return $string;
		}

		if( ! empty( $cname ) ){
			$context = $cname;
		} else {
			$context = 'default';
		}

		if( $prefix == 'default' ){
			$front = 'WPWHPRO: ';
		} elseif ( ! empty( $prefix ) ){
			$front = $prefix;
		} else {
			$front = '';
		}

		// WPML String Translation Logic (WPML itself has problems with _x in some versions)
		if( function_exists( 'icl_t' ) ){
			return icl_t( (string) 'wp-webhooks-pro', $context, $string );
		} else {
			return $front . _x( $string, $context, (string) 'wp-webhooks-pro' );
		}
	}

	/**
	 * Checks if the parsed param is available on the current site
	 *
	 * @param $param
	 * @return bool
	 */
	public function is_page( $param ){
		if( empty( $param ) ){
			return false;
		}

		if( isset( $_GET['page'] ) ){
			if( $_GET['page'] == $param ){
				return true;
			}
		}

		return false;
	}

	/**
	 * Creates a formatted admin notice
	 *
	 * @param $content - notice content
	 * @param string $type - Status of the specified notice
	 * @param bool $is_dismissible - If the message should be dismissible
	 * @return string - The formatted admin notice
	 */
	public function create_admin_notice($content, $type = 'info', $is_dismissible = true){
		if(empty($content))
			return '';

		/**
		 * Block an admin notice based onn the specified values
		 */
		$throwit = apply_filters('wpwhpro/helpers/throw_admin_notice', true, $content, $type, $is_dismissible);
		if(!$throwit)
			return '';

		if($is_dismissible !== true){
			$isit = '';
		} else {
			$isit = 'is-dismissible';
		}


		switch($type){
			case 'info':
				$notice = 'notice-info';
				break;
			case 'success':
				$notice = 'notice-success';
				break;
			case 'warning':
				$notice = 'notice-warning';
				break;
			case 'error':
				$notice = 'notice-error';
				break;
			default:
				$notice = 'notice-info';
				break;
		}

		if( is_array( $content ) ){
			$validated_content = sprintf( $this->translate($content[0], 'create-admin-notice'), $content[1] );
        } else {
			$validated_content = $this->translate($content, 'create-admin-notice');
        }

		ob_start();
		?>
		<div class="notice <?php echo $notice; ?> <?php echo $isit; ?>">
			<p><?php echo $validated_content; ?></p>
		</div>
		<?php
		$res = ob_get_clean();

		return $res;
	}

	/**
	 * Formats a specific date to datetime
	 *
	 * @param $date
	 * @return DateTime
	 */
	public function get_datetime($date){
		$date_new = date('Y-m-d H:i:s', strtotime($date));
		$date_new_formatted = new DateTime($date_new);

		return $date_new_formatted;
	}

	/**
	 * Retrieves a response from an url
	 *
	 * @param $url
	 * @param $data - specifies a special part of the response
	 * @return array|bool|int|string|WP_Error
	 */
	public function get_from_api( $url, $data = '' ){

		if(empty($url))
			return false;

		if(!empty($this->disable_ssl)){
			$setting = array(
				'sslverify'     => false,
				'timeout' => 30
			);
		} else {
			$setting = array(
				'timeout' => 30
			);
		}

		$val = wp_remote_get( $url, $setting );

		if($data == 'body'){
			$val = wp_remote_retrieve_body( $val );
		} elseif ($data == 'response'){
			$val = wp_remote_retrieve_response_code( $val );
		}


		return $val;
	}

	/**
	 * Builds an url out of the mai values
	 *
	 * @param $url - the default url to set the params to
	 * @param $args - the available args
	 * @return string - the url
	 */
	public function built_url( $url, $args ){
		if(!empty($args)){
			$url .= '?' . http_build_query($args);
		}

		return $url;
	}

	/**
	 * Creates the home url in a more optimized way
	 *
	 * @param $path - the default url to set the params to
	 * @param $scheme - the available args
	 * @return string - the validated url
	 */
	public function safe_home_url( $path = '', $scheme = 'irndefault' ){
		
		if( $scheme === 'irndefault' ){
			if( is_ssl() ){
				$scheme = 'https';
			} else {
				$scheme = null;
			}
		}

		return home_url( $path, $scheme );
	}

	/**
	 * Get Parameters from URL string
	 *
	 * @param $url - the url
	 *
	 * @return array - the parameters of the url
	 */
	public function get_parameters_from_url( $url ){

		$parts = parse_url($url);

		parse_str($parts['query'], $url_parameter);

		return empty( $url_parameter ) ? array() : $url_parameter;

	}

	/**
	 * Builds an url out of the mai values
	 *
	 * @param $url - the default url to set the params to
	 * @param $args - the available args
	 * @return string - the url
	 */
	public function get_current_url($with_args = true){

		$current_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';

		$host_part = $_SERVER['SERVER_NAME'];
		if( strpos( $host_part, $_SERVER['HTTP_HOST'] ) === false ){

		    //Validate against HTTP_HOST in case SERVER_NAME has no "www" set
			if( strpos( $_SERVER['HTTP_HOST'], '://www.' ) !== false && strpos( $host_part, '://www.' ) === false ){
				$host_part = str_replace( '://', '://www.', $host_part );
			}

		}

		$current_url .= sanitize_text_field( $host_part ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );

	    if($with_args){
	        return $current_url;
        } else {
	        return strtok( $current_url, '?' );
        }
	}

	/**
     * Get value in between two of our custom tags
     *
     * Usage example:
     * if you want to get the key post_id, you need
     * to have the following tags inside of your content
     * @post_id-start@12345@post_id-end@
     *
	 * @param $ident - the key for a tag you want to check against
	 * @param $content - the content that should be checked against the tag
	 *
	 * @return mixed
	 */
	function get_value_between($ident, $content){
		$matches = array();
		$t = preg_match('/@' . $ident . '-start@(.*?)\\@' . $ident . '-end@/s', $content, $matches);
		return isset( $matches[1] ) ? $matches[1] : '';
	}

	/**
	 * Decode a json response
	 *
	 * @param $response - the response
	 * @return array|mixed|object - the encoded content
	 */
	public function decode_response($response){
		if(!empty($response))
			return json_decode($response, true);

		return $response;
	}

	/**
	 * Evaluate the content type and validate it properly
	 *
	 * @return array - the response content and content_type
	 */
	public function get_response_body(){

	    $return = array(
            'content_type' => 'unknown',
            'content' => ''
        );

	    //We don't support other income streams than POST
        if( ! isset( $_SERVER["CONTENT_TYPE"] ) ){
            return $return;
        }

        //Cache current content
        if( empty( $this->incoming_content ) ){
	        $this->incoming_content = $_SERVER["CONTENT_TYPE"];
        }

	    $current_content_type = $this->incoming_content;
		$return['content_type'] = $current_content_type;

		$response = file_get_contents('php://input');
		$content_evaluated = false;

		if( strpos( $current_content_type, 'application/json' ) !== false ){
			if( $this->is_json( $response ) ){
				$return['content'] = json_decode( $response );
				$content_evaluated = true;
			}
        }

		if( strpos( $current_content_type, 'application/xml' ) !== false && ! $content_evaluated ){
			if( $this->is_xml( $response ) ){
				$return['content'] = simplexml_load_string( $response );
				$content_evaluated = true;
			}
        }

		if( strpos( $current_content_type, 'application/x-www-form-urlencoded' ) !== false && ! $content_evaluated ){
			parse_str( $response, $form_data );
			$form_data= (object)$form_data;
			if( is_object( $form_data ) ){
				$return['content'] = $form_data;
				$content_evaluated = true;
            }
        }

        //If nothing is set, we take the content as it comes
        if( ! $content_evaluated && is_string( $response ) ){
	        $return['content'] = $response;
        }

		return apply_filters( 'wpwhpro/helpers/validate_response_body', $return, $current_content_type, $response );
	}

	/**
     * Check if a given string is a json
     *
	 * @param $string - the string that should be checked
	 *
	 * @return bool - True if it is json, otherwise false
	 */
	public function is_json( $string ) {
		json_decode( $string );
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
     * Check if a specified content is xml
     *
	 * @param $content - the string that should be checked
	 *
	 * @return bool - True if it is xml, otherwise false
	 */
	public function is_xml($content) {
	    //Make sure libxml is available
	    if( ! function_exists( 'libxml_use_internal_errors' ) ){
	        return false;
        }

		$content = trim( $content );
		if( empty( $content ) ) {
			return false;
		}

		if( stripos( $content, '<!DOCTYPE html>' ) !== false ) {
			return false;
		}

		libxml_use_internal_errors( true );
		simplexml_load_string( $content );
		$errors = libxml_get_errors();
		libxml_clear_errors();

		return empty( $errors );
	}

	/**
	 * Check if a specified content is xml
	 *
	 * @param $object - the simplexml object
	 * @param $data - the data that should be converted
	 *
	 * @return $obbject - The current simple xml element
	 */
	function convert_to_xml( SimpleXMLElement $object, array $data ) {

		foreach( $data as $key => $value ) {
			if( is_array( $value ) ) {
				$new_object = $object->addChild( $key );
				$this->convert_to_xml( $new_object, $value );
			} else {
				// if the key is an integer, it needs text with it to actually work.
				if( is_numeric( $key ) ) {
					$prefix = apply_filters( 'wpwhpro/helpers/convert_to_xml_int_prefix', 'wpwh_', $object, $data );
					$key = $prefix . $key;
				}

				$object->addChild( $key, $value );
			}
		}

		return $object;
	}

	/**
     * This function validates all necessary tags for displayable content.
     *
	 * @param $content - The validated content
	 * @since 1.4
	 * @return mixed
	 */
	public function validate_local_tags( $content ){

	    $user = get_user_by( 'id', get_current_user_id() );

	    $user_name = 'there';
	    if( ! empty( $user ) && ! empty( $user->data ) && ! empty( $user->data->display_name ) ){
	        $user_name = $user->data->display_name;
        }

		$content = str_replace(
			array( '%home_url%', '%admin_url%', '%product_version%', '%product_name%', '%user_name%' ),
			array( home_url(), get_admin_url(), WPWH_VERSION, WPWH_NAME, $user_name ),
			$content
		);

		return $content;
    }

	/**
     * Creates a unique user name for existing users
     *
	 * @param $email - the email address of the user
	 * @param string $prefix - a custom prefix
	 *
	 * @return string
	 */
	public function create_random_unique_username( $email, $prefix = '' ){
		$user_exists = 1;
		$email = sanitize_title( $email );
		do {
			$rnd_str = sprintf("%0d", mt_rand(1, 999999));
			$user_exists = username_exists( $prefix . $email . $rnd_str );
		} while( $user_exists > 0 );
		return $prefix . $email . $rnd_str;
	}

	/**
     * Display a particular variable
     *
	 * @param string $code - the variable
	 *
	 * @return false|string
	 */
	public function display_var( $code = '' ){
	    ob_start();
	    print_r( $code );
	    return ob_get_clean();
    }

	/**
     * Main value validator
     *
     * You can use it to validate various tags as listed
     * down below.
     *
     * For our string values, we focus on grabbing
     * the content that is set within our custo mtag logic
     *
	 * @param $content
	 * @param $key
	 */
	public function validate_request_value( $content, $key ){
	    $return = false;

        if( is_object( $content ) ){

            if( isset( $content->$key ) ){
                $return = $content->$key;
            }

        } elseif( is_array( $content ) ){

            if( isset( $content[ $key ] ) ){
                $return = $content[ $key ];
            }

        } elseif( is_string( $content ) ) {

	        $return = $this->get_value_between( $key, $content );

        }

        //Validate a left over object to an array
        if( is_object( $return ) ){
            $return = json_decode( json_encode( $return ), true );
        }

        if( is_array( $return ) ){
			//Make sure we don't pass single arrays as well
			if( isset( $return[0] ) && count( $return ) <= 1 ){
				$return = $return[0];
			} else {
				//other arrays will be again encoded to a json
				$return = json_encode( $return );
			}
	        
		}
		
		//Validate form url encode strings again
		if( is_string( $return ) ){
            $stripslashes = apply_filters( 'wpwhpro/helpers/request_values_stripslashes', true, $return );
			if( $stripslashes ){
				$return = stripslashes( $return );
			}
		}

        return apply_filters( 'wpwhpro/helpers/request_return_value', $return, $content, $key );
    }

	public function get_current_ip() {
		$ipaddress = false;
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ){
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif( isset( $_SERVER['HTTP_X_FORWARDED'] ) ){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ){
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif( isset( $_SERVER['HTTP_FORWARDED'] ) ){
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif( isset( $_SERVER['REMOTE_ADDR'] ) ){
			$ipaddress = $_SERVER['REMOTE_ADDR'];
        }

		return $ipaddress;
	}
}
