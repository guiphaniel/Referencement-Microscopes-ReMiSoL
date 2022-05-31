<?php
    include_once("../config/config.php");
    include_once("../model/services/UserService.php");

    if(!isUserSessionValid()) 
        redirect("/index.php");

    if(empty($_POST["groupId"]))
        redirect("/index.php");

    $groupId = intval($_POST["groupId"]);

    $microscopesGroupService = MicroscopesGroupService::getInstance();

    $owner = $microscopesGroupService->findGroupOwnerByGroupId($groupId);

    if(isset($owner) && ($_SESSION["user"]["admin"] || $_SESSION["user"]["id"] == $owner->getId())) {
        $group = $microscopesGroupService->findMicroscopesGroupById($groupId);
        $microscopesGroupService->delete($group);
    }

    redirect("/index.php");