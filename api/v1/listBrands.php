<?php
    include_once("../../model/services/BrandService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset) TODO: renvoyer la liste des groupes de microscopes (séparer les requetes SQL, et convertir en objets)
            //TODO: if no compagny is provided, return all compagnies -> change the behavior of the BrandService::getAllBrands method, with optional parameter
            if(!isset($_GET["compagny"])) {
                echo "You must provide a compagny name : ?compagny=compagnyName";
                die();
            }

            $brands = BrandService::getInstance()->getAllBrands(new Compagny($_GET["compagny"]));
            
            header('Content-Type: application/json');
            echo json_encode($brands, JSON_PRETTY_PRINT);
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

