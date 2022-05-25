<?php
$dsn = 'sqlite:' . __DIR__ . '/database.db';

try {    
    $pdo = new PDO($dsn);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
    die();
}

$pdo->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
$pdo->exec( 'PRAGMA foreign_keys = ON;' );

$pdo->sqliteCreateFunction("REGEXP", "REGEXP", 2);
$pdo->sqliteCreateFunction("CONCAT", "CONCAT");

function REGEXP($pattern, $subject) {
    return preg_match("/" . $pattern . "/", $subject);
}

function CONCAT(...$strings) {
    return implode("", $strings);
}

