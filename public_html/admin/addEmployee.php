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

$form    = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = new EditEmployeeForm($_POST);
    
    if ($form->isValid()) {
        $employee = new Employee();
        $employee->first_name = $form->getCleanData('first_name');
        $employee->last_name  = $form->getCleanData('last_name');
        $employee->active     = $form->getCleanData('active');
        $employee->save();
        
        redirect('employee.php?id='.$employee->id.'&employeeAdded');
    }
} else {
    $form = new EditEmployeeForm(null, array('active' => true)); 
}

display_template('templates/admin/addEmployee.php',
    array(
        'title'      => 'Add employee',
    	'message'    => $message,
    	'form'       => $form
    )
);
?>
