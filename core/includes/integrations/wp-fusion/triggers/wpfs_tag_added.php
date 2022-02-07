<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fusion_Triggers_wpfs_tag_added' ) ) :

 /**
  * Load the wpfs_tag_added trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_fusion_Triggers_wpfs_tag_added {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpf_tags_applied',
				'callback' => array( $this, 'wpfs_tag_added_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpfs_tag_added-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that was updated.', $translation_ident ) ),
			'tag' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The tag that was added to the user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Tag added',
			'webhook_slug' => 'wpfs_tag_added',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpf_tags_applied',
					'url' => 'https://wpfusion.com/documentation/actions/wpf_tags_modified/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific tags only. To do that, simply specify the tag id(s) within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_wp_fusion_trigger_on_selected_tags' => array(
					'id'		  => 'wpwhpro_wp_fusion_trigger_on_selected_tags',
					'type'		=> 'text',
					'multiple'	=> true,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected tags', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Trigger this webhook only on specific tags. You can also choose multiple ones by comma-separating them. If none are set, all are triggered. This argument accepts a comma-separeted list of tag ids.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wpfs_tag_added',
			'name'			  => WPWHPRO()->helpers->translate( 'Tag added', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a tag was added', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a tag was added within WP Fusion.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-fusion',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers after tags are loaded for the user, contains just the new tags that were applied
	 *
	 * @param int   $user_id      ID of the user that was updated
	 * @param array $tags_applied Tags that were applied to the user
	 */
	public function wpfs_tag_added_callback( $user_id, $tags_applied ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpfs_tag_added' );

		$response_data_array = array();

		foreach( $tags_applied as $tag ){

			$tag = intval( $tag );

			$payload = array(
				'user_id' => $user_id,
				'tag' => $tag,
			);

			if( ! isset( $response_data_array[ $tag ] ) ){
				$response_data_array[ $tag ] = array();
			}

			foreach( $webhooks as $webhook ){

				$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
				$is_valid = true;
	
				if( isset( $webhook['settings'] ) ){
	
					if( isset( $webhook['settings']['wpwhpro_wp_fusion_trigger_on_selected_tags'] ) && ! empty( $webhook['settings']['wpwhpro_wp_fusion_trigger_on_selected_tags'] ) ){
						$is_valid = false;

						$filter_tags = explode( ',', $webhook['settings']['wpwhpro_wp_fusion_trigger_on_selected_tags'] );

						if( ! empty( $filter_tags ) ){
							foreach( $filter_tags as $stag ){
								$stag = intval( trim( $stag ) );
								if( $stag === $tag ){
									$is_valid = true;
								}
							}
						}
					}
	
				}
	
				if( $is_valid ){
					if( $webhook_url_name !== null ){
						$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					} else {
						$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					}
				}
	
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wpfs_tag_added', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 155,
			'tag' => '4',
		);

		return $data;
	}

  }

endif; // End if class_exists check.