function mostrarAluno() {
    document.getElementById("escolha").style.display = "none";
    document.getElementById("formAluno").style.display = "block";
}

function mostrarEmpresa() {
    document.getElementById("escolha").style.display = "none";
    document.getElementById("formEmpresa").style.display = "block";
}

// ================= SUBMIT =================

document.querySelector("form").addEventListener("submit", function(e) {
    e.preventDefault();

    const valido =
        validarNomeAluno() &&
        validarRA() &&
        validarData() &&
        validarEmail('aluno') &&
        validarSenha('aluno') &&
        validarConfirmacao('aluno');

    if (!valido) return;

    verificarBanco();
});


// ================= VALIDAÇÕES =================

function validarNomeAluno() {
    const valor = document.getElementById('nome_aluno').value.trim();
    const msg = document.getElementById("msg_nome_aluno");

    const regex = /^[A-Za-zÀ-ÿ\s]+$/;

    if (valor === "") {
        msg.innerHTML = "Nome obrigatório!";
        return false;
    }

    if (!regex.test(valor)) {
        msg.innerHTML = "Nome inválido!";
        return false;
    }

    msg.innerHTML = "";
    return true;
}

function validarRA() {
    const valor = document.getElementById('ra').value.trim();
    const msg = document.getElementById("msg_ra_aluno");

    const regex = /^\d{12}-\d$/;

    if (valor === "") return true;

    if (!regex.test(valor)) {
        msg.innerHTML = "RA inválido!";
        return false;
    }

    msg.innerHTML = "";
    return true;
}

function validarData() {
    const valor = document.getElementById('data_nasc').value;
    const msg = document.getElementById("msg_data_nasc_aluno");

    if (!valor) {
        msg.innerHTML = "Data obrigatória!";
        return false;
    }

    let hoje = new Date();
    let nascimento = new Date(valor);
    let idade = hoje.getFullYear() - nascimento.getFullYear();
    let m = hoje.getMonth() - nascimento.getMonth();

    if (m < 0 || (m === 0 && hoje.getDate() < nascimento.getDate())) {
        idade--;
    }

    if (idade < 16) {
        msg.innerHTML = "Mínimo 16 anos.";
        return false;
    }

    msg.innerHTML = "";
    return true;
}

function validarEmail(tipo) {
    const valor = document.getElementById(
        tipo === 'aluno' ? 'email_aluno' : 'email_empresa'
    ).value.trim();

    const msg = document.getElementById(
        tipo === 'aluno' ? "msg_email_aluno" : "msg_email_empresa"
    );

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (valor === "") {
        msg.innerHTML = "Email obrigatório!";
        return false;
    }

    if (!regex.test(valor)) {
        msg.innerHTML = "Email inválido!";
        return false;
    }

    msg.innerHTML = "";
    return true;
}

function validarSenha(tipo) {
    const valor = document.getElementById(
        tipo === 'aluno' ? 'senhaAL' : 'senhaEM'
    ).value.trim();

    const msg = document.getElementById(
        tipo === 'aluno' ? "msg_senha_aluno" : "msg_senha_empresa"
    );

    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    if (valor === "") {
        msg.innerHTML = "Senha obrigatória!";
        return false;
    }

    if (!regex.test(valor)) {
        msg.innerHTML = "Senha fraca!";
        return false;
    }

    msg.innerHTML = "";
    return true;
}

function validarConfirmacao(tipo) {
    const senha = document.getElementById(
        tipo === 'aluno' ? 'senhaAL' : 'senhaEM'
    ).value.trim();

    const confirmar = document.getElementById(
        tipo === 'aluno' ? 'confirmar_senha_aluno' : 'confirmar_senha_empresa'
    ).value.trim();

    const msg = document.getElementById(
        tipo === 'aluno' ? "msg_confirmar_aluno" : "msg_confirmar_empresa"
    );

    if (confirmar === "") {
        msg.innerHTML = "Confirmação obrigatória!";
        return false;
    }

    if (senha !== confirmar) {
        msg.innerHTML = "Senhas não coincidem!";
        return false;
    }

    msg.innerHTML = "";
    return true;
}


// ================= FETCH (BANCO) =================

function verificarBanco() {

    const form = document.querySelector("form");

    const email = document.getElementById("email_aluno").value.trim();
    const ra = document.getElementById("ra").value.trim();
    const cnpj = document.getElementById("cnpj")?.value.trim();
    const razao = document.getElementById("razao_social")?.value.trim();

    fetch(`verificacoes.php?email=${encodeURIComponent(email)}&ra=${encodeURIComponent(ra)}&cnpj=${encodeURIComponent(cnpj)}&razao_social=${encodeURIComponent(razao)}`)
    .then(res => res.json())
    .then(dados => {

        let erro = false;

        if (dados.email_exists) {
            document.getElementById("msg_email_aluno").innerHTML = "Email já cadastrado!";
            erro = true;
        }

        if (dados.ra_exists) {
            document.getElementById("msg_ra_aluno").innerHTML = "RA já cadastrado!";
            erro = true;
        }

        if (dados.cnpj_exists) {
            document.getElementById("msg_cnpj_empresa").innerHTML = "CNPJ já cadastrado!";
            erro = true;
        }

        if (dados.razao_social_exists) {
            document.getElementById("msg_razao_empresa").innerHTML = "Razão social já cadastrada!";
            erro = true;
        }

        if (!erro) {
            form.submit(); // ✅ envia de verdade
        }

    })
    .catch(err => console.error(err));
}