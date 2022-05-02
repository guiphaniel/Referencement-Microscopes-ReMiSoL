<?php
    include_once("../include/config.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/Model.php");
    include_once("../model/entities/Controller.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopesGroupService.php");
    
    session_start();

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (!isset($_POST["lab"]) || !isset($_POST["coor"]) || !isset($_POST["contacts"]) || !isset($_POST["micros"])) {       
        redirect("/form.php");
    }

    $labInfos = $_POST["lab"];
    if(!isset($labInfos["name"]) || !isset($labInfos["address"]) || !isset($labInfos["website"])) {     
        redirect("/form.php");
    }

    $labAddress = $labInfos["address"];
    if (!isset($labAddress["street"]) || !isset($labAddress["zipCode"]) || !isset($labAddress["city"]) || !isset($labAddress["country"])) {
        redirect("/form.php");
    }


    $coorInfos = $_POST["coor"];
    if(!isset($coorInfos["lat"]) || !isset($coorInfos["lon"])) {     
        redirect("/form.php");
    }    

    foreach($_POST["contacts"] as $contact) {
        if (!isset($contact["firstname"]) || !isset($contact["lastname"]) || !isset($contact["role"]) || !isset($contact["email"]) || !isset($contact["phoneCode"]) || !isset($contact["phone"])) {
            redirect("/form.php");
        }
    }

    foreach($_POST["micros"] as $micro) {
        if (!isset($micro["compagny"]) || !isset($micro["brand"]) || !isset($micro["model"]) || !isset($micro["controller"]) || !isset($micro["desc"]) || !isset($micro["type"]) || !isset($micro["access"])) {
            redirect("/form.php");
        }
    }

    try {
        // Convert form values into objects...
        $address = $labAddress["street"] . "\n" . $labAddress["zipCode"] . " " . $labAddress["city"] . "\n" . $labAddress["country"];
        $lab = new Lab($labInfos["name"], $address, $labInfos["website"]);
    
        $contacts = [];
        foreach($_POST["contacts"] as $contact) {
            // retrieve phone number
            $contact["phone"] = $contact["phoneCode"] . " " . substr($contact["phone"], -9);

            $contacts[] = new Contact($contact["firstname"], strtoupper($contact["lastname"]),  ucfirst($contact["role"]), $contact["email"], $contact["phone"]);
        }
        
        $group = new MicroscopesGroup(new Coordinates($coorInfos["lat"], $coorInfos["lon"]), $lab, $contacts);

        foreach($_POST["micros"] as $micro) {
            $com = new Compagny($micro["compagny"]);
            $bra = new Brand($micro["brand"], $com);
            $mod = new Model($micro["model"], $bra);
            $ctr = new Controller($micro["controller"], $bra);

            $group->addMicroscope(new Microscope($mod, $ctr, $micro["rate"]??null, $micro["desc"], $micro["type"], $micro["access"], $micro["keywords"]??[]));
        }
            
        // ...and save the group into the db
        MicroscopesGroupService::getInstance()->add($group);
    } catch (\Throwable $th) {
        $_SESSION["microForm"]["errorMsg"]=$th->getMessage();
        redirect("/form.php");
    }
    
    redirect("/index.php");
