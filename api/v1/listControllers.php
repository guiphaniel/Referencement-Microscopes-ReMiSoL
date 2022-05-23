<?php
    include_once("../../model/services/ControllerService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)            
            if(isset($_GET["brand"])) {
                $brand = BrandService::getInstance()->findBrandByName($_GET["brand"]);
                $controllers = ControllerService::getInstance()->findAllControllersByBrand($brand);
            }
            else
                $controllers = ControllerService::getInstance()->findAllControllers();
            
            header('Content-Type: application/json');
            echo json_encode(array_values($controllers), JSON_PRETTY_PRINT); // we need to get rid of the keys (which are the ids), else, the json won't be an array but an object
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }
