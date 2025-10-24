<?php
session_start();
require_once "./config/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["usuario_id"];
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["atualizar"])) {
    function limpar($dado) { return htmlspecialchars(trim($dado)); }

    $campos = [
        "nome", "cpf", "rg", "data_nascimento", "genero", "estado_civil",
        "endereco", "telefone", "email", "instituicao", "curso", "periodo", "turno",
        "matricula", "conclusao", "experiencia", "cursos_complementares", "idiomas",
        "competencias_tecnicas", "competencias_comportamentais", "area_interesse",
        "modalidade", "carga_horaria", "pretensao_bolsa", "cidade_preferencia"
    ];

    foreach ($campos as $campo) {
        $$campo = limpar($_POST[$campo] ?? "");
    }

    $sql = "UPDATE usuarios SET 
        nome=?, cpf=?, rg=?, data_nascimento=?, genero=?, estado_civil=?, endereco=?, telefone=?, email=?,
        instituicao=?, curso=?, periodo=?, turno=?, matricula=?, conclusao=?, experiencia=?, cursos_complementares=?,
        idiomas=?, competencias_tecnicas=?, competencias_comportamentais=?, area_interesse=?, modalidade=?,
        carga_horaria=?, pretensao_bolsa=?, cidade_preferencia=?
        WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssssssssssssssssssi",
        $nome, $cpf, $rg, $data_nascimento, $genero, $estado_civil, $endereco, $telefone, $email,
        $instituicao, $curso, $periodo, $turno, $matricula, $conclusao, $experiencia, $cursos_complementares,
        $idiomas, $competencias_tecnicas, $competencias_comportamentais, $area_interesse, $modalidade,
        $carga_horaria, $pretensao_bolsa, $cidade_preferencia, $id_usuario
    );

    if ($stmt->execute()) {
        $mensagem = "<p style='color:green;text-align:center;'>✅ Perfil atualizado com sucesso!</p>";
    } else {
        $mensagem = "<p style='color:red;text-align:center;'>❌ Erro ao atualizar: {$stmt->error}</p>";
    }
}

if (isset($_POST["excluir"])) {
    $conn->query("DELETE FROM candidaturas WHERE id = $id_usuario");
    $conn->query("DELETE FROM usuarios WHERE id = $id_usuario");
    session_destroy();
    header("Location: index.html");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

$sqlCandidaturas = "SELECT v.titulo_vaga, v.empresa, v.modalidade, v.tipo_contrato, c.data_candidatura
                    FROM candidaturas c
                    JOIN vagas_completa v ON c.id_vaga = v.id
                    WHERE c.id_usuario = ?";
$stmtC = $conn->prepare($sqlCandidaturas);
$stmtC->bind_param("i", $id_usuario);
$stmtC->execute();
$candidaturas = $stmtC->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Meu Perfil - BitVagas</title>
<link rel="stylesheet" href="css/perfil.css">
<style>
body {
    background-color: #0d1117;
    color: #c9d1d9;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}
header {
    background-color: #161b22;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header h2 {
    color: #58a6ff;
}
main {
    max-width: 900px;
    margin: 30px auto;
    background: #161b22;
    border-radius: 10px;
    padding: 25px 40px;
    box-shadow: 0 0 10px #000;
}
input, textarea {
    width: 100%;
    background: #0d1117;
    border: 1px solid #30363d;
    color: #c9d1d9;
    border-radius: 5px;
    padding: 8px;
}
label { font-weight: bold; margin-top: 10px; display: block; }
button {
    background-color: #238636;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}
button:hover { background-color: #2ea043; }
.excluir {
    background-color: #da3633;
    margin-left: 10px;
}
.excluir:hover { background-color: #f85149; }
.vagas {
    margin-top: 30px;
    background-color: #0d1117;
    border: 1px solid #30363d;
    border-radius: 8px;
    padding: 15px;
}
.vaga-item {
    border-bottom: 1px solid #30363d;
    padding: 10px 0;
}
.vaga-item:last-child { border-bottom: none; }
</style>
</head>
<body>
<header>
    <h2>BitVagas</h2>
    <a href="vagas.php" style="color:#58a6ff;text-decoration:none;">Voltar</a>
</header>

<main>
    <h1>Meu Perfil</h1>
    <?php echo $mensagem; ?>

    <form method="POST" action="">
        <label>Nome completo</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>">

        <label>Telefone</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">

        <label>Endereço</label>
        <input type="text" name="endereco" value="<?php echo htmlspecialchars($usuario['endereco']); ?>">

        <label>Área de interesse</label>
        <input type="text" name="area_interesse" value="<?php echo htmlspecialchars($usuario['area_interesse']); ?>">

        <label>Competências técnicas</label>
        <textarea name="competencias_tecnicas"><?php echo htmlspecialchars($usuario['competencias_tecnicas']); ?></textarea>

        <label>Experiência</label>
        <textarea name="experiencia"><?php echo htmlspecialchars($usuario['experiencia']); ?></textarea>

        <input type="hidden" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf']); ?>">
        <input type="hidden" name="rg" value="<?php echo htmlspecialchars($usuario['rg']); ?>">
        <input type="hidden" name="data_nascimento" value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>">
        <input type="hidden" name="genero" value="<?php echo htmlspecialchars($usuario['genero']); ?>">
        <input type="hidden" name="estado_civil" value="<?php echo htmlspecialchars($usuario['estado_civil']); ?>">
        <input type="hidden" name="instituicao" value="<?php echo htmlspecialchars($usuario['instituicao']); ?>">
        <input type="hidden" name="curso" value="<?php echo htmlspecialchars($usuario['curso']); ?>">
        <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($usuario['periodo']); ?>">
        <input type="hidden" name="turno" value="<?php echo htmlspecialchars($usuario['turno']); ?>">
        <input type="hidden" name="matricula" value="<?php echo htmlspecialchars($usuario['matricula']); ?>">
        <input type="hidden" name="conclusao" value="<?php echo htmlspecialchars($usuario['conclusao']); ?>">
        <input type="hidden" name="cursos_complementares" value="<?php echo htmlspecialchars($usuario['cursos_complementares']); ?>">
        <input type="hidden" name="idiomas" value="<?php echo htmlspecialchars($usuario['idiomas']); ?>">
        <input type="hidden" name="competencias_comportamentais" value="<?php echo htmlspecialchars($usuario['competencias_comportamentais']); ?>">
        <input type="hidden" name="modalidade" value="<?php echo htmlspecialchars($usuario['modalidade']); ?>">
        <input type="hidden" name="carga_horaria" value="<?php echo htmlspecialchars($usuario['carga_horaria']); ?>">
        <input type="hidden" name="pretensao_bolsa" value="<?php echo htmlspecialchars($usuario['pretensao_bolsa']); ?>">
        <input type="hidden" name="cidade_preferencia" value="<?php echo htmlspecialchars($usuario['cidade_preferencia']); ?>">

        <button type="submit" name="atualizar">Atualizar Perfil</button>
        <button type="submit" name="excluir" class="excluir" onclick="return confirm('Tem certeza que deseja excluir sua conta? Essa ação é irreversível.')">Excluir Conta</button>
    </form>

    <div class="vagas">
        <h2>Minhas Candidaturas</h2>
        <?php if ($candidaturas->num_rows > 0): ?>
            <?php while ($vaga = $candidaturas->fetch_assoc()): ?>
                <div class="vaga-item">
                    <strong><?php echo htmlspecialchars($vaga['titulo_vaga']); ?></strong><br>
                    <small><?php echo htmlspecialchars($vaga['empresa']); ?> • <?php echo htmlspecialchars($vaga['modalidade']); ?> • <?php echo htmlspecialchars($vaga['tipo_contrato']); ?></small><br>
                    <em>Candidatado em <?php echo date("d/m/Y H:i", strtotime($vaga['data_candidatura'])); ?></em>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma candidatura encontrada.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
