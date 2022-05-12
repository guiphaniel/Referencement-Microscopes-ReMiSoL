<?php
    include_once("../include/config.php");
    include_once("../utils/resize_image_proportionnaly.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/Model.php");
    include_once("../model/entities/Controller.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopesGroupService.php");
    
    if(!isUserSessionValid()) 
        redirect("/index.php");

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
        if (empty($contact["firstname"]) || empty($contact["lastname"]) || empty($contact["role"]) || empty($contact["email"]) || empty($contact["phoneCode"]) || empty($contact["phoneNum"])) {
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
        $address = new Address($labAddress["school"], $labAddress["street"], $labAddress["zipCode"], $labAddress["city"], $labAddress["country"]);
        $lab = new Lab($labInfos["name"], $labInfos["type"], $labInfos["code"], $labInfos["website"], $address);
    
        $contacts = [];
        foreach($_POST["contacts"] as $contact) {
            $contacts[] = new Contact($contact["firstname"], strtoupper($contact["lastname"]),  ucfirst($contact["role"]), $contact["email"], $contact["phoneCode"], substr($contact["phoneNum"], -9));
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
        MicroscopesGroupService::getInstance()->save($group);

        //save the micros' imgs
        $nbImgs = count($_FILES['imgs']['name']);
        if($nbImgs > count($_POST["micros"]))
            throw new Exception("Vous ne pouvez envoyer qu'une seule image par microscope au maximum.");

        for ($i=0; $i < $nbImgs; $i++) { 
            $microId = $group->getMicroscopes()[$i]->getId();
            $imgs = $_FILES["imgs"];
            // retrieve the file extension
            $fileType = $imgs['type'][$i];
            $fileType = substr($fileType, strrpos($fileType, "/") + 1);
            $tmpName = $imgs['tmp_name'][$i];
            $image;
            switch ($fileType) {
                case "png":
                    $image = imagecreatefrompng($tmpName);
                    break;
                case "jpg":
                case "jpeg":
                    $image = imagecreatefromjpeg($tmpName);
                    break;
                case "webp":
                    $image = imagecreatefromwebp($tmpName);
                    break;
                default:
                    throw("Le format d'image fourni n'est pas supporté");
            }

            $image = resizeImageProportionnaly($image, 1280, 720);

            // save the image
            imagejpeg(
                $image,
                __DIR__ . '/../public/img/micros/' . $microId . '.jpg'
            );  

            imagewebp(
                $image,
                __DIR__ . '/../public/img/micros/' . $microId . '.webp'
            ); 
        }
    } catch (\Throwable $th) {
        $_SESSION["form"]["errorMsg"]=$th->getMessage();
        redirect("/form.php");
    }
    
    redirect("/index.php");