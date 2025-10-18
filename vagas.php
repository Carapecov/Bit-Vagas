<?php
session_start();
require_once "./config/conexao.php";

$filtro_empresa = isset($_GET['empresa']) ? "%" . trim($_GET['empresa']) . "%" : "";
$filtro_linguagem = isset($_GET['linguagem']) ? "%" . trim($_GET['linguagem']) . "%" : "";
$filtro_localidade = isset($_GET['localidade']) ? "%" . trim($_GET['localidade']) . "%" : "";
$filtro_remunerado = isset($_GET['remunerado']) ? $_GET['remunerado'] : "";

$sql = "SELECT * FROM vagas_estagio WHERE 1=1";
$params = [];
$types = "";

if ($filtro_empresa) {
    $sql .= " AND empresa LIKE ?";
    $params[] = $filtro_empresa;
    $types .= "s";
}

if ($filtro_linguagem) {
    $sql .= " AND linguagem LIKE ?";
    $params[] = $filtro_linguagem;
    $types .= "s";
}

if ($filtro_localidade) {
    $sql .= " AND localidade LIKE ?";
    $params[] = $filtro_localidade;
    $types .= "s";
}

if ($filtro_remunerado !== "") {
    $sql .= " AND remunerado = ?";
    $params[] = (bool)$filtro_remunerado;
    $types .= "i";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado_vagas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Vagas de Estágio</title>
    <link rel="stylesheet" href="css/vagas.css">
</head>
<body>
    <header>
        <nav>
            <h3>BitVagas</h3>
            <ul class="menu">
                <li><a href="index.html">Home</a></li>
            </ul>
            <?php if (isset($_SESSION["usuario_nome"])): ?>
                <p style="color: var(--cor-destaque);">Olá, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>!</p>
            <?php else: ?>
                <a href="usuário.php" class="btn-destaque">Perfil</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h1>Buscar Vagas de Estágio</h1>

        <section class="filtro-container">
            <form method="GET" class="filtro-form">
                <input type="text" name="empresa" placeholder="Empresa" 
                       value="<?php echo isset($_GET['empresa']) ? htmlspecialchars(trim($_GET['empresa'])) : ''; ?>">
                <input type="text" name="linguagem" placeholder="Linguagem de Programação" 
                       value="<?php echo isset($_GET['linguagem']) ? htmlspecialchars(trim($_GET['linguagem'])) : ''; ?>">
                <input type="text" name="localidade" placeholder="Localidade" 
                       value="<?php echo isset($_GET['localidade']) ? htmlspecialchars(trim($_GET['localidade'])) : ''; ?>">
                <select name="remunerado">
                    <option value="">Remuneração</option>
                    <option value="1" <?php echo (isset($_GET['remunerado']) && $_GET['remunerado'] === "1") ? "selected" : ""; ?>>Remunerado</option>
                    <option value="0" <?php echo (isset($_GET['remunerado']) && $_GET['remunerado'] === "0") ? "selected" : ""; ?>>Não Remunerado</option>
                </select>
                <button type="submit" class="btn-destaque">Filtrar</button>
                <a href="vagas.php" class="btn-limpar">Limpar</a>
            </form>
        </section>

        <section class="vagas-listagem" style="background-color: var(--cor-fundo-principal); border: none; box-shadow: none;">
            <?php if ($resultado_vagas->num_rows > 0): ?>
                <?php while($vaga = $resultado_vagas->fetch_assoc()): ?>
                    <article class="vaga-card">
                        <h2><?php echo htmlspecialchars($vaga['empresa']); ?></h2>
                        <p><strong>Linguagem:</strong> <?php echo htmlspecialchars($vaga['linguagem']); ?></p>
                        <p><strong>Localidade:</strong> <?php echo htmlspecialchars($vaga['localidade']); ?></p>

                        <?php if ($vaga['remunerado']): ?>
                            <p><strong>Remunerado:</strong> Salário: R$ <?php echo number_format($vaga['salario'], 2, ',', '.'); ?></p>
                        <?php else: ?>
                            <p><strong>Remunerado:</strong> Não</p>
                        <?php endif; ?>

                        <?php if (isset($_SESSION["usuario_id"])): ?>
                            <form method="POST" action="candidatar.php" style="margin-top: 1rem;">
                                <input type="hidden" name="id_vaga" value="<?php echo $vaga['id']; ?>">
                                <button type="submit" class="btn-detalhes">Candidatar-se</button>
                            </form>
                        <?php else: ?>
                            <a href="login.php" class="btn-detalhes">Faça Login para Candidatar-se</a>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="erro" style="text-align:center; color: var(--cor-texto-principal); margin-top: 1rem;">Nenhuma vaga encontrada com os filtros aplicados.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 BitVagas - Todos os direitos reservados.</p>
    </footer>
</body>
</html>