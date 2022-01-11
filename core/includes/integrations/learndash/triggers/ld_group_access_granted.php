<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_group_access_granted' ) ) :

 /**
  * Load the ld_group_access_granted trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_group_access_granted {

	public function get_details(){

		$translation_ident = "action-ld_group_access_granted-description";
		$validated_groups = array();

		if( defined( 'LEARNDASH_VERSION' ) ){
			$ld_helpers = WPWHPRO()->integrations->get_helper( 'learndash', 'ld_helpers' );
		
			$validated_groups = $ld_helpers->get_groups();
		}
		

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that got access to the group.', $translation_ident ) ),
			'group_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the group the user got access to.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the assigned user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Group access granted',
			'webhook_slug' => 'ld_group_access_granted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'ld_added_group_access',
					'url' => 'https://developers.learndash.com/hook/ld_added_group_access/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific groups only. To do that, select one or multiple groups within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_learndash_trigger_on_groups' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_groups',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_groups,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected groups', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the groups you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'ld_group_access_granted',
			'name'			  => WPWHPRO()->helpers->translate( 'Group access granted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a group access was granted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a group access was granted for a user within LearnDash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);

	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 94,
			'group_id' => 353,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '94',
				'user_login' => 'jondoe',
				'user_pass' => '$P$Bh2I8hUTsh0UvuUBNeXXXXXXXXs.',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jon@doe.test',
				'user_url' => '',
				'user_registered' => '2019-09-06 16:08:27',
				'user_activation_key' => '1567786108:$P$Bz.CL7BJuyXXXXXXXXXXLNdFfG0',
				'user_status' => '0',
				'display_name' => '',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 94,
			  'caps' => 
			  array (
				'subscriber' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				0 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				'read' => true,
				'level_0' => true,
				'read_private_locations' => true,
				'read_private_events' => true,
				'subscriber' => true,
			  ),
			  'filter' => NULL,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.