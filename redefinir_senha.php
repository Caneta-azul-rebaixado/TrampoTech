<?php
require_once "acesso.php";

$token = $_GET['token'] ?? '';
$mensagem = "";

$sql = $mysqli->query("
    SELECT pr.id AS reset_id, pr.usuario_id
    FROM reset_senha pr
    WHERE pr.token = '$token'
      AND pr.expira_em > NOW()
      AND pr.usado = 0
");

if ($sql->num_rows == 0) {
    die("Token inválido ou expirado.");
}

$reset = $sql->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // atualiza senha
    $mysqli->query("
        UPDATE login 
        SET senha = '$senha'
        WHERE id = {$reset['usuario_id']}
    ");

    // invalida token
    $mysqli->query("
        UPDATE reset_senha
        SET usado = 1
        WHERE id = {$reset['reset_id']}
    ");

    echo "Senha redefinida com sucesso!";
    exit;
}
?>

<form method="post">
    <input type="password" name="senha" placeholder="Nova senha" required>
    <button type="submit">Redefinir senha</button>
</form>
