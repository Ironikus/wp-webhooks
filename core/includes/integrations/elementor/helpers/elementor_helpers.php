<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_elementor_Helpers_elementor_helpers' ) ) :

	class WP_Webhooks_Integrations_elementor_Helpers_elementor_helpers {

		protected $forms = null;

		public function get_forms(){

			if( $this->forms !== null ){
				return $this->forms;
			}

			$sql = "SELECT pm.meta_value FROM {postmeta} pm JOIN {posts} p on p.ID = pm.post_id WHERE pm.meta_key LIKE '_elementor_data' AND p.post_status = 'publish' AND pm.meta_value LIKE '%form_fields%';";
			$results = WPWHPRO()->sql->run( $sql );
			$forms = array();
			
			if( ! empty( $results ) ){
				foreach( $results as $meta ){
					$elementor_data = json_decode( $meta->meta_value );
					$forms = array_merge( $forms, $this->find_forms( $elementor_data ) );
				}
			}

			return $forms;
		}

		public function find_forms( $elementor_data ){
			$return = array();

			if( ! empty( $elementor_data ) ){
				foreach( $elementor_data as $element ){

					if ( 
						property_exists( $element, 'widgetType' ) 
						&& property_exists( $element, 'elType' ) 
						&& $element->widgetType === 'form' 
						&& $element->elType === 'widget' 
					) {
						$return[ $element->id ] = $element->settings;
					}

					if ( ! empty( $element->elements ) ) {
						$sub_elements = $this->find_forms( $element->elements );
						if ( ! empty( $sub_elements ) ) {
							$return = array_merge( $return, $sub_elements );
						}
					}

				}
			}

			return $return;
		}

    }

endif; // End if class_exists check.