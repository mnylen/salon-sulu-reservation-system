<?php
require_once 'lib/shortcuts.php';
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/DatePicker.php';
require_once 'models/Employee.php';
require_once 'models/Service.php';
require_once 'models/Reservation.php';

init();

if (!(is_authenticated()))
    redirect('index.php');

$employee = null;
if (!(is_numeric($_GET['employee_id'])) || ($employee = Employee::find($_GET['employee_id'])) == null)
    http404();


$date = DateTime::createFromFormat('Y-m-d', $_GET['date']);
if (!($date))
    http404();

$reservations = Reservation::findByEmployee($employee,
    $date->getTimestamp(), $date->getTimestamp());

display_template('templates/admin/workSchedule.php',
	array(
		"title"        => "Work schedule",
		"employee"     => $employee,
		"date"         => $date,
        "reservations" => $reservations
	)
);
?>