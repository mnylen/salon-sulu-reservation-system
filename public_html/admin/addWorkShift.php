<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Employee.php';
require_once 'models/WorkShift.php';
require_once 'forms/EditWorkShiftForm.php';

init();

if (!(is_authenticated()))
    redirect('index.php');
    

$employee = null;
if (!(is_numeric($_GET['employee_id'])) || ($employee = Employee::find($_GET['employee_id'])) == null)
    http404();

$form    = new EditWorkShiftForm();
$message = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel']))
        redirect('employee.php?id='.$employee->id);
    
    $form = new EditWorkShiftForm($_POST);
    
    if ($form->isValid()) {
        $shift = new WorkShift();
        $shift->employee_id = $employee->id;
        $shift->start_time  = $form->getCleanData('start_time')->getTimestamp();
        $shift->end_time    = $form->getCleanData('end_time')->getTimestamp();
        $shift->save();
        
        if (isset($_POST['saveAndAddNew'])) {
            $message = 'Saved.';
            $form = new EditWorkShiftForm();
        } else {
            redirect('employee.php?id='.$employee->id.'&workShiftAdded');
        }
    }
}

display_template('templates/admin/addWorkShift.php',
    array(
        'title'     => 'Add work shift',
        'employee'  => $employee,
        'form'      => $form,
        'message'   => $message
    )
);
?>