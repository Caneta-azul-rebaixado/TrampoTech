<?php
include("acesso.php");
session_start();

header('Content-Type: application/json');

// Verifica login
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'aluno') {
    echo json_encode([
        "success" => false, 
        "message" => "Você precisa estar logado como aluno."
    ]);
    exit;
}

// Verifica requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['vaga_id'])) {
    echo json_encode([
        "success" => false, 
        "message" => "Requisição inválida."
    ]);
    exit;
}

$login_id = $_SESSION['id'];
$vaga_id = intval($_POST['vaga_id']);

// Buscar aluno_id corretamente (prepare)
$stmt = $mysqli->prepare("SELECT id FROM alunos WHERE aluno_id = ?");
$stmt->bind_param("i", $login_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo json_encode([
        "success" => false, 
        "message" => "Aluno não encontrado."
    ]);
    exit;
}

$aluno = $res->fetch_assoc();
$aluno_id = $aluno['id'];

// Verificar se já existe candidatura (prepare)
$stmt = $mysqli->prepare("
    SELECT id FROM interesse_vagas 
    WHERE aluno_id = ? AND vaga_id = ?
");
$stmt->bind_param("ii", $aluno_id, $vaga_id);
$stmt->execute();
$check = $stmt->get_result();

if ($check->num_rows > 0) {
    echo json_encode([
        "success" => false, 
        "message" => "Você já se candidatou a esta vaga."
    ]);
    exit;
}

// Inserir candidatura (prepare)
$stmt = $mysqli->prepare("
    INSERT INTO interesse_vagas (aluno_id, vaga_id, data_interesse) 
    VALUES (?, ?, NOW())
");
$stmt->bind_param("ii", $aluno_id, $vaga_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Interesse registrado com sucesso!"
    ]);
} else {
    if ($mysqli->errno == 1062) {
        echo json_encode([
            "success" => false, 
            "message" => "Você já se candidatou a esta vaga."
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Erro no servidor."
        ]);
    }
}
?>