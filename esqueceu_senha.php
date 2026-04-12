<?php
require_once "acesso.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";
require "PHPMailer/src/Exception.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $mysqli->real_escape_string($_POST['email']);

    // busca usuário
    $sql = $mysqli->query("SELECT id FROM login WHERE email = '$email'");

    if ($sql->num_rows > 0) {

        $usuario = $sql->fetch_assoc();
        $usuario_id = $usuario['id'];

        $token  = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // salva token
        $mysqli->query("
            INSERT INTO reset_senha (usuario_id, token, expira_em)
            VALUES ('$usuario_id', '$token', '$expira')
        ");

        $link = "http://localhost/TCC/redefinir_senha.php?token=$token";

        // ENVIO EMAIL
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'SEU_EMAIL@gmail.com';
            $mail->Password = 'SENHA_DO_APP';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('SEU_EMAIL@gmail.com', 'Sistema');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de senha';
            $mail->Body = "
                Clique no link para redefinir sua senha:<br>
                <a href='$link'>$link</a>
            ";

            $mail->send();
            $mensagem = "Email enviado com sucesso!";
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar email.";
        }

    } else {
        // resposta genérica (segurança)
        $mensagem = "Se o email existir, enviaremos instruções.";
    }
}
?>

<form method="post">
    <input type="email" name="email" placeholder="Seu email" required>
    <button type="submit">Recuperar senha</button>
</form>

<p><?= $mensagem ?></p>
