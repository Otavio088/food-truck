<?php
    include('../../connection.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manipulação de Entregas</title>
    <link rel="icon" href="/foodtruck/img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/header-admin-style.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/manipulate-deliveries-admin-style.css">
</head>
<body>
    <?php
        include("../header.php");
    ?>
    <main>
        <div>
            <h1>ENTREGAS</h1>
            <?php
                try {
                    $deliveries = [];

                    if (isset($_POST['search_code'])) { //caso o name "search_code" for enviado pelo método POST
                        $code = $_POST['search_code'];

                        $sql = "SELECT orders.*, customers.*, deliveries.*
                        FROM orders, customers, deliveries
                        WHERE orders.order_id = :order_id
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'entregue'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $code
                        ]);
                        
                        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($deliveries)) {
                            echo "O entrega com o ID $code não foi encontrado!";
                        }
                    } else if (isset($_POST['client_name'])) { //caso o name "client_name" for enviado pelo método POST
                        $client_name = $_POST['client_name'];

                        $sql = "SELECT orders.*, customers.*, deliveries.*
                        FROM orders, customers, deliveries
                        WHERE customers.name LIKE :name
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'entregue'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':name' => '%'.$client_name.'%'
                        ]);
                        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($deliveries)) {
                            echo "A entrega com o nome do cliente aproximado de $client_name não foi encontrado!";
                        }

                    } else if (isset($_POST['delete_code'])) { //caso o name "delete_code" for enviado pelo método POST
                        $delete_code = $_POST['delete_code'];

                        $sql = "SELECT customer_id FROM orders 
                        WHERE order_id = :order_id";
                        $stmt = $conn->prepare($sql);

                        $stmt->execute([
                            ':order_id' => $delete_code
                        ]);
                        
                        $customerId = $stmt->fetchColumn();

                        $sql = "SELECT status FROM deliveries
                        WHERE order_id = :order_id
                        AND status = 'entregue'";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $delete_code
                        ]);

                        $status = $stmt->fetchColumn();

                        if ($customerId && $status) {
                            $sql = "DELETE FROM deliveries WHERE order_id = :order_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([':order_id' => $delete_code]);

                            $sql = "DELETE FROM orders WHERE order_id = :order_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([':order_id' => $delete_code]);

                            $sql = "DELETE FROM customers WHERE customer_id = :customer_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([':customer_id' => $customerId]);

                            echo "Pedido excluído com sucesso!";
                        } else {
                            echo "O pedido com o Código $delete_code não foi encontrado!";
                        }
                    } else { //caso nenhum botão seja clicado, listar todos
                        $sql = "SELECT orders.*, customers.*, deliveries.*
                        FROM orders, customers, deliveries
                        WHERE customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'entregue'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute();

                        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($deliveries)) {
                            echo "Nenhum registro de entrega encontrado!";
                        }
                    }

                    if (!empty($deliveries)) { //mostra a tabela se houver algum registro
                        echo "<table border='1'>";
                        echo "<tr>
                                <th>CODIGO</th>
                                <th>PEDIDO</th>
                                <th>VALOR</th>
                                <th>CLIENTE</th>
                                <th>TELEFONE</th>
                                <th>ENDEREÇO</th>
                                <th>DATA ENTREGA</th>
                            </tr>";

                        foreach ($deliveries as $delivery) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($delivery['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['value']) . "</td>";
                            echo "<td>" . htmlspecialchars($delivery['name']) . "</td>";
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

            <form method="POST">
                <label for="delete_code">Codigo:</label>
                <input type="text" name="delete_code" id="delete_code" required>
                <button type='submit' style="background-color: #d84444;">DELETAR</button>    
            </form>
        </div>
    </main>
</body>
</html>