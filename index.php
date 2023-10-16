<!DOCTYPE html>
<html>
<head>
    <title>Agenda de Contatos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        h1 {
            background-color: #007bff;
            color: white;
            padding: 20px;
            margin: 0;
            text-align: center;
        }

        h2 {
            margin-top: 20px;
           
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px 0;
        }

        a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }

        a:hover {
            text-decoration: underline;
        }

        form {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            width: 50%;
            margin: 0 auto;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        #incluir{
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Agenda de Contatos</h1>

    <?php
    $conexao = new mysqli("localhost", "root", "", "agendecontatos");

    if ($conexao->connect_error) {
        die("Conexão falhou: " . $conexao->connect_error);
    }

    function listarContatos($conexao) {
        $sql = "SELECT * FROM contatos";
        $result = $conexao->query($sql);

        if ($result->num_rows > 0) {
            echo "<h2>Listagem de Contatos</h2>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                $codigo = $row['código'];
                $nome = $row['nome'];
                $telefone = $row['telefone'];
                $email = $row['e-mail'];

                echo "<li>$nome - $telefone - $email <a href='?acao=editar&codigo=$codigo'>Editar</a> <a href='#' onclick='confirmarExclusao($codigo)'>Excluir</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum contato encontrado.</p>";
        }
    }

    listarContatos($conexao);

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["acao"])) {
        $acao = $_GET["acao"];

        if ($acao == "editar" && isset($_GET["codigo"])) {
            $codigo = $_GET["codigo"];
            $sql = "SELECT * FROM contatos WHERE código = $codigo";
            $result = $conexao->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<h2>Editar Contato</h2>";
                echo "<form method='post' onsubmit='return validarFormulario()'>";
                echo "<input type='hidden' name='codigo' value='$codigo'>";
                echo "Nome: <input type='text' name='nome' value='{$row['nome']}' required><br>";
                echo "Telefone: <input type='text' name='telefone' value='{$row['telefone']}' required><br>";
                echo "E-mail: <input type='text' name='email' value='{$row['e-mail']}' required><br>";
                echo "<input type='submit' value='Salvar Edição'>";
                echo "</form>";
            } else {
                echo "<p>Contato não encontrado.</p>";
            }
        } elseif ($acao == "excluir" && isset($_GET["codigo"])) {
            $codigo = $_GET["codigo"];
            $sql = "DELETE FROM contatos WHERE código = $codigo";
            if ($conexao->query($sql) === TRUE) {
                header("Location: index.php");
                exit;
            } else {
                echo "<p>Erro ao excluir contato: " . $conexao->error . "</p>";
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nome"]) && isset($_POST["email"]) && isset($_POST["telefone"]) && !empty($_POST["nome"]) && !empty($_POST["email"]) && !empty($_POST["telefone"])) {
        $nome = $_POST["nome"];
        $telefone = $_POST["telefone"];
        $email = $_POST["email"];

        if (isset($_POST["codigo"])) {
            $codigo = $_POST["codigo"];
            $sql = "UPDATE contatos SET nome = '$nome', telefone = '$telefone', `e-mail` = '$email' WHERE código = $codigo";
        } else {
            $sql = "INSERT INTO contatos (nome, telefone, `e-mail`) VALUES ('$nome', '$telefone', '$email')";
        }

        if ($conexao->query($sql) === TRUE) {
            if (isset($_POST["codigo"])) {
                echo "<p>Contato atualizado com sucesso.</p>";
            } else {
                echo "<p>Contato inserido com sucesso.</p>";
            }
        } else {
            echo "<p>Erro ao salvar contato: " . $conexao->error . "</p>";
        }

        header("Location: index.php");
        exit;
    }

    $conexao->close();
    ?>
    
   
    <h2 id="incluir">Incluir Contato</h2>
    <form method="post" >
        Nome: <input type="text" name="nome" required><br>
        Telefone: <input type="text" name="telefone" required><br>
        E-mail: <input type="text" name="email" required><br>
        <input type="submit" value="Incluir">
    </form>

    <script>
        function confirmarExclusao(codigo) {
            if (confirm("Tem certeza de que deseja excluir este contato?")) {
               
                window.location.href = '?acao=excluir&codigo=' + codigo;
            }
        }

    
    </script>
</body>
</html>
