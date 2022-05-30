<?php
    session_start();
    
    enum DBMS {
        case SQLite;
        case MySQL;
    }

    const WEBSITE_URL = "guilhem.davidalbertini.fr";
    const MY_DBMS = DBMS::MySQL; // switch here from SQLite to MySQL as necessary
    const DB_HOST = '<database hostname>'; // fill this as necessery
    const DB_NAME = '<database name>'; // fill this as necessery
    const DB_USER = '<database user>'; // fill this as necessery
    const DB_PASSWORD = "<database user's password>"; // fill this as necessery

    function isUserSessionValid() {
        return isset($_SESSION["user"]);
    }
    
    function redirect(string $link) {
        header('location: ' . $link);
        exit();
    }