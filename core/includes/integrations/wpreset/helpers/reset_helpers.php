<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wpreset_Helpers_reset_helpers' ) ) :

	class WP_Webhooks_Integrations_wpreset_Helpers_reset_helpers {

		private $wpwhpro_wp_reset = false;

		public function get_wp_reset(){

		    if( $this->wpwhpro_wp_reset ){
		        return $this->wpwhpro_wp_reset;
            }

            global $wp_reset;

		    if( $wp_reset ){
				$this->wpwhpro_wp_reset = $wp_reset;
			} else {
				$this->wpwhpro_wp_reset = WP_Reset::getInstance(); //Initialize it by ourselves if it it not set on the frontend
			}

            return $this->wpwhpro_wp_reset;

        }

		/**
         * Return false to deactivate the redirection that gets initialized from WP reset
         *
		 * @param $location
		 * @param $status
		 *
		 * @return bool
		 */
        public function wpwhpro_remove_redirect_filter( $location, $status ){

            return false;

        }

		/**
         * Temporarily activate CLI to make it possible to call certain function from outside
         *
		 * @param $val
		 *
		 * @return bool
		 */
        public function activate_cli_for_wp_reset( $val ){

		    return true;

        }

    }

endif; // End if class_exists check.