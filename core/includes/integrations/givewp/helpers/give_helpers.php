<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Helpers_give_helpers' ) ) :

	/**
	 * Load the Give helpers
	 *
	 * @since 4.3.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_givewp_Helpers_give_helpers {

        public function get_payment_forms(){

            $forms = get_posts( array(
                'post_type'      => 'give_forms',
                'post_status'    => 'publish',
                'posts_per_page' => 9999,
            ) );

            $validated_forms = array();
            if( ! empty( $forms ) ){
                foreach( $forms as $form ){
                    $validated_forms[ $form->ID ] = $form->post_title;
                }
            }

            return $validated_forms;

        }

	}

endif; // End if class_exists check.