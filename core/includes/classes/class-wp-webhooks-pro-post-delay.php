<?php

/**
 * WP_Webhooks_Pro_Post_Delay Class
 *
 * This class contains all of the available api functions
 * Post Delay is a feature that allows you to fire triggers
 * not directly on its original trigger, but before PHP
 * shuts down. This allows every plugin to make it's unique changes
 * to keep track of them as well.
 *
 * @since 1.1.3
 */

/**
 * The api class of the plugin.
 *
 * @since 1.1.3
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Post_Delay {

    /**
     * List of all currently object cached post triggers
     *
     * @var array
     */
    private $post_delay_triggers = array();

    /**
	 * WP_Webhooks_Pro_Post_Delay constructor.
	 */
    function __construct(){
        $this->is_active = ( get_option( 'wpwhpro_deactivate_post_delay' ) == 'yes' ) ? false : true;
        $this->add_hooks();
    }

    /**
	 * The main function for adding our WordPress related hooks
	 *
	 * @return void
	 */
    public function add_hooks(){
        if( $this->is_active ){
            add_action( 'shutdown', array( $this, 'launch_delayed_triggers' ), 1000 );
        }
    }

    /**
     * Launch all post delayed triggers
     *
     * @return void
     */
    public function launch_delayed_triggers(){
        $triggers = apply_filters( 'wpwhpro/post_delay/post_delay_triggers', $this->post_delay_triggers );

        foreach( $triggers as $trigger ){
            $this->launch_trigger( $trigger );
        }
    }

	/**
	 * ################################
	 * ###
	 * ##### CORE LOGIC
	 * ###
	 * ################################
	 */

     /**
      * Launch a single trigger
      *
      * @param array $trigger - The trigger data containing a callback and arguments
      * @return void
      */
    public function launch_trigger( $trigger ){
        if( is_array( $trigger ) && isset( $trigger['callback'] ) && isset( $trigger['arguments'] ) && is_array( $trigger['arguments'] ) ){
            call_user_func_array( $trigger['callback'], $trigger['arguments'] );
        }
    }
    
    /**
     * Add a trigger to the post delay collector
     * 
     * This enqueues the trigger to fire it before the 
     * PHP shutdown.
     * If the feature is not active, we fire the trigger immediately.
     *
     * @param mixed $callback - Either a string, containgin the function name or an array containing the class + function
     * @param array $arguments - arguments that should be parsed to the function
     * @return mixed - Bool if is not active 
     */
	public function add_post_delayed_trigger( $callback, $arguments = array(), $options = '' ){

        //Terminate the trigger immediately if the logic is deactivated
        if( ! $this->is_active ){
            $this->launch_trigger( array(
                'callback' => $callback,
                'arguments' => $arguments,
                'options' => $options,
            ) );
            return false;
        }

        $triggers = $this->post_delay_triggers;
        
        if( ! empty( $callback ) && is_array( $arguments ) ){
            $triggers[] = array(
                'callback' => $callback,
                'arguments' => $arguments,
                'options' => $options,
            );
        }

        $this->post_delay_triggers = $triggers;
        

        return $triggers;
    }

}
