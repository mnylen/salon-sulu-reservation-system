<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'lib/reservation.php';
require_once 'models/Service.php';
require_once 'models/Employee.php';
require_once 'models/Reservation.php';

init();
validateProcess('confirm');



$service  = unserialize($_SESSION['reservation.service']);
$employee = unserialize($_SESSION['reservation.employee']);
$datetime = unserialize($_SESSION['reservation.datetime']);

// Save the reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
	$reservation = new Reservation();
	$reservation->service_id  = $service->id;
	$reservation->employee_id = $employee->id;
	$reservation->price       = $service->price;
	$reservation->start_time  = $datetime;
	$reservation->end_time 	  = $datetime + (Calendar::SecondsInMinute * $service->duration);
	$reservation->cust_fname  = $_SESSION['reservation.cust_fname'];
	$reservation->cust_lname  = $_SESSION['reservation.cust_lname'];
	$reservation->cust_email  = $_SESSION['reservation.cust_email'];
	$reservation->cust_phone  = $_SESSION['reservation.cust_phone'];
	$reservation->save();
	
	// Clean up the reservation data from session
	cleanupReservation();
	$_SESSION['reservation.id'] = $reservation->id;
	
	// Display summary
	redirect('summary.php');
}

display_template('templates/confirm.php',
	array(
		'service'  => $service,
		'employee' => $employee,
		'datetime' => $datetime
	)
);
?>