<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'models/Service.php';

init();


if (!(is_authenticated()))
    redirect('index.php');


display_template('templates/admin/serviceList.php',
    array(
        'title'     => 'Manage services',
        'services'  => Service::findAll(),
    	'activeTab' => 'services'
    )
);
?>
