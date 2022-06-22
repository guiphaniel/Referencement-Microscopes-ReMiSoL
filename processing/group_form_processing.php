<?php
    include_once("../config/config.php");
    include_once("../utils/resize_image_proportionnaly.php");
    include_once("../utils/send_email.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/ModelService.php");
    include_once("../model/services/ControllerService.php");
    include_once("../model/services/MicroscopesGroupService.php");
    include_once("../model/services/KeywordService.php");
    
    if(!isUserSessionValid()) 
        redirect("/index.php");

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["lab"]) || empty($_POST["coor"]) || empty($_POST["contacts"]) || empty($_POST["micros"])) {       
        redirect("/form.php");
    }

    $labInfos = $_POST["lab"];
    if(empty($labInfos["name"]) || empty($labInfos["type"]) || empty($labInfos["address"]) || empty($labInfos["website"])) {     
        redirect("/form.php");
    }

    if($labInfos["type"] != "Autre" && empty($labInfos["code"])) {
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
        if (empty($micro["compagny"]) || empty($micro["brand"]) || empty($micro["model"]) || empty($micro["controller"]) || empty($micro["descr"]) || strlen($micro["descr"]) > 2000 || empty($micro["type"]) || empty($micro["access"])) {
            redirect("/form.php");
        }
    }

    //if the user is modify an existing group, check he's indeed the owner of it or an admin
    if(isset($_POST["id"])) {
        $user = MicroscopesGroupService::getInstance()->findGroupOwnerByGroupId($_POST["id"]);
        if(!isset($user) || !($user->getId() == $_SESSION["user"]["id"] || $_SESSION["user"]["admin"])) {
            $_SESSION["form"]["errorMsg"] = "Vous n'êtes pas autorisé à modifier ce groupe";
            redirect("/form.php");
        }
    }

    try {
        // Convert form values into objects...
        $address = new Address($labAddress["school"], $labAddress["street"], $labAddress["zipCode"], $labAddress["city"], $labAddress["country"]);
        $lab = new Lab($labInfos["name"], $labInfos["type"], $labInfos["code"]??null, $labInfos["website"], $address);
    
        $contacts = [];
        foreach($_POST["contacts"] as $id => $contact) {
            $contacts[] = (new Contact($contact["firstname"], strtoupper($contact["lastname"]), $contact["email"], $contact["phoneCode"], substr($contact["phoneNum"], -9),  ucfirst($contact["role"])))
                ->setId($id);
        }
        
        $group = new MicroscopesGroup(new Coordinates($coorInfos["lat"], $coorInfos["lon"]), $lab, $contacts);

        foreach($_POST["micros"] as $id => $micro) {
            $cmp = CompagnyService::getInstance()->findCompagnyByName($micro["compagny"]);

            $bra = BrandService::getInstance()->findBrandByName($micro["brand"]);

            $mod = ModelService::getInstance()->findModelByName($micro["model"]);

            $ctr = ControllerService::getInstance()->findControllerByName($micro["controller"]);

            //check material validity
            if($cmp === null || !in_array($cmp, CompagnyService::getInstance()->findAllCompagnies()))
                throw new Exception("La société suivante n'est pas pris en charge : {$micro["compagny"]}.");

            if($labAddress === null || !in_array($bra, BrandService::getInstance()->findAllBrands($cmp)))
                throw new Exception("La marque suivante n'est pas pris en charge : Société : {$micro["compagny"]} ; Marque : {$micro["brand"]}.");

            if($mod === null || !in_array($mod, ModelService::getInstance()->findAllModels($bra)))
                throw new Exception("Le modèle suivant n'est pas pris en charge : Société : {$micro["compagny"]} ; Marque : {$micro["brand"]} ; Modèle : {$micro["model"]}.");

            if($ctr === null || !in_array($ctr, ControllerService::getInstance()->findAllControllers($bra)))
                throw new Exception("L'électronique suivante n'est pas pris en charge : Société : {$micro["compagny"]} ; Marque : {$micro["brand"]} ; Électronique : {$micro["controller"]}.");

            $kws = [];
            foreach($micro["keywords"]??[] as $cat => $tags) {
                foreach($tags as $tag) {
                    $kw = new Keyword(new Category($cat), $tag);
                    $kwId = KeywordService::getInstance()->getKeywordId($kw);

                    if($kwId == -1)
                        throw new Exception("Le mot clé suivant n'est pas pris en charge : catégorie ($cat), étiquette ($tag).");

                    $kws[] = $kw->setId($kwId);
                }
            }

            $group->addMicroscope((new Microscope($mod, $ctr, $micro["rate"]??null, $micro["descr"], $micro["type"], $micro["access"], $kws))->setId($id));
        }
            
        // ...and save/update the group into the db
        $microscopesGroupService = MicroscopesGroupService::getInstance();
        if(isset($_POST["id"])) {
            $oldGroup = $microscopesGroupService->findMicroscopesGroupById($_POST["id"]);
            $microscopesGroupService->update($oldGroup, $group);
            $groupId = $_POST["id"];
        } else {
            $groupId = $microscopesGroupService->save($group);
            $microscopesGroupService->lock($group);
        }

        //save the micros' imgs
        foreach ($group->getMicroscopes() as $formMicroId => $micro) { 
            $microId = $micro->getId();
            $imgs = $_FILES["imgs"];

            // if no image has been sent, keep it if it hasn't change, or remove it if it exists on the server
            if($imgs['size'][$formMicroId] == 0) {
                if(isset($_POST["keepImg"]) && isset($_POST["keepImg"][$formMicroId]) && $_POST["keepImg"][$formMicroId])
                    continue;

                $existingImgs = glob(__DIR__ . "/../public/img/micros/" . "$microId.*");
                if($existingImgs) {
                    foreach ($existingImgs as $img)
                        unlink($img);
                }
                continue;
            }

            // retrieve the file extension
            $tmpName = $imgs['tmp_name'][$formMicroId];
            $image;
            switch (exif_imagetype($tmpName)) {
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($tmpName);
                    break;
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($tmpName);
                    break;
                case IMAGETYPE_WEBP:
                    $image = imagecreatefromwebp($tmpName);
                    break;
                default:
                    throw new Exception("Le format d'image fourni n'est pas supporté. Votre fiche a été soumise sans image.");
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
        
        if(isset($_POST["id"])) {
            $url = "/edit_micros_group.php?id=" . $_POST["id"];
        } else
            $url = "/form.php";
        redirect($url);
    }

    // if the group has been updated...
    if(isset($_POST["id"])) {
        $groupId = $_POST["id"];
        // ...and edited by an admin, unlock the group
        $microscopesGroupService = MicroscopesGroupService::getInstance();
        if($_SESSION["user"]["admin"])
            $microscopesGroupService->unlock($groupId);

        if($microscopesGroupService->isLocked($groupId))
            redirect("/account.php");
        else
            redirect("/group-details.php?id=$groupId");
    }

    // else, send an email to all the admins
    $subject = "[RéMiSoL] Nouvelle fiche";
    $content = "Bonjour,\n\nUne nouvelle fiche a été créée par {$_SESSION["user"]["firstname"]} {$_SESSION["user"]["lastname"]} ({$_SESSION["user"]["email"]}).\n\nPour l'administrer, suivez le lien suivant : https://" . WEBSITE_URL . "/group-details.php?id=$groupId.";

    sendEmail(WEB_MASTER_EMAIL, $subject, $content);
    
    redirect("/account.php");