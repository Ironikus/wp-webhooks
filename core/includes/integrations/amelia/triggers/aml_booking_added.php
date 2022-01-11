<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_amelia_Triggers_aml_booking_added' ) ) :

 /**
  * Load the aml_booking_added trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_amelia_Triggers_aml_booking_added {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'AmeliaBookingAdded',
				'callback' => array( $this, 'aml_booking_added_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-aml_booking_added-description";
		$validated_types = array();

		if( defined( 'AMELIA_VERSION' ) ){
			$aml_helpers = WPWHPRO()->integrations->get_helper( 'amelia', 'aml_helpers' );
		
			$validated_types = $aml_helpers->get_types();
		}
		

		$parameter = array(
			'appointment' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the related appointment.', $translation_ident ) ),
			'bookings' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the added booking.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Booking added',
			'webhook_slug' => 'aml_booking_added',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'AmeliaBookingAdded',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific types only. To do that, select one or multiple types within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_amelia_trigger_on_type' => array(
					'id'		  => 'wpwhpro_amelia_trigger_on_type',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => $validated_types,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on specific type', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'aml_booking_added',
			'name'			  => WPWHPRO()->helpers->translate( 'Booking added', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a booking was added', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a booking was added within Amelia.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'amelia',
			'premium'		   => false,
		);

	}

	/**
	 * Triggers once a booking was added
	 *
	 * @param int $user_id  User ID.
	 * @param int $group_id Group ID.
	 */
	public function aml_booking_added_callback( $reservation, $bookings, $container ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'aml_booking_added' );
		$aml_helpers = WPWHPRO()->integrations->get_helper( 'amelia', 'aml_helpers' );
		$payload = $aml_helpers->process_webhook_data( $reservation, $bookings, $container );

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_amelia_trigger_on_type'] ) && ! empty( $webhook['settings']['wpwhpro_amelia_trigger_on_type'] ) ){
					$is_valid = false;
					
					if( isset( $reservation['type'] ) && in_array( $reservation['type'], $webhook['settings']['wpwhpro_amelia_trigger_on_type'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_aml_booking_added', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'appointment' => 
			array (
			  'id' => 3,
			  'bookings' => 
			  array (
				0 => 
				array (
				  'id' => 3,
				  'customerId' => 2,
				  'customer' => 
				  array (
					'id' => 2,
					'firstName' => 'Jon',
					'lastName' => 'Doe',
					'birthday' => NULL,
					'email' => 'jondoecustomer@test.test',
					'phone' => NULL,
					'type' => 'customer',
					'status' => 'visible',
					'note' => NULL,
					'zoomUserId' => NULL,
					'countryPhoneIso' => NULL,
					'externalId' => NULL,
					'pictureFullPath' => NULL,
					'pictureThumbPath' => NULL,
					'gender' => NULL,
				  ),
				  'status' => 'approved',
				  'extras' => 
				  array (
				  ),
				  'couponId' => NULL,
				  'price' => 20,
				  'coupon' => NULL,
				  'customFields' => 
				  array (
				  ),
				  'info' => NULL,
				  'appointmentId' => 3,
				  'persons' => 1,
				  'token' => '527ed24841',
				  'payments' => 
				  array (
					0 => 
					array (
					  'id' => 3,
					  'customerBookingId' => 3,
					  'packageCustomerId' => NULL,
					  'parentId' => NULL,
					  'amount' => 0,
					  'gateway' => 'onSite',
					  'gatewayTitle' => '',
					  'dateTime' => '2021-12-30 14:00:00',
					  'status' => 'pending',
					  'data' => '',
					  'entity' => 'appointment',
					  'created' => NULL,
					  'actionsCompleted' => true,
					),
				  ),
				  'utcOffset' => NULL,
				  'aggregatedPrice' => true,
				  'isChangedStatus' => true,
				  'packageCustomerService' => NULL,
				  'cancelUrl' => 'https://yourdomain.test/wp-admin/admin-ajax.php?action=wpamelia_api&call=/bookings/cancel/3&token=527ed24841&type=appointment',
				  'customerPanelUrl' => '',
				  'infoArray' => NULL,
				),
			  ),
			  'notifyParticipants' => 1,
			  'internalNotes' => '',
			  'status' => 'approved',
			  'serviceId' => 1,
			  'parentId' => NULL,
			  'providerId' => 1,
			  'locationId' => NULL,
			  'provider' => 
			  array (
				'id' => 1,
				'firstName' => 'Jon',
				'lastName' => 'Doe',
				'birthday' => NULL,
				'email' => 'jondoe@test.test',
				'phone' => '',
				'type' => 'provider',
				'status' => 'visible',
				'note' => NULL,
				'zoomUserId' => NULL,
				'countryPhoneIso' => NULL,
				'externalId' => NULL,
				'pictureFullPath' => NULL,
				'pictureThumbPath' => NULL,
				'weekDayList' => 
				array (
				  0 => 
				  array (
					'id' => 1,
					'dayIndex' => 1,
					'startTime' => '09:00:00',
					'endTime' => '17:00:00',
					'timeOutList' => 
					array (
					),
					'periodList' => 
					array (
					  0 => 
					  array (
						'id' => 1,
						'startTime' => '09:00:00',
						'endTime' => '17:00:00',
						'locationId' => NULL,
						'periodServiceList' => 
						array (
						),
					  ),
					),
				  ),
				  1 => 
				  array (
					'id' => 2,
					'dayIndex' => 2,
					'startTime' => '09:00:00',
					'endTime' => '17:00:00',
					'timeOutList' => 
					array (
					),
					'periodList' => 
					array (
					  0 => 
					  array (
						'id' => 2,
						'startTime' => '09:00:00',
						'endTime' => '17:00:00',
						'locationId' => NULL,
						'periodServiceList' => 
						array (
						),
					  ),
					),
				  ),
				  2 => 
				  array (
					'id' => 3,
					'dayIndex' => 3,
					'startTime' => '09:00:00',
					'endTime' => '17:00:00',
					'timeOutList' => 
					array (
					),
					'periodList' => 
					array (
					  0 => 
					  array (
						'id' => 3,
						'startTime' => '09:00:00',
						'endTime' => '17:00:00',
						'locationId' => NULL,
						'periodServiceList' => 
						array (
						),
					  ),
					),
				  ),
				  3 => 
				  array (
					'id' => 4,
					'dayIndex' => 4,
					'startTime' => '09:00:00',
					'endTime' => '17:00:00',
					'timeOutList' => 
					array (
					),
					'periodList' => 
					array (
					  0 => 
					  array (
						'id' => 4,
						'startTime' => '09:00:00',
						'endTime' => '17:00:00',
						'locationId' => NULL,
						'periodServiceList' => 
						array (
						),
					  ),
					),
				  ),
				  4 => 
				  array (
					'id' => 5,
					'dayIndex' => 5,
					'startTime' => '09:00:00',
					'endTime' => '17:00:00',
					'timeOutList' => 
					array (
					),
					'periodList' => 
					array (
					  0 => 
					  array (
						'id' => 5,
						'startTime' => '09:00:00',
						'endTime' => '17:00:00',
						'locationId' => NULL,
						'periodServiceList' => 
						array (
						),
					  ),
					),
				  ),
				),
				'serviceList' => 
				array (
				),
				'dayOffList' => 
				array (
				),
				'specialDayList' => 
				array (
				),
				'locationId' => NULL,
				'googleCalendar' => NULL,
				'outlookCalendar' => NULL,
			  ),
			  'service' => 
			  array (
				'id' => 1,
				'name' => 'Demo Service',
				'description' => '',
				'color' => '#1788FB',
				'price' => 20,
				'deposit' => 0,
				'depositPayment' => 'disabled',
				'depositPerPerson' => true,
				'pictureFullPath' => NULL,
				'pictureThumbPath' => NULL,
				'extras' => 
				array (
				),
				'coupons' => 
				array (
				),
				'position' => NULL,
				'settings' => '{"payments":{"onSite":true,"payPal":{"enabled":false},"stripe":{"enabled":false},"mollie":{"enabled":false}},"zoom":{"enabled":false},"lessonSpace":{"enabled":false}}',
				'fullPayment' => false,
				'minCapacity' => 1,
				'maxCapacity' => 1,
				'duration' => 3600,
				'timeBefore' => NULL,
				'timeAfter' => NULL,
				'bringingAnyone' => true,
				'show' => true,
				'aggregatedPrice' => true,
				'status' => 'visible',
				'categoryId' => 1,
				'category' => NULL,
				'priority' => 
				array (
				),
				'gallery' => 
				array (
				),
				'recurringCycle' => NULL,
				'recurringSub' => NULL,
				'recurringPayment' => 0,
				'translations' => NULL,
				'minSelectedExtras' => NULL,
				'mandatoryExtra' => NULL,
			  ),
			  'location' => NULL,
			  'googleCalendarEventId' => NULL,
			  'googleMeetUrl' => NULL,
			  'outlookCalendarEventId' => NULL,
			  'zoomMeeting' => NULL,
			  'lessonSpace' => NULL,
			  'bookingStart' => '2021-12-30 14:00:00',
			  'bookingEnd' => '2021-12-30 15:00:00',
			  'type' => 'appointment',
			  'isRescheduled' => NULL,
			),
			'bookings' => 
			array (
			  0 => 
			  array (
				'id' => 3,
				'customerId' => 2,
				'customer' => 
				array (
				  'id' => 2,
				  'firstName' => 'Jon',
				  'lastName' => 'Doe',
				  'birthday' => NULL,
				  'email' => 'jondoecustomer@test.test',
				  'phone' => NULL,
				  'type' => 'customer',
				  'status' => 'visible',
				  'note' => NULL,
				  'zoomUserId' => NULL,
				  'countryPhoneIso' => NULL,
				  'externalId' => NULL,
				  'pictureFullPath' => NULL,
				  'pictureThumbPath' => NULL,
				  'gender' => NULL,
				),
				'status' => 'approved',
				'extras' => 
				array (
				),
				'couponId' => NULL,
				'price' => 20,
				'coupon' => NULL,
				'customFields' => 
				array (
				),
				'info' => NULL,
				'appointmentId' => 3,
				'persons' => 1,
				'token' => '527ed24841',
				'payments' => 
				array (
				  0 => 
				  array (
					'id' => 3,
					'customerBookingId' => 3,
					'packageCustomerId' => NULL,
					'parentId' => NULL,
					'amount' => 0,
					'gateway' => 'onSite',
					'gatewayTitle' => '',
					'dateTime' => '2021-12-30 14:00:00',
					'status' => 'pending',
					'data' => '',
					'entity' => 'appointment',
					'created' => NULL,
					'actionsCompleted' => true,
				  ),
				),
				'utcOffset' => NULL,
				'aggregatedPrice' => true,
				'isChangedStatus' => true,
				'packageCustomerService' => NULL,
				'cancelUrl' => 'https://yourdomain.test/wp-admin/admin-ajax.php?action=wpamelia_api&call=/bookings/cancel/3&token=527ed24841&type=appointment',
				'customerPanelUrl' => '',
				'infoArray' => NULL,
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.