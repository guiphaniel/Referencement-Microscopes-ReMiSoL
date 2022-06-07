<?php
    include_once("../config/config.php");
    include_once("../model/services/UserService.php");

    if(!isUserSessionValid()) 
        redirect("/index.php");

    if(empty($_POST["userId"]))
        redirect("/index.php");

    $userId = intval($_POST["userId"]);

    $UserService = UserService::getInstance();

    if($_SESSION["user"]["admin"] || $_SESSION["user"]["id"] == $userId) {
        $user = $UserService->findUserById($userId);
        $UserService->delete($user);
    }

    if($_SESSION["user"]["id"] == $userId) {
        session_unset();
        redirect("/index.php");
    }

    redirect("/admin.php?action=users");
