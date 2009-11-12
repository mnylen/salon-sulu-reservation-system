<?php
/**
 * A singleton wrapping an <code>PDO</code> object for database
 * interaction.
 * 
 */
class Database {
    private static $pdo;
    
    /**
     * Gets an PDO object. If there's no PDO object already, a new one
     * is created using the database settings given in the file
     * specified by <code>SettingsFile</code> constant.
     *
     * @return PDO the <code>PDO</code> object created
     */
    public static function getPDOObject() {
        if (Database::$pdo == null) {
            // Load database settings
            require 'config/settings.php';
            
            Database::$pdo = new PDO(Database::buildDSN($SETTINGS));
        }

        return Database::$pdo;
    }
    
    /**
     * Builds a DSN string from the given settings.
     *
     * @param array $settings the database connection settings
     * @return string the generated DSN string
     */
    private static function buildDSN($settings) {
        $dsn  = "pgsql:host=".$settings['db_host']." ";
        $dsn .= "user=".$settings['db_user']." ";
        $dsn .= "password=".$settings['db_password']." ";
        $dsn .= "dbname=".$settings['db_name'];

        return $dsn;
    }
}
?>
