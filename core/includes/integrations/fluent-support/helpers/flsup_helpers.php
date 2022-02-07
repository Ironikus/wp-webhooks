<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_support_Helpers_flsup_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 4.3.4
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_support_Helpers_flsup_helpers {

        public function get_person_types(){

            $types = array(
                'agent' => WPWHPRO()->helpers->translate( 'Agent', 'helpers-flsup_helpers-get_person_types' ),
                'customer' => WPWHPRO()->helpers->translate( 'Customer', 'helpers-flsup_helpers-get_person_types' ),
            );

            $types = apply_filters( 'wpwhpro/webhooks/fluent_support/helpers/flsup_helpers', $types );

            return $types;

        }

	}

endif; // End if class_exists check.