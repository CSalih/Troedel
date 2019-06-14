<?php
    define('DB_SERVER', '127.0.0.1');
    define('DB_PORT', 3306);
    define('DB_USERNAME', 'jodel_database');
    define('DB_PASSWORD', 'zoLta1U1hYWAzLNr');
    define('DB_DATABASE', 'jodel');
     
    /* Attempt to connect to MySQL database */
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
     
    // Check connection
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }
    ?>