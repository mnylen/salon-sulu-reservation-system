<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'lib/reservation.php';
require_once 'models/Service.php';
require_once 'forms/ServiceSelectionForm.php';

init();
validateProcess('selectService');

$form = null;

if ($_POST) {
	$form = new ServiceSelectionForm($_POST);
	
	if ($form->isValid()) {
		if (!(isset($_SESSION['reservation.step1'])))
			$_SESSION['reservation.beginTime'] = serialize(time());
		
		$_SESSION['reservation.service'] = serialize($form->getCleanData('service'));
		$_SESSION['reservation.step1']   = true;
		
		redirect('calendar.php');
	}
} else {
	$initial = array();
	
	if (isset($_SESSION['reservation.service']))
		$initial['service'] = unserialize($_SESSION['reservation.service'])->id;
	
	$form = new ServiceSelectionForm(null, $initial);
}

display_template("templates/selectService.php",
	array(
		"title"			=> "Make reservation | Select service",
		"form"			=> $form,
	)
);
?>