<?php
    include_once("../model/start_db.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/Model.php");
    include_once("../model/entities/Controller.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopesGroupService.php");

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form TODO: check that keywords aren't duplicated
    if (!isset($_POST["labName"]) || !isset($_POST["labAddress"]) || !isset($_POST["contactFirstname"]) || !isset($_POST["contactLastname"]) || !isset($_POST["contactEmail"]) || !isset($_POST["lat"]) || !isset($_POST["lon"])) {
        header('location: /form.php');
        exit();
    }

    foreach($_POST["microscopes"] as $micro) {
        if (!isset($micro["compagny"]) || !isset($micro["brand"]) || !isset($micro["model"]) || !isset($micro["controller"]) || !isset($micro["rate"]) || !isset($micro["desc"])) {
            header('location: /form.php');
            exit();
        }
    }

    // Convert form values into objects...
    $lab = new Lab($_POST["labName"], $_POST["labAddress"]);
    $contact = new Contact($_POST["contactFirstname"], $_POST["contactLastname"], $_POST["contactEmail"]);
    $group = new MicroscopesGroup(new Coordinates($_POST["lat"], $_POST["lon"]), $lab, $contact);

    foreach($_POST["microscopes"] as $micro) {
        $com = new Compagny($micro["compagny"]);
        $bra = new Brand($micro["brand"], $com);
        $mod = new Model($micro["model"], $bra);
        $ctr = new Controller($micro["controller"], $bra);

        $group->addMicroscope(new Microscope($mod, $ctr, $micro["rate"], $micro["desc"], $micro["keywords"]??[]));
    }
        
    // ...and save the lab, contact and group into the db
    LabService::getInstance()->save($group->getLab());
    ContactService::getInstance()->save($group->getContact()); 
    MicroscopesGroupService::getInstance()->add($group);

    header('location: /index.php');

