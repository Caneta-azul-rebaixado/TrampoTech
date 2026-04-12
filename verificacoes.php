<?php
include "acesso.php";

$email = $_GET['email'] ?? null;
$ra = $_GET['ra'] ?? null;
$cnpj = $_GET['cnpj'] ?? null;
$razao_social = $_GET['razao_social'] ?? null;

// Criamos um array vazio para guardar as respostas
$respostas = [];

// Só faz a busca se o dado foi enviado via GET
if ($email) {
    $stmt = $mysqli->prepare("SELECT id FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $respostas['email_exists'] = $stmt->get_result()->num_rows > 0;
}

if ($ra) {
    $stmt = $mysqli->prepare("SELECT id FROM alunos WHERE ra = ?");
    $stmt->bind_param("s", $ra);
    $stmt->execute();
    $respostas['ra_exists'] = $stmt->get_result()->num_rows > 0;
}

if ($cnpj) {
    $stmt = $mysqli->prepare("SELECT id FROM empresas WHERE cnpj = ?");
    $stmt->bind_param("s", $cnpj);
    $stmt->execute();
    $respostas['cnpj_exists'] = $stmt->get_result()->num_rows > 0;
}

if ($razao_social) {
    $stmt = $mysqli->prepare("SELECT id FROM empresas WHERE razao_social = ?");
    $stmt->bind_param("s", $razao_social);
    $stmt->execute();
    $respostas['razao_social_exists'] = $stmt->get_result()->num_rows > 0;
}

// ÚNICO echo no final do arquivo
header('Content-Type: application/json'); // Avisa o navegador que é um JSON
echo json_encode($respostas);
exit;
?>