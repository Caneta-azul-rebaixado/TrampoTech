<?php
include("acesso.php");
session_start();

if (!isset($_SESSION['id'])) {
    echo "Usuário não logado, Faça login por gentileza.";
    die();
}

// ========================
// ATUALIZAÇÃO (POST)
// ========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($_SESSION['tipo'] === 'aluno') {

        // UPDATE dados principais
        $stmt = $mysqli->prepare("
            UPDATE alunos 
            SET nome=?, ra=?, data_nasc=?, regiao=?, telefone=?, experiencia=?
            WHERE aluno_id=?
        ");

        $stmt->bind_param(
            "ssssssi",
            $_POST['nome'],
            $_POST['ra'],
            $_POST['data_nasc'],
            $_POST['regiao'],
            $_POST['telefone_aln'],
            $_POST['experiencia'],
            $_SESSION['id']
        );

        $stmt->execute();

        // ========================
        // ATUALIZAR HABILIDADES
        // ========================

        // remove antigas
        $stmt = $mysqli->prepare("
            DELETE FROM aluno_habilidades 
            WHERE aluno_id = ?
        ");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();

        // insere novas
        if (isset($_POST['habilidades'])) {
            foreach ($_POST['habilidades'] as $req_id) {

                $req_id = (int)$req_id;

                $stmt = $mysqli->prepare("
                    INSERT INTO aluno_habilidades (aluno_id, requisito_id)
                    VALUES (?, ?)
                ");
                $stmt->bind_param("ii", $_SESSION['id'], $req_id);
                $stmt->execute();
            }
        }

        echo "<p>Dados atualizados com sucesso!</p>";
    }

    elseif ($_SESSION['tipo'] === 'empresa') {

        $stmt = $mysqli->prepare("
            UPDATE empresas 
            SET nome_fantasia=?, cnpj=?, razao_social=?, endereco=?, telefone=?, descricao=?
            WHERE empresa_id=?
        ");

        $stmt->bind_param(
            "ssssssi",
            $_POST['nome_empresa'],
            $_POST['cnpj'],
            $_POST['razao_social'],
            $_POST['endereco'],
            $_POST['telefone_emp'],
            $_POST['descricao_empresa'],
            $_SESSION['id']
        );

        $stmt->execute();

        echo "<p>Dados atualizados com sucesso!</p>";
    }
}

// ========================
// BUSCAR DADOS
// ========================
$dados = null;

if ($_SESSION['tipo'] === 'aluno') {

    // dados do aluno
    $stmt = $mysqli->prepare("SELECT * FROM alunos WHERE aluno_id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $dados = $stmt->get_result()->fetch_assoc();

    // habilidades do aluno
    $habilidades_usuario = [];

    $stmt = $mysqli->prepare("
        SELECT requisito_id 
        FROM aluno_habilidades 
        WHERE aluno_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $habilidades_usuario[] = $row['requisito_id'];
    }
}

elseif ($_SESSION['tipo'] === 'empresa') {

    $stmt = $mysqli->prepare("SELECT * FROM empresas WHERE empresa_id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $dados = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
</head>
<body>

<h1>Editar Perfil</h1>

<?php if ($_SESSION['tipo'] === 'aluno'): ?>

<form method="post">

<input type="text" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>" required><br>

<input type="text" name="ra" value="<?= htmlspecialchars($dados['ra']) ?>" required><br>

<input type="date" name="data_nasc" value="<?= $dados['data_nasc'] ?>" required><br>

<select name="regiao" required>
    <option value="">Selecione a região</option>
    <option value="Varzera Paulista" <?= ($dados['regiao']=="Varzera Paulista")?'selected':'' ?>>Várzea Paulista</option>
    <option value="Campo Limpo Paulista" <?= ($dados['regiao']=="Campo Limpo Paulista")?'selected':'' ?>>Campo Limpo Paulista</option>
    <option value="Cabreuva" <?= ($dados['regiao']=="Cabreuva")?'selected':'' ?>>Cabreúva</option>
    <option value="Itupeva" <?= ($dados['regiao']=="Itupeva")?'selected':'' ?>>Itupeva</option>
    <option value="Jarinu" <?= ($dados['regiao']=="Jarinu")?'selected':'' ?>>Jarinu</option>
    <option value="Itatiba" <?= ($dados['regiao']=="Itatiba")?'selected':'' ?>>Itatiba</option>
    <option value="Louveira" <?= ($dados['regiao']=="Louveira")?'selected':'' ?>>Louveira</option>
</select><br>

<input type="text" name="telefone_aln" value="<?= htmlspecialchars($dados['telefone']) ?>" required><br>

<!-- HABILIDADES -->
<label>Habilidades:</label><br>
<table>
<tr>
<td><input type="checkbox" name="habilidades[]" value="1" <?= in_array(1, $habilidades_usuario) ? 'checked' : '' ?>> PHP</td>
<td><input type="checkbox" name="habilidades[]" value="2" <?= in_array(2, $habilidades_usuario) ? 'checked' : '' ?>> JavaScript</td>
<td><input type="checkbox" name="habilidades[]" value="3" <?= in_array(3, $habilidades_usuario) ? 'checked' : '' ?>> MySQL</td>
</tr>
<tr>
<td><input type="checkbox" name="habilidades[]" value="4" <?= in_array(4, $habilidades_usuario) ? 'checked' : '' ?>> HTML/CSS</td>
<td><input type="checkbox" name="habilidades[]" value="5" <?= in_array(5, $habilidades_usuario) ? 'checked' : '' ?>> Suporte Técnico</td>
<td><input type="checkbox" name="habilidades[]" value="6" <?= in_array(6, $habilidades_usuario) ? 'checked' : '' ?>> Redes</td>
</tr>
<tr>
<td><input type="checkbox" name="habilidades[]" value="7" <?= in_array(7, $habilidades_usuario) ? 'checked' : '' ?>> Excel</td>
<td><input type="checkbox" name="habilidades[]" value="8" <?= in_array(8, $habilidades_usuario) ? 'checked' : '' ?>> Git</td>
<td><input type="checkbox" name="habilidades[]" value="9" <?= in_array(9, $habilidades_usuario) ? 'checked' : '' ?>> Linux</td>
</tr>
<tr>
<td><input type="checkbox" name="habilidades[]" value="10" <?= in_array(10, $habilidades_usuario) ? 'checked' : '' ?>> Power BI</td>
</tr>
</table>

<textarea name="experiencia"><?= htmlspecialchars($dados['experiencia']) ?></textarea><br>

<button type="submit">Salvar</button>

</form>

<?php elseif ($_SESSION['tipo'] === 'empresa'): ?>

<form method="post">

<input type="text" name="nome_empresa" value="<?= htmlspecialchars($dados['nome_fantasia']) ?>" required><br>

<input type="text" name="cnpj" value="<?= htmlspecialchars($dados['cnpj']) ?>" required><br>

<input type="text" name="razao_social" value="<?= htmlspecialchars($dados['razao_social']) ?>" required><br>

<input type="text" name="endereco" value="<?= htmlspecialchars($dados['endereco']) ?>" required><br>

<input type="text" name="telefone_emp" value="<?= htmlspecialchars($dados['telefone']) ?>" required><br>

<textarea name="descricao_empresa"><?= htmlspecialchars($dados['descricao']) ?></textarea><br>

<button type="submit">Salvar</button>

</form>

<?php endif; ?>

</body>
</html>