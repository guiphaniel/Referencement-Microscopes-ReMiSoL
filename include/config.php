<?php
    function isUserSessionValid() {
        return isset($_SESSION["user"]);
    }

    function redirect(string $link) {
        header('location: ' . $link);
        exit();
    }