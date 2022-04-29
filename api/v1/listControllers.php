<?php
    include_once("../../model/services/ControllerService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)
            //TODO: if no controller is provided, return all controllers -> change the behavior of the ControllerService::getAllControllers method, with optional parameter
            if(!isset($_GET["compagny"])) {
                echo "You must provide a compagny name : ?compagny=compagnyName";
                die();
            }
            
            if(!isset($_GET["brand"])) {
                echo "You must provide a brand name : &brand=brandName";
                die();
            }

            $controllers = ControllerService::getInstance()->getAllControllers(new Brand($_GET["brand"], new Compagny($_GET["compagny"])));
            
            header('Content-Type: application/json');
            echo json_encode($controllers, JSON_PRETTY_PRINT);
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }
