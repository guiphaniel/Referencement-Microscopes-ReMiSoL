<?php
    include_once("../include/config.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/Model.php");
    include_once("../model/entities/Controller.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopesGroupService.php");
    
    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form TODO: check that the two contacts aren't the same ones
    if (empty($_POST["lab"]) || empty($_POST["coor"]) || empty($_POST["contacts"]) || empty($_POST["micros"])) {       
        redirect("/form.php");
    }

    $labInfos = $_POST["lab"];
    if(empty($labInfos["name"]) || empty($labInfos["type"]) || empty($labInfos["code"]) || empty($labInfos["address"]) || empty($labInfos["website"])) {     
        redirect("/form.php");
    }

    $labAddress = $labInfos["address"];
    if (empty($labAddress["street"]) || empty($labAddress["zipCode"]) || empty($labAddress["city"]) || empty($labAddress["country"])) {
        redirect("/form.php");
    }


    $coorInfos = $_POST["coor"];
    if(empty($coorInfos["lat"]) || empty($coorInfos["lon"])) {     
        redirect("/form.php");
    }    

    foreach($_POST["contacts"] as $contact) {
        if (empty($contact["firstname"]) || empty($contact["lastname"]) || empty($contact["role"]) || empty($contact["email"]) || empty($contact["phoneCode"]) || empty($contact["phone"])) {
            redirect("/form.php");
        }
    }

    foreach($_POST["micros"] as $micro) {
        if (empty($micro["compagny"]) || empty($micro["brand"]) || empty($micro["model"]) || empty($micro["controller"]) || empty($micro["desc"]) || empty($micro["type"]) || empty($micro["access"])) {
            redirect("/form.php");
        }
    }

    try {
        // Convert form values into objects...
        $labCode = $labInfos["type"] . $labInfos["code"];
        $address = $labAddress["street"] . "\n" . $labAddress["zipCode"] . " " . $labAddress["city"] . "\n" . $labAddress["country"];
        $lab = new Lab($labInfos["name"], $labCode, $address, $labInfos["website"]);
    
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

        //save the micros' imgs
        $nbImgs = count($_FILES['imgs']['name']);
        if($nbImgs > count($_POST["micros"]))
            throw new Exception("Vous ne pouvez envoyer qu'une seule image par microscope au maximum.");

        for ($i=0; $i < $nbImgs; $i++) { 
            $microId = $group->getMicroscopes()[$i]->getId();
            $imgs = $_FILES["imgs"];
            // retrieve the file extension
            $fileType = $imgs['type'][$i];
            $fileType = substr($fileType, strpos($fileType, "/") + 1);  
            // save the image
            move_uploaded_file(
                $imgs['tmp_name'][$i],
                __DIR__ . '/../public/img/micros/' . $microId . '.' . $fileType
            );  
        }
    } catch (\Throwable $th) {
        $_SESSION["microForm"]["errorMsg"]=$th->getMessage();
        redirect("/form.php");
    }
    
    redirect("/index.php");
