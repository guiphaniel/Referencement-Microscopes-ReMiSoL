<?php
    include("../config/config.php");
    include("../model/services/MicroscopesGroupService.php");

    if(!$_SESSION["user"]["admin"])
        redirect("/index.php");

    if(isset($_GET["groupId"]))
        MicroscopesGroupService::getInstance()->unlock($_GET["groupId"]);

    redirect("/admin.php");