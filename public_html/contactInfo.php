<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'lib/reservation.php';
require_once 'forms/ContactInfoForm.php';
require_once 'models/Service.php';
require_once 'models/Employee.php';

init();
validateProcess('contactInfo');


$service  = unserialize($_SESSION['reservation.service']);
$employee = unserialize($_SESSION['reservation.employee']);
$datetime = unserialize($_SESSION['reservation.datetime']);

// Process the form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$form = new ContactInfoForm($_POST);
	
	if ($form->isValid()) {
		$cleanedData = $form->getCleanedData();
		
		// Save the form data to session
		$_SESSION['reservation.cust_fname'] = $cleanedData['cust_fname'];
		$_SESSION['reservation.cust_lname'] = $cleanedData['cust_lname'];
		$_SESSION['reservation.cust_email'] = $cleanedData['cust_email'];
		$_SESSION['reservation.cust_phone'] = $cleanedData['cust_phone'];
		$_SESSION['reservation.step3']      = true;
		
		redirect('confirm.php');
	}
} else {
	$initialData = array(
		'cust_fname' => $_SESSION['reservation.cust_fname'],
		'cust_lname' => $_SESSION['reservation.cust_lname'],
		'cust_email' => $_SESSION['reservation.cust_email'],
		'cust_phone' => $_SESSION['reservation.cust_phone']
	);
	
	$form = new ContactInfoForm(null, $initialData);
}

display_template('templates/contactInfo.php',
	array(
		'title'    => 'Contact info',
		'form'     => $form,
		'employee' => $employee,
		'datetime' => $datetime,
		'service'  => $service
	)
);
?>