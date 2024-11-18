<?php
    include('../../connection.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pedidos</title>
    <link rel="icon" href="/foodtruck/img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/header-admin-style.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/register-admin-style.css">
</head>
<body>
    <?php
      include("../header.php");
    ?>
    <main>
        <div class="post-php">
            <?php 
                // Verifica se o valor de 'REQUEST_METHOD' é POST
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Dentro de cada $_POST está o 'name' do html ('name' seria a chave para pegar o valor do campo)
                    $name = $_POST['name'];
                    $phone = $_POST['phone'];
                    $address = $_POST['address'];
                    $itemorder = $_POST['itemorder'];

                    $sql = "INSERT INTO customers(name, phone, address) 
                    VALUES(:name,:phone,:address)"; // Declara Placeholders (variáveis que serão substituídas pelos valores reais) para injeção de dados
                    $stmt = $conn->prepare($sql); //prepara a consulta SQL para ser executada.

                    // Executa a consulta, vincula valores passados no array aos respectivos placeholders na query SQL
                    $stmt->execute([
                        ':name' => $name,
                        ':phone' => $phone,
                        ':address' => $address
                    ]);

                    // Função que pega o último ID inserido, o customer_id que é gerado na hora da inserção
                    $customerId = $conn->lastInsertId();

                    // Faz mapeamento dos itens do select com seus respectivos valores (campo value do banco de dados)
                    $itemValues = [
                        "Hambúrguer" => 25.50,
                        "Pastel" => 20.00,
                        "Batata Frita" => 15.25,
                        "Refrigerante" => 8.00,
                        "Suco" => 6.00,
                        "Sorvete" => 5.50
                    ];

                    // Verifica se o item escolhido existe no array
                    if (array_key_exists($itemorder, $itemValues)) {
                        $value = $itemValues[$itemorder]; // Pega o valor do Item
                    }

                    $sql = "INSERT INTO orders(customer_id, description, value)
                    VALUES(:customer_id,:description,:value)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':customer_id' => $customerId,
                        ':description' => $itemorder,
                        ':value' => $value
                    ]);

                    // Função que pega o último ID inserido, o order_id que é gerado na hora da inserção
                    $orderId = $conn->lastInsertId();

                    $sql = "INSERT INTO deliveries(order_id)
                    VALUES(:order_id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'order_id' => $orderId
                    ]);

                    echo "<p id='msg-php'>Pedido cadastrado com sucesso!</p>";
                }
            ?>
        </div>
        <div class="box-register">
            <h1>Cadastro de Pedidos</h1>
            <form action="" method="POST" onsubmit="return validateForm()"> <!-- A validateForm() ocorre quando o formulário é submetido -->
                <div>
                    <label for="name">Nome do Cliente:</label> 
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="phone">Telefone:</label>
                    <input type="tel" id="phone" name="phone" pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" placeholder="(XX) XXXXX-XXXX"  maxlength="15" required>
                </div>
                <div>
                    <label for="address">Endereço de Entrega:</label>
                    <textarea id="address" name="address" rows="5" placeholder="Rua, Número, Bairro, Cidade" required></textarea>
                </div>
                <div>
                    <label for="itemorder">Itens do Pedido:</label>
                    <select id="itemorder" name="itemorder">
                        <option value="0">Selecione uma opção...</option>
                        <option value="Hambúrguer">Hambúrguer</option>
                        <option value="Pastel">Pastel</option>
                        <option value="Batata Frita">Batata Frita</option>
                        <option value="Refrigerante">Refrigerante</option>
                        <option value="Suco">Suco</option>
                        <option value="Sorvete">Sorvete</option>
                    </select>
                </div>
                    <button type="submit">Cadastrar Pedido</button>
            </form>
        </div>
    </main>
</body>
<script src="/foodtruck/js/admin.js"></script>
</html>