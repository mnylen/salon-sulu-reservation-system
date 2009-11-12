<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'forms/CancelReservationForm.php';
require_once 'models/Reservation.php';
require_once 'models/Service.php';
require_once 'models/Employee.php';

init();

$form = new CancelReservationForm($_GET);

if (!($form->isValid()))
	redirect('cancelReservation.php');
	
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
	$reservation = $form->getCleanData('reservation');
	$reservation->cancelled = true;
	$reservation->save();
	
	redirect('reservationCancelled.php');
}

$reservation = $form->getCleanData('reservation');
$service     = Service::find($reservation->service_id);
$employee    = Employee::find($reservation->employee_id);

display_template('templates/confirmCancellation.php',
	array(
		'reservation' => $reservation,
		'service'     => $service,
		'employee'    => $employee	
	)
);
?>