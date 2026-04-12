<?php
include 'acesso.php';
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>TrampoTec</title>
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
       
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url("./images_data/background_inicio.png") center/cover no-repeat fixed;
    padding-top: 70px; /* Espaço para o menu fixo */

    color: white;
    font-family: Arial, Helvetica, sans-serif;
}

.hero-section {
    display: flex;
    gap: 40px;
    align-items: flex-start;
    padding: 40px 60px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

@media (max-width: 900px) {
    .hero-section {
        flex-direction: column;
        padding: 30px 24px;
    }
    .hero-right {
        width: 100%;
    }
}

.hero-left {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.hero-right {
    flex: 0 0 auto;
    width: 300px;
}

/* CAIXA PRINCIPAL */
.caixa_cadastroinicio {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);

    border-radius: 15px;
    padding: 60px;

    text-align: center;
    position: sticky;
    top: 90px;
}

/* TÍTULO */
h1 {
    margin-top: 0;
    color: orange;
    text-align: left;
    font-size: 36px;
}

/* TEXTO */
h2 {
    text-align: left;
    margin: 20px 0;
    line-height: 1.6;
    font-size: 16px;
}

/* BOTÕES */
button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;

    border: none;
    border-radius: 10px;

    
    font-size: 17px;
    cursor: pointer;

    transition: 0.3s;
}


/* BOTÃO CADASTRO */
.btn-cadastro {
    background: orange;
    color: black;
}

.btn-cadastro:hover {
    background: #ffb733;
}

/* BOTÃO LOGIN */
.btn-login {
    background: transparent;
    color: white;
    border: 1px solid white;
}

.btn-login:hover {
    background: white;
    color: black;
}

menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background: rgba(0, 0, 0, 0.78);
    backdrop-filter: blur(12px);
    padding: 12px 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    gap: 16px;
}

menu a {
    color: white;
    text-decoration: none;
    padding: 10px 14px;
    transition: background 0.25s ease, transform 0.2s ease, border-color 0.25s ease;
}

menu a:hover {
    background: rgba(255,255,255,0.16);
    transform: translateY(-1px);
    border-color: rgba(255,255,255,0.28);
}
#sobre {
    scroll-margin-top: 90px;
    margin: 40px auto 0;
    max-width: 900px;
    width: 90%;
}

/* QUEM SOMOS */
#quem-somos {
    scroll-margin-top: 90px;
    margin: 40px auto 0;
    max-width: 900px;
    width: 90%;
}

.quem-somos-card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 22px;
    padding: 40px 35px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    backdrop-filter: blur(12px);
}

.quem-somos-card p {
    color: #ddd;
    line-height: 1.8;
    font-size: 16px;
    margin-bottom: 18px;
    text-align: left;
}

.sobre-card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 22px;
    padding: 40px 35px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    backdrop-filter: blur(12px);
}

.sobre-badge {
    display: inline-block;
    background: rgba(255,165,0,0.18);
    color: orange;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 12px;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.sobre-card h2, .quem-somos-card h2 {
    color: white;
    font-size: 32px;
    margin: 16px 0 24px;
    text-align: left;
}

.sobre-card p {
    color: #ddd;
    line-height: 1.8;
    font-size: 16px;
    margin-bottom: 18px;
    text-align: left;
}

#quem-somos h2 {
    color: orange;
    font-size: 28px;
    margin-top: 0;
    margin-bottom: 20px;
    text-align: center;
}

#quem-somos p {
    line-height: 1.8;
    font-size: 16px;
    color: #ddd;
    margin-bottom: 20px;
    text-align: center;
}

.team-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
    max-width: 900px;
}

.team-card {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.team-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(255,165,0,0.3);
    border-color: rgba(255,165,0,0.5);
}

.team-card strong {
    color: orange;
    display: block;
    font-size: 16px;
    margin-bottom: 8px;
}

.team-card p {
    font-size: 13px;
    color: #aaa;
    margin: 0;
    text-align: center;
}

/* FOOTER */
footer {
    position: relative;
    margin-top: 40px;
    padding: 20px;
    text-align: center;
    font-size: 12px;
    color: #aaa;
    border-top: 1px solid rgba(255,255,255,0.1);
}
</style>

</head>
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
            <a href="ver_alunos.php">Alunos</a>
        <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="cadastro_users.php">Cadastrar</a>
    <?php endif; ?>
    <a href="#sobre">Sobre</a>
    <a href="#quem-somos">Quem Somos ?</a>
    <?php if (isset($_SESSION['id'])): ?>
        <a href="logout.php">Sair</a>
    <?php endif; ?>
</menu>
    <div class="hero-section">
        <div class="hero-left">
            <h1>Bem-vindo ao TrampoTec</h1>
            
            <h2>
                Nosso site é uma plataforma dedicada a conectar estudantes do curso de Desenvolvimento de Sistemas da ETECVAV com o mercado de trabalho.<br><br>

                Assim como sabemos toda empresa independete de seu porte precisa de tecnologia, e ela precisa de vocês alunos que buscam crescer numa area tão vasta e cheia de oportunidades pela nossa região.<br><br>
                Nosso objetivo é facilitar essa conexão, permitindo que vocês encontrem vagas de estágio, emprego e projetos relacionados à tecnologia, criados pelas proprias empresas locais que buscam por novos talentos em nosso curso técnico.<br><br>
            </h2>
        </div>

        <?php if (!isset($_SESSION['id'])): ?>
        <div class="hero-right">
            <div class="caixa_cadastroinicio">
                <p>Seu futuro começa aqui !</p>
                <button class="btn-cadastro" onclick="manda_cadastro_anime()">
                    Cadastre-se
                </button>

                <p style="margin-top:20px;">Já possuo cadastro:</p>
                <button class="btn-login" onclick="location.href='login.php'">
                    Login
                </button>

            </div>
        </div>
        <?php endif; ?>
    </div>

<section id="sobre">
    <div class="sobre-card">
        <div class="sobre-badge">Sobre o Projeto</div>
        <h2>Por que o TrampoTec existe?</h2>
        <p>O TrampoTec nasceu como um projeto de TCC do curso técnico em Desenvolvimento de Sistemas da ETECVAV. Ele conecta estudantes com vagas de estágio e emprego na área de tecnologia, ajudando alunos e empresas locais a se encontrarem de forma mais rápida e profissional.</p>
        <p>O sistema foi construído com HTML, CSS e JavaScript no frontend, PHP e MySQL no backend, usando XAMPP para facilitar o desenvolvimento. Nosso objetivo foi criar uma plataforma funcional, intuitiva e útil para os nossos colegas e para as empresas da região.</p>
        <p>Esse projeto representa as competências que desenvolvemos ao longo do curso: lógica, arquitetura web, validação de dados e experiência do usuário. Ele é uma ponte entre estudantes e oportunidades reais.</p>
    </div>
</section>

<section id="quem-somos">
    <div class="quem-somos-card">
        <div class="sobre-badge">Quem Somos</div>
        <p>Somos uma equipe formada por 4 alunos da ETECVAV, após chegarmos ao período do segundo semestre do nosso curso foi feito um estudo para procurar um problema e desenvolver uma solução. Percebemos que muitos alunos têm dificuldade de encontrar vagas de estágio e emprego na área de tecnologia, e por isso criamos o TrampoTec.</p>
        
        <p>Nossa meta era abranjear todos os cursos técnicos da escola, mas focamos no nosso curso para entregar um produto de qualidade que realmente atendesse as necessidades dos nossos colegas.</p>
        
        <p>O objetivo é oferecer uma plataforma que seja a ponte entre aluno e empresa, facilitando oportunidades de estágio e emprego. <strong style="color: orange;">Futuro esse que começa com vocês alunos.</strong></p>
        
        <p>Agradecemos a todos que contribuíram para a realização deste projeto, e esperamos que o TrampoTec seja uma ferramenta útil para os estudantes do curso de Desenvolvimento de Sistemas da ETECVAV e para as empresas locais que buscam por novos talentos.</p>

        <h2 style="margin-top: 40px;">Nossa Equipe</h2>
        <div class="team-container">
            <div class="team-card">
                <strong>Leonardo Duarte</strong>
                <p>Back-end e Banco de Dados</p>
            </div>
            <div class="team-card">
                <strong>Joaquim Ramalho</strong>
                <p>Front-end e Design</p>
            </div>
            <div class="team-card">
                <strong>Gabriel Silva</strong>
                <p>Front-end e Design</p>
            </div>
            <div class="team-card">
                <strong>Fernando Torres</strong>
                <p>Documentação e Design</p>
            </div>
        </div>
    </div>
    </section>

    <footer>
    © 2026 TrampoTec
</footer>

</body>
</html>