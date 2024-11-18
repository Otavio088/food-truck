<?php
    $servername = ""; //nome do servidor (localhost)
    $username = ""; //usuário do banco de dados
    $password = ""; //senha do banco de dados

    try {
        $conn = new PDO("mysql:host=$servername;dbname=foodtruck", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>