<?php
    include_once("../model/start_db.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopeService.php");

    // TODO: verifier que les laboratoires, les microscopes et les thèmes font bien parti de ceux déjà présent en bd
    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form 
    if (!isset($_POST["labName"]) || !isset($_POST["labAddress"]) || !isset($_POST["contactFirstname"]) || !isset($_POST["contactLastname"]) || !isset($_POST["contactEmail"]) || !isset($_POST["lat"]) || !isset($_POST["lon"])) {
        header('location: /form.php');
        exit();
    }

    foreach($_POST["microscopes"] as $micro) {
        if (!isset($micro["brand"]) || !isset($micro["ref"]) || !isset($micro["rate"]) || !isset($micro["desc"])) {
            header('location: /form.php');
            exit();
        }
    }

    // Convert form values into objects...
    $lab = new Lab($_POST["labName"], $_POST["labAddress"]);
    $contact = new Contact($_POST["contactFirstname"], $_POST["contactLastname"], $_POST["contactEmail"]);
    $group = new MicroscopesGroup($_POST["lat"], $_POST["lon"], $lab, $contact);

    foreach($_POST["microscopes"] as $micro)
        $group->addMicroscope(new Microscope($micro["brand"], $micro["ref"], $micro["rate"], $micro["desc"]));

    // ...and save the group into db
    MicroscopeService::getInstance()->saveGroup($group);

    header('location: /index.php');

