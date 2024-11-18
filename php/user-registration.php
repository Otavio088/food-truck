<?php
    include("connection.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="icon" href="/foodtruck/img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/user-registration-style.css">
</head>
<body>
    <main>
        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                // Gera um hash seguro da senha
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO login (username, password, role) 
                VALUES (:username, :password, :role)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $passwordHash,
                    ':role' => $role,
                ]);

                echo "Usuário cadastrado com sucesso!";
            }
        ?>
        <div class="box-register">
            <h1>Cadastrar Usuário</h1>    
            <form action="" method="POST">
                <div>
                    <label for="username">Usuário:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div>
                    <label for="password">Senha:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="type-user">
                    <label>Tipo de Usuário:</label>
                    <div>
                        <input type="radio" name="role" value="admin" id="admin" required>
                        <label for="admin">Administrador</label>
                    </div>
                    <div>
                        <input type="radio" name="role" value="deliverier" id="deliverier" required>
                        <label for="deliverier">Entregador</label>
                    </div>
                </div>
                <input type="submit" id="btn-register" value="Cadastrar">
            </form>
            <!-- Usado para redirecionar o navegador para uma nova URL -->
            <button id="btn-back" onclick="window.location.href='/foodtruck/index.php'">Voltar</button>
        </div>
        </main>
</body>
</html>
