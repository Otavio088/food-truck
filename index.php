<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/index-style.css">
</head>
<body>
    <main>
        <div class="box-login">
            <h1>Bem-Vindo</h1>
            <form action="/foodtruck/php/user-validation.php" method="POST">
                <div>
                    <label for="username">Usuário:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div>
                    <label for="password">Senha:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                    <input type="submit" id="btn-enter" value="Entrar">
            </form>
            <p><a href="/foodtruck/php/user-registration.php">cadastrar usuário</a></p>
            <?php
                session_start(); // Sessões são usadas para armazenar informações entre diferentes requisições HTTP. Ligada com a sessão da user-validation
                if (isset($_SESSION['error'])) { // isset verifica se a $_SESSION existe com algum erro vindo nos parâmetros
                    echo "<p id='warn'>{$_SESSION['error']}</p>"; // Se tiver ocorrido algum erro, irá mostrar na tela
                    unset($_SESSION['error']); // Apaga o atributo error da váriavel superglobal
                }
            ?>
        </div>
    </main>
</body>
</html>