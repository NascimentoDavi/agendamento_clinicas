<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

{{-- Bootsrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

{{-- Bootstrap CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<style>
    body {
        font-family: "Montserrat", sans-serif;
    }

    :root {
        --blue-color: #2596be;
        --secondary-color: #7aacce;
        --third-color: #fc7c34;
        --light-color: #ecf5f9;
    }

    .link-agendar {
        background-color: var(--secondary-color);
        color: white;
        text-decoration: none;
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .link-agendar:hover {
        background-color: var(--blue-color);
        color: white;
        transform: translateX(4px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .link-agendar i {
        transition: transform 0.3s ease;
    }

    .link-agendar:hover i {
        transform: scale(1.2) rotate(-5deg);
    }

    .link-logout {
        background-color: var(--third-color);
        color: white;
        text-decoration: none;
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .link-logout:hover {
        background-color: var(--third-color);
        color: white;
        transform: translateX(4px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .link-logout i {
        transition: transform 0.3s ease;
    }

    .link-logout:hover i {
        transform: scale(1.2) rotate(-5deg);
    }

    @media (max-width: 991.98px) {
        #main-container {
            margin-top: 56px; /* ALTURA NAVBAR FIXA */
        }
    }

    @media (min-width: 992px) {
        #main-container {
            margin-top: 0;
        }
    }

    /* Esconde o submenu por padrão */
    .collapse-custom {
    display: none;
    transition: all 0.3s ease;
    }

    /* Mostra quando ativo */
    .collapse-custom.show {
    display: block;
    }
    
    /* --- ESTILOS DA NAVBAR E TRANSIÇÃO --- */
    #mainNavbar {
        width: 250px;
        background-color: var(--blue-color);
        transition: width 0.3s ease;
        overflow-x: hidden; /* Previne conteúdo de vazar durante a transição */
    }

    /* --- ESTILOS PARA NAVBAR RECOLHIDA (CLASSE .collapsed) --- */
    
    /* 1. Reduz a largura da navbar */
    #mainNavbar.collapsed {
        width: 80px;
    }

    /* 2. Esconde os elementos que não são ícones */

    #mainNavbar.collapsed h5,
    #mainNavbar.collapsed div[classs=""] { /* OBS: Há um typo 'classs' no seu HTML original */
        opacity: 0;
    }

    /* 3. Esconde o texto dos links e centraliza os ícones */
    #mainNavbar.collapsed .link-agendar,
    #mainNavbar.collapsed .link-logout {
        font-size: 0; /* Truque para esconder o nó de texto */
        justify-content: center;
        padding: 0.75rem;
    }

    /* 4. Restaura o tamanho do ícone, que foi afetado pelo font-size: 0 */
    #mainNavbar.collapsed .link-agendar i,
    #mainNavbar.collapsed .link-logout i {
        font-size: 1.1rem; /* Ou o tamanho que preferir para os ícones */
        margin: 0;
    }

    #logo-faesa {
        transition: opacity 0.3s ease-in-out;
    }
</style>

<nav class="navbar navbar-dark bg-primary d-lg-none fixed-top shadow-sm px-3" style="height: 56px">
    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
        <i class="bi bi-list"></i>
    </button>
    <img src="{{ asset('img/faesa_logo_expandido.png') }}" alt="Logo FAESA" class="mx-auto d-block" style="width: 100px;" >
</nav>


<div id="main-container" class="d-flex min-vh-100">

<!-- SIDEBAR DESKTOP -->
<nav class="p-3 d-none d-lg-flex flex-column align-items-center shadow-lg" id="mainNavbar">
    <!-- LOGO DA FAESA - NAVBAR -->
    <img src="{{ asset('img/faesa_logo_expandido.png') }}" alt="Logo" class="img-fluid mb-2" id="logo-faesa" width="150px" />

    <!-- TITULO SIDEBAR -->
    <h5 class="mb-2 mt-3 p-2 rounded-3 text-center"
        style="color: white; font-size: 18px;">
        <strong>Clínica de Psicologia</strong>
        <p class="p-0 m-0 text-center" style="font-size: 12px;"><em>Aluno</em></p>
    </h5>

    <!-- DADOS DA SESSAO DO USUARIO -->
    <div classs="">
        <p style="color:#ecf5f9" class="p-0 m-0 text-center">
            {{ session('aluno')[0] }}
        </p>
        <p style="color:#ecf5f9" class="p-0 m-0 text-center">
            {{ session('aluno')[1] }}
        </p>
        <p style="color:#ecf5f9" class="p-0 m-0 text-center">
            {{ session('aluno')[2] }}
        </p>
    </div>

    <ul class="list-group list-group-flush w-100 gap-1 mt-3">
        <!-- LINKS -->

        <!-- PÁGINA INICIAL - MENU AGENDA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/aluno" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-house"></i> Início
            </a>
        </li>

        <!-- INCLUIR AGENDAMENTO -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/aluno/criar-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-calendar-plus"></i> Criar Agenda
            </a>
        </li>

        <!-- CONSULTAR AGENDA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/aluno/consultar-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-pencil-square"></i> Agendas
            </a>
        </li>

        <!-- LOGOUT -->
        <li class="list-group-item mt-auto rounded-1 p-0 overflow-hidden ">
            <a href="/logout" class="link-logout d-flex align-items-center gap-2 p-1">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </li>

    </ul>
</nav>

<!-- OFFCANVAS MOBILE - ESQUERDA PARA DIREITA -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header" style="background-color: var(--blue-color);">
        <h5 class="offcanvas-title text-white" id="offcanvasMenuLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body p-0" style="background-color: var(--light-color);">
        <ul class="list-group list-group-flush w-100">
            <!-- PÁGINA INICIAL - MENU AGENDA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/aluno" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="fas fa-home"></i> Início
            </a>
        </li>

        <!-- INCLUIR AGENDAMENTO -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/aluno/criar-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="fas fa-calendar-plus"></i> Criar Agenda
            </a>
        </li>

        <!-- CONSULTAR AGENDA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/aluno/consultar-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="fas fa-edit"></i> Agendas
            </a>
        </li>

        <!-- LOGOUT -->
        <li class="list-group-item mt-auto rounded-1 p-0 overflow-hidden ">
            <a href="/logout" class="link-logout d-flex align-items-center gap-2 p-1">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
        </ul>
    </div>
</div>
