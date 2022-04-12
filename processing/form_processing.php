<?php
    include_once("../model/start_db.php");
    include_once("../model/Microscope.php");
    include_once("../model/Coordinates.php");

    //TODO: verify that all fields are sent by form

    $microscope = new Microscope();
    $microscope
        ->setLabName($_POST["labName"])
        ->setRef($_POST["microRef"])
        ->setDesc($_POST["desc"])
        ->setCoor(new Coordinates($_POST["lat"], $_POST["lon"]));

    $sql = $pdo->prepare("INSERT INTO microscopes VALUES (NULL, :serialized)");

    $sql->execute([
        'serialized' => serialize($microscope)
    ]);

    header('location: /index.php');
    exit();

