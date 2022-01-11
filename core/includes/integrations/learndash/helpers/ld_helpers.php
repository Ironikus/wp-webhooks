<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Helpers_ld_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_learndash_Helpers_ld_helpers {

		/**
		 * The cached courses
		 */
		private $cache_get_courses = false;

		/**
		 * The cached lessons
		 */
		private $cache_get_lessons = false;

		/**
		 * The cached groups
		 */
		private $cache_get_groups = false;

		/**
		 * Get all LearnDash courses with labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_courses(){

			if( $this->cache_get_courses !== false ){
				return $this->cache_get_courses;
			}
			
			$validated_courses = array();
			if( class_exists( 'WP_Query' ) ){

				$courses = new WP_Query( array(
					'post_type' => 'sfwd-courses',
					'numberposts' => 9999,
					'post_status' => 'publish',
				) );

				if( ! empty( $courses ) && isset( $courses->posts ) ){
					foreach( $courses->posts as $course ){
						$validated_courses[ $course->ID ] = WPWHPRO()->helpers->translate( $course->post_title, 'learndash-ld_helpers' );
					}
				}

			}

			$this->cache_get_courses = $validated_courses;

			return $validated_courses;
		}

		/**
		 * Get all LearnDash lessons with labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_lessons(){

			if( $this->cache_get_lessons !== false ){
				return $this->cache_get_lessons;
			}
			
			$validated_lessons = array();
			if( class_exists( 'WP_Query' ) ){

				$lessons = new WP_Query( array(
					'post_type' => 'sfwd-lessons',
					'numberposts' => 9999,
					'post_status' => 'publish',
				) );

				if( ! empty( $lessons ) && isset( $lessons->posts ) ){
					foreach( $lessons->posts as $lesson ){
						$validated_lessons[ $lesson->ID ] = WPWHPRO()->helpers->translate( $lesson->post_title, 'learndash-ld_helpers' );
					}
				}

			}

			$this->cache_get_lessons = $validated_lessons;

			return $validated_lessons;
		}

		/**
		 * Get all LearnDash groups with labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_groups(){

			if( $this->cache_get_groups !== false ){
				return $this->cache_get_groups;
			}
			
			$validated_groups = array();
			if( class_exists( 'WP_Query' ) ){

				$groups = new WP_Query( array(
					'post_type' => 'groups',
					'numberposts' => 9999,
					'post_status' => 'publish',
				) );

				if( ! empty( $groups ) && isset( $groups->posts ) ){
					foreach( $groups->posts as $group ){
						$validated_groups[ $group->ID ] = WPWHPRO()->helpers->translate( $group->post_title, 'learndash-ld_helpers' );
					}
				}

			}

			$this->cache_get_groups = $validated_groups;

			return $validated_groups;
		}

		/**
		 * Complete learndash courses
		 *
		 * @return array A list of the real list ids 
		 */
		public function complete_courses( $user_id, $course_ids ){

			$response = array(
				'success' => false,
				'courses' => array(),
			);

			foreach( $course_ids as $course_id ){

				$course_id = intval( $course_id );

				//Complete lessons first
				$completed_lessons = $this->complete_lessons( $user_id, $course_id );

				$completed = learndash_process_mark_complete( $user_id, $course_id );

				if( $completed ){
					$response['success'] = true;
				}

				$response['courses'][ $course_id ] = array(
					'user_id' => $user_id,
					'course_id' => $course_id,
					'response' => $completed,
					'completed_lessons' => $completed_lessons,
				);

			}

			return $response;
		}

		/**
		 * Complete learndash lessons
		 *
		 * @return array A list of the real list ids 
		 */
		public function complete_lessons( $user_id, $course_id, $lessons_in = 'all' ){

			$response = array(
				'success' => false,
				'lessons' => array(),
			);
			$course_id = intval( $course_id );

			if( is_string( $lessons_in ) && $lessons_in === 'all' ){
				$lessons = learndash_get_lesson_list( $course_id, array( 
					'num' => 0 
				) );
			} else {
				$lessons = array();

				foreach( $lessons_in as $lesson ){
					if( is_numeric( $lesson ) ){
						$lessons[ $lesson ] = get_post( $lesson );
					}
				}
			}

			foreach( $lessons as $lesson ){

				//Complete topics first
				$completed_topics = $this->complete_topics( $user_id, $course_id, $lesson->ID );

				$completed = learndash_process_mark_complete( $user_id, $lesson->ID, false, $course_id );

				if( $completed ){
					$response['success'] = true;
				}

				$response['lessons'][ $lesson->ID ] = array(
					'user_id' => $user_id,
					'course_id' => $course_id,
					'lesson_id' => $lesson->ID,
					'response' => $completed,
					'completed_topics' => $completed_topics,
				);

			}

			return $response;
		}

		/**
		 * Complete learndash topics
		 *
		 * @return array A list of the real list ids 
		 */
		public function complete_topics( $user_id, $course_id, $lesson_id, $topics_in = 'all' ){

			$response = array(
				'success' => false,
				'topics' => array(),
			);
			$course_id = intval( $course_id );

			$topics = array();

			if( is_string( $topics_in ) && $topics_in === 'all' && ! empty( $lesson_id ) ){
				$topics = learndash_get_topic_list( $lesson_id, $course_id );
			} elseif( is_array( $topics_in ) ) {
				$topics = array();

				foreach( $topics_in as $topic ){
					if( is_numeric( $topic ) ){
						$topics[ $topic ] = get_post( $topic );
					}
				}
			}

			foreach( $topics as $topic ){

				$completed = learndash_process_mark_complete( $user_id, $topic->ID, false, $course_id );

				if( $completed ){
					$response['success'] = true;
				}

				$response['topics'][ $topic->ID ] = array(
					'user_id' => $user_id,
					'course_id' => $course_id,
					'lesson_id' => $lesson_id,
					'topic_id' => $topic->ID,
					'response' => $completed,
				);

			}

			return $response;
		}

		/**
		 * Incomplete learndash lessons
		 *
		 * @return array A list of the real list ids 
		 */
		public function incomplete_lessons( $user_id, $course_id, $lessons_in = 'all' ){

			$response = array(
				'success' => false,
				'lessons' => array(),
			);
			$course_id = intval( $course_id );

			if( is_string( $lessons_in ) && $lessons_in === 'all' ){
				$lessons = learndash_get_lesson_list( $course_id, array( 
					'num' => 0 
				) );
			} else {
				$lessons = array();

				foreach( $lessons_in as $lesson ){
					if( is_numeric( $lesson ) ){
						$lessons[ $lesson ] = get_post( $lesson );
					}
				}
			}

			foreach( $lessons as $lesson ){

				//Complete topics first
				$incompleted_topics = $this->incomplete_topics( $user_id, $course_id, $lesson->ID );

				$deleted_quiz_progress = array();
				$lesson_quiz_list = learndash_get_lesson_quiz_list( $lesson->ID, $user_id, $course_id );
				if( $lesson_quiz_list ){
					foreach( $lesson_quiz_list as $single_quiz ){

						if( is_array( $single_quiz['id'] ) ){
							$deleted_quiz_progress[ $single_quiz['id'] ] = learndash_delete_quiz_progress( $user_id, $single_quiz['id'] );
						}
						
					}
				}

				$incompleted = learndash_process_mark_incomplete( $user_id, $course_id, $lesson->ID, false );

				if( $incompleted ){
					$response['success'] = true;
				}

				$response['lessons'][ $lesson->ID ] = array(
					'user_id' => $user_id,
					'course_id' => $course_id,
					'lesson_id' => $lesson->ID,
					'response' => $incompleted,
					'incompleted_topics' => $incompleted_topics,
					'deleted_quiz_progress' => $deleted_quiz_progress,
				);

			}

			return $response;
		}

		/**
		 * Incomplete learndash topics
		 *
		 * @return array A list of the real list ids 
		 */
		public function incomplete_topics( $user_id, $course_id, $lesson_id, $topics_in = 'all' ){

			$response = array(
				'success' => false,
				'topics' => array(),
			);
			$course_id = intval( $course_id );

			$topics = array();

			if( is_string( $topics_in ) && $topics_in === 'all' && ! empty( $lesson_id ) ){
				$topics = learndash_get_topic_list( $lesson_id, $course_id );
			} elseif( is_array( $topics_in ) ) {
				$topics = array();

				foreach( $topics_in as $topic ){
					if( is_numeric( $topic ) ){
						$topics[ $topic ] = get_post( $topic );
					}
				}
			}

			foreach( $topics as $topic ){

				$deleted_quiz_progress = array();
				$topic_quiz_list = learndash_get_lesson_quiz_list( $topic->ID, $user_id, $course_id );
				if( $topic_quiz_list ){
					foreach( $topic_quiz_list as $single_quiz ){

						if( is_array( $single_quiz['post'] ) && isset( $single_quiz['post']->ID ) ){
							$deleted_quiz_progress[ $single_quiz['post']->ID ] = learndash_delete_quiz_progress( $user_id, $single_quiz['post']->ID );
						}
						
					}
				}

				$completed = learndash_process_mark_incomplete( $user_id, $course_id, $topic->ID, false );

				if( $completed ){
					$response['success'] = true;
				}

				$response['topics'][ $topic->ID ] = array(
					'user_id' => $user_id,
					'course_id' => $course_id,
					'lesson_id' => $lesson_id,
					'topic_id' => $topic->ID,
					'response' => $completed,
					'deleted_quiz_progress' => $deleted_quiz_progress,
				);

			}

			return $response;
		}

	}

endif; // End if class_exists check.