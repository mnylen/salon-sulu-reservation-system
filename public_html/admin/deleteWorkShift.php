<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Employee.php';
require_once 'models/WorkShift.php';

init();

if (!(is_authenticated()))
    redirect('index.php');

$shift = null;
if (!(is_numeric($_GET['id'])) || ($shift = WorkShift::find($_GET['id'])) == null)
    http404();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    if ($shift->reservationCount != 0) {
        // Cancel all of the reservations
        $reservations = $shift->reservations;
        
        while ( ($reservation = $reservations->next()) !== null ) {
            $reservation->cancelled = true;
            $reservation->save();   
        }
    }
    
    // Delete the shift
    $shift->delete();
    redirect('employee.php?id='.$shift->employee_id.'&workShiftDeleted');
}

display_template('templates/admin/confirmDeleteWorkShift.php',
    array(
        'title'      => 'Confirm deleting work shift',
    	'activeTab'  => 'employees',
    	'shift'      => $shift
    )
);
?>