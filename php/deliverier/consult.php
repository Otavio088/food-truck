<?php
    include('../connection.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Entregas</title>
    <link rel="icon" href="/foodtruck/img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/header-deliverier-style.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/consult-deliverier-style.css">
</head>
<body>
    <?php
        include("header.php");
    ?>
    <main>
        <h1>ENTREGAS</h1>
        <div class="table-wrapper">
            <?php
                try { 
                    $deliveries = [];

                    if (isset($_POST['search_code'])) { //verifica se a chave "codigo busca" existe no método post.
                        $code = $_POST['search_code'];

                        $sql = "SELECT *
                        FROM orders, customers, deliveries
                        WHERE orders.order_id = :order_id
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'entregue'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([':order_id' => $code]);
                        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC); //pega todos os valores

                        if (empty($deliveries)) {
                            echo "A entrega com o Código $code não foi encontrado!";
                        }
                    } else if (isset($_POST['client_name'])) { //verifica se a chave "nome do cliente" existe no método post.
                        $client_name = $_POST['client_name'];

                        $sql = "SELECT *
                        FROM orders, customers, deliveries
                        WHERE customers.name LIKE :name
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'entregue'";

                        $stmt=$conn->prepare($sql);
                        $stmt->execute(
                            [':name' => '%'.$client_name.'%']
                        );
                        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC); //pega todos os valores

                        if (empty($deliveries)) {
                            echo "A entrega com o nome aproximado do cliente $client_name não foi encontrado!";
                        }
                    } else {
                        //consulta todos os dados ta tabela de prdidos e clientes onde os ids são iguais e onde o status de entrega é pendente
                        $sql = "SELECT *
                        FROM orders, customers, deliveries
                        WHERE customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'entregue'";

                        $stmt=$conn->prepare($sql); //prepara a consulta
                        $stmt->execute(); //executa tudo
                        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC); //pega todos os valores
                    }

                    if (!empty($deliveries)) {
                        echo "<table border='1'>";
                        echo "<tr>
                                <th>CODIGO</th>
                                <th>CLIENTE</th>
                                <th>PEDIDO</th>
                                <th>VALOR</th>
                                <th>TELEFONE</th>
                                <th>ENDEREÇO</th>
                                <th>DATA DE ENTREGA</th>
                            </tr>";
            
                        foreach ($deliveries as $delivery) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($delivery['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['value']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['phone']) . "</td>"; 
                            echo "<td>" . htmlspecialchars($delivery['address']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['delivery_date']) . "</td>";
                            echo "</tr>";
                        }
            
                        echo "</table>";
                    }
                } catch (PDOException $e) {
                    echo "Erro: ".$e->getMessage();
                }
            ?>
        </div>
        <div class="forms">
            <form method="POST">
                <button type='submit'>LISTAR TUDO</button>    
            </form>

            <form method="POST">
                <label for="search_code">Código:</label>
                <input type="text" name="search_code" id="search_code" required>
                <button type='submit'>BUSCAR</button>    
            </form>

            <form method="POST">
                <label for="client_name">Nome:</label>
                <input type="text" name="client_name" id="client_name" required>
                <button type='submit'>BUSCAR</button>    
            </form>
        </div>
    </main>
</body>
</html>