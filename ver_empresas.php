<?php
include("acesso.php");
session_start();

$nome_social = $_GET['nome_social'] ?? '';

$sql = "SELECT e.*, l.email FROM empresas e 
        JOIN login l ON l.id = e.empresa_id 
        WHERE l.tipo = 'empresa'";

$params = [];
$types = "";

// Se digitou algo, adiciona filtro
if (!empty($nome_social)) {
    $sql .= " AND (e.nome_fantasia LIKE ? OR e.razao_social LIKE ?)";
    $busca = "%" . $nome_social . "%";
    $params[] = $busca;
    $params[] = $busca;
    $types .= "ss";
}

$stmt = $mysqli->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ver Empresas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url("./images_data/background_inicio.png") center/cover no-repeat fixed;
        }

        .page-title {
            margin: 30px 0 15px;
            color: orange;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
        }

        .empresas-list {
            width: 90%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            margin-bottom: 40px;
            padding: 0 8px;
        }

        .empresa {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            padding: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .empresa:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.35);
        }

        .empresa-title {
            margin: 0;
            font-size: 20px;
            color: orange;
        }

        .empresa-summary {
            margin: 10px 0;
            font-size: 14px;
        }

        .empresa-extra {
            display: none;
            margin-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 10px;
            color: #ddd;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }

        .empresa-extra.show {
            display: block;
            opacity: 1;
            max-height: 500px;
        }

        .toggle-button {
            margin-top: 10px;
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            color: white;
            font-size: 13px;
            cursor: pointer;
        }

        .toggle-button:hover {
            background: rgba(255,255,255,0.2);
        }

        menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background: rgba(0, 0, 0, 0.78);
    backdrop-filter: blur(12px);
    padding: 12px 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    gap: 16px;
}

menu a {
    color: white;
    text-decoration: none;
    padding: 10px 14px;
    transition: background 0.25s ease, transform 0.2s ease, border-color 0.25s ease;
}

menu a:hover {
    background: rgba(255,255,255,0.16);
    transform: translateY(-1px);
    border-color: rgba(255,255,255,0.28);
}

.filtros-form select {
    padding: 10px 12px;

    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.2);

    background: rgba(255,255,255,0.08);
    color: white;

    font-size: 14px;
    cursor: pointer;

    outline: none;
    appearance: none;

    /* mantém mesmo tamanho dos inputs */
    height: 42px;
}

/* hover igual input */
.filtros-form select:hover {
    background: rgba(255,255,255,0.15);
}

/* seta personalizada */
.filtros-form select {
    background-image: url("data:image/svg+xml;utf8,<svg fill='white' height='20' viewBox='0 0 24 24' width='20'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
}

/* opções do dropdown */
.filtros-form select option {
    background: #1e1e1e;
    color: white;
}

/* ===== FORM FILTROS ===== */
        .filtros-form {
            width: 90%;
            max-width: 900px;

            margin-bottom: 20px;

            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);

            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;

            padding: 16px;

            display: flex;
            flex-wrap: wrap;
            gap: 12px;

            align-items: flex-start; /* NÃO deixa crescer tudo junto */
        }

        .filtros-form input,
        .filtros-form button,
        .ts-wrapper {
            flex: 1;
            min-width: 160px;
        }

        .filtros-form input {
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);

            background: rgba(255,255,255,0.08);
            color: white;
        }

        .filtros-form input::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .filtros-form button {
            padding: 10px;
            border-radius: 10px;
            border: none;

            background: orange;
            color: black;
            font-weight: bold;

            cursor: pointer;
            transition: 0.2s;
        }

        .filtros-form button:hover {
            transform: translateY(-2px);
        }

        /* ===== TOM SELECT (CORRIGIDO) ===== */
        .ts-wrapper.multi .ts-control {
            min-height: 42px;
            max-height: 80px;      /* LIMITA TAMANHO */
            overflow-y: auto;      /* SCROLL INTERNO */

            display: flex;
            flex-wrap: wrap;
            align-items: center;

            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
        }

        .ts-control input {
            color: white !important;
        }
    </style>
</head>
<body>
    
<!-- <menu class="menu">
    <a href="inicio.php">Início</a>
    <?php if (isset($_SESSION['id'])): ?>
        <a href="ver_vagas.php">Vagas</a>
        <?php if ($_SESSION['tipo'] === 'aluno'): ?>
            <a href="ver_empresas.php">Empresas</a>
        <?php elseif ($_SESSION['tipo'] === 'empresa'): ?>
            <a href="criar_vagas.php">Criar Vagas</a>
        <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="cadastro_users.php">Cadastrar</a>
    <?php endif; ?>
    <a href="inicio.php#sobre">Sobre</a>
    <a href="inicio.php#quem-somos">Quem Somos ?</a>
    <?php if (isset($_SESSION['id'])): ?>
        <a href="logout.php">Sair</a>
    <?php endif; ?>
</menu> -->    

<a>Filtros</a>
<form method="GET" action="ver_empresas.php" class="filtros-form">
    <input type="text" name="nome_social" placeholder="Pesquisar por nome social" value="<?php echo htmlspecialchars($nome_social); ?>">

    <select name="regiao">
        <option value="">Todas Regiões</option>
        <option value="Várzea Paulista">Várzea Paulista</option>
        <option value="Campo Limpo Paulista">Campo Limpo Paulista</option>
        <option value="Cabreúva">Cabreúva</option>
        <option value="Itupeva">Itupeva</option>
        <option value="Jarinu">Jarinu</option>
        <option value="Itatiba">Itatiba</option>
        <option value="Louveira">Louveira</option>    
    </select>
    <button type="submit">Filtrar</button>
</form>

<h1 class="page-title">Empresas Cadastradas</h1>

<div class="empresas-list">
<?php
if ($result->num_rows > 0) {
    $i = 0;
    while ($empresa = $result->fetch_assoc()) {
        $i++;
?>
    <div class="empresa">
        <h3 class="empresa-title"><?php echo htmlspecialchars($empresa['nome_fantasia']); ?></h3>

        <div class="empresa-summary">
            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($empresa['endereco']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($empresa['email']); ?></p>
        </div>

        <button class="toggle-button" type="button" onclick="toggleDetalhes(this, <?php echo $i; ?>)">
            Mostrar mais
        </button>

        <div class="empresa-extra extra-<?php echo $i; ?>">
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($empresa['telefone']); ?></p>
            <p><strong>CNPJ:</strong> <?php echo htmlspecialchars($empresa['cnpj']); ?></p>
        </div>
    </div>
<?php
    }
} else {
    echo '<p style="color:white;">Nenhuma empresa cadastrada.</p>';
}
?>
</div>

<script>
function toggleDetalhes(btn, i) {
    const extra = document.querySelector('.empresa-extra.extra-' + i);

    if (!extra) return;

    const isOpen = extra.classList.contains('show');

    if (isOpen) {
        extra.classList.remove('show');
        btn.textContent = 'Mostrar mais';
    } else {
        extra.classList.add('show');
        btn.textContent = 'Mostrar menos';
    }
}
</script>

</body>
</html>