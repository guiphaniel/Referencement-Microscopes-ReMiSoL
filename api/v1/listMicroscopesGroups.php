<?php
    include_once("../../model/services/MicroscopesGroupService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset) TODO: renvoyer la liste des groupes de microscopes (séparer les requetes SQL, et convertir en objets)
            
            $groups = MicroscopesGroupService::getInstance()->getAllMicroscopesGroup();
            
            header('Content-Type: application/json');
            echo json_encode($groups, JSON_PRETTY_PRINT);
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

