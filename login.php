<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $senha   = $_POST['senha'] ?? '';
    $termos  = isset($_POST['termos']);

    if (empty($usuario) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit();
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
                    <input type="text" id="usuario" name="usuario" required>

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

