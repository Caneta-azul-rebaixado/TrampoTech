<?php
require_once "acesso.php";
session_start();
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo'])) {

    $tipo  = $_POST['tipo'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // ========================
    // VERIFICA EMAIL
    // ========================
    $stmt = $mysqli->prepare("SELECT id FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check_email = $stmt->get_result();

    if ($check_email->num_rows > 0) {
        $mensagem = "Este email já está cadastrado.";
    } else {

        // ========================
        // ALUNO
        // ========================
        if ($tipo === "aluno") {

            $nome      = $_POST['nome'];
            $ra        = $_POST['ra'];
            $data_nasc = $_POST['data_nasc'];
            $regiao    = $_POST['regiao'];
            $telefone  = $_POST['telefone_aln'];
            $experiencia = $_POST['experiencia'];

            // verifica RA
            $stmt = $mysqli->prepare("SELECT id FROM alunos WHERE ra = ?");
            $stmt->bind_param("s", $ra);
            $stmt->execute();
            $check_ra = $stmt->get_result();

            if ($check_ra->num_rows > 0) {
                $mensagem = "Este RA já está cadastrado.";
            } else {

                // cria login
                $stmt = $mysqli->prepare("
                    INSERT INTO login (email, senha, tipo)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("sss", $email, $senha, $tipo);
                $stmt->execute();

                $id = $mysqli->insert_id;

                // cria aluno
                $stmt = $mysqli->prepare("
                    INSERT INTO alunos (aluno_id, nome, ra, data_nasc, regiao, telefone, experiencia)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "issssss",
                    $id,
                    $nome,
                    $ra,
                    $data_nasc,
                    $regiao,
                    $telefone,
                    $experiencia
                );

                $stmt->execute();

                // habilidades (CORRIGIDO)
                if (isset($_POST['habilidades'])) {
                   $aluno_id = $mysqli->insert_id;

                    foreach ($_POST['habilidades'] as $req_id) {

                        $req_id = (int)$req_id;

                        $stmt = $mysqli->prepare("
                            INSERT INTO aluno_habilidades (aluno_id, requisito_id)
                            VALUES (?, ?)
                        ");

                        // ✅ AGORA CERTO
                        $stmt->bind_param("ii", $aluno_id, $req_id);
                        $stmt->execute();
                    }
                }

                // LOGIN AUTOMÁTICO
                $_SESSION['id'] = $id;
                $_SESSION['tipo'] = 'aluno';

                header("Location: ver_vagas.php");
                exit;
            }
        }

        // ========================
        // EMPRESA
        // ========================
        elseif ($tipo === "empresa") {

            $nome_fantasia = $_POST['nome_empresa'];
            $razao_social  = $_POST['razao_social'];
            $cnpj          = $_POST['cnpj'];
            $regiao        = $_POST['regiao'];
            $endereco      = $_POST['endereco'];
            $descricao     = $_POST['descricao_empresa'];
            $telefone      = $_POST['telefone_emp'];

            // verifica CNPJ
            $stmt = $mysqli->prepare("SELECT id FROM empresas WHERE cnpj = ?");
            $stmt->bind_param("s", $cnpj);
            $stmt->execute();
            $check_cnpj = $stmt->get_result();

            // verifica razão social
            $stmt = $mysqli->prepare("SELECT id FROM empresas WHERE razao_social = ?");
            $stmt->bind_param("s", $razao_social);
            $stmt->execute();
            $check_razao = $stmt->get_result();

            if ($check_cnpj->num_rows > 0) {
                $mensagem = "Este CNPJ já está cadastrado.";
            } elseif ($check_razao->num_rows > 0) {
                $mensagem = "Esta razão social já está cadastrada.";
            } else {

                // cria login
                $stmt = $mysqli->prepare("
                    INSERT INTO login (email, senha, tipo)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("sss", $email, $senha, $tipo);
                $stmt->execute();

                $id = $mysqli->insert_id;

                // cria empresa
                $stmt = $mysqli->prepare("
                    INSERT INTO empresas (empresa_id, nome_fantasia, razao_social, cnpj, telefone, regiao, endereco, descricao)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "isssssss",
                    $id,
                    $nome_fantasia,
                    $razao_social,
                    $cnpj,
                    $telefone,
                    $regiao,
                    $endereco,
                    $descricao
                );

                $stmt->execute();

                // LOGIN AUTOMÁTICO
                $_SESSION['id'] = $id;
                $_SESSION['tipo'] = 'empresa';

                header("Location: criar_vagas.php");
                exit;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="password.js"></script>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

#bg{
    position: fixed;
    inset: 0 ;
    z-index: -1;

    background: 
        linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url("./images_data/background_inicio.png") center/cover no-repeat;
    
    transition: opacity 0.5s ease;
}

body {
    margin: 0;
    min-height: 100vh;

    display: flex;
    flex-direction: column;
    align-items: center;

    font-family: Arial, Helvetica, sans-serif;
    color: white;
}

/* TÍTULO */
h2 {
    margin-top: 40px;
    margin-bottom: 20px;
    text-align: center;
}

/* ESCOLHA */
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

/* INPUTS */
input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 8px;

    border-radius: 8px;
    border: none;
}

textarea {
    min-height: 100px;
    resize: none;
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
}

button {
    background: orange;
}

button:hover {
    background: #ffb733
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
#btn_voltar{
    width: 18%;
}

/* MENSAGENS */
span {
    font-size: 20px;
    font-weigth: bold;
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

<script>
// pré-carrega imagens
const imagens = {
    aluno: "./images_data/background_cadastro_aluno.png",
    empresa: "./images_data/background_cadastro_empresas.png",
    padrao: "./images_data/background_inicio.png"
};

Object.values(imagens).forEach(src => {
    const img = new Image();
    img.src = src;
});

// FUNDO
function mudarFundo(tipo) {

    let imagem = imagens.padrao;

    if (tipo === "aluno") {
        imagem = imagens.aluno;
    } 
    else if (tipo === "empresa") {
        imagem = imagens.empresa;
    }

    const bg = document.getElementById("bg");

    bg.style.opacity = 0.5;

    setTimeout(() => {
        bg.style.background = `
            linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
            url('${imagem}') center/cover no-repeat
        `;
        bg.style.opacity = 1;
    }, 300);
}

// Redireciona com fade
function voltar_cadastro(url, tempo = 500) {
    const bg = document.getElementById("bg");
    if (bg) {
        bg.style.opacity = 0.5;
        setTimeout(() => {
            window.location.href = "cadastro_users.php";
        }, tempo);
    } else {
        window.location.href = "cadastro_users.php"; // Fallback se não houver #bg
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let textarea = document.getElementById("experiencia_aln");
    let contador = document.getElementById("contador_experiencia_aln");

    textarea.addEventListener("input", function() {
        let total = textarea.value.length;
        contador.textContent = total + " / 1500";

        // muda de cor perto do limite
        if (total > 1400) {
            contador.style.color = "red";
        } else {
            contador.style.color = "gray";
        }
    });

    let textareaEmpresa = document.getElementById("descricao_empresa");
    let contadorEmpresa = document.getElementById("contador_descricao_empresa");
    if (textareaEmpresa && contadorEmpresa) {
        textareaEmpresa.addEventListener("input", function() {
            let total = textareaEmpresa.value.length;
            contadorEmpresa.textContent = total + " / 1500";

            if (total > 1400) {
                contadorEmpresa.style.color = "red";
            } else {
                contadorEmpresa.style.color = "gray";
            }
        });
    }
});

</script>

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


<h2>Cadastro</h2>

<?php if (!empty($mensagem)) echo "<p>$mensagem</p>"; ?>

<!-- Escolhe qual fórmulário preencher -->
    <div id="escolha">
        <div class="cardAluno" onclick="mostrarAluno(); mudarFundo('aluno')">Sou Aluno</div>
        <div class="cardEmpresa" onclick="mostrarEmpresa(); mudarFundo('empresa')">Sou Empresa</div>
    </div>

<!-- FORM ALUNO -->
<form method="post" id="formAluno" style="display:none;" onsubmit="return validarForm('aluno')">

<input type="hidden" name="tipo" value="aluno">

<input type="text" name="nome" id="nome_aluno" placeholder="Nome completo" onblur="VerifNomeAL()" required><br>

<span id = "msg_nome_aluno" style="color:red;"></span>
<br>

<input type="text" name="ra" id="ra" placeholder="RA" onblur="VerifRA()" required><br>

<span id="msg_ra_aluno" style="color:red;"></span>
<br>

<input type="date" name="data_nasc" id="data_nasc" onblur="VerifIDADE()" required><br>

<span id="msg_data_nasc_aluno" style="color:red;"></span>
<br>

<input type="email" name="email" id="email_aluno" placeholder="Email" onblur="Verifemail('aluno')" required><br>

<span id="msg_email_aluno" style="color:red;"></span>
<br>

<select name="regiao" id="regiao" placeholder="Região do Endereço" onblur="VerifRegiao()" required>
    <option value="">Selecione a região</option>
    <option value="Varzera Paulista">Várzea Paulista</option>
    <option value="Campo Limpo Paulista">Camp Limpo Paulista</option>
    <option value="Cabreuva">Cabreúva</option>
    <option value="Itupeva">Itupeva</option>
    <option value="Jarinu">Jarinu</option>
    <option value="Itatiba">Itatiba</option>
    <option value="Louveira">Louveira</option>
</select><br>

<span id="msg_regiao_aluno" style="color:red;"></span>
<br>

<input type="text" name="telefone_aln" id="telefone_aluno" placeholder="Telefone" onblur="VerifTelefoneAL()" required><br>

<span id="msg_telefone_aluno" style="color:red;"></span>
<br>

<label>Habilidades:</label><br>
<table class="habilidades-table">
    <tr>
        <td><input type="checkbox" name="habilidades[]" value="1"> PHP</td>
        <td><input type="checkbox" name="habilidades[]" value="2"> JavaScript</td>
        <td><input type="checkbox" name="habilidades[]" value="3"> MySQL</td>
    </tr>
    <tr>
        <td><input type="checkbox" name="habilidades[]" value="4"> HTML/CSS</td>
        <td><input type="checkbox" name="habilidades[]" value="5"> Suporte Técnico</td>
        <td><input type="checkbox" name="habilidades[]" value="6"> Redes</td>
    </tr>
    <tr>
        <td><input type="checkbox" name="habilidades[]" value="7"> Excel</td>
        <td><input type="checkbox" name="habilidades[]" value="8"> Git</td>
        <td><input type="checkbox" name="habilidades[]" value="9"> Linux</td>
    </tr>
    <tr>
        <td><input type="checkbox" name="habilidades[]" value="10"> Power BI</td>
    </tr>
</table>

<div style="position: relative; width: 100%;">
    <textarea
        name="experiencia"
        id="experiencia_aln"
        placeholder="Breve descrição"
        maxlength="1500"
        rows="4"></textarea>

    <span
        id="contador_experiencia_aln"
        style="
            position: absolute;
            bottom: 8px;
            right: 10px;
            font-size: 12px;
            color: gray;
            pointer-events: none;">
        0 / 1500
    </span>
</div>

<input type="password" name="senha" id="senhaAL" placeholder="Senha" onblur="VerifPassword('aluno')" required>
<i class="bi bi-eye-fill" id="btn_senhaver_al" onclick="mostrarsenha('aluno')"></i>

<br>
<span id="msg_senha_aluno" style="color:red;"></span><br>

<input type="password" name="confirmar_senha" id="confirmar_senha_aluno" placeholder="Confirmar senha" onblur="ConfirmPassword('aluno')" required>

<br>
<span id="msg_confirmar_aluno" style="color:red;"></span>

<br>
<button type="submit">Cadastrar Aluno</button>
<input type="button" value="Voltar" onclick="voltar_cadastro()">

</form>

<!-- FORM EMPRESA -->
<form method="post" id="formEmpresa" style="display:none;" onsubmit="return validarForm('empresa')">

<input type="hidden" name="tipo" value="empresa">

<input type="text" name="nome_empresa" id="nome_empresa" placeholder="Nome da empresa" onblur="VerifNomeEmp()" required><br>

<span id = "msg_nome_empresa" style="color:red;"></span>
<br>

<input type="text" name="cnpj" id="cnpj" placeholder="CNPJ" onblur="Valid_cnpj()" required><br>

<span id ="msg_cnpj_empresa" style="color:red;"></span>
<br>

<input type="text" name="razao_social" id="razao_social" placeholder="Razão Social" onblur="VerifRazaoSocial()" required><br>

<span id="msg_razao_empresa" style="color:red;"></span>
<br>

<input type="email" name="email" id="email_empresa" placeholder="Email" onblur="Verifemail('empresa')" required><br>

<span id="msg_email_empresa" style="color:red;"></span>
<br>

<input type="text" name="endereco" id="endereco" placeholder="Endereço"  required><br>

<span id="msg_endereco_empresa" style="color:red;"></span>
<br>

<input type="text" name="telefone_emp" id="Tel" placeholder="Telefone" onblur="VerifTelefone()" required><br>

<span id="msg_telefone_empresa" style="color:red;"></span>
<br>

<div style="position: relative; width: 100%;">
    <textarea
        name="descricao_empresa"
        id="descricao_empresa"
        placeholder="Breve descrição"
        maxlength="1500"
        rows="4"></textarea>

    <span
        id="contador_descricao_empresa"
        style="
            position: absolute;
            bottom: 8px;
            right: 10px;
            font-size: 12px;
            color: gray;
            pointer-events: none;">
        0 / 1500
    </span>
</div>

<input type="password" name="senha" id="senhaEM" placeholder="Senha" onblur="VerifPassword('empresa')" required>
<i class="bi bi-eye-fill" id="btn_senhaver_em" onclick="mostrarsenha('empresa')"></i>

<br>
<span id="msg_senha_empresa" style="color:red;"></span><br>

<input type="password" name="confirmar_senha" id="confirmar_senha_empresa" placeholder="Confirmar senha" onblur="ConfirmPassword('empresa')" required>

<br>
<span id="msg_confirmar_empresa" style="color:red;"></span>
<br>    
<button type="submit">Cadastrar Empresa</button>
<input type="button" value="Voltar" onclick="voltar_cadastro()">
</form>

<button id="btn_voltar" onclick="vlt_inicio_anime()">inicio</button>

</body>
</html>
