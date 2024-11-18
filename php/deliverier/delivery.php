<?php
    include('../connection.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Entregas</title>
    <link rel="icon" href="/foodtruck/img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/header-deliverier-style.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/delivery-deliverier-style.css">
</head>
<body>
    <?php
        include("header.php");
    ?>
    <main>
        <h1>PEDIDOS</h1>
        <div class="table-wrapper">
            <?php
                try { 
                    $orders = [];

                    if (isset($_POST['search_code'])) { //verifica se a chave "codigo busca" existe no método post.
                        $code = $_POST['search_code'];

                        $sql = "SELECT orders.*, customers.*
                        FROM orders, customers, deliveries
                        WHERE orders.order_id = :order_id
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'pendente'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([':order_id' => $code]);
                        
                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC); 

                        if (empty($orders)) {
                            echo "O pedido com o Código $code não foi encontrado!";
                        }
                    } else if (isset($_POST['client_name'])) { //verifica se a chave "nome do cliente" existe no método post.
                        $client_name = $_POST['client_name'];

                        $sql = "SELECT orders.*, customers.*
                        FROM orders, customers, deliveries
                        WHERE customers.name LIKE :name
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'pendente'";

                        $stmt=$conn->prepare($sql);
                        $stmt->execute([
                            ':name' => '%'.$client_name.'%'
                        ]);

                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($orders)) {
                            echo "O pedido com o nome aproximado do cliente $client_name não foi encontrado!";
                        }
                    } else if (isset($_POST['delivered_code'])) { //verifica se a chave "entregue codigo" existe no método post.
                        $delivered_code = $_POST['delivered_code'];

                        $sql = "UPDATE deliveries 
                                SET status = 'entregue', delivery_date = NOW() 
                                WHERE order_id = :order_id 
                                AND status = 'pendente'"; //NOW() pega a data atual

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $delivered_code
                        ]);

                        if ($stmt) {
                            echo "Pedido marcado como entregue!";
                        } else {
                            echo "Código não encontrado ou pedido já entregue.";
                        }
                    } else {
                        //consulta todos os dados ta tabela de pedidos e clientes onde os ids são iguais e onde o status de entrega é pendente
                        $sql = "SELECT orders.*, customers.*
                        FROM orders, customers, deliveries
                        WHERE customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'pendente'";

                        $stmt=$conn->prepare($sql); //prepara a consulta
                        $stmt->execute(); //executa tudo
                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC); //pega todos os valores
                    }

                    if (!empty($orders)) {
                        echo "<table border='1'>";
                        echo "<tr>
                                <th>CODIGO</th>
                                <th>PEDIDO</th>
                                <th>VALOR</th>
                                <th>CLIENTE</th>
                                <th>TELEFONE</th>
                                <th>ENDEREÇO</th>
                            </tr>";
            
                        foreach ($orders as $order) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['value']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['phone']) . "</td>";                                echo "<td>" . htmlspecialchars($order['address']) . "</td>";
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

            <form method="POST">
                <label for="delivered_code">Codigo:</label>
                <input type="text" name="delivered_code" id="delivered_code" required>
                <button type='submit' style="background-color: #6fca5d;">MARCAR COMO ENTREGUE</button>    
            </form>

        </div>
    </main>
</body>
</html>