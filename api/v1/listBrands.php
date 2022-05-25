<?php
    include_once("../../model/services/BrandService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)
            if(isset($_GET["compagny"])) {
                $cmp = CompagnyService::getInstance()->findCompagnyByName($_GET["compagny"]);
                $brands = BrandService::getInstance()->getAllBrands($cmp);
            } else
                $brands = BrandService::getInstance()->getAllBrands();            
            
            header('Content-Type: application/json');
            echo json_encode(array_values($brands), JSON_PRETTY_PRINT); // we need to get rid of the keys (which are the ids), else, the json won't be an array but an object
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

