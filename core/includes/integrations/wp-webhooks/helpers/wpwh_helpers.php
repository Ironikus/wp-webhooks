<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Helpers_wpwh_helpers' ) ) :

	/**
	 * Load the WP Webhooks helpers
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Helpers_wpwh_helpers {

		public function validate_data( $attr, $shortcode_tags ){

            if( is_array( $attr ) ){
                foreach( $attr as $ak => $av ){
                    $attr[ $ak ] = call_user_func( array( $this, 'validate_data' ), $av, $shortcode_tags );
                }
            } elseif( is_string( $attr ) ) {
                $attr = $this->validate_shortcode_tags( $attr, $shortcode_tags );
            }

            return $attr;
        }

        /**
         * This function validates all necessary tags for the shortcode.
         *
         * @param $content - The validated content
         * @since 1.4
         * @return mixed
         */
        public function validate_shortcode_tags( $content, $shortcode_tags ){
            $tags = array();
            $values = array();

            foreach( $shortcode_tags as $st ){
                if( isset( $st['tag_name'] ) && isset( $st['value'] ) ){
                    $fulltag = '%' . $st['tag_name'] . '%';
                    $tvalue = ( is_array( $st['value'] ) ) ? call_user_func_array( $st['value'], array( 'content' => $content ) ) : $st['value'];

                    //pre-return single content tags to also allow arrays and objects
                    if( strlen( str_replace( $fulltag, '', $content  ) ) === 0 ){
                        return $tvalue;
                    }

                    //Make sure to only allow strings here
                    if( is_string( $tvalue ) ){
                        $tags[] = $fulltag ;
                        $values[] = $tvalue;
                    }
                    
                }
            }

            $content = str_replace(
                $tags,
                $values,
                $content
            );

            return $content;
        }

        public function get_shortcode_tags(){
            $tags = array(

                'home_url' => array(
                    'tag_name' => 'home_url',
                    'title' => WPWHPRO()->helpers->translate( 'Home URL', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the home URL of the website.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_home_url' ),
                ),

                'admin_url' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Admin URL', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'Returns the admin URL of the website.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_admin_url' ),
                ),

                'date' => array(
                    'tag_name' => 'admin_url',
                    'title' => WPWHPRO()->helpers->translate( 'Date and Time', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The date and time in mySQL format: Y-m-d H:i:s', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_date' ),
                ),

                'user_id' => array(
                    'tag_name' => 'user_id',
                    'title' => WPWHPRO()->helpers->translate( 'User ID', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The ID of the currenty logged in user. 0 if none.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user_id' ),
                ),

                'user' => array(
                    'tag_name' => 'user',
                    'title' => WPWHPRO()->helpers->translate( 'Full User', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user data of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user' ),
                ),

                'user_email' => array(
                    'tag_name' => 'user_email',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user_email' ),
                ),

                'display_name' => array(
                    'tag_name' => 'display_name',
                    'title' => WPWHPRO()->helpers->translate( 'User Display Name', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The display name of the currently logged in user.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_display_name' ),
                ),

                'user_login' => array(
                    'tag_name' => 'user_login',
                    'title' => WPWHPRO()->helpers->translate( 'User Login Name', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The login name of the currently logged in user.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user_login' ),
                ),

                'user_nicename' => array(
                    'tag_name' => 'user_nicename',
                    'title' => WPWHPRO()->helpers->translate( 'User Nicename', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The nicename of the currently logged in user.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user_nicename' ),
                ),

                'user_roles' => array(
                    'tag_name' => 'user_roles',
                    'title' => WPWHPRO()->helpers->translate( 'User Roles', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The roles of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user_roles' ),
                ),

                'user_meta' => array(
                    'tag_name' => 'user_meta',
                    'title' => WPWHPRO()->helpers->translate( 'User Meta', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full user meta of the currently logged in user. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_user_meta' ),
                ),

                'post_id' => array(
                    'tag_name' => 'post_id',
                    'title' => WPWHPRO()->helpers->translate( 'Post ID', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post id of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_id' ),
                ),

                'post' => array(
                    'tag_name' => 'post',
                    'title' => WPWHPRO()->helpers->translate( 'Post Data', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post data of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post' ),
                ),

                'post_title' => array(
                    'tag_name' => 'post_title',
                    'title' => WPWHPRO()->helpers->translate( 'Post Title', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post title of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_title' ),
                ),

                'post_excerpt' => array(
                    'tag_name' => 'post_excerpt',
                    'title' => WPWHPRO()->helpers->translate( 'Post Excerpt', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post excerpt of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_excerpt' ),
                ),

                'post_content' => array(
                    'tag_name' => 'post_content',
                    'title' => WPWHPRO()->helpers->translate( 'Post Content', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post content of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_content' ),
                ),

                'post_author' => array(
                    'tag_name' => 'post_author',
                    'title' => WPWHPRO()->helpers->translate( 'Post Author', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post author of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_author' ),
                ),

                'post_type' => array(
                    'tag_name' => 'post_type',
                    'title' => WPWHPRO()->helpers->translate( 'Post Type', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post type of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_type' ),
                ),

                'post_status' => array(
                    'tag_name' => 'post_status',
                    'title' => WPWHPRO()->helpers->translate( 'Post Status', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post status of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_status' ),
                ),

                'post_date' => array(
                    'tag_name' => 'post_date',
                    'title' => WPWHPRO()->helpers->translate( 'Post Date', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The post date of the currently given post.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_date' ),
                ),

                'post_meta' => array(
                    'tag_name' => 'post_meta',
                    'title' => WPWHPRO()->helpers->translate( 'Post Meta', 'trigger-wpwh_button' ),
                    'description' => WPWHPRO()->helpers->translate( 'The full post meta of the currently given post. Please make sure to add this dynamic tag as the only content to your specific parameter as it will include an array and not a string.', 'trigger-wpwh_button' ),
                    'value' => array( $this, 'tag_get_post_meta' ),
                ),

            );

            return apply_filters( 'wpwhpro/triggers/wpwh_button/tags', $tags );
        }

        public function tag_get_home_url(){
            return home_url();
        }

        public function tag_get_admin_url(){
            return admin_url();
        }

        public function tag_get_date(){
            return date("Y-m-d H:i:s");
        }

        public function tag_get_user_id(){
            return get_current_user_id();
        }

        public function tag_get_user(){
            return $this->get_user();
        }

        public function tag_get_user_email(){
            return $this->get_user('user_email');
        }

        public function tag_get_display_name(){
            return $this->get_user('display_name');
        }

        public function tag_get_user_login(){
            return $this->get_user('user_login');
        }

        public function tag_get_user_nicename(){
            return $this->get_user('user_nicename');
        }

        public function tag_get_user_roles(){
            return $this->get_user('user_roles');
        }

        public function tag_get_user_meta(){
            $return = array();
            $user_id = get_current_user_id();

            if( ! empty( $user_id ) ){
                $return = get_user_meta( $user_id );
            }
            
            return $return;
        }

        public function get_user( $single_val = false ){

            $return = false;
            $user = get_user_by( 'id', get_current_user_id() );

            if( $single_val && ! empty( $user ) ){

                switch( $single_val ){
                    case 'user_email': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_email ) ){
                            $return = $user->data->user_email;
                        }
                        break;
                    case 'display_name': 
                        if( ! empty( $user->data ) && ! empty( $user->data->display_name ) ){
                            $return = $user->data->display_name;
                        }
                        break;
                    case 'user_login': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_login ) ){
                            $return = $user->data->user_login;
                        }
                        break;
                    case 'user_nicename': 
                        if( ! empty( $user->data ) && ! empty( $user->data->user_nicename ) ){
                            $return = $user->data->user_nicename;
                        }
                        break;
                    case 'user_roles': 
                        if( isset( $user->roles ) ){
                            $return = $user->data->user_nicename;
                        }
                        break;
                }
                
            } else {
                $return = $user;
            }

            return $return;
        }

        public function tag_get_post_id(){
            return get_the_ID();
        }

        public function tag_get_post(){
            return $this->get_post();
        }

        public function tag_get_post_title(){
            return $this->get_post('post_title');
        }

        public function tag_get_post_excerpt(){
            return $this->get_post('post_excerpt');
        }
        
        public function tag_get_post_content(){
            return $this->get_post('post_content');
        }

        public function tag_get_post_author(){
            return $this->get_post('post_author');
        }

        public function tag_get_post_type(){
            return $this->get_post('post_type');
        }

        public function tag_get_post_status(){
            return $this->get_post('post_status');
        }

        public function tag_get_post_date(){
            return $this->get_post('post_date');
        }

        public function tag_get_post_meta(){
            $return = array();
            $post_id = get_the_ID();

            if( ! empty( $post_id ) ){
                $return = get_post_meta( $post_id );
            }
            
            return $return;
        }

        public function get_post( $single_val = false ){

            $return = false;
            $post = get_post( get_the_ID() );

            if( $single_val && ! empty( $post ) ){

                switch( $single_val ){
                    case 'post_title': 
                        if( isset( $post->post_title ) && ! empty( $post->post_title ) ){
                            $return = $post->post_title;
                        }
                        break;
                    case 'post_excerpt': 
                        if( isset( $post->post_excerpt ) && ! empty( $post->post_excerpt ) ){
                            $return = $post->post_excerpt;
                        }
                        break;
                    case 'post_content': 
                        if( isset( $post->post_content ) && ! empty( $post->post_content ) ){
                            $return = $post->post_content;
                        }
                        break;
                    case 'post_author': 
                        if( isset( $post->post_author ) && ! empty( $post->post_author ) ){
                            $return = $post->post_author;
                        }
                        break;
                    case 'post_type': 
                        if( isset( $post->post_type ) && ! empty( $post->post_type ) ){
                            $return = $post->post_type;
                        }
                        break;
                    case 'post_status': 
                        if( isset( $post->post_status ) && ! empty( $post->post_status ) ){
                            $return = $post->post_status;
                        }
                        break;
                    case 'post_date': 
                        if( isset( $post->post_date ) && ! empty( $post->post_date ) ){
                            $return = $post->post_date;
                        }
                        break;
                }
                
            } else {
                $return = $post;
            }

            return $return;
        }

	}

endif; // End if class_exists check.