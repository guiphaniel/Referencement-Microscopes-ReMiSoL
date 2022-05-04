<?php
    include_once("../include/config.php");

    if(isUserSessionValid())
        session_unset();

    redirect("/index.php");