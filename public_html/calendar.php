<?php
require_once 'lib/shortcuts.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/util.php';
require_once 'lib/reservation.php';
require_once 'lib/Calendar.php';
require_once 'models/Employee.php';
require_once 'models/Service.php';
require_once 'models/WorkShift.php';

init();
validateProcess('calendar');


$service   = unserialize($_SESSION['reservation.service']);
$employees = $service->getPerformingEmployees();
$errors    = array();

// Set the options for calendar display
$employee    = (isset($_GET['employee'])) ? $employees->get('id', $_GET['employee']) : null;
$calStartDay = (isset($_GET['week'])) ? getFirstDayOfWeek($_GET['week']) : null;

// Use default employee (the first possible) if the selected employee was not valid
if (!($employee))
	$employee = $employees->first();
    
// Use the current weeks monday if the provided week string was not set/valid or was in past
$nextShift = WorkShift::findNextWorkShift($employee);
$currentWeekStartDay = getFirstDayOfWeek(date("Y\-\WW",
    ($nextShift != null) ? $nextShift->start_time : time()));

if (!($calStartDay) || ($calStartDay < $currentWeekStartDay))
	$calStartDay = $currentWeekStartDay;

// The date & time is first picked using an link
$pickedDateTime = (isset($_GET['datetime'])) ? $_GET['datetime'] : null;

if ($pickedDateTime && !($employee->isAvailable($pickedDateTime, $service->duration))) {
	$pickedDateTime = null;
	$errors['datetime'] = "Selected date and time is not reservable.";
}

// Save the data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $pickedDateTime != null) {
	$_SESSION['reservation.employee'] = serialize($employee);
	$_SESSION['reservation.datetime'] = serialize($pickedDateTime);
	$_SESSION['reservation.step2']    = true;
		
	// Next, the user should insert his or her contact information
	redirect('contactInfo.php');
}

// Load the calendar
$calendar = new Calendar($employee, $calStartDay, $calStartDay+7*Calendar::SecondsInDay);
$calendar->tagAllReservable($service->duration);

display_template('templates/calendar.php',
	array(
		"title"          => "Make reservation | Pick date & time",
		"service"        => $service,
		"employees"		 => $employees,
		"employee"       => $employee,
		"pickedDateTime" => $pickedDateTime,
		"week"			 => $_GET['week'],
		"nextWeek" 		 => (date("Y\-\WW", $calStartDay+7*Calendar::SecondsInDay)),
		"prevWeek"		 => ($calStartDay != $currentWeekStartDay) ? date('Y\-\WW', $calStartDay-7*Calendar::SecondsInDay) : null,
		"errors"         => $errors,
		"calendar"       => $calendar
	)
);
?>