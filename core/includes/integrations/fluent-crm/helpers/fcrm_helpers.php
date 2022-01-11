<?php

use FluentCrm\App\Models\SubscriberPivot;
use FluentCrm\App\Models\Lists;
use FluentCrm\App\Models\Tag;
use FluentCrm\App\Models\Subscriber;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Helpers_fcrm_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Helpers_fcrm_helpers {

        /**
		 * The cached statuses
		 */
        private $cache_get_statuses = false;

        /**
		 * The cached lists
		 */
        private $cache_get_lists = false;

        /**
		 * The cached tags
		 */
        private $cache_get_tags = false;

		/**
         * Validate the list ids against attached list ids 
		 *
		 * @return array A list of the real list ids 
		 */
		public function validate_list_ids( $list_ids ){
		    
            if( class_exists('\FluentCrm\App\Models\SubscriberPivot') ){
                $attached_ids = SubscriberPivot::whereIn( 'id', $list_ids )->get();
                if( is_object( $attached_ids ) && ! $attached_ids->isEmpty() ) {
                    $list_ids = array();
    
                    foreach( $attached_ids as $attached_id ) {
                        $list_ids[] = ( isset( $attached_id->object_id ) ) ? $attached_id->object_id : 0;
                    }
                }
            }

		    return $list_ids;
        }

		/**
         * Validate the tag ids against attached tag ids 
		 *
		 * @return array A list of the real list ids 
		 */
		public function validate_tag_ids( $tag_ids ){  
		    
            if( class_exists('\FluentCrm\App\Models\SubscriberPivot') ){

                $attached_ids = SubscriberPivot::whereIn( 'id', $tag_ids )->get();
                if( is_object( $attached_ids ) && ! $attached_ids->isEmpty() ) {
                    $tag_ids = array();
    
                    foreach( $attached_ids as $attached_id ) {
                        $tag_ids[] = ( isset( $attached_id->object_id ) ) ? $attached_id->object_id : 0;
                    }
                }

            }

		    return $tag_ids;
        }

		/**
         * Get all FluentCRM statuses with labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_statuses(){

            if( $this->cache_get_statuses !== false ){
                return $this->cache_get_statuses;
            }
		    
            $validated_statuses = array();
            if( function_exists( 'fluentcrm_subscriber_statuses' ) ) {
                $statuses = fluentcrm_subscriber_statuses();

                if( ! empty( $statuses ) ) {
                    foreach ( $statuses as $status_slug ) {
                        $validated_statuses[ $status_slug ] = WPWHPRO()->helpers->translate( ucfirst( $status_slug ) );
                    }
                }
            }

            $this->cache_get_statuses = $validated_statuses;

		    return $validated_statuses;
        }

        /**
         * Find a contact by given information
		 *
		 * @return array The Subscriber object
		 */
		public function get_contact( $type, $value, $first = true ){
		    
            $subscriber = null;
            if( class_exists( '\FluentCrm\App\Models\Subscriber' ) ){
                $subscribers = Subscriber::where( $type, $value );
   
                if( is_object( $subscribers ) ) {
                    if( $first ){
                        $subscriber = $subscribers->first();
                    } else {
                        $subscriber = $subscribers->get();
                    }
                    
                }
            }

		    return $subscriber;
        }

		/**
         * Get all FluentCRM lists with its labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_lists(){

            if( $this->cache_get_lists !== false ){
                return $this->cache_get_lists;
            }

            $validated_lists = array();
            if( class_exists( '\FluentCrm\App\Models\Lists' ) ){
                $lists = Lists::orderBy( 'title', 'DESC' )->get();

                   

                if( ! empty( $lists ) ) {
                    foreach( $lists as $list ) {
                        $validated_lists[ $list->id ] = esc_html( $list->title );
                    }
                }
            }

            $this->cache_get_lists = $validated_lists;

		    return $validated_lists;
        }

		/**
         * Get all FluentCRM tags with its labels
		 *
		 * @return array A list of the real list ids 
		 */
		public function get_tags(){

            if( $this->cache_get_tags !== false ){
                return $this->cache_get_tags;
            }

            $validated_tags = array();
            if( class_exists( '\FluentCrm\App\Models\Tag' ) ){
                $tags = Tag::orderBy( 'title', 'DESC' )->get();

                   

                if( ! empty( $tags ) ) {
                    foreach( $tags as $tag ) {
                        $validated_tags[ $tag->id ] = esc_html( $tag->title );
                    }
                }
            }

            $this->cache_get_tags = $validated_tags;

		    return $validated_tags;
        }

	}

endif; // End if class_exists check.