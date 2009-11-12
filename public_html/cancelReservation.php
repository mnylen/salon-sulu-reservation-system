<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Employee.php';
require_once 'forms/CancelReservationForm.php';

init();


$form = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$form = new CancelReservationForm($_POST);
	
	if ($form->isValid()) {
		// Redirect to confirmation
		redirect('confirmCancellation.php?'.
		         'cust_email='.$form->getCleanData('cust_email').'&'.
				 'cancel_key='.$form->getCleanData('cancel_key'));
	}
} else {
	$form = new CancelReservationForm();
}

display_template('templates/cancelReservation.php',
	array(
		'title' => 'Cancel reservation',
		'form'  => $form
	)
);
?>