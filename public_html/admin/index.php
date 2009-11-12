<?php
require_once 'lib/auth.php';
require_once 'lib/init_cleanup.php';
require_once 'lib/shortcuts.php';
require_once 'forms/LoginForm.php';

init();


if (is_authenticated())
    redirect('admin.php');


$form = new LoginForm();
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = new LoginForm($_POST);

    if ($form->isValid()) {
        if (auth($_POST['username'], $_POST['password'])) 
            redirect('admin.php');

        else
            $errorMessage = "Either the username does not exist ".
                            "or the password provided is invalid.";
    }
}

display_template('templates/admin/index.php',
    array(
        'title'        => 'Login',
        'form'         => $form,
        'errorMessage' => $errorMessage,
    )
);
?>
