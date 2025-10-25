<?php
session_start();
require_once "./config/conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_login = $_POST["tipo_login"] ?? "usuario";
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos!";
    } else {
        if ($tipo_login === "empresa") {
            $sql = "SELECT * FROM empresa WHERE email = ? AND senha = ? LIMIT 1";
        } else {
            $sql = "SELECT * FROM usuarios WHERE (nome = ? OR email = ?) AND senha = ? LIMIT 1";
        }

        $stmt = $conn->prepare($sql);

        if ($tipo_login === "empresa") {
            $stmt->bind_param("ss", $email, $senha);
        } else {
            $stmt->bind_param("sss", $email, $email, $senha);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $row = $resultado->fetch_assoc();

            if ($tipo_login === "empresa") {
                $_SESSION["empresa_id"] = $row["id"];
                $_SESSION["empresa_nome"] = $row["nome"];
                header("Location: painel_empresa.php");
            } else {
                $_SESSION["usuario_id"] = $row["id"];
                $_SESSION["usuario_nome"] = $row["nome"];
                header("Location: vagas.php");
            }
            exit();
        } else {
            $erro = "E-mail ou senha inválidos!";
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
<body class="usuario">
    <header>
        <h2>Login <span>BitVagas</span></h2>
    </header>

    <main>
        <section>
            <article>
                <button id="switchBtn" class="switch-btn">Entrar como Empresa</button>

                <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>

                <form method="POST" class="login-form">
                    <input type="hidden" name="tipo_login" id="tipo_login" value="usuario">

                    <label for="email">Usuário ou Email:</label>
                    <input type="text" id="email" name="email" required>

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>

                    <button type="submit" class="btn">Entrar</button>
                </form>

                <p class="registro">Não tem uma conta? <a href="registro.php">Crie uma aqui</a></p>
            </article>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 BitVagas - Todos os direitos reservados.</p>
    </footer>

    <script>
        const switchBtn = document.getElementById("switchBtn");
        const tipoLogin = document.getElementById("tipo_login");
        const emailLabel = document.querySelector("label[for='email']");
        const body = document.body;
        const article = document.querySelector("article");
        const header = document.querySelector("header");

        let isEmpresa = false;

        switchBtn.addEventListener("click", () => {
            isEmpresa = !isEmpresa;

            if (isEmpresa) {
                tipoLogin.value = "empresa";
                emailLabel.textContent = "Email da Empresa:";
                switchBtn.textContent = "Entrar como Usuário";
                body.classList.remove("usuario");
                body.classList.add("empresa");
                article.classList.add("empresa-ativo");
                header.classList.add("empresa-ativo");
            } else {
                tipoLogin.value = "usuario";
                emailLabel.textContent = "Usuário ou Email:";
                switchBtn.textContent = "Entrar como Empresa";
                body.classList.remove("empresa");
                body.classList.add("usuario");
                article.classList.remove("empresa-ativo");
                header.classList.remove("empresa-ativo");
            }
        });
    </script>
</body>
</html>
