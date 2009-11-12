<?php
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';

init();

display_template('templates/index.php',
    array(
        "title" => "Home"
    )
);
?>