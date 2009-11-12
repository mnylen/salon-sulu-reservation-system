<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Employee.php';

init();


if (!(is_authenticated()))
    redirect('index.php');


$employees = Employee::findAll(false);
display_template('templates/admin/employees.php',
    array(
        'title'     => 'Employees',
    	'activeTab' => 'employees',
    	'employees' => $employees
    )
);
?>
