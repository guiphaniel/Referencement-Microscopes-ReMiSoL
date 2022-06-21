<?php
    include_once("../../model/services/CompagnyService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)
            $cmps = CompagnyService::getInstance()->findAllCompagnies();
            
            header('Content-Type: application/json');
            echo json_encode(array_values($cmps), JSON_PRETTY_PRINT); // we need to get rid of the keys (which are the ids), else, the json won't be an array but an object
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

