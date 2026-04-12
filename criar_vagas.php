<?php
session_start();
require_once "acesso.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario_id = $_SESSION['id'];

    // busca empresa
    $sql_empresa = $mysqli->query("SELECT id FROM empresas WHERE empresa_id = '$usuario_id'");
    $empresa = $sql_empresa->fetch_assoc();

    if (!$empresa) {
        die("Empresa não encontrada.");
    }

    $id_empresa = $empresa['id'];

    // dados
    $titulo = $_POST['titulo'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $descricao = $_POST['descricao'];
    $beneficios = $_POST['beneficios'];
    $salario = $_POST['salario'];
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $disponibilidade = $_POST['disponibilidade'];
    $localizacao = $_POST['localizacao'];
    $regiao_vaga = $_POST['regiao'];

    // 🔥 INSERT da vaga (SEM requisitos)
    $sql = "INSERT INTO vagas 
    (id_empresas, titulo, tipo_contrato, descricao, beneficios, salario, tipo_pagamento, disponibilidade, localizacao, regiao_vaga)
    VALUES 
    ('$id_empresa', '$titulo', '$tipo_contrato', '$descricao', '$beneficios', '$salario', '$tipo_pagamento', '$disponibilidade', '$localizacao', '$regiao_vaga')";

    if (!$mysqli->query($sql)) {
        die("Erro ao criar vaga: " . $mysqli->error);
    }

    // 🔥 pega ID da vaga criada
    $vaga_id = $mysqli->insert_id;

    // 🔥 SALVAR REQUISITOS (checkbox)
    if (isset($_POST['requisitos'])) {
        foreach ($_POST['requisitos'] as $req_id) {
            $mysqli->query("
                INSERT INTO vaga_requisitos (vaga_id, requisito_id)
                VALUES ('$vaga_id', '$req_id')
            ");
        }
    }

    echo "<script>alert('Vaga criada com sucesso!'); window.location.href='ver_vagas.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Vagas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<script>
    
function VerifTitulo() {
    const titulo = document.getElementById('titulo').value;
    const msg = document.getElementById('msg_titulo');

    const regex = /^[A-Za-zÀ-ÿ0-9\s\-_]{3,100}$/;

    if (!regex.test(titulo)) {
        msg.textContent = "Título inválido (mínimo 3 caracteres).";
    } else {
        msg.textContent = "";
    }
}

function VerifDescricao() {
    const descricao = document.getElementById('descricao').value;
    const msg = document.getElementById('msg_descricao');

    if (descricao.length < 10) {
        msg.textContent = "Mínimo 10 caracteres.";
    } else {
        msg.textContent = "";
    }
}

function VerifRequisitos() {
    const requisitos = document.getElementById('requisitos').value;
    const msg = document.getElementById('msg_requisitos');

    if (requisitos.length < 5) {
        msg.textContent = "Mínimo 5 caracteres.";
    } else {
        msg.textContent = "";
    }
}

function VerifBeneficios() {
    const beneficios = document.getElementById('beneficios').value;
    const msg = document.getElementById('msg_beneficios');

    if (beneficios.length < 5) {
        msg.textContent = "Mínimo 5 caracteres.";
    } else {
        msg.textContent = "";
    }
}

function VerifSalario() {
    const salario = document.getElementById('salario').value;
    const msg = document.getElementById('msg_salario');

    if (salario === "" || isNaN(salario) || Number(salario) <= 0) {
        msg.textContent = "Digite um número válido.";
    } else {
        msg.textContent = "";
    }
}

function VerifLocalizacao() {
    const localizacao = document.getElementById('localizacao').value;
    const msg = document.getElementById('msg_localizacao');

    if (localizacao.length < 3) {
        msg.textContent = "Mínimo 3 caracteres.";
    } else {
        msg.textContent = "";
    }
}

function VerifSelect(id, msgId, texto) {
    const valor = document.getElementById(id).value;
    const msg = document.getElementById(msgId);

    if (valor === "") {
        msg.textContent = texto;
    } else {
        msg.textContent = "";
    }
}

</script>

    <style>
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* FUNDO */
        #bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url("./images_data/background_inicio.png") center/cover no-repeat;
            transition: opacity 0.5s ease;
        }

        /* BODY */
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
            color: white;
        }

        /* TÍTULOS */
        h2 {
            margin-top: 40px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* ESCOLHA (se usado) */
        #escolha {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .cardAluno, .cardEmpresa {
            width: 150px;
            padding: 20px;
            border-radius: 12px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            cursor: pointer;
            text-align: center;
            transition: 0.3s;
        }

        .cardAluno:hover, .cardEmpresa:hover {
            transform: scale(1.05);
            background: rgba(255,255,255,0.2);
        }

        /* FORMULÁRIOS */
        form {
            width: 90%;
            max-width: 400px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        /* INPUTS E SELECTS */
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* LABELS */
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: white;
        }

        /* BOTÕES */
        button, input[type="button"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: 0.3s;
            font-size: 16px;
            font-weight: bold;
        }

        button {
            background: orange;
            color: black;
        }

        button:hover {
            background: #ffb733;
        }

        input[type="button"] {
            background: transparent;
            color: white;
            border: 1px solid white;
        }

        input[type="button"]:hover {
            background: white;
            color: black;
        }

        /* BOTÃO VOLTAR */
        #btn_voltar {
            width: 18%;
        }

        /* MENSAGENS */
        span {
            font-size: 12px;
            color: red;
        }

        /* TABLE (para layout antigo) */
        table {
            width: 100%;
        }

        td {
            padding: 5px 0;
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
           .habilidades-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .habilidades-table td {
        padding: 8px;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 5px;
    }
    .habilidades-table input[type="checkbox"] {
        margin-right: 5px;
    }
    </style>
</head>
<body>
    <div id="bg"></div>

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


    <h2>Criar Vaga</h2>
  <form method="POST">
    <table>

        <!-- TÍTULO -->
        <tr>
            <td>
                <input type="text" name="titulo" placeholder="Título da vaga" id="titulo" onblur="VerifTitulo()">
                <br>
                <span id="msg_titulo" style="color:red;"></span>
            </td>
        </tr>

        <!-- DESCRIÇÃO -->
        <tr>
            <td>
                <textarea name="descricao" placeholder="Descrição" id="descricao" onblur="VerifDescricao()"></textarea>
                <br>
                <span id="msg_descricao" style="color:red;"></span>
            </td>
        </tr>

        <!-- TIPO DE CONTRATO -->
        <tr>
            <td>
                <label>Tipo de contrato:</label>
                <select name="tipo_contrato" id="tipo_contrato" onblur="VerifTipoContrato()" required>
                    <option value="">Selecione...</option>
                    <option value="CLT">CLT</option>
                    <option value="Estágio">Estágio</option>
                    <option value="PJ">PJ</option>
                    <option value="Aprendiz">Aprendiz</option>
                </select>
                <br>
                <span id="msg_tipo_contrato" style="color:red;"></span>
            </td>
        </tr>

        <!-- TIPO DE PAGAMENTO -->
        <tr>
            <td>
                <label>Tipo de pagamento:</label>
                <select name="tipo_pagamento" id="tipo_pagamento" onblur="VerifTipoPagamento()" required>
                    <option value="">Selecione...</option>
                    <option value="mensal">Mensal</option>
                    <option value="hora">Por hora</option>
                    <option value="projeto">Por projeto</option>
                </select>
                <br>
                <span id="msg_tipo_pagamento" style="color:red;"></span>
            </td>
        </tr>

        <!-- SALÁRIO -->
        <tr>
            <td>
                <input type="text" name="salario" placeholder="Salário" id="salario" onblur="VerifSalario()">
                <br>
                <span id="msg_salario" style="color:red;"></span>
            </td>
        </tr>

        <!-- REQUISITOS -->
        <table class="habilidades-table">
            <tr>
                <?php
                $sql = $mysqli->query("SELECT * FROM requisitos");
                $contagem = 0; // Contador para saber em qual item estamos

                while ($r = $sql->fetch_assoc()) {
                    // Se já exibimos 3 itens, fecha a linha e abre uma nova
                    if ($contagem > 0 && $contagem % 3 == 0) {
                        echo '</tr><tr>';
                    }

                    echo '<td>
                            <input type="checkbox" name="requisitos[]" value="'.$r['id'].'"> '.$r['nome'].'
                        </td>';

                    $contagem++;
                }
                ?>
            </tr>
        </table>


        <!-- BENEFÍCIOS -->
        <tr>
            <td>
                <textarea name="beneficios" placeholder="Benefícios" id="beneficios" onblur="VerifBeneficios()"></textarea>
                <br>
                <span id="msg_beneficios" style="color:red;"></span>
            </td>
        </tr>
        
        <!-- DISPONIBILIDADE -->
        <tr>
            <td>
                <label>Disponibilidade:</label>
                <select name="disponibilidade" id="disponibilidade" onblur="VerifDisponibilidade()" required>
                    <option value="">Selecione...</option>
                    <option value="manha">Manhã</option>
                    <option value="tarde">Tarde</option>
                    <option value="noite">Noite</option>
                    <option value="integral">Integral</option>
                    <option value="flexivel">Flexível</option>
                    <option value="combinar">A combinar</option>
                </select>
            </td>
        </tr>

        <!-- LOCALIZAÇÃO -->
        <tr>
            <td>
                <input type="text" name="localizacao" placeholder="Localização" id="localizacao" onblur="VerifLocalizacao()">
                <br>
                <span id="msg_localizacao" style="color:red;"></span>
            </td>
        </tr>
        <!-- Região -->
        <tr>
            <td>
                <select name="regiao" id="regiao" placeholder="Região do Endereço" onblur="VerifRegiao()" required>
                    <option value="">Selecione a região</option>
                    <option value="Varzera Paulista">Várzea Paulista</option>
                    <option value="Campo Limpo Paulista">Campo Limpo Paulista</option>
                    <option value="Cabreuva">Cabreúva</option>
                    <option value="Itupeva">Itupeva</option>
                    <option value="Jarinu">Jarinu</option>
                    <option value="Itatiba">Itatiba</option>
                    <option value="Louveira">Louveira</option>
                </select><br>
            </td>
        </tr>
        <!-- BOTÃO -->
        <tr>
            <td>
                <button type="submit">Criar vaga</button>
            </td>
        </tr>

    </table>
</form>
    </body>
    </html>
