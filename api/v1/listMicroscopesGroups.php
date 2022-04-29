<?php
    include_once("../../model/services/MicroscopesGroupService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)
            
            $groups = MicroscopesGroupService::getInstance()->getAllMicroscopesGroup();
            
            header('Content-Type: application/json');
            echo json_encode($groups, JSON_PRETTY_PRINT);
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

