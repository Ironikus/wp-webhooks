<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_fusion_Triggers_wpfs_tag_removed' ) ) :

 /**
  * Load the wpfs_tag_removed trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_fusion_Triggers_wpfs_tag_removed {

	public function get_details(){

		$translation_ident = "action-wpfs_tag_removed-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that was updated.', $translation_ident ) ),
			'tag' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The tag that was removed from the user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Tag removed',
			'webhook_slug' => 'wpfs_tag_removed',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpf_tags_removed',
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
			'trigger'		   => 'wpfs_tag_removed',
			'name'			  => WPWHPRO()->helpers->translate( 'Tag removed', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a tag was removed', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a tag was removed within WP Fusion.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-fusion',
			'premium'		   => true,
		);

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