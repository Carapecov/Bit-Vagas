<?php
session_start();
require_once "./config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_SESSION["usuario_id"])) {

        $id_usuario     = $_SESSION["usuario_id"];
        $id_vaga        = $_POST["id_vaga"];
        $nome           = $_POST["nome"];
        $email          = $_POST["email"];
        $idade          = $_POST["idade"];
        $deficiencia    = $_POST["deficiencia"];
        $experiencia    = $_POST["experiencia"];
        $motivo         = $_POST["motivo"];
        $personalidade  = $_POST["personalidade"];
        $linkedin       = $_POST["linkedin"];
        $github         = $_POST["github"];

        if (empty($nome) || empty($email) || empty($motivo)) {
            echo "<p style='color:red;text-align:center;'>Preencha todos os campos obrigatórios.</p>";
        } else {
            $sql = "INSERT INTO candidaturas 
                    (id_usuario, id_vaga, nome, email, idade, deficiencia, experiencia, motivo, personalidade, linkedin, github, data_candidatura)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "iississssss",
                $id_usuario,
                $id_vaga,
                $nome,
                $email,
                $idade,
                $deficiencia,
                $experiencia,
                $motivo,
                $personalidade,
                $linkedin,
                $github
            );

            if ($stmt->execute()) {
                echo "<p style='color:green;text-align:center;'>✅ Candidatura enviada com sucesso!</p>";
            } else {
                echo "<p style='color:red;text-align:center;'>❌ Erro ao enviar candidatura. Tente novamente.</p>";
            }
        }

    } else {
        echo "<p style='color:red;text-align:center;'>Você precisa estar logado para se candidatar.</p>";
    }
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color:red; text-align:center;'>Vaga inválida ou não encontrada.</p>";
    exit;
}

$id_vaga = (int) $_GET['id'];

$sql = "SELECT * FROM vagas_completa WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_vaga);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red; text-align:center;'>Vaga não encontrada.</p>";
    exit;
}

$vaga = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($vaga['empresa']); ?> - Vaga Completa</title>
    <link rel="stylesheet" href="css/candidatar.css">
</head>
<body>
    <header>
        <nav>
            <h3>BitVagas</h3>
            <ul class="header-menu">
                <li><a href="index.html">Home</a></li>
            </ul>
            <?php if (isset($_SESSION["usuario_nome"])): ?>
                
            <?php else: ?>
                <a href="login.php" class="btn-destaque">Cadastre-se</a>
            <?php endif; ?>
        </nav>
    </header>
<main>
    <div class="container">
        <h1><?php echo htmlspecialchars($vaga['titulo_vaga']); ?></h1>
        <div class="vaga-info">
            <div>
                <label>Empresa:</label>
                <p><?php echo htmlspecialchars($vaga['empresa']); ?></p>
            </div>
            <div>
                <label>Linguagem requisitada:</label>
                <p><?php echo htmlspecialchars($vaga['linguagem']); ?></p>
            </div>
            <div>
                <label>Tipo de contrato:</label>
                <p><?php echo htmlspecialchars($vaga['tipo_contrato']); ?></p>
            </div>
            <div>
                <label>Modalidade:</label>
                <p><?php echo htmlspecialchars($vaga['modalidade']); ?></p>
            </div>
            <div>
                <label>Localidade:</label>
                <p><?php echo htmlspecialchars($vaga['localidade']); ?></p>
            </div>
            <div>
                <label>Carga horária:</label>
                <p><?php echo htmlspecialchars($vaga['carga_horaria']); ?></p>
            </div>
            <div>
                <label>Remuneração:</label>
                <p><?php echo $vaga['remunerado'] ? "Sim" : "Não"; ?></p>
            </div>
            <?php if ($vaga['remunerado']): ?>
            <div>
                <label>Salário:</label>
                <p>R$ <?php echo number_format($vaga['salario'], 2, ',', '.'); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="descricao">
            <h2>Sobre a vaga</h2>
            <p><?php echo nl2br(htmlspecialchars($vaga['descricao_vaga'])); ?></p>

            <h2>Requisitos</h2>
            <p><?php echo nl2br(htmlspecialchars($vaga['requisitos'])); ?></p>

            <h2>Benefícios</h2>
            <p><?php echo nl2br(htmlspecialchars($vaga['beneficios'])); ?></p>
        </div>

        <div class="descricao">
            <h2>Informações da empresa</h2>
            <p><strong>Site:</strong> <a href="<?php echo htmlspecialchars($vaga['site_empresa']); ?>" target="_blank"><?php echo htmlspecialchars($vaga['site_empresa']); ?></a></p>
            <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($vaga['email_contato']); ?>"><?php echo htmlspecialchars($vaga['email_contato']); ?></a></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($vaga['telefone']); ?></p>
            <p><strong>Data de publicação:</strong> <?php echo date("d/m/Y H:i", strtotime($vaga['data_postagem'])); ?></p>
        </div>

        <?php if (isset($_SESSION["usuario_id"])): ?>
        <div class="descricao">
            <h2> Entrevista Interativa</h2>
            <form method="POST" action="candidatar.php" class="formulario-candidatura">
                <input type="hidden" name="id_vaga" value="<?php echo $vaga['id']; ?>">

                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">Email para Contato</label>
                <input type="email" id="email" name="email" required>

                <label for="idade">Idade</label>
                <input type="number" id="idade" name="idade" min="15" max="70" required>

                <label for="deficiencia">Possui alguma deficiência?</label>
                <select id="deficiencia" name="deficiencia" required>
                    <option value="">Selecione</option>
                    <option value="Não">Não</option>
                    <option value="Sim">Sim</option>
                </select>

                <label for="experiencia">Você já teve alguma experiência profissional?</label>
                <textarea id="experiencia" name="experiencia" placeholder="Conte sobre seus estágios, freelas ou experiências pessoais com tecnologia..."></textarea>

                <label for="motivo">Por que você se interessou por esta vaga?</label>
                <textarea id="motivo" name="motivo" placeholder="Fale o que te motivou a se candidatar a essa oportunidade..." required></textarea>

                <label for="personalidade">Como você se descreveria em uma frase?</label>
                <input type="text" id="personalidade" name="personalidade" placeholder="Ex: Sou curioso e gosto de resolver problemas complexos.">

                <label for="linkedin">LinkedIn (opcional)</label>
                <input type="url" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/seu-perfil">

                <label for="github">GitHub (opcional)</label>
                <input type="url" id="github" name="github" placeholder="https://github.com/seuusuario">

                <button type="submit">Enviar candidatura</button>
            </form>
        </div>
        <?php else: ?>
            <a href="login.php" class="btn-candidatar">Faça login para se candidatar</a>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 BitVagas - Todos os direitos reservados.</p>
</footer>
</body>
</html>
