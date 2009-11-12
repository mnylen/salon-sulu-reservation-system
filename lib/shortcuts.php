<?php
require_once 'lib/template.php';

/**
 * Displays a template, cleans up and exits the script execution.
 */
function display_template($template_filename, $vars = array()) {
    extract($vars, EXTR_SKIP);
    include($template_filename);

    cleanup();
    exit();
}

/**
 * Sets the Location header, cleans up and exists the script execution.
 */
function redirect($location) {
    header("Location: ".$location);

    cleanup();
    exit();
}

/**
 * Shows 404 - Not Found page, cleans up and exists the script execution.
 *
 */
function http404() {
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");

    display_template('templates/404.php');
    
    cleanup();
    exit();
}
?>
