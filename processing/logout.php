<?php
    include_once("../config/config.php");

    if(isUserSessionValid())
        session_unset();

    redirect("/index.php");