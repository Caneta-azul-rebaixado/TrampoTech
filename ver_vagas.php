<?php
include("acesso.php");
session_start();

$aluno_id = null;


if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'aluno') {
    $login_id = $_SESSION['id'];

    $stmt = $mysqli->prepare("SELECT id FROM alunos WHERE aluno_id = ?");
    $stmt->bind_param("i", $login_id);
    $stmt->execute();
    $resAluno = $stmt->get_result();

    if ($resAluno->num_rows > 0) {
        $aluno = $resAluno->fetch_assoc();
        $aluno_id = $aluno['id'];
    }
}


$contrato = $_GET['contrato'] ?? '';
$regiao = $_GET['regiao'] ?? '';
$disponibilidade = $_GET['disponibilidade'] ?? '';
$tipo_pagamento = $_GET['tipo_pagamento'] ?? '';


if ($aluno_id) {
    $sql = "
        SELECT v.*, iv.id AS interesse_id
        FROM vagas v
        LEFT JOIN interesse_vagas iv 
            ON iv.vaga_id = v.id 
            AND iv.aluno_id = $aluno_id
        WHERE 1=1
    ";
} else {
    $sql = "
        SELECT v.*, NULL AS interesse_id
        FROM vagas v
        WHERE 1=1
    ";
}


if ($contrato != '') {
    $sql .= " AND v.tipo_contrato = '" . $mysqli->real_escape_string($contrato) . "'";
}

if($regiao !=''){
    $sql .= " AND v.regiao_vaga = '" . $mysqli->real_escape_string($regiao) . "'";
}

if ($disponibilidade != '') {
    $sql .= " AND v.disponibilidade = '" . $mysqli->real_escape_string($disponibilidade) . "'";
}

if ($tipo_pagamento != '') {
    $sql .= " AND v.tipo_pagamento = '" . $mysqli->real_escape_string($tipo_pagamento) . "'";
}


$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ver Vagas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        /* MENU DE NAVEGAÇÃO */
        .menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .menu .logo {
            color: orange;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .menu .nav-links {
            display: flex;
            gap: 20px;
        }

        .menu .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .menu .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        .menu .user-info {
            color: white;
            font-size: 14px;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url("./images_data/background_inicio.png") center/cover no-repeat fixed;
            padding-top: 60px; /* Espaço para o menu fixo */
        }

        .page-title {
            margin: 30px 0 15px;
            color: orange;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
        }

        .vagas-list {
            width: 90%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            margin-bottom: 40px;
            padding: 0 8px;
        }

        .vaga {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 14px;
            padding: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .vaga:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.35);
        }

        .vaga-title {
            margin: 0;
            font-size: 20px;
            color: orange;
        }

        .vaga-summary {
            margin: 10px 0;
            color: #fff;
            font-size: 14px;
        }

        .vaga-extra {
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

        .vaga-extra.show {
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

        /* MENU */
        .menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            z-index: 1000;
            display: flex;
            justify-content: flex-start;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .menu a:hover {
            background: rgba(255,255,255,0.2);
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

    align-items: flex-start;
}

.filtros-form select,
.filtros-form button {
    flex: 1;
    min-width: 160px;
}

/* SELECT BONITO */
.filtros-form select {
    padding: 10px 10px;

    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.2);

    background: rgba(255,255,255,0.08);
    color: white;

    font-size: 14px;
    cursor: pointer;

    height: 42px;

    appearance: none;
    outline: none;

    background-image: url("data:image/svg+xml;utf8,<svg fill='white' height='20' viewBox='0 0 24 24' width='20'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
}

.filtros-form select:hover {
    background: rgba(255,255,255,0.15);
}

.filtros-form select option {
    background: #1e1e1e;
    color: white;
}

/* BOTÃO */
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
    <a class="vaga-title">filtros</a>

    <form method="GET" action="ver_vagas.php" class="filtros-form">

    <select name="contrato">
        <option value="">Todos os contratos</option>
        <option value="CLT">CLT</option>
        <option value="estagio">Estágio</option>
        <option value="PJ">PJ</option>
        <option value="aprendiz">Aprendiz</option>
    </select>

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

    <select name="disponibilidade">
        <option value="">Todas Horários</option>
        <option value="manha">Manhã</option>
        <option value="tarde">Tarde</option>
        <option value="noite">Noite</option>
        <option value="integral">Integral</option>
        <option value="flexivel">Flexível</option>
        <option value="combinar">A combinar</option>
    </select>

    <select name="tipo_pagamento">
        <option value="">Métodos de pagamento</option>
        <option value="mensal">Mensal</option>
        <option value="hora">Por hora</option>
        <option value="projeto">Por projeto</option>
    </select>

    <button type="submit">Filtrar</button>

</form>

    <h1 class="page-title">Vagas Disponíveis</h1>
    <div class="vagas-list">
        <?php
            if ($result->num_rows > 0) {
                $i = 0;
                while ($vaga = $result->fetch_assoc()) {
                    $i++;
                    ?>
                    <div class="vaga">
                        <h3 class="vaga-title"><?php echo htmlspecialchars($vaga['titulo']); ?></h3>
                        <div class="vaga-summary">
                            <p><strong>Contrato:</strong> <?php echo htmlspecialchars($vaga['tipo_contrato']); ?></p>
                            <p><strong>Salário:</strong> <?php echo htmlspecialchars($vaga['salario']); ?></p>
                            <p><strong>Localização:</strong> <?php echo htmlspecialchars($vaga['localizacao']); ?></p>
                            <p style="color: yellow;">ID da vaga: <?php echo $vaga['id']; ?></p>
                        </div>
                        <button class="toggle-button extra-<?php echo $i; ?>" type="button" onclick="toggleDetalhes(this, <?php echo $i; ?>)">Mostrar mais</button>
                        <div class="vaga-extra extra-<?php echo $i; ?>">
                            <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($vaga['descricao'])); ?></p>
                            <p><strong>Requisitos:</strong> <?php echo nl2br(htmlspecialchars($vaga['requisitos'])); ?></p>
                            <p><strong>Benefícios:</strong> <?php echo nl2br(htmlspecialchars($vaga['beneficios'])); ?></p>
                            <p><strong>Pagamento:</strong> <?php echo htmlspecialchars($vaga['tipo_pagamento']); ?></p>
                            <p><strong>Disponibilidade:</strong> <?php echo htmlspecialchars($vaga['disponibilidade']); ?></p>  
                            <?php if (isset($_SESSION['id']) && $_SESSION['tipo'] === 'aluno'): ?>

                                <?php if ($vaga['interesse_id']): ?>
                                    <button class="toggle-button" disabled>
                                        Já candidatado
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="toggle-button" 
                                    onclick="candidatar(<?php echo $vaga['id']; ?>, this)">
                                        Candidatar-se
                                    </button>
                                <?php endif; ?>
                                
                            <?php elseif (isset($_SESSION['id']) && $_SESSION['tipo'] === 'empresa'):?>
                                <button class="toggle-button" disabled>
                                    <p style="color: red;">Empresas não podem se canditadar</p>
                                </button>
                            <?php else: ?>
                                <p style="color: yellow;">Faça login como aluno para se candidatar.</p>
                           <?php endif;?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p style="color:white;">Nenhuma vaga disponível no momento.</p>';
            }
        ?>
    </div>
    <script>
        function toggleDetalhes(btn, i) {
            const extra = document.querySelector('.vaga-extra.extra-' + i);
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

        function candidatar(vagaId, btn) {
    const formData = new FormData();
    formData.append('vaga_id', vagaId);

    fetch('interesse.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);

        if (data.success) {
            btn.disabled = true;
            btn.textContent = "Já candidatado";
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
