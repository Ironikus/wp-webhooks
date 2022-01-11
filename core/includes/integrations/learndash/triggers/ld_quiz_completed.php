<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_quiz_completed' ) ) :

 /**
  * Load the ld_quiz_completed trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_quiz_completed {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'learndash_quiz_completed',
				'callback' => array( $this, 'learndash_quiz_completed_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-ld_quiz_completed-description";
		$validated_courses = array();

		if( defined( 'LEARNDASH_VERSION' ) ){
			$ld_helpers = WPWHPRO()->integrations->get_helper( 'learndash', 'ld_helpers' );
		
			$validated_courses = $ld_helpers->get_courses();
		}
		

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that completed the course.', $translation_ident ) ),
			'course_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the completed course.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the user.', $translation_ident ) ),
			'course' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the course.', $translation_ident ) ),
			'lesson' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the lesson.', $translation_ident ) ),
			'quiz' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the quiz.', $translation_ident ) ),
			'score' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The score of the quiz.', $translation_ident ) ),
			'count' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The qzuiz count.', $translation_ident ) ),
			'question_show_count' => array( 'short_description' => WPWHPRO()->helpers->translate( '(learndash_quiz_completed) A count of how often the question has been shown.', $translation_ident ) ),
			'pass' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) Whether the quiz was passed or not.', $translation_ident ) ),
			'rank' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) If a reank was given, it is set here.', $translation_ident ) ),
			'time' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) A timestamp of the time the quiz was completed.', $translation_ident ) ),
			'pro_quizid' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The pro quiz ID.', $translation_ident ) ),
			'topic' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) A topic ID in case given.', $translation_ident ) ),
			'points' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The number of points achieven by the quiz.', $translation_ident ) ),
			'total_points' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The total points achievable within this quiz.', $translation_ident ) ),
			'percentage' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The percentage of achieved points.', $translation_ident ) ),
			'timespent' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Float) The amount of time spent on the quiz.', $translation_ident ) ),
			'has_graded' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) Whether the user was graded or not.', $translation_ident ) ),
			'statistic_ref_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The static reference id.', $translation_ident ) ),
			'started' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) A timestamp of the time the quiz was started.', $translation_ident ) ),
			'completed' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) A timestamp of the time the quiz was completed.', $translation_ident ) ),
			'ld_version' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The current version of Learndash.', $translation_ident ) ),
			'quiz_key' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The unique key for this quiz.', $translation_ident ) ),
			'questions' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array)An array containing the IDs of the related questions.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Quiz completed',
			'webhook_slug' => 'ld_quiz_completed',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'learndash_quiz_completed',
					'url' => 'https://developers.learndash.com/hook/learndash_quiz_completed/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific courses only. To do that, select one or multiple courses wihtin the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also possible to fire the quiz only once it was passed. To do that, simply check the setting within the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'By default, LearnDash only triggers a Quiz as completed if the user passed. You can adjust that by defining <code>define( \'LEARNDASH_QUIZ_ESSAY_SUBMIT_COMPLETED\', true )</code> within your wp-config.php file.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also possible to fire the trigger only when a minimum or maximum score or percentage was met. Further information is available within teh webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_learndash_trigger_on_pass' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_pass',
					'type'		=> 'checkbox',
					'multiple'	=> true,
 					'choices'	  => $validated_courses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on passed quizzes only', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Check this if you want to fire the trigger only when the quizz was passed.', $translation_ident )
				),
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
				'wpwhpro_learndash_trigger_on_percentage_min' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_percentage_min',
					'type'		=> 'text',
					'multiple'	=> true,
 					'choices'	  => $validated_courses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on minimum percentage', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set a minimum percentage that should be achieved to trigger this webhook URL. E.g.: 75. This will trigger the webhook if the percentage is equal or higher.', $translation_ident )
				),
				'wpwhpro_learndash_trigger_on_percentage_max' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_percentage_max',
					'type'		=> 'text',
					'multiple'	=> true,
 					'choices'	  => $validated_courses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on maximum percentage', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set a maximum percentage that should be achieved to trigger this webhook URL. E.g.: 30. This will trigger the webhook if the percentage is equal or lower.', $translation_ident )
				),
				'wpwhpro_learndash_trigger_on_score_min' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_score_min',
					'type'		=> 'text',
					'multiple'	=> true,
 					'choices'	  => $validated_courses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on minimum score', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set a minimum score that should be achieved to trigger this webhook URL. E.g.: 75. This will trigger the webhook if the score is equal or higher.', $translation_ident )
				),
				'wpwhpro_learndash_trigger_on_score_max' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_score_max',
					'type'		=> 'text',
					'multiple'	=> true,
 					'choices'	  => $validated_courses,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on maximum score', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set a maximum score that should be achieved to trigger this webhook URL. E.g.: 30. This will trigger the webhook if the score is equal or lower.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'ld_quiz_completed',
			'name'			  => WPWHPRO()->helpers->translate( 'Quiz completed', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a quiz was completed', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a quiz was completed within LearnDash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a lesson was completed within LearnDash
	 *
	 * @param array $data Further information about the lesson, course, and the user
	 */
	public function learndash_quiz_completed_callback( $data, $user ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ld_quiz_completed' );

		$payload = array(
			'user_id' => ( ! empty( $user ) && is_object( $user ) ) ? $user->ID : 0,
			'course_id' => ( isset( $data['course'] ) && is_object( $data['course'] ) ) ? $data['course']->ID : 0,
			'lesson_id' => ( isset( $data['lesson'] ) && is_object( $data['lesson'] ) ) ? $data['lesson']->ID : 0,
			'user' => $user,
		);
		$payload = array_merge( $payload, $data );

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
					if( ! in_array( $payload['course_id'], $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_percentage_min'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_percentage_min'] ) ){
					$quiz_percentage = isset( $data['percentage'] ) ? $data['percentage'] : 0;
					if( intval( $webhook['settings']['wpwhpro_learndash_trigger_on_percentage_min'] ) > $quiz_percentage ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_percentage_max'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_percentage_max'] ) ){
					$quiz_percentage = isset( $data['percentage'] ) ? $data['percentage'] : 0;
					if( intval( $webhook['settings']['wpwhpro_learndash_trigger_on_percentage_max'] ) < $quiz_percentage ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_score_min'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_score_min'] ) ){
					$quiz_score = isset( $data['score'] ) ? $data['score'] : 0;
					if( intval( $webhook['settings']['wpwhpro_learndash_trigger_on_score_min'] ) > $quiz_score ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_score_max'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_score_max'] ) ){
					$quiz_score = isset( $data['score'] ) ? $data['score'] : 0;
					if( intval( $webhook['settings']['wpwhpro_learndash_trigger_on_score_max'] ) < $quiz_score ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_pass'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_pass'] ) ){
					$is_valid = false;
					
					if( isset( $data['pass'] ) && intval( $data['pass'] ) !== 0 ){
						$is_valid = true;
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

		do_action( 'wpwhpro/webhooks/trigger_ld_quiz_completed', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 1,
			'course_id' => 8053,
			'lesson_id' => 8055,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '1',
				'user_login' => 'admin',
				'user_pass' => '$P$B4B1t8fCUMzXXXXXXX7EbzY1',
				'user_nicename' => 'jondoe',
				'user_email' => 'jon@doe.test',
				'user_url' => '',
				'user_registered' => '2017-07-27 23:58:11',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'jondoe',
				'spam' => '0',
				'deleted' => '0',
				'membership_level' => 
				array (
				  'ID' => '1',
				  'id' => '1',
				  'subscription_id' => '6',
				  'name' => 'First Level',
				  'description' => 'This is a demo level',
				  'confirmation' => '',
				  'expiration_number' => '0',
				  'expiration_period' => '',
				  'allow_signups' => '1',
				  'initial_payment' => 0,
				  'billing_amount' => 0,
				  'cycle_number' => '0',
				  'cycle_period' => '',
				  'billing_limit' => '0',
				  'trial_amount' => 0,
				  'trial_limit' => '0',
				  'code_id' => '0',
				  'startdate' => '1626948898',
				  'enddate' => NULL,
				  'categories' => 
				  array (
				  ),
				),
				'membership_levels' => 
				array (
				  0 => 
				  array (
					'ID' => '1',
					'id' => '1',
					'subscription_id' => '6',
					'name' => 'First Level',
					'description' => 'This is a demo level',
					'confirmation' => '',
					'expiration_number' => '0',
					'expiration_period' => '',
					'initial_payment' => 0,
					'billing_amount' => 0,
					'cycle_number' => '0',
					'cycle_period' => '',
					'billing_limit' => '0',
					'trial_amount' => 0,
					'trial_limit' => '0',
					'code_id' => '0',
					'startdate' => '1626948898',
					'enddate' => NULL,
				  ),
				),
			  ),
			  'ID' => 1,
			  'caps' => 
			  array (
				'read' => true,
			  ),
			  'filter' => NULL,
			),
			'course' => 
			array (
			  'ID' => 8053,
			  'post_author' => '1',
			  'post_date' => '2021-12-17 18:03:08',
			  'post_date_gmt' => '2021-12-17 18:03:08',
			  'post_content' => '<!-- wp:paragraph -->
		  <p>Some course content</p>
		  <!-- /wp:paragraph -->',
			  'post_title' => 'Another Course',
			  'post_excerpt' => '',
			  'post_status' => 'publish',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_password' => '',
			  'post_name' => 'another-course',
			  'to_ping' => '',
			  'pinged' => '',
			  'post_modified' => '2021-12-17 18:06:07',
			  'post_modified_gmt' => '2021-12-17 18:06:07',
			  'post_content_filtered' => '',
			  'post_parent' => 0,
			  'guid' => 'https://doe.test/?post_type=sfwd-courses&#038;p=8053',
			  'menu_order' => 0,
			  'post_type' => 'sfwd-courses',
			  'post_mime_type' => '',
			  'comment_count' => '0',
			  'filter' => 'raw',
			),
			'quiz' => 895,
			'score' => 3,
			'count' => 3,
			'question_show_count' => 3,
			'pass' => 1,
			'rank' => '-',
			'time' => 1639825740,
			'pro_quizid' => 1,
			'course' => 
			array (
				'ID' => 354,
				'post_author' => '1',
				'post_date' => '2019-07-30 12:48:37',
				'post_date_gmt' => '2019-07-30 12:48:37',
				'post_content' => '',
				'post_title' => 'Demo Course',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'open',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => 'demo-course',
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2019-07-30 12:48:38',
				'post_modified_gmt' => '2019-07-30 12:48:38',
				'post_content_filtered' => '',
				'post_parent' => 0,
				'guid' => 'https://zipfme.dev/?post_type=sfwd-courses&#038;p=354',
				'menu_order' => 0,
				'post_type' => 'sfwd-courses',
				'post_mime_type' => '',
				'comment_count' => '0',
				'filter' => 'raw',
			),
			'lesson' => 0,
			'topic' => 0,
			'points' => 3,
			'total_points' => 3,
			'percentage' => 100,
			'timespent' => 8.594,
			'has_graded' => false,
			'statistic_ref_id' => 18,
			'started' => 1639825731,
			'completed' => 1639825740,
			'ld_version' => '3.6.0.2',
			'quiz_key' => '1639825740_1_895_354',
			'questions' => 
			array (
				896 => 
				array (
				),
				901 => 
				array (
				),
				903 => 
				array (
				),
			),
		  );

		return $data;
	}

  }

endif; // End if class_exists check.