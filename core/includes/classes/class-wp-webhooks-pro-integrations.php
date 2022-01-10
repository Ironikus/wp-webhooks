<?php

/**
 * WP_Webhooks_Pro_Integrations Class
 *
 * This class contains all of the webhook integrations
 *
 * @since 3.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The webhook integration class of the plugin.
 *
 * @since 3.2.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Integrations {

	/**
	 * If an action call is present, this var contains the webhook
	 *
	 * @since 3.2.0
	 * @var - The currently present action webhook
	 */
	public $integrations = array();

    function __construct(){
        add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 10 );
        add_action( 'plugins_loaded', array( $this, 'register_trigger_callbacks' ), 10 );
    }

    /**
	 * ######################
	 * ###
	 * #### INTEGRATION AUTOLOADER
	 * ###
	 * ######################
	 */

     /**
      * Initialize all default integrations
      *
      * @return void
      */
     public function load_integrations(){
         $integration_folder = $this->get_integrations_folder();
         $integration_folders = $this->get_integrations_directories();
         if( is_array( $integration_folders ) ){
             foreach( $integration_folders as $integration ){
                 $file_path = $integration_folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
                 $this->register_integration( array(
                     'slug' => $integration,
                     'path' => $file_path,
                 ) );
             }
         }
     }

     /**
      * Get an array contianing all of the currently given default integrations
      * The directory folder name acts as well as the integration slug.
      *
      * @return array The available default integrations
      */
    public function get_integrations_directories() {

        $integrations = array();
		
        try {
            $integrations = WPWHPRO()->helpers->get_folders( $this->get_integrations_folder() );
        } catch ( Exception $e ) {
            throw WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
        }

		return apply_filters( 'wpwhpro/integrations/get_integrations_directories', $integrations );
	}

    /**
     * Get the main integration folder
     *
     * @return void
     */
    public function get_integrations_folder(){
        $folder = WPWH_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations';
        return apply_filters( 'wpwhpro/integrations/get_integrations_folder', $folder );
    }

    /**
     * Register an integration 
     * 
     * This function can also be used to register third-party extensions. 
     * The following parameters are required: 
     * 
     * "path" => contains the integrations full path + file name + file extension
     * "slug" => contains the slug (folder name) of the integration
     * 
     * All other values are dynamically included (in case you define them.)
     *
     * @param array $integration
     * @return bool Whether the integration was added or not
     */
    public function register_integration( $integration = array() ){
        $return = false;
        $default_dependencies = WPWHPRO()->settings->get_default_integration_dependencies();

        if( is_array( $integration ) && isset( $integration['slug'] ) && isset( $integration['path'] ) ){
            $path = $integration['path'];
            $slug = $integration['slug'];

            if( file_exists( $path ) ){
                require_once $path;
                
                $directory = dirname( $path );
                $class = $this->get_integration_class( $slug );
                if( ! empty( $class ) && class_exists( $class ) && ! isset( $this->integrations[ $slug ] ) ){
                    $integration_class = new $class();
        
                    $is_active = ( ! method_exists( $integration_class, 'is_active' ) || method_exists( $integration_class, 'is_active' ) && $integration_class->is_active() ) ? true : false;
                    $is_active = apply_filters( 'wpwhpro/integrations/integration/is_active', $is_active, $slug, $class, $integration_class );

                    if( $is_active ) {
                        $this->integrations[ $slug ] = $integration_class;
        
                        //Register Depenencies
                        foreach( $default_dependencies as $default_dependency ){

                            //Make sure the default dependencies exists
                            if( ! property_exists( $this->integrations[ $slug ], $default_dependency ) ){
                                $this->integrations[ $slug ]->{$default_dependency} = new stdClass();
                            }

                            if( ! is_array( $this->integrations[ $slug ]->{$default_dependency} ) ){
                                $this->integrations[ $slug ]->{$default_dependency} = new stdClass();
                            }

                            $dependency_path = $directory . DIRECTORY_SEPARATOR . $default_dependency;
                            if( is_dir( $dependency_path ) ){
                                $dependencies = array();
    
                                try {
                                    $dependencies = WPWHPRO()->helpers->get_files( $dependency_path, array(
                                        'index.php'
                                    ) );
                                } catch ( Exception $e ) {
                                    throw WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
                                }
    
                                if( is_array( $dependencies ) && ! empty( $dependencies ) ){

                                    foreach( $dependencies as $dependency ){
                                        $basename = basename( $dependency );
                                        $basename_clean = basename( $dependency, ".php" );
    
                                        $ext = pathinfo( $basename, PATHINFO_EXTENSION );
                                        if ( (string) $ext !== 'php' ) {
                                            continue;
                                        }
    
                                        require_once $dependency_path . DIRECTORY_SEPARATOR . $dependency;
    
                                        $dependency_class = $this->get_integration_class( $slug, $default_dependency, $basename_clean );

                                        if( class_exists( $dependency_class ) ){
                                            $dependency_class_object = new $dependency_class();
    
                                            $is_active = ( ! method_exists( $dependency_class_object, 'is_active' ) || method_exists( $dependency_class_object, 'is_active' ) && $dependency_class_object->is_active() ) ? true : false;
                                            $is_active = apply_filters( 'wpwhpro/integrations/dependency/is_active', $is_active, $slug, $basename_clean, $dependency_class, $dependency_class_object );

                                            if( $is_active ){
                                                $this->integrations[ $slug ]->{$default_dependency}->{$basename_clean} = $dependency_class_object;
                                            }
    
                                        }
                                    }
                                }
                            }
                        }
        
                    }
        
                    $return = true;{

                    }
                }
    
            }
        }

        return $return;
    }

    /**
     * Builds the dynamic class based on the integration name and a sub file name
     *
     * @param string $integration The integration slug
     * @param string $type The type fetched from WPWHPRO()->settings->get_default_integration_dependencies()
     * @param string $sub_class A sub file name in case we add something from te default dependencies
     * @return string The integration class
     */
    public function get_integration_class( $integration, $type = '', $sub_class = '' ){
        $class = false;

        if( ! empty( $integration ) ){
            $class = 'WP_Webhooks_Integrations_' . $this->validate_class_name( $integration );
        }

        if( ! empty( $type ) && ! empty( $sub_class ) ){
            $validate_class_type = ucfirst( strtolower( $type ) );
            $class .= '_' . $validate_class_type . '_' . $this->validate_class_name( $sub_class );
        }
        
        return apply_filters( 'wpwhpro/integrations/get_integration_class', $class );
    }

    /**
     * Format the class name to make it compatible with our
     * dynamic structure
     *
     * @param string $class_name
     * @return string The class name
     */
    public function validate_class_name( $class_name ){

        $class_name = str_replace( ' ', '_', $class_name );
        $class_name = str_replace( '-', '_', $class_name );

        return apply_filters( 'wpwhpro/integrations/validate_class_name', $class_name );
    }

    /**
     * Grab the details of a given integration
     *
     * @param string $slug
     * @return array The integration details
     */
    public function get_details( $slug ){
        $return = array();

        if( ! empty( $slug ) ){
            if( isset( $this->integrations[ $slug ] ) ){
                if( method_exists( $this->integrations[ $slug ], 'get_details' ) ){
                    $return = $this->integrations[ $slug ]->get_details();
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_details', $return );
    }

    /**
     * Get all available integrations
     *
     * @param string $slug
     * @return array The integration details
     */
    public function get_integrations( $slug = false ){
        $return = $this->integrations;

        if( $slug !== false ){
            if( isset( $this->integrations[ $slug ] ) ){
                $return = $this->integrations[ $slug ];
            } else {
                $return = false;
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_integrations', $return );
    }

    /**
     * Grab a specific helper from the given integration
     *
     * @param string $integration The integration slug (folder name)
     * @param string $helper The helper slug (file name)
     * @return object|stdClass The helper class
     */
    public function get_helper( $integration, $helper ){
        $return = new stdClass();

        if( ! empty( $integration ) && ! empty( $helper ) ){
            if( isset( $this->integrations[ $integration ] ) ){
                if( property_exists( $this->integrations[ $integration ], 'helpers' ) ){
                    if( property_exists( $this->integrations[ $integration ]->helpers, $helper ) ){
                        $return = $this->integrations[ $integration ]->helpers->{$helper};
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_helper', $return );
    }

    /**
     * Get a list of all available actions
     *
     * @return array The actions
     */
    public function get_actions( $slug = false ){
        $actions = array();

        if( ! empty( $this->integrations ) ){

            if( $slug !== false ){
                if( isset( $this->integrations[ $slug ] ) ){
                    if(  property_exists( $this->integrations[ $slug ], 'actions' ) ){
                        foreach( $this->integrations[ $slug ]->actions as $action ){
                            if( method_exists( $action, 'get_details' ) ){
                                $details = $action->get_details();
                                if( is_array( $details ) && isset( $details['action'] ) && ! empty( $details['action'] ) ){

                                    //Validate parameter globally
                                    if( isset( $details['parameter'] ) && is_array( $details['parameter'] ) ){
                                        foreach( $details['parameter'] as $arg => $arg_data ){

                                            //Add name
                                            if( ! isset( $details['parameter'][ $arg ]['id'] ) ){
                                                $details['parameter'][ $arg ]['id'] = $arg;
                                            }

                                            //Add label
                                            if( ! isset( $details['parameter'][ $arg ]['label'] ) ){
                                                $details['parameter'][ $arg ]['label'] = $arg;
                                            }

                                            //Add type
                                            if( ! isset( $details['parameter'][ $arg ]['type'] ) ){
                                                $details['parameter'][ $arg ]['type'] = 'text';
                                            }

                                            //Add required
                                            if( ! isset( $details['parameter'][ $arg ]['required'] ) ){
                                                $details['parameter'][ $arg ]['required'] = false;
                                            }

                                            //Add variable
                                            if( ! isset( $details['parameter'][ $arg ]['variable'] ) ){
                                                $details['parameter'][ $arg ]['variable'] = true;
                                            }
                                            
                                        }
                                    }

                                    $actions[ $details['action'] ] = $details;
                                }
                            }
                        }
                    }
                }
            } else {
                foreach( $this->integrations as $si ){
                    if( property_exists( $si, 'actions' ) ){
                        foreach( $si->actions as $action ){
                            if( method_exists( $action, 'get_details' ) ){
                                $details = $action->get_details();
                                if( is_array( $details ) && isset( $details['action'] ) && ! empty( $details['action'] ) ){

                                    //Validate parameter globally
                                    if( isset( $details['parameter'] ) && is_array( $details['parameter'] ) ){
                                        foreach( $details['parameter'] as $arg => $arg_data ){

                                            //Add name
                                            if( ! isset( $details['parameter'][ $arg ]['id'] ) ){
                                                $details['parameter'][ $arg ]['id'] = $arg;
                                            }

                                            //Add label
                                            if( ! isset( $details['parameter'][ $arg ]['label'] ) ){
                                                $details['parameter'][ $arg ]['label'] = $arg;
                                            }

                                            //Add type
                                            if( ! isset( $details['parameter'][ $arg ]['type'] ) ){
                                                $details['parameter'][ $arg ]['type'] = 'text';
                                            }

                                            //Add required
                                            if( ! isset( $details['parameter'][ $arg ]['required'] ) ){
                                                $details['parameter'][ $arg ]['required'] = false;
                                            }

                                            //Add variable
                                            if( ! isset( $details['parameter'][ $arg ]['variable'] ) ){
                                                $details['parameter'][ $arg ]['variable'] = true;
                                            }
                                            
                                        }
                                    }

                                    $actions[ $details['action'] ] = $details;
                                }
                            }
                        }
                    }
                }
            }
            
        }

        return apply_filters( 'wpwhpro/integrations/get_actions', $actions );
    }

    /**
     * Execute the acion logic
     *
     * @param array $default_return_data
     * @param string $action
     * @return array The data we return to the webhook caller
     */
    public function execute_actions( $default_return_data, $action ){
        $return_data = $default_return_data;
        $response_body = WPWHPRO()->helpers->get_response_body();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'actions' ) ){
                    $actions = $si->actions;
                    if( is_object( $actions ) && isset( $actions->{$action} ) ){
                        if( method_exists( $actions->{$action}, 'execute' ) ){
                            $return_data = $actions->{$action}->execute( $return_data, $response_body );
                        }
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/execute_actions', $return_data );
    }

    /**
     * Get all available triggers
     *
     * @return array Te triggers
     */
    public function get_triggers( $slug = false ){
        $triggers = array();

        if( ! empty( $this->integrations ) ){

            if( $slug !== false ){
                if( isset( $this->integrations[ $slug ] ) ){
                    if( property_exists( $this->integrations[ $slug ], 'triggers' ) ){
                        foreach( $this->integrations[ $slug ]->triggers as $trigger ){
                            if( method_exists( $trigger, 'get_details' ) ){
                                $details = $trigger->get_details();
                                if( is_array( $details ) && isset( $details['trigger'] ) && ! empty( $details['trigger'] ) ){
                                    $triggers[ $details['trigger'] ] = $details;
                                }
                            }
                        }
                    }
                }
            } else {
                foreach( $this->integrations as $si ){
                    if( property_exists( $si, 'triggers' ) ){
                        foreach( $si->triggers as $trigger ){
                            if( method_exists( $trigger, 'get_details' ) ){
                                $details = $trigger->get_details();
                                if( is_array( $details ) && isset( $details['trigger'] ) && ! empty( $details['trigger'] ) ){
                                    $triggers[ $details['trigger'] ] = $details;
                                }
                            }
                        }
                    }
                }
            }
            
        }

        return apply_filters( 'wpwhpro/integrations/get_triggers', $triggers );
    }

    /**
     * Get demo data from a given trigger
     *
     * @param string $trigger
     * @param array $options
     * @return array The demo data
     */
    public function get_trigger_demo( $trigger, $options = array() ){
        $demo_data = array(); 

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = get_object_vars( $si->triggers );
                    
                    if( is_array( $triggers ) && isset( $triggers[ $trigger ] ) ){
                        if( is_object( $triggers[ $trigger ] ) && method_exists( $triggers[ $trigger ], 'get_demo' ) ){
                            $demo_data = $triggers[ $trigger ]->get_demo( $options );
                            break;
                        }
                    }
                }
            }
        }  

        return apply_filters( 'wpwhpro/integrations/get_trigger_demo', $demo_data );
    }

    /**
     * Register the callbacks for all available triggers
     *
     * @return void
     */
    public function register_trigger_callbacks(){
        $default_callback_vars = apply_filters( 'wpwhpro/integrations/default_callback_vars', array(
            'priority' => 10,
            'arguments' => 1,
            'delayed' => false,
        ) );

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = get_object_vars( $si->triggers );
                    if( is_array( $triggers ) ){
                        foreach( $triggers as $trigger_name => $trigger ){
                            if( is_object( $trigger ) && method_exists( $trigger, 'get_callbacks' ) && ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', $trigger_name ) ) ){
                                $callbacks = $trigger->get_callbacks();
                                if( ! empty( $callbacks ) && is_array( $callbacks ) ){
                                    foreach( $callbacks as $callback ){
                                        if( 
                                            isset( $callback['type'] ) 
                                            && isset( $callback['hook'] ) 
                                            && isset( $callback['callback'] )
                                        ){
                                            $type = $callback['type'];
                                            $hook = $callback['hook'];
                                            $hook_callback = $callback['callback'];
                                            $priority = isset( $callback['priority'] ) ? $callback['priority'] : $default_callback_vars['priority'];
                                            $arguments = isset( $callback['arguments'] ) ? $callback['arguments'] : $default_callback_vars['arguments'];
                                            $delayed = isset( $callback['delayed'] ) ? $callback['delayed'] : $default_callback_vars['delayed'];

                                            $callback_func = $hook_callback;

                                            if( $delayed ){
                                                $callback_func = function() use ( $type, $hook_callback, $trigger_name, $trigger ) {
                                                    $func_args = func_get_args();
                                                    WPWHPRO()->delay->add_post_delayed_trigger( $hook_callback, $func_args, array(
                                                        'trigger_name' => $trigger_name,
                                                        'trigger' => $trigger,
                                                    ) );

                                                    if( $type === 'filter' || $type === 'shortcode' ){
                                                        $return ='';

                                                        if( is_array( $func_args ) && isset( $func_args[0] ) ){
                                                            $return = $func_args[0];
                                                        }

                                                        return $return;
                                                    }
                                                };
                                            }

                                            switch( $type ){
                                                case 'filter':
                                                    add_filter( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                                case 'action':
                                                    add_action( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                                case 'shortcode':
                                                    add_shortcode( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        do_action( 'wpwhpro/integrations/callbacks_registered' );
    }

}
