<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/util.php';
require_once 'lib/shortcuts.php';
require_once 'lib/Report.php';
require_once 'lib/Calendar.php';

init();


if (!(is_authenticated()))
    redirect('index.php');

$calStartDay = (isset($_GET['week'])) ? getFirstDayOfWeek($_GET['week']) : false;

// Use the current weeks monday if the provided week string was not set/valid or was in past
$currentWeekStartDay = getFirstDayOfWeek(date("Y\-\WW"));
if (!($calStartDay) || ($calStartDay > $currentWeekStartDay))
	$calStartDay = $currentWeekStartDay;
	
$report = new Report($calStartDay, $calStartDay + (7 * Calendar::SecondsInDay));

display_template('templates/admin/weeklyReport.php',
	array(
		'title'     => 'Weekly report',
		'activeTab' => 'report',
		'report'    => $report
	)
);
?>