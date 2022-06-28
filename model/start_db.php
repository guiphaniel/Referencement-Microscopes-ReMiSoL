<?php
include_once(__DIR__ . "/../config/config.php");

function REGEXP($pattern, $subject) {
    return preg_match("/" . $pattern . "/", $subject);
}

function CONCAT(...$strings) {
    return implode("", $strings);
}

function CONCAT_WS($separator, ...$strings) {
    return implode($separator, $strings);
}

if(MY_DBMS == DBMS::SQLite) {
    $dsn = 'sqlite:' . __DIR__ . '/database.db';

    try {    
        $pdo = new PDO($dsn);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
        die();
    }

    $pdo->exec( 'PRAGMA foreign_keys = ON;' );
    $pdo->sqliteCreateFunction("REGEXP", "REGEXP", 2);
    $pdo->sqliteCreateFunction("CONCAT", "CONCAT");
    $pdo->sqliteCreateFunction("CONCAT_WS", "CONCAT_WS");
}
else if(MY_DBMS == DBMS::MySQL) {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;

    try {    
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
        die();
    }
}

$pdo->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );




