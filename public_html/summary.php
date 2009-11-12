<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Reservation.php';
require_once 'models/Employee.php';
require_once 'models/Service.php';

init();

if (!($_SESSION['reservation.id']))
	redirect('selectService.php');

$reservation = Reservation::find($_SESSION['reservation.id']);
if (!($reservation) || ($reservation->cancelled))
	redirect('selectService.php');

$employee = Employee::find($reservation->employee_id);
$service  = Service::find($reservation->service_id);
	
display_template('templates/reservationSummary.php',
	array(
		'title' 	  => 'Reservation summary',
		'reservation' => $reservation,
		'employee'    => $employee,
		'service'     => $service
	)
);
?>