<?php
    session_start();
    
    const WEBSITE_URL = "guilhem.davidalbertini.fr";

    function isUserSessionValid() {
        return isset($_SESSION["user"]);
    }

    function redirect(string $link) {
        header('location: ' . $link);
        exit();
    }