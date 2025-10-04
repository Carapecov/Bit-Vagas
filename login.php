<?php
session_start();

require_once "./config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_email = trim($_POST["usuario_email"]);
    $senha = trim($_POST["senha"]);

    if (empty($usuario_email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        $sql = "SELECT * FROM usuarios 
                WHERE (nome = ? OR email = ?) 
                AND senha = ? 
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario_email, $usuario_email, $senha);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $row = $resultado->fetch_assoc();

            $_SESSION["usuario_id"] = $row["id"];
            $_SESSION["usuario_nome"] = $row["nome"];

            header("Location: vagas.php");
            exit();
        } else {
            $erro = "Usuário/E-mail ou senha inválidos!";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - BitVagas</title>
    <link rel="stylesheet" href="css/loginStyle.css">
</head>
<body>
    <header>
        <h2>Login <span>Bitvagas</span></h2>
    </header>

    <main>
        <section>
            <article>
                <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>

                <form method="POST" class="login-form">
                    <label for="usuario">Usuário ou Email:</label>
                    <input type="text" id="usuario" name="usuario_email" required>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>

                    <button type="submit" class="btn">Entrar</button>
                </form>

                <p class="registro">Não tem uma conta? <a href="registro.php">Crie uma aqui</a></p>
            </article>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Portal de Estágio - Todos os direitos reservados.</p>
    </footer>
</body>
</html>
