// MOSTRAR FORM
function mostrarAluno() {
    document.getElementById("escolha").style.display = "none";
    document.getElementById("formAluno").style.display = "block";
}

function mostrarEmpresa() {
    document.getElementById("escolha").style.display = "none";
    document.getElementById("formEmpresa").style.display = "block";
}

// MOSTRAR SENHA
function mostrarsenha(tipo) {
    if (tipo === 'aluno') {
        input = document.getElementById("senhaAL");
        icon = document.getElementById("btn_senhaver_al");
    } else if (tipo === 'empresa') {
        input = document.getElementById("senhaEM");
        icon = document.getElementById("btn_senhaver_em");
    } else {
        input = document.getElementById("senhalogin");
        icon = document.getElementById("btn_senhaver_login");
    }
     if (!input || !icon) return; // evita erro
     
     if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('bi-eye-fill','bi-eye-slash-fill');
    } else {
        input.type = "password";
        icon.classList.replace('bi-eye-slash-fill','bi-eye-fill');
    }
}

function VerifNomeAL() {
    let nome_al = document.getElementById("nome_aluno").value.trim();
    let msg = document.getElementById("msg_nome_aluno");
    
    if (nome_al === "") {
        msg.innerHTML = "O nome do aluno é obrigatório !";
        return false;
    }
    
    const regex = /^[A-Za-zÀ-ÿ\s]+$/
    if (!regex.test(nome_al)) {
        msg.innerHTML = "Nome inválido! Use apenas letras e espaços.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

function VerifNomeEmp(){
    let nome_emp = document.getElementById("nome_empresa").value.trim();
    let msg = document.getElementById("msg_nome_empresa");

    if(nome_emp === ""){
        msg.innerHTML ="O nome da empresa é obrigatório !";
        return false;
    }

    const regex = /^[a-zA-Z\s]+$/;
    if(!regex.test(nome_emp)){
        msg.innerHTML = "Nome Inválido ! Use apenas letras e espaços.";
        return false;
    }else{
        msg.innerHTML = "";
        return true;
    }
}

function VerifRA(){
    let ra = document.getElementById("ra").value.trim();
    let msg = document.getElementById("msg_ra_aluno");

    const regex = /^\d{12}-\d$/;
    
    if (ra === "") {
        msg.innerHTML = "O RA é obrigatório.";
        return false;
    }
    
    if (!regex.test(ra)) {
        msg.innerHTML = "RA inválido! Use o formato: 000000000000-0";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

function VerifIDADE() {
    let data_dig = document.getElementById("data_nasc").value;
    let msg = document.getElementById("msg_data_nasc_aluno");

    if (!data_dig) {
        msg.innerHTML = "A data de nascimento é obrigatória !";
        return false;
    }

    const actualYear = new Date().getFullYear();
    const yearMin = actualYear - 100; // Ex: 1926
    const yearMax = actualYear;       // Ex: 2026

    // Criamos o objeto de data para extrair o ano digitado
    let nascimento = new Date(data_dig);
    let anoNascimento = nascimento.getUTCFullYear(); // Usar UTC evita erros de fuso horário

    // 1ª VALIDAÇÃO: O ano existe na nossa realidade? (Deve vir ANTES de calcular idade)
    if (anoNascimento < yearMin || anoNascimento > yearMax) {
        msg.innerHTML = "Ano de nascimento inválido (fora do limite)!";
        return false;
    }

    // 2ª VALIDAÇÃO: Cálculo da idade real
    let hoje = new Date();
    let idade = hoje.getFullYear() - anoNascimento;
    let m = hoje.getMonth() - nascimento.getUTCMonth();
    let d = hoje.getDate() - nascimento.getUTCDate();

    if (m < 0 || (m === 0 && d < 0)) {
        idade--;
    }

    if (idade < 16) {
        msg.innerHTML = "Você deve ter pelo menos 16 anos para se cadastrar.";
        return false;
    }

    // Se chegou aqui, está tudo certo!
    msg.innerHTML = "";
    return true;
}


function Verifemail(tipo) {
    let email = document.getElementById(tipo === 'aluno' ? "email_aluno" : "email_empresa").value.trim();
    let msg = document.getElementById(tipo === 'aluno' ? "msg_email_aluno" : "msg_email_empresa");

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

if (email === "") {
    msg.innerHTML = "O email é obrigatório !";
    return false;
}

    if (!regex.test(email)) {
        msg.innerHTML = "Email inválido!";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

//Verifica a região digitada pelo usuário
function VerifRegiao() {
    let regiao = document.getElementById("regiao").value.trim();
    let msg = document.getElementById("msg_regiao_aluno");

    const regex = /^(?=.*[A-Za-zÀ-ÿ])[A-Za-zÀ-ÿ0-9\s]{2,150}$/;

    if (regiao === "") {
        msg.innerHTML = "A região é obrigatória!";
        return false;
    } 
    else if (!regex.test(regiao)) {
        msg.innerHTML = "Digite uma região válida (mínimo 2 caracteres)";
        return false;
    } 
    else {
        msg.innerHTML = "";
        return true;
    }
}

// VALIDAR SENHA
function VerifPassword(tipo) {
    let senha = document.getElementById(tipo === 'aluno' ? "senhaAL" : "senhaEM").value.trim();
    let msg = document.getElementById(tipo === 'aluno' ? "msg_senha_aluno" : "msg_senha_empresa");
    
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    if (senha === "") {
        msg.innerHTML = "A senha é obrigatória.";
        return false;
    }

    if (!regex.test(senha)) {
        msg.innerHTML = "Senha fraca! Use 8+ caracteres com maiúscula, minúscula, número e símbolo.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

// CONFIRMAR SENHA
function ConfirmPassword(tipo) {
    let senha = document.getElementById(tipo === 'aluno' ? "senhaAL" : "senhaEM").value.trim();
    let confirmar = document.getElementById(tipo === 'aluno' ? "confirmar_senha_aluno" : "confirmar_senha_empresa").value.trim();
    let msg = document.getElementById(tipo === 'aluno' ? "msg_confirmar_aluno" : "msg_confirmar_empresa");

    if (confirmar === "") {
        msg.innerHTML = "A confirmação de senha é obrigatória.";
        return false;
    }

    if (senha !== confirmar) {
        msg.innerHTML = "As senhas não coincidem.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

function Valid_cnpj() {
    let cnpj = document.getElementById("cnpj").value.trim();
    let msg = document.getElementById("msg_cnpj_empresa");

    const regex = /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/;

    if (cnpj === "") {
        msg.innerHTML = "O CNPJ é obrigatório.";
        return false;
    }

    if(!regex.test(cnpj)) {
        msg.innerHTML = "CNPJ inválido! Use o formato: 00.000.000/0000-00";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}




function VerifRazaoSocial() {
    let razao = document.getElementById("razao_social").value.trim();
    let msg = document.getElementById("msg_razao_empresa");

    if (razao === "") {
        msg.innerHTML = "A razão social é obrigatória.";
        return false;
    }

    const regex = /^[A-Za-zÀ-ÿ0-9\s&.\-\/]{3,100}$/;

    if (!regex.test(razao)) {
        msg.innerHTML = "Razão social inválida! Use apenas letras e espaços.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}


function VefTitulo() {
    let titulo = document.getElementById("titulo_vaga").value.trim();
    let msg = document.getElementById("msg_titulo_vaga");

    const regex = /^[A-Za-zÀ-ÿ0-9\s&.\-\/]{3,100}$/;

    if (titulo === "") {
        msg.innerHTML = "O título da vaga é obrigatório.";
        return false;
    }

    if (!regex.test(titulo)) {
        msg.innerHTML = "Título inválido! Use apenas letras e espaços.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}
 function VerifDescricao() {
    let descricao = document.getElementById("descricao_vaga").value.trim();
    let msg = document.getElementById("msg_descricao_vaga");

    const regex = /^[A-Za-zÀ-ÿ0-9\s&.\-\/]{10,500}$/;

    if (descricao === "") {
        msg.innerHTML = "A descrição da vaga é obrigatória.";
        return false;
    }
    
    if (!regex.test(descricao)) {
        msg.innerHTML = "Descrição inválida! Use entre 10 e 500 caracteres.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

function VerifRequisitos() {
    let requisitos = document.getElementById("requisitos_vaga").value.trim();
    let msg = document.getElementById("msg_requisitos_vaga");

    const regex = /^[A-Za-zÀ-ÿ0-9\s&.\-\/]{10,500}$/;

    if (requisitos === "") {
        msg.innerHTML = "Os requisitos da vaga são obrigatórios.";
        return false;
    }

    if (!regex.test(requisitos)) {
        msg.innerHTML = "Requisitos inválidos! Use entre 10 e 500 caracteres.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

function VerifBeneficios() {
    let beneficios = document.getElementById("beneficios_vaga").value.trim();
    let msg = document.getElementById("msg_beneficios_vaga");

    const regex = /^[A-Za-zÀ-ÿ0-9\s&.\-\/]{10,500}$/;

    if (beneficios === "") {
        msg.innerHTML = "Os benefícios da vaga são obrigatórios.";
        return false;
    }

    if (!regex.test(beneficios)) {
        msg.innerHTML = "Benefícios inválidos! Use entre 10 e 500 caracteres.";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}  

function VerifSalario() {
    let salario = document.getElementById("salario_vaga").value.trim();
    let msg = document.getElementById("msg_salario_vaga");

    const regex = /^[0-9]{1,7}([,.][0-9]{1,2})?$/;

    if (salario === "") {
        msg.innerHTML = "O salário da vaga é obrigatório.";
        return false;
    }

    if (!regex.test(salario)) {
        msg.innerHTML = "Salário inválido! Use o formato: 000.00";
        return false;
    } else {
        msg.innerHTML = "";
        return true;
    }
}

function VerifTelefone() {
        let telefone = document.getElementById("Tel").value.trim();
        let msg = document.getElementById("msg_telefone_empresa");
        
        if (telefone === "") {
            msg.innerHTML = "O telefone é obrigatório.";
            return false;
        }

        const regex = /^\(\d{2}\) \d{4,5}-\d{4}$/;

        if (!regex.test(telefone)) {
            msg.innerHTML = "Telefone inválido! Use o formato: (00) 0000-0000";
            return false;
        } else {
            msg.innerHTML = "";
            return true;
        }
    }

function validarForm(tipo){
    if (tipo === 'aluno') {
        if (
            VerifNomeAL() &&
            VerifRA() &&
            VerifIDADE() &&
            Verifemail('aluno') &&
            VerifRegiao()&&
            VerifPassword('aluno') &&
            ConfirmPassword('aluno')
        ) {
            return true;
        }
    } else if (tipo === 'empresa') {
        if (
            VerifNomeEmp() &&
            Valid_cnpj() &&
            VerifRazaoSocial() &&
            Verifemail('empresa') &&
            VerifTelefone() &&
            VerifPassword('empresa') &&
            ConfirmPassword('empresa')
        ) {
            return true;
        }
    }
    window.alert("ERRO: Confira os dados do formulário por gentileza!");
    return false;
}

// Redireciona com fade
function vlt_inicio_anime(url, tempo = 500) {
    const bg = document.getElementById("bg");
    if (bg) {
        bg.style.opacity = 0.5;
        setTimeout(() => {
            window.location.href = "inicio.php";
        }, tempo);
    } else {
        window.location.href = "inicio.php"; // Fallback se não houver #bg
    }
}

function manda_cadastro_anime(url, tempo = 500) {
    const bg = document.getElementById("bg");
    if (bg) {
        bg.style.opacity = 0.5;
        setTimeout(() => {
            window.location.href = "Cadastro_users.php";
        }, tempo);
    } else {
        window.location.href = "Cadastro_users.php"; // Fallback se não houver #bg
    }
}
