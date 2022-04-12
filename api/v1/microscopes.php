<?php
    include_once("../../model/start-db.php");
    include_once("../../model/Microscope.php");

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch($request_method)
    {
        case 'GET':
            //TODO: parametres (limit, offset)
            $serializedMicroscopes = $pdo->query("SELECT serialized FROM microscopes")->fetchAll(PDO::FETCH_NUM);
            
            $microscopes = [];
            foreach ($serializedMicroscopes as $serializedMicroscope) {
                $microscopes[] = unserialize($serializedMicroscope[0]);
            }
            header('Content-Type: application/json');
            echo json_encode($microscopes, JSON_PRETTY_PRINT);
            break;
        default:
        // Requête invalide
        header("HTTP/1.0 405 Method Not Allowed");
        break;
    }

