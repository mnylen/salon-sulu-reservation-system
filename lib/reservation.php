<?php
require_once 'lib/Calendar.php';

/**
 * Validates the reservation process and redirects the user to correct
 * place if invalid values are detected.
 *
 */
function validateProcess($currentStep) {
	if ($currentStep == 'calendar' || $currentStep == 'contactInfo' || $currentStep == 'confirm') {
		if (!(isset($_SESSION['reservation.step1'])))
			redirect('selectService.php');
	}
	
	if ($currentStep == 'contactInfo' || $currentStep == 'confirm') {
		if (!(isset($_SESSION['reservation.step2'])))
			redirect('calendar.php');
	}
	
	if ($currentStep == 'confirm') {
		if (!(isset($_SESSION['reservation.step3'])))
			redirect('contactInfo.php');
	}
	
	$service = null;
	
	if ($_SESSION['reservation.step1']) {
		// Check for the timeout
		require 'config/settings.php';
		$beginTime = unserialize($_SESSION['reservation.beginTime']);
		
		if (time() > ($beginTime + $SETTINGS['reservation_timeout'] * Calendar::SecondsInMinute)) {
			cleanupReservation();
			display_template('templates/reservationTimeout.php');
		}
		
		// Validate the selected service is reservable
		$service = unserialize($_SESSION['reservation.service']);
		
		if (!($service->isReservable()))
			display_template('templates/serviceUnavailable.php');
	}
	
	$employee = null;
	$datetime = null;
	
	if ($_SESSION['reservation.step2']) {
		// Validate the selected user performs the service
		$employee = unserialize($_SESSION['reservation.employee']);
		$datetime = unserialize($_SESSION['reservation.datetime']);

		if ($service->getPerformingEmployees()->get('id', $employee->id) === null || !($employee->isAvailable($datetime, $service->duration)))
			display_template('templates/employeeUnavailable.php');
	}
}

function cleanupReservation() {
	unset($_SESSION['reservation.step1']);
	unset($_SESSION['reservation.step2']);
	unset($_SESSION['reservation.step3']);
	unset($_SESSION['reservation.beginTime']);
	unset($_SESSION['reservation.datetime']);
	unset($_SESSION['reservation.service']);
	unset($_SESSION['reservation.employee']);
	unset($_SESSION['reservation.cust_fname']);
	unset($_SESSION['reservation.cust_lname']);
	unset($_SESSION['reservation.cust_email']);
	unset($_SESSION['reservation.cust_phone']);
}
?>