<?php
session_start();
require_once "./config/conexao.php";

$empresa = null;
$mensagem = "";

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id_empresa_visualizar = (int)$_GET["id"];
} else {
    $id_empresa_visualizar = 0;
}

if ($id_empresa_visualizar === 0) {
    $mensagem = "<p style='color:red;text-align:center;'>❌ ID de empresa não fornecido ou inválido.</p>";
} else {
    $stmt = $conn->prepare("SELECT * FROM empresa WHERE id = ?");
    $stmt->bind_param("i", $id_empresa_visualizar);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $empresa = $resultado->fetch_assoc();
    $stmt->close();
    
    if (!$empresa) {
        $mensagem = "<p style='color:red;text-align:center;'>❌ Empresa não encontrada.</p>";
    }
}

$candidato_logado = isset($_SESSION["usuario_id"]); 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Perfil da Empresa: <?php echo $empresa ? htmlspecialchars($empresa['nome_fantasia']) : 'Detalhes'; ?></title>
<link rel="stylesheet" href="css/empresa.css">
</head>
<body>
<header>
    <h2>Detalhes da Empresa</h2>
        <a href="vagas.php" class="btn-voltar">Voltar para Vagas</a> 
</header>

<main>
    <h1>Perfil de: <?php echo $empresa ? htmlspecialchars($empresa['nome']) : 'Empresa'; ?></h1>
    <?php echo $mensagem; ?>

    <?php if ($empresa): ?>
        <label>Razão Social</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['razao_social']); ?></div>

        <label>Nome Fantasia</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['nome']); ?></div>

        <label>CNPJ</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['cnpj']); ?></div>

        <label>Inscrição Estadual</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['inscricao_estadual']); ?></div>

        <label>Ano de Fundação</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['ano_fundacao']); ?></div>

        <label>Email Corporativo</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['email']); ?></div>

        <label>Telefone Comercial</label>
        <div class="dado-visualizacao"><?php echo htmlspecialchars($empresa['telefone_comercial']); ?></div>

        <label>Descrição</label>
        <div class="dado-visualizacao"><?php echo nl2br(htmlspecialchars($empresa['descricao'])); ?></div>
    
    <?php endif; ?>
</main>
</body>
</html>