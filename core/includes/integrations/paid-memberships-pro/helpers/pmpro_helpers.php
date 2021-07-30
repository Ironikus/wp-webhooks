<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Helpers_pmpro_helpers' ) ) :

	class WP_Webhooks_Integrations_paid_memberships_pro_Helpers_pmpro_helpers {

		public function get_membership_levels( $include_hidden = false, $use_cache = true, $force = false ) {
	
            $levels = array();
            if( function_exists( 'pmpro_getAllLevels' ) ){
                $levels = pmpro_getAllLevels( $include_hidden, $use_cache, $force );
            }
			
            return $levels;
		}

    }

endif; // End if class_exists check.