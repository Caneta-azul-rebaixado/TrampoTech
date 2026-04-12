<?php
include("acesso.php");
session_start();

$erro = [];

if (isset($_POST['login']) && isset($_POST['senha'])) {

    $login = $mysqli->real_escape_string($_POST['login']);
    $senha = $_POST['senha'];

    // busca usuário
    $sql = "SELECT id, senha, tipo FROM login WHERE email = '$login'";
    $query = $mysqli->query($sql);

    if ($query->num_rows == 0) {
        $erro[] = "Usuário não encontrado";
    } else {
        $usuario = $query->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {

            // sessão
            $_SESSION['id']   = $usuario['id'];
            $_SESSION['tipo'] = $usuario['tipo'];

            // redireciona conforme tipo
            if ($usuario['tipo'] === 'aluno') {
                header("Location: ver_vagas.php");
            } else {
                header("Location: criar_vagas.php");
            }
            exit;

        } else {
            $erro[] = "Senha incorreta";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Acesso TrampoTec</title>
    <link rel="stylesheet" href="Tela_Inicio.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="password.js"></script>
<style>
    /* RESET */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* FUNDO */
    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url("./images_data/background_inicio.png") center/cover no-repeat;
        color: white;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* CAIXA PRINCIPAL */
    .caixa_login {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        margin-top: 20px;
    }

    /* TÍTULOS */
    h1 {
        margin-top: 0;
        margin-bottom: 20px;
        color: orange;
        font-size: 28px;
        text-align: center;
    }

    /* LABELS/TEXTO */
    a {
        display: block;
        text-align: left;
        font-size: 14px;
        margin: 10px 0 5px 0;
        font-weight: normal;
        color: white;
    }

    /* INPUTS */
    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
    }

    /* BOTÕES */
    button {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    /* BOTÃO ENTRAR */
    .btn_enter {
        background: orange;
        color: black;
    }

    .btn_enter:hover {
        background: #ffb733;
    }

    /* BOTÃO VOLTAR */
    .btn_voltar {
        background: transparent;
        color: white;
        border: 1px solid white;
        margin-top: 10px;
    }

    .btn_voltar:hover {
        background: white;
        color: black;
        transform: translateY(-2px);
    }

    /* FOOTER */
    footer {
        position: absolute;
        bottom: 10px;
        font-size: 12px;
        color: #aaa;
    }
</style>


</head>

<body>

<form method="post" class="caixa_login">
    <h1>Login</h1>

    <a>Usuário:</a>
    <input type="text" name="login" placeholder="Email" required><br>

    <a>Senha:</a>
    <input type="password" name="senha" id="senhalogin" required>
    <i class="bi bi-eye-fill" id="btn_senhaver_login" onclick="mostrarsenha('login')"></i>
     <br>
    <?php
        if(count($erro) > 0){
            foreach($erro as $msg){
                echo "<p><strong>ERRO: </strong>".$msg."</p>";
            }
        }
    ?>
    

    <button type="submit" class="btn_enter">Entrar</button>

    <button type="submit" value="Voltar" onclick="location.href='inicio.php'" class="btn_voltar">Voltar</button>
</form>

    <footer>
    © 2026 TrampoTec
    </footer>
</body>
</html>
    