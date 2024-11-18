<?php
    session_start(); //inicia a sessão
    include("connection.php");

    // Captura os dados do formulário de login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta o banco de dados para obter o hash da senha com base no nome de usuário
    $sql = "SELECT * FROM login 
    WHERE username = :username";

    // Prepara a consulta e atribui $username para o parâmetro na hora de executar
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username' => $username
    ]);

    // fetch usado para recuperar uma única linha de um conjunto de resultados da consulta SQL
    // PDO::FETCH_ASSOC especifica o formato do dado retornado. Linha retornada como um array associativo. Itens formado por chave/valor
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o nome de usuário e a senha existem
    if ($user && password_verify($password, $user['password'])) { // Verifica se a senha digitada corresponde ao hash armazenado no banco de dados
        // Se o papel na empresa é admin
        if ($user['role'] === "admin") {
            $_SESSION['username'] = $username; // Atribui a sessão o nome
            header("Location: /foodtruck/php/admin/home.php"); // header envia um cabeçalho HTTP bruto, que monta o cabeçalho de resposta HTTP que será enviado para o browser. Local a ser enviado é a home
            exit(); // Sai da página
        } 
        
        // Se o papel na empresa é entregador
        if ($user['role'] === "deliverier") {
            $_SESSION['username'] = $username;
            header("Location: /foodtruck/php/deliverier/home.php");
            exit();
        }
    } else {
        // Em caso de falha, define uma mensagem de erro e redireciona para o login de novo
        $_SESSION['error'] = "Nome de usuário ou senha incorretos!"; // Atribui mensagem para o 'error' de $_SESSION
        header("Location: /foodtruck/index.php");
        exit();
    }
?>