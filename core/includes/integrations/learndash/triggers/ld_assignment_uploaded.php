<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_assignment_uploaded' ) ) :

 /**
  * Load the ld_assignment_uploaded trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_assignment_uploaded {

	public function get_details(){

		$translation_ident = "action-ld_assignment_uploaded-description";
		$validated_courses = array();
		$validated_lessons = array();

		if( defined( 'LEARNDASH_VERSION' ) ){
			$ld_helpers = WPWHPRO()->integrations->get_helper( 'learndash', 'ld_helpers' );
		
			$validated_courses = $ld_helpers->get_courses();
			$validated_lessons = $ld_helpers->get_lessons();
		}
		

		$parameter = array(
			'assignment_post_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the uploaded assignment.', $translation_ident ) ),
			'file_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The name of the uploaded file including the file extension.', $translation_ident ) ),
			'file_link' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The URL of the uploaded assignment file.', $translation_ident ) ),
			'user_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The name of the user that uploaded the assignment.', $translation_ident ) ),
			'disp_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The display name of the user that uploaded the assignment.', $translation_ident ) ),
			'file_path' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full file path of the uploaded assignment.', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that uploaded the assignment.', $translation_ident ) ),
			'course_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the course the assignment was uploaded to.', $translation_ident ) ),
			'lesson_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the lesson the assignment was uploaded to.', $translation_ident ) ),
			'lesson_title' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The title of the lesson the assignment was uploaded to.', $translation_ident ) ),
			'lesson_type' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The post type of the lesson the assignment was uploaded to.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Assignment uploaded',
			'webhook_slug' => 'ld_assignment_uploaded',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'learndash_assignment_uploaded',
					'url' => 'https://developers.learndash.com/hook/learndash_assignment_uploaded/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific courses only. To do that, select one or multiple courses within the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also possible to fire this trigger only on specific lessons. To do that, select one or multiple lessons within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_learndash_trigger_on_courses' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_courses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_courses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected courses', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the courses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
				'wpwhpro_learndash_trigger_on_lessons' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_lessons',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_lessons,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected lessons', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the lessons you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'ld_assignment_uploaded',
			'name'			  => WPWHPRO()->helpers->translate( 'Assignment uploaded', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'an assignment was uploaded', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an assignment was uploaded within LearnDash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);

	}

	public function get_demo( $options = array() ) {

		$data = array (
			'assignment_post_id' => 8077,
			'file_name' => 'assignment_8055_163986028318_demo_pdf_file.pdf',
			'file_link' => 'https://doe.test/wp-content/uploads/assignments/assignment_8055_163986028318_demo_pdf_file.pdf',
			'user_name' => 'admin',
			'disp_name' => 'admin',
			'file_path' => '%2Fthe%2Ftull%2Ffile%2Fpath%2Fwp-content%2Fuploads%2Fassignments%assignment_8055_163986028318_demo_pdf_file.pdf',
			'user_id' => 1,
			'lesson_id' => 8055,
			'course_id' => 8053,
			'lesson_title' => 'Demo Lesson 1',
			'lesson_type' => 'sfwd-lessons',
		  );

		return $data;
	}

  }

endif; // End if class_exists check.