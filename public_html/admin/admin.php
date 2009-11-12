<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';

init();

if (!(is_authenticated()))
    redirect('index.php');

display_template('templates/admin/admin.php',
    array(
        'title'     => 'Admin Index',
    	'activeTab' => 'home'
    )
);
?>
