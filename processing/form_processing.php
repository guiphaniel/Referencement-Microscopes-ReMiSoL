<?php
    include_once("../model/start_db.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/Model.php");
    include_once("../model/entities/Controller.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopesGroupService.php");

    session_start();

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form TODO: check that keywords aren't duplicated
    if (!isset($_POST["lab"]) || !isset($_POST["coor"])) {       
        header('location: /form.php');
        exit();
    }

    $labInfos = $_POST["lab"];
    if(!isset($labInfos["name"]) || !isset($labInfos["address"]) || !isset($labInfos["website"])) {       
        header('location: /form.php');
        exit();
    }

    $coorInfos = $_POST["coor"];
    if(!isset($coorInfos["lat"]) || !isset($coorInfos["lon"])) {       
        header('location: /form.php');
        exit();
    }    

    foreach($_POST["contacts"] as $contact) {
        if (!isset($contact["firstname"]) || !isset($contact["lastname"]) || !isset($contact["role"]) || !isset($contact["email"])) {
            header('location: /form.php');
            exit();
        }
    }

    foreach($_POST["micros"] as $micro) {
        if (!isset($micro["compagny"]) || !isset($micro["brand"]) || !isset($micro["model"]) || !isset($micro["controller"]) || !isset($micro["type"]) || !isset($micro["desc"])) {
            header('location: /form.php');
            exit();
        }

        if($micro["type"] == "SERVICE" && !isset($micro["rate"])) {
            header('location: /form.php');
            exit();
        }

        if($micro["type"] != "SERVICE" && isset($micro["rate"])) {
            header('location: /form.php');
            exit();
        }
    }

    try {
        // Convert form values into objects...
        $lab = new Lab(...$labInfos);
    
        $contacts = [];
        foreach($_POST["contacts"] as $contact) {
            $contacts[] = new Contact($contact["firstname"], $contact["lastname"], $contact["role"], $contact["email"], $contact["phone"]??null);
        }
        
        $group = new MicroscopesGroup(new Coordinates(...$coorInfos), $lab, $contacts);

        foreach($_POST["micros"] as $micro) {
            $com = new Compagny($micro["compagny"]);
            $bra = new Brand($micro["brand"], $com);
            $mod = new Model($micro["model"], $bra);
            $ctr = new Controller($micro["controller"], $bra);

            $group->addMicroscope(new Microscope($mod, $ctr, $micro["desc"], $micro["access"], $micro["rate"]??null, $micro["keywords"]??[]));
        }
            
        // ...and save the group into the db
        MicroscopesGroupService::getInstance()->add($group);
    } catch (\Throwable $th) {
        $_SESSION["micro_form"]["error_msg"]=$th->getMessage();
        header('location: /form.php');
        exit();
    }
    
    header('location: /index.php');

