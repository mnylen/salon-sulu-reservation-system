<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Employee.php';
require_once 'models/WorkShift.php';
require_once 'forms/EditEmployeeForm.php';

init();

if (!(is_authenticated()))
    redirect('index.php');


$employee = null;
if (!(is_numeric($_GET['id'])) || ($employee = Employee::find($_GET['id'])) == null)
    http404();

$form    = null;
$message = null;

if (isset($_GET['workShiftAdded'])) {
    $message = "The work shift was added successfully.";
} else if (isset($_GET['workShiftDeleted'])) {
    $message = "The work shift was deleted.";
} else if (isset($_GET['employeeAdded'])) {
    $message = $employee->first_name." ".$employee->last_name." was added.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = new EditEmployeeForm($_POST);
    
    if ($form->isValid()) {
        $employee->first_name = $form->getCleanData('first_name');
        $employee->last_name  = $form->getCleanData('last_name');
        $employee->active     = $form->getCleanData('active');
        
        $employee->save();
        $message = "Saved.";        
    }
} else {
    $form = new EditEmployeeForm(null,
        array(
            'first_name' => $employee->first_name,
            'last_name'  => $employee->last_name,
            'active'     => $employee->active
       )
    );   
}

$workShifts = WorkShift::findByEmployee($employee, time(), null);

display_template('templates/admin/employee.php',
    array(
        'title'      => 'Employee details',
    	'activeTab'  => 'employees',
    	'employee'   => $employee,
    	'workShifts' => $workShifts,
    	'message'    => $message,
    	'form'       => $form
    )
);
?>
