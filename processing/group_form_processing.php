<?php
    include_once("../config/config.php");
    include_once("../utils/resize_image_proportionnaly.php");
    include_once("../utils/send_email.php");
    include_once("../model/entities/Lab.php");
    include_once("../model/entities/Contact.php");
    include_once("../model/entities/Model.php");
    include_once("../model/entities/Controller.php");
    include_once("../model/entities/MicroscopesGroup.php");
    include_once("../model/services/MicroscopesGroupService.php");
    include_once("../model/services/KeywordService.php");
    
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
        if (empty($micro["compagny"]) || empty($micro["brand"]) || empty($micro["model"]) || empty($micro["controller"]) || empty($micro["descr"]) || empty($micro["type"]) || empty($micro["access"])) {
            redirect("/form.php");
        }
    }

    //if the user is modify an existing group, check he's indeed the owner of it
    if(isset($_POST["id"])) {
        $user = MicroscopesGroupService::getInstance()->findGroupOwnerByGroupId($_POST["id"]);
        if(!isset($user) || !($user->getId() == $_SESSION["user"]["id"] || $user->isAdmin())) {
            $_SESSION["form"]["errorMsg"] = "Vous n'êtes pas autorisé à modifier ce groupe";
            redirect("/form.php");
        }
    }

    try {
        // Convert form values into objects...
        $address = new Address($labAddress["school"], $labAddress["street"], $labAddress["zipCode"], $labAddress["city"], $labAddress["country"]);
        $lab = new Lab($labInfos["name"], $labInfos["type"], $labInfos["code"], $labInfos["website"], $address);
    
        $contacts = [];
        foreach($_POST["contacts"] as $id => $contact) {
            $contacts[] = (new Contact($contact["firstname"], strtoupper($contact["lastname"]), $contact["email"], $contact["phoneCode"], substr($contact["phoneNum"], -9),  ucfirst($contact["role"])))
                ->setId($id);
        }
        
        $group = new MicroscopesGroup(new Coordinates($coorInfos["lat"], $coorInfos["lon"]), $lab, $contacts);

        foreach($_POST["micros"] as $id => $micro) {
            $com = new Compagny($micro["compagny"]);
            $bra = new Brand($micro["brand"], $com);
            $mod = new Model($micro["model"], $bra);
            $ctr = new Controller($micro["controller"], $bra);

            $kws = [];
            foreach($micro["keywords"]??[] as $cat => $tags) {
                foreach($tags as $tag) {
                    $kw = new Keyword(new Category($cat), $tag);
                    $kwId = KeywordService::getInstance()->getKeywordId($kw);

                    if($kwId == -1)
                        throw new Exception("Le mot clé suivant n'est pas pris en charge : catégorie ($cat), étiquette ($tag)");

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
        foreach (array_keys($group->getMicroscopes()) as $microId) { 
            $imgs = $_FILES["imgs"];

            // if no image has been sent, keep it if it hasn't change, or remove it if it exists on the server
            if($imgs['size'][$microId] == 0) {
                if(isset($_POST["keepImg"]) && isset($_POST["keepImg"][$microId]) && $_POST["keepImg"][$microId])
                    continue;

                $existingImgs = glob(__DIR__ . "/../public/img/micros/" . "$microId.*");
                if($existingImgs) {
                    foreach ($existingImgs as $img)
                        unlink($img);
                }
                continue;
            }

            // retrieve the file extension
            $fileType = $imgs['type'][$microId];
            $fileType = substr($fileType, strrpos($fileType, "/") + 1);
            $tmpName = $imgs['tmp_name'][$microId];
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
        if($_SESSION["user"]["admin"])
            MicroscopesGroupService::getInstance()->unlock($groupId);

        redirect("/group-details.php?id=$groupId");
    }

    // else, send an email to all the admins
    $object = "[RéMiSoL] Nouvelle fiche";
    $content = "Bonjour,\n\nUne nouvelle fiche a été créée par {$_SESSION["user"]["firstname"]} {$_SESSION["user"]["lastname"]} ({$_SESSION["user"]["email"]}).\n\nPour l'administrer, suivez le lien suivant : https://" . WEBSITE_URL . "/group-details.php?id=$groupId.";

    foreach (UserService::getInstance()->findAllAdmins() as $admin) {
        sendEmail($admin->getEmail(), $object, $content);
    }
    
    redirect("/index"); //TODO: redirect to user's account