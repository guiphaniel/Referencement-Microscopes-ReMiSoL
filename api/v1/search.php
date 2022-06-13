<?php
    include("../../model/services/MicroscopesGroupService.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)
            if(!isset($_GET["filters"]))
                $filters = [];
            else if(is_array($_GET["filters"]))
                $filters = $_GET["filters"];
            else 
                $filters =  explode(" ", $_GET["filters"]);

            $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroup(false, $filters);

            header('Content-Type: application/json');
            echo json_encode(array_values($groups), JSON_PRETTY_PRINT); // we need to get rid of the keys (which are the ids), else, the json won't be an array but an object
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }


    