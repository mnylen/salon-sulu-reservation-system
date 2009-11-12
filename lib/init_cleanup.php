<?php
require_once 'lib/db/Database.php';

/**
 * Initializes the system.
 *
 */
function init() {
	// TODO: Use a custom exception handler	
	
	// Set PDO attributes
	Database::getPDOObject()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	// Start a new session / initialize the session with the data from old one
	session_start();
	
	// Begin a transaction
	Database::getPDOObject()->beginTransaction();
}

/**
 * Cleans up any opened resources and commits the transaction.
 * 
 * If committing the transaction fails, the transaction will be rolled back and the
 * exception will be thrown.
 *
 */
function cleanup() {
    try {
        Database::getPDOObject()->commit();
    } catch (PDOException $ex) {
        Database::getPDOObject()->rollBack();
        throw $ex;
    }
}
?>
