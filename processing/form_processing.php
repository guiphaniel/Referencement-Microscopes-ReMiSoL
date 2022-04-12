<?php
    include_once("../model/start_db.php");
    include_once("../model/Microscope.php");
    include_once("../model/Coordinates.php");

    //verify that all fields were sent by the form
    if (!isset($_POST["labName"]) || !isset($_POST["microRef"]) || !isset($_POST["desc"]) || !isset($_POST["lat"]) || !isset($_POST["lon"])) {
        header('location: /form.php');
        exit();
    }

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

