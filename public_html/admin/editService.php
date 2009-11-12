<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'forms/EditServiceForm.php';
require_once 'models/Service.php';

init();

if (!(is_authenticated()))
    redirect("index.php");

$service = null;
$form    = null;
$message = null;

if (is_numeric($_GET['id'])) {
	$service = Service::find($_GET['id']);
	
	if (!($service))
		http404();	
		
	// Initialize the form with initial data
	$initialData = array(
		'name'        => $service->name,
		'description' => $service->description,
		'price'       => $service->price,
		'duration'    => $service->duration,
		'available'   => $service->available,
		'perf_emp'    => $service->getPerformingEmployees()->flatten('id')
	);
	
	$form = new EditServiceForm(null, $initialData);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$form = new EditServiceForm($_POST);
		
	if ($form->isValid()) {
		if ($service == null)
			$service = new Service();
			
		$service->name        = $form->getCleanData('name');
		$service->description = $form->getCleanData('description');
		$service->price       = $form->getCleanData('price');
		$service->duration    = $form->getCleanData('duration');
		$service->available   = $form->getCleanData('available');
		$service->save();
		
		$service->updatePerformingEmployees($form->getCleanData('perf_emp'));
		$message = 'Saved';
	}
}

if (!($form))
	$form = new EditServiceForm();

display_template("templates/admin/editService.php",
    array(
        'title'     => ($service != null) ? 'Edit service info' : 'Add new service',
        'form'      => $form,
    	'servid'    => (is_numeric($_GET['id']) ? $_GET['id'] : null),
        'service'   => $service,
    	'activeTab' => 'services',
    	'message'   => $message
    )
);
?>
