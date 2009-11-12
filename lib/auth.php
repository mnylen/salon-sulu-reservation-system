<?php
require_once 'lib/db/Database.php';

/**
 * Tries to authenticate an user with the given credentials.
 *
 * @param $username the username
 * @param $password the password
 * 
 * @returns <code>true</code> if the user was authenticated succesfully;
 *          otherwise <code>false</code> is returned
 */
function auth($username, $password) {
    $sql  = "SELECT * FROM managers ";
    $sql .= "  WHERE username = :username AND password = :password";

    $stmt = Database::getPDOObject()->prepare($sql);
    $stmt->bindValue(":username", $username);
    $stmt->bindValue(":password", md5($password));
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
    	$managerId = $row['id'];
    	
        // Authenticate the user: create a new session
        $sql  = "INSERT INTO sessions (id, manager_id, last_activity) ";
        $sql .= "  VALUES (gen_sess_id(), :manid, :last_activity) ";
        $sql .= "RETURNING id";

        $stmt = Database::getPDOObject()->prepare($sql);
        $stmt->bindValue(':last_activity', date('Y-m-d H:i')); 
        $stmt->bindValue(':manid', $managerId);        
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['sessid'] = $row['id'];
        $_SESSION['manid']  = $managerId;
        
        return true;
    }

    return false;
}

/**
 * Checks if the current user is authenticated or not. This is determined
 * by the current session contents.
 *
 * @returns <code>true</code> if the user is authenticated;
 *          otherwise <code>false</code> is returned
 */
function is_authenticated() {
    prune_sessions();
    
    $sql = "SELECT COUNT(*) FROM sessions WHERE id = :sessid AND manager_id = :manid";

    $stmt = Database::getPDOObject()->prepare($sql);
    $stmt->bindValue(":sessid", $_SESSION['sessid']);
    $stmt->bindValue(":manid", $_SESSION['manid']);
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 1) {
        // Update the last activity
        $stmt = Database::getPDOObject()->prepare(
            "UPDATE sessions SET last_activity = now() ".
            "  WHERE id = :sessid");
            
        $stmt->bindValue(':sessid', $_SESSION['sessid']);
        $stmt->execute();
        
        return true;
    }
    
    return false;
}

/**
 * Prunes outdated sessions.
 */
function prune_sessions() {
    require 'config/settings.php';
    
    Database::getPDOObject()->query(
        "DELETE FROM sessions WHERE ".
        "  age(now(), last_activity) > interval '".$SETTINGS['session_timeout']." min'");
}
?>
