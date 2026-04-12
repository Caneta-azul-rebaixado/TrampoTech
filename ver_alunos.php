<?php
include("acesso.php");
session_start();

$empresa_id = null;

// pega id da empresa logada
if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'empresa') {
    $stmt = $mysqli->prepare("SELECT id FROM empresas WHERE empresa_id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $empresa = $res->fetch_assoc();
        $empresa_id = $empresa['id'];
    }
}

// filtros
$regiao = $_GET['regiao'] ?? '';
$idade = $_GET['idade'] ?? '';
$requisitos = $_GET['requesitos'] ?? [];

// ================= QUERY =================
$params = [];
$types  = "";

$sql = "
SELECT DISTINCT
    a.*, 
    l.email,
    i.id AS interessado
FROM alunos a

JOIN login l 
    ON l.id = a.aluno_id

LEFT JOIN interesse_alunos i 
    ON i.id_aluno_interss = a.id
";

// filtro por empresa (IMPORTANTE: dentro do JOIN)
if ($empresa_id) {
    $sql .= " AND i.empresa_int_id = ? ";
    $params[] = $empresa_id;
    $types .= "i";
}

// JOIN habilidades (ANTES do WHERE)
if (!empty($requisitos)) {
    $sql .= "
    JOIN aluno_habilidades ah 
        ON ah.aluno_id = a.id
    ";
}

// WHERE base
$sql .= " WHERE l.tipo = 'aluno' ";

// filtros adicionais
if (!empty($regiao)) {
    $sql .= " AND a.regiao = ? ";
    $params[] = $regiao;
    $types .= "s";
}


if (!empty($idade)) {
    $data_limite = date('Y-m-d', strtotime("-$idade years"));

    $sql .= " AND a.data_nasc <= ? ";
    $params[] = $data_limite;
    $types .= "s";
}

if (!empty($experiencia)) {
    $sql .= " AND a.experiencia = ? ";
    $params[] = $experiencia;
    $types .= "s";
}

// filtro habilidades (CORRIGIDO)
if (!empty($requisitos)) {
    $placeholders = implode(',', array_fill(0, count($requisitos), '?'));
    $sql .= " AND ah.requisito_id IN ($placeholders) ";

    foreach ($requisitos as $req) {
        $params[] = $req;
        $types .= "i";
    }
}

// prepara e executa
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
    <title>Ver Alunos</title>

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<style>
        /* ===== MENU ===== */
        menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;

            z-index: 9999;

            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(12px);

            padding: 0 24px;

            display: flex;
            align-items: center;
            gap: 16px;
        }

        menu a {
            color: white;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            transition: 0.25s;
        }

        menu a:hover {
            background: rgba(255,255,255,0.16);
            transform: translateY(-1px);
        }

        /* ===== BODY ===== */
        body {
            margin: 0;
            padding-top: 90px; /* espaço pro menu */

            min-height: 100vh;

            font-family: Arial, Helvetica, sans-serif;
            color: white;

            display: flex;
            flex-direction: column;
            align-items: center;

            background:
                linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                url("./images_data/background_inicio.png") center/cover no-repeat fixed;
        }

        /* ===== TÍTULO ===== */
        .page-title {
            margin: 20px 0 10px;
            color: orange;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
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

        .ts-wrapper.multi .ts-control > div {
            background: orange;
            color: black;
            border-radius: 8px;
            padding: 4px 8px;
            margin: 2px;
            font-size: 12px;
            font-weight: bold;
        }

        .ts-dropdown {
            background: #1e1e1e;
            color: white;
            border-radius: 10px;
        }

        .ts-dropdown .option {
            padding: 10px;
        }

        .ts-dropdown .option:hover,
        .ts-dropdown .active {
            background: orange;
            color: black;
        }

        /* ===== LISTA DE ALUNOS ===== */
        .alunos-list {
            width: 90%;
            max-width: 900px;

            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;

            margin-bottom: 40px;
        }

        /* ===== CARD ===== */
        .aluno {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);

            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;

            padding: 16px;

            transition: 0.2s;
        }

        .aluno:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.35);
        }

        .aluno-title {
            margin: 0;
            font-size: 20px;
            color: orange;
        }

        .aluno-summary {
            margin: 10px 0;
            font-size: 14px;
        }

        /* ===== DETALHES (TOGGLE) ===== */
        .aluno-extra {
            display: none;

            margin-top: 10px;
            padding-top: 10px;

            border-top: 1px solid rgba(255,255,255,0.2);

            max-height: 0;
            overflow: hidden;

            opacity: 0;
            transition: 0.3s;
        }

        .aluno-extra.show {
            display: block;
            max-height: 500px;
            opacity: 1;
        }

        /* ===== BOTÕES ===== */
        .toggle-button {
            margin-top: 10px;

            padding: 8px 12px;

            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.4);

            background: rgba(255,255,255,0.08);
            color: white;

            cursor: pointer;
        }

        .toggle-button:hover {
            background: rgba(255,255,255,0.2);
        }

        .interesse-btn {
            margin-top: 10px;

            padding: 10px 14px;

            border-radius: 8px;
            border: none;

            background: orange;
            color: black;

            font-weight: bold;
            cursor: pointer;
        }

        .interesse-btn:disabled {
            background: rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.6);
            cursor: not-allowed;
        }
/* ===== SELECT (REGIÃO E OUTROS) ===== */
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

</style>
</head>
<body>
    <menu class="menu">
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
</menu>

<form method="GET" action="ver_alunos.php" class="filtros-form">
    <select name="regiao">
    <option value="">Todas regiões</option>
    <option value="Várzea Paulista">Várzea Paulista</option>
    <option value="Campo Limpo Paulista">Campo Limpo Paulista</option>
    <option value="Cabreúva">Cabreúva</option>
    <option value="Itupeva">Itupeva</option>
    <option value="Jarinu">Jarinu</option>
    <option value="Itatiba">Itatiba</option>
    <option value="Louveira">Louveira</option>
</select>

    <select name="idade">
        <option value="">Todas idades</option>
        <option value="<18"  <?= $idade < "18" ? 'selected' : '' ?>>Menos de 18 anos</option>
        <option value="18" <?= $idade === "18" ? 'selected' : '' ?>>18 anos ou mais</option>
        <option value="25" <?= $idade === "25" ? 'selected' : '' ?>>25 anos ou mais</option>
        <option value="30" <?= $idade === "30" ? 'selected' : '' ?>>30 anos ou mais</option>
    </select>

    <select name="requesitos[]" id="requesitos" multiple>
        <option value="1" <?= in_array("1", $requisitos) ? 'selected' : '' ?>>HTML</option>
        <option value="2" <?= in_array("2", $requisitos) ? 'selected' : '' ?>>CSS</option>
        <option value="3" <?= in_array("3", $requisitos) ? 'selected' : '' ?>>JavaScript</option>
        <option value="4" <?= in_array("4", $requisitos) ? 'selected' : '' ?>>PHP</option>
        <option value="5" <?= in_array("5", $requisitos) ? 'selected' : '' ?>>MySQL</option>
    </select>

    <button type="submit">Filtrar</button>
</form>

<h1 class="page-title">Alunos Cadastrados</h1>

<div class="alunos-list">
<?php
if ($result->num_rows > 0) {
    $i = 0;
    while ($aluno = $result->fetch_assoc()) {
        $i++;
?>
    <div class="aluno">
        <h3 class="aluno-title"><?= htmlspecialchars($aluno['nome']) ?></h3>

        <div class="aluno-summary">
            <p><strong>RA:</strong> <?= htmlspecialchars($aluno['ra']) ?></p>
            <p><strong>Região:</strong> <?= htmlspecialchars($aluno['regiao']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($aluno['email']) ?></p>
        </div>

        <button
            class="toggle-button"
            type="button"
            onclick="toggleDetalhes(this, <?= $i ?>)">
            Mostrar mais
        </button>

        <div class="aluno-extra extra-<?= $i ?>">
            <p><strong>Data de Nascimento:</strong> <?= htmlspecialchars($aluno['data_nasc']) ?></p>

            <?php if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'empresa'): ?>

                <?php if ($aluno['interessado']): ?>
                    <button type="button" class="interesse-btn" disabled>
                        Já Interessado
                    </button>
                <?php else: ?>
                    <button
                        type="button"
                        class="interesse-btn"
                        onclick="interessarAluno(<?= $aluno['id'] ?>, this)">
                        Tenho Interesse
                    </button>
                <?php endif; ?>

            <?php elseif (isset($_SESSION['id']) && $_SESSION['tipo'] === 'aluno'): ?>

                <button type="button" class="interesse-btn" disabled>
                    Alunos não podem se interessar
                </button>

            <?php else: ?>

                <p style="color:red;">
                    Faça login como empresa para se interessar por este aluno
                </p>

            <?php endif; ?>
        </div>
    </div>
<?php
    }
} else {
    echo '<p style="color:white;">Nenhum aluno cadastrado no momento.</p>';
}
?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("requesitos");

    if (select) {
        new TomSelect(select, {
            plugins: ['remove_button'],
            placeholder: 'Selecione habilidades...',
            maxItems: 5,
            create: false
        });
    }
});

function toggleDetalhes(btn, i) {
    const extra = document.querySelector('.extra-' + i);

    if (!extra) return;

    const aberto = extra.classList.contains('show');

    if (aberto) {
        extra.classList.remove('show');
        btn.textContent = 'Mostrar mais';
    } else {
        extra.classList.add('show');
        btn.textContent = 'Mostrar menos';
    }
}

function interessarAluno(alunoId, btn) {
    const formData = new FormData();
    formData.append('id_aluno_interss', alunoId);

    fetch('interesse_teste.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);

        if (data.success) {
            btn.disabled = true;
            btn.textContent = 'Já Interessado';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar a solicitação.');
    });
}
</script>

</body>
</html>