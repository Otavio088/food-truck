<?php
    include('../../connection.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manipulação de Pedidos</title>
    <link rel="icon" href="/foodtruck/img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/header-admin-style.css">
    <link rel="stylesheet" type="text/css" href="/foodtruck/css/manipulate-orders-admin-style.css">
</head>
<body>
    <?php
        include("../header.php");
    ?>
    <main>
        <div class="tables">
            <h1>PEDIDOS</h1>
            <?php
                try { 
                    $orders = [];

                    if (isset($_POST['search_code'])) { //caso o name "search_code" for enviado pelo método POST
                        $code = $_POST['search_code'];

                        $sql = "SELECT orders.*, customers.*
                        FROM orders, customers, deliveries
                        WHERE orders.order_id = :order_id
                        AND customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'pendente'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $code
                        ]);
                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC); //pega todos os valores

                        if (empty($orders)) {
                            echo "O pedido com o Código " . $code . " não foi encontrado!";
                        }
                    } else if (isset($_POST['client_name'])) { //caso o name "client_name" for enviado pelo método POST
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
                            echo "Pedidos com o nome aproximado de cliente " . $client_name . " não foi encontrado!";
                        }
                    } else if (isset($_POST['delete_code'])) { //caso o name "delete_code" for enviado pelo método POST
                        $delete_code = $_POST['delete_code'];

                        $sql = "SELECT customer_id FROM orders 
                        WHERE order_id = :order_id"; 

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $delete_code
                        ]);
                        
                        $customerId = $stmt->fetchColumn(); // Pega uma única coluna de uma linha no conjunto de resultados. Pega só o id no caso

                        $sql = "SELECT status FROM deliveries
                        WHERE order_id = :order_id
                        AND status = 'pendente'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $delete_code
                        ]);

                        $status = $stmt->fetch();

                        if ($customerId && $status) {
                            $sql = "DELETE FROM deliveries 
                            WHERE order_id = :order_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([':order_id' => $delete_code]);

                            $sql = "DELETE FROM orders 
                            WHERE order_id = :order_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([':order_id' => $delete_code]);

                            $sql = "DELETE FROM customers 
                            WHERE customer_id = :customer_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([':customer_id' => $customerId]);

                            echo "Pedido excluído com sucesso!";
                        } else {
                            echo "O pedido com o Código $delete_code não foi encontrado!";
                        }
                    } else if(isset($_POST['code_edit'])) { //caso o name "code_edit" for enviado pelo método POST
                        $code = $_POST['code_edit'];
                        $name = $_POST['name_edit'] ?? null; // atribui null se não tiver sido enviado nada no lugar de "$_POST['name_edit']" (Operador de Coalescência Nula) 
                        $phone = $_POST['phone_edit'] ?? null;
                        $address = $_POST['address_edit'] ?? null;
                        $itemorder = $_POST['itemorder_edit'];

                        if ($itemorder == "0") {
                            $itemorder = null;
                        }

                        $sql = "SELECT * FROM orders 
                        WHERE order_id = :order_id";

                        $stmt = $conn->prepare($sql); 
                        $stmt->execute([
                            ':order_id' => $code
                        ]);

                        $order = $stmt->fetch();

                        $sql = "SELECT status FROM deliveries
                        WHERE order_id = :order_id
                        AND status = 'pendente'";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([':order_id' => $code]);

                        $status = $stmt->fetchColumn();

                        if ($order && $status) {
                            // Array para armazenar partes da consulta e parâmetros
                            $fieldsToUpdateCustomer = [];
                            $fieldsToUpdateOrder = [];
                            $paramsCustomer = [];
                            $paramsOrder = [];
                            $paramsOrder[':order_id'] = $code;

                            // Adiciona apenas os campos que foram preenchidos
                            if (!empty($name)) {
                                $fieldsToUpdateCustomer[] = "name = :name";
                                $paramsCustomer[':name'] = $name;
                            }
                            if (!empty($phone)) {
                                $fieldsToUpdateCustomer[] = "phone = :phone";
                                $paramsCustomer[':phone'] = $phone;
                            }
                            if (!empty($address)) {
                                $fieldsToUpdateCustomer[] = "address = :address";
                                $paramsCustomer[':address'] = $address;
                            }
                            if (!empty($itemorder)) {
                                $fieldsToUpdateOrder[] = "description = :itemorder";
                                $paramsOrder[':itemorder'] = $itemorder;

                                $itemValues = [
                                    "Hambúrguer" => 25.50,
                                    "Pastel" => 20.00,
                                    "Batata Frita" => 15.25,
                                    "Refrigerante" => 8.00,
                                    "Suco" => 6.00,
                                    "Sorvete" => 5.50
                                ];

                                if (array_key_exists($itemorder, $itemValues)) {
                                    $value = $itemValues[$itemorder];
                                    $paramsOrder[':value'] = $value;
                                    $fieldsToUpdateOrder[] = "value = :value";
                                }
                            }

                             // Verifica se há campos a atualizar
                            if (!empty($fieldsToUpdateCustomer)) {
                                $sql = "SELECT customer_id 
                                FROM orders 
                                WHERE order_id = :order_id";
                                
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([
                                    ':order_id' => $code
                                ]);
                                
                                $customerId = $stmt->fetchColumn();

                                if ($customerId) { //implode() converte um array em uma string, unindo os elementos do array por um delimitador específico. ("separador", "array cujos elementos serão concatenados")
                                    $sql = "UPDATE customers 
                                    SET ".implode(", ", $fieldsToUpdateCustomer)." 
                                    WHERE customer_id = $customerId";

                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute($paramsCustomer); //executa de forma diferente. Envia tudo de uma vez por que ja ocorreu os parametros
                                }

                            } 
                            if (!empty($fieldsToUpdateOrder)) {
                                $sql = "UPDATE orders 
                                SET ".implode(", ", $fieldsToUpdateOrder)." 
                                WHERE order_id = :order_id";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute($paramsOrder);
                            } 

                            $sql = "SELECT orders.*, customers.*
                            FROM orders, customers, deliveries
                            WHERE customers.customer_id = orders.customer_id
                            AND orders.order_id = :order_id
                            AND deliveries.status = 'pendente'
                            LIMIT 1"; // Garante que apenas um registro será retornado

                            $stmt = $conn->prepare($sql);
                            $stmt->execute([
                                ':order_id' => $code
                            ]);
                            
                            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC); 
                            
                            if (!empty($fieldsToUpdateCustomer) || !empty($fieldsToUpdateOrder)) {
                                echo "Pedido atualizado com sucesso!";
                            }

                        } else {
                            echo "O pedido com o Código $delete_code não foi encontrado! Não será feita a edição!";
                        }
                    } else { //caso nenhum botão seja clicado, listar todos
                        //consulta todos os dados ta tabela de prdidos e clientes onde os ids são iguais e onde o status de entrega é pendente
                        $sql = "SELECT orders.*, customers.*
                        FROM orders, customers, deliveries
                        WHERE customers.customer_id = orders.customer_id
                        AND orders.order_id = deliveries.order_id
                        AND deliveries.status = 'pendente'";

                        $stmt=$conn->prepare($sql);
                        $stmt->execute(); //executa tudo

                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                
                    // Mostra o cabeçalho da tabela se o array não estiver vazio
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
                        
                        // Mostra celula por celula dos Pedidos
                        foreach ($orders as $order) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>"; //converte caracteres especiais para a forma codificada no HTML
                            echo "<td>" . htmlspecialchars($order['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['value']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['address']) . "</td>";
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
            <div>
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
            <div>
                <form action="" method="POST">
                    <label for="code_edit">Código:</label>
                    <input type="text" name="code_edit" id="code_edit" required>
                    
                    <label for="name_edit">Nome do Cliente:</label> 
                    <input type="text" id="name_edit" name="name_edit">

                    <label for="phone_edit">Telefone:</label>
                    <input type="tel" id="phone" name="phone_edit" pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" placeholder="(XX) XXXXX-XXXX"  maxlength="15">
                            
                    <label for="address_edit">Endereço de Entrega:</label>
                    <textarea id="address_edit" name="address_edit" rows="3" placeholder="Rua, Número, Bairro, Cidade"></textarea>

                    <label for="itemorder_edit">Itens do Pedido:</label>
                    <select id="itemorder" name="itemorder_edit">
                        <option value="0">Selecione uma opção...</option>
                        <option value="Hambúrguer">Hambúrguer</option>
                        <option value="Pastel">Pastel</option>
                        <option value="Batata Frita">Batata Frita</option>
                        <option value="Refrigerante">Refrigerante</option>
                        <option value="Suco">Suco</option>
                        <option value="Sorvete">Sorvete</option>
                    </select>    
                    <button type="submit">EDITAR</button>
                </div>
            </form>
        </div>
    </main>
</body>
<script src="/foodtruck/js/admin.js"></script>
</html>