<?php
session_start();
require_once "./config/conexao.php";

if (!isset($_SESSION["empresa_nome"])) {
    header("Location: login.php");
    exit;
}

$empresa_nome = $_SESSION["empresa_nome"];
$id_empresa = $_SESSION["empresa_id"] ?? null;

$filtro_linguagem = isset($_GET['linguagem']) ? "%" . trim($_GET['linguagem']) . "%" : "";
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : "";
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : "";

$sql = "SELECT * FROM vagas_estagio WHERE empresa = ?";
$params = [$empresa_nome];
$types = "s";

if ($filtro_linguagem) {
    $sql .= " AND linguagem LIKE ?";
    $params[] = $filtro_linguagem;
    $types .= "s";
}

if ($filtro_data_inicio) {
    $sql .= " AND DATE(data_postagem) >= ?";
    $params[] = $filtro_data_inicio;
    $types .= "s";
}

if ($filtro_data_fim) {
    $sql .= " AND DATE(data_postagem) <= ?";
    $params[] = $filtro_data_fim;
    $types .= "s";
}

$sql .= " ORDER BY data_postagem DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado_vagas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel da Empresa</title>
    <link rel="stylesheet" href="css/painel_empresa.css">
</head>
<body>
    <header>
        <nav>
            <h3>BitVagas</h3>
            <ul class="menu">
                <li><a href="index.html">Home</a></li>
            </ul>
            <p>Olá, <?php echo htmlspecialchars($empresa_nome); ?>!</p>
        </nav>
    </header>

    <main>
        <h1>Minhas Vagas Publicadas</h1>

        <section class="filtro-container">
            <form method="GET" class="filtro-form">
                <input type="text" name="linguagem" placeholder="Linguagem de Programação"
                       value="<?php echo isset($_GET['linguagem']) ? htmlspecialchars(trim($_GET['linguagem'])) : ''; ?>">
                <input type="date" name="data_inicio"
                       value="<?php echo isset($_GET['data_inicio']) ? htmlspecialchars($_GET['data_inicio']) : ''; ?>">
                <input type="date" name="data_fim"
                       value="<?php echo isset($_GET['data_fim']) ? htmlspecialchars($_GET['data_fim']) : ''; ?>">
                <button type="submit" class="btn-destaque">Filtrar</button>
                <a href="painel_empresa.php" class="btn-limpar">Limpar</a>
                <a href="criar_vaga.php" class="btn-editar">+ Nova Vaga</a>
            </form>
        </section>

        <section class="vagas-listagem">
            <?php if ($resultado_vagas->num_rows > 0): ?>
                <?php while ($vaga = $resultado_vagas->fetch_assoc()): ?>
                    <article class="vaga-card <?php echo isset($vaga['ativa']) && $vaga['ativa'] ? 'ativa' : 'desativada'; ?>">
                        <h2><?php echo htmlspecialchars($vaga['empresa']); ?></h2>
                        <p><strong>Linguagem:</strong> <?php echo htmlspecialchars($vaga['linguagem']); ?></p>
                        <p><strong>Localidade:</strong> <?php echo htmlspecialchars($vaga['localidade']); ?></p>
                        <p><strong>Remuneração:</strong> 
                            <?php echo $vaga['remunerado'] ? "R$ " . number_format($vaga['salario'], 2, ',', '.') : "Não Remunerado"; ?>
                        </p>
                        <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($vaga['data_postagem'])); ?></p>
                        <p><strong>Status:</strong> 
                            <?php echo isset($vaga['ativa']) && $vaga['ativa'] ? '<span class="status-ativo">Ativa</span>' : '<span class="status-desativada">Desativada</span>'; ?>
                        </p>
                        <br>
                        <div class="acoes">
                            <a href="editar_vaga.php?id=<?php echo $vaga['id']; ?>" class="btn-editar">Editar</a>
                            <button type="button" class="btn-desativar" onclick="abrirModal(<?php echo $vaga['id']; ?>)">Desativar</button>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="erro">Nenhuma vaga encontrada.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 BitVagas - Todos os direitos reservados.</p>
    </footer>

    <div id="modalSenha" class="modal">
        <div class="modal-content">
            <h2>Confirme sua senha</h2>
            <form method="POST" action="desativar.php">
                <input type="hidden" name="id_vaga" id="id_vaga_modal">
                <input type="password" name="senha" placeholder="Digite sua senha" required>
                <button type="submit" class="btn-destaque">Confirmar</button>
                <button type="button" class="btn-limpar" onclick="fecharModal()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModal(id) {
            document.getElementById('id_vaga_modal').value = id;
            document.getElementById('modalSenha').style.display = 'flex';
        }
        function fecharModal() {
            document.getElementById('modalSenha').style.display = 'none';
        }
        window.onclick = function(e) {
            if (e.target === document.getElementById('modalSenha')) {
                fecharModal();
            }
        }
    </script>
</body>
</html>
