<?php
include("acesso.php");
session_start();

header('Content-Type: application/json');

// Verifica login
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'empresa') {
    echo json_encode([
        "success" => false, 
        "message" => "Você precisa estar logado como empresa."
    ]);
    exit;
}

// Verifica requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_aluno_interss'])) {
    echo json_encode([
        "success" => false, 
        "message" => "Requisição inválida."
    ]);
    exit;
}

$login_id = $_SESSION['id'];
$id_aluno_interss = intval($_POST['id_aluno_interss']);

$stmt = $mysqli->prepare("SELECT id FROM empresas WHERE empresa_id = ?");
$stmt->bind_param("i", $login_id);
$stmt->execute();
$result = $stmt->get_result();

// ❌ ESTAVA INVERTIDO
if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Empresa não encontrada."
    ]);
    exit;
}

$empresa = $result->fetch_assoc();
$empresa_id = $empresa['id'];

$stmt = $mysqli->prepare("
    SELECT id FROM interesse_alunos 
    WHERE empresa_int_id = ? AND id_aluno_interss = ?
");

$stmt->bind_param("ii", $empresa_id, $id_aluno_interss);
$stmt->execute();
$check = $stmt->get_result();

if ($check->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Você já interessou por este aluno"
    ]);
    exit;
}

$stmt = $mysqli->prepare("
    INSERT INTO interesse_alunos (empresa_int_id, id_aluno_interss, data_int_emp) 
    VALUES (?, ?, NOW())
");
$stmt->bind_param("ii", $empresa_id, $id_aluno_interss);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Interesse registrado com sucesso."
    ]);
} else {
    if ($mysqli->errno == 1062) {
        echo json_encode([
            "success" => false, 
            "message" => "Você já interessou por este aluno."
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Erro ao registrar interesse."
        ]);
    }
}
?>