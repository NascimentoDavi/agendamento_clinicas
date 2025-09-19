<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />

{{-- Bootsrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

{{-- Bootstrap CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">


<style>
    body {
        font-family: "Montserrat", sans-serif;
        background-color: var(--light-color);
    }

    :root {
        --blue-color: #0094CF;
        --secondary-color: #A0D6E9;
        --third-color: #fc7c34;
        --light-color: #ecf5f9;
        --black-color: #161F28;
        --sidebar-width: 260px;
        --sidebar-width-collapsed: 85px; /* Ajustei para um bom visual com o logo recolhido */
    }
    
    /* --- ESTRUTURA PRINCIPAL --- */
    #mainNavbar {
        background-color: var(--blue-color);
        width: var(--sidebar-width);
        min-width: var(--sidebar-width);
        transition: width 0.3s ease, min-width 0.3s ease;
        overflow-x: hidden;
    }
    
    /* O #content é o container do seu conteúdo principal (fora da navbar) */
    #content {
        flex: 1;
        transition: margin-left 0.3s ease;
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width)); /* Garante que o conteúdo não ultrapasse a tela */
    }
    
    #mainNavbar.collapsed {
        width: var(--sidebar-width-collapsed);
        min-width: var(--sidebar-width-collapsed);
    }
    
    #mainNavbar.collapsed + #content,
    #mainNavbar.collapsed ~ #content { /* O '~' ajuda a selecionar o content mesmo que não seja irmão direto */
        margin-left: var(--sidebar-width-collapsed);
        width: calc(100% - var(--sidebar-width-collapsed));
    }

    /* Esconde os textos e a seta quando estiver recolhida */
    #mainNavbar.collapsed .link-text,
    #mainNavbar.collapsed .arrow-icon,
    #mainNavbar.collapsed #sidebar-header {
        display: none;
    }
    
    /* Centraliza os ícones quando a navbar estiver recolhida */
    #mainNavbar.collapsed .sidebar-link {
        justify-content: center;
        padding: 0.75rem !important;
    }

    #mainNavbar.collapsed .submenu-link {
        display: none; /* Esconde os links do submenu quando a navbar está recolhida */
    }
    
    #logo-faesa {
        transition: opacity 0.2s ease-in-out;
    }
    
    /* --- ESTILO DOS LINKS DA SIDEBAR --- */
    .nav-item-custom {
        list-style: none;
        padding: 0; margin: 0;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        color: white;
        text-decoration: none;
        padding: 0.8rem 1rem;
        border-radius: 8px;
        transition: background-color 0.2s ease, color 0.2s ease;
        white-space: nowrap;
    }

    .sidebar-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
    }

    /* ESTADO ATIVO: Mostra qual página o usuário está */
    .sidebar-link.active {
        background-color: rgba(0, 0, 0, 0.2);
        font-weight: bold;
    }
    
    /* --- ESTILOS DOS SUBMENUS --- */
    .submenu-link {
        padding-left: 3.2rem !important;
        font-size: 0.9em;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .submenu-link:hover {
        background-color: rgba(0, 0, 0, 0.2);
    }
    
    .arrow-icon {
        transition: transform 0.3s ease;
    }

    .arrow-icon.rotate {
        transform: rotate(180deg);
    }
    .collapse {
        display: none;
    }
    /* Adiciona margin-bottom ao navbar mobile em telas pequenas */
@media (max-width: 991.98px) { /* Bootstrap define lg como >= 992px */
    nav.navbar.d-lg-none {
        margin-bottom: 56px; /* altura da navbar mobile */
    }

    /* Opcional: ajusta o conteúdo para não ficar escondido atrás do navbar */
    #main-container {
        margin-top: 56px; /* mesmo valor da navbar mobile */
    }
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
<nav class="bg-primary p-3 d-none d-lg-flex flex-column shadow-lg" id="mainNavbar">
    <div class="w-100 mb-2 text-center">
        <img src="{{ asset('img/faesa_logo_expandido.png') }}" alt="Logo" class="img-fluid" id="logo-faesa" style="width: 150px;" />
    </div>

    <div id="sidebar-header" class="text-center">
        <h5 class="mb-2 mt-3 p-2 rounded-3 text-white" style="font-size: 18px;">
            Clínica de Psicologia
            <p class="p-0 m-0" style="font-size: 12px;"><em>Administrador</em></p>
        </h5>
        <p style="color:#ecf5f9" class="p-0 m-0">
            {{ session('usuario')->USUARIO }}
        </p>
    </div>

    <ul class="list-group list-group-flush w-100 gap-1 mt-4 flex-grow-1">

        <li class="nav-item-custom">
            <a href="/psicologia" class="sidebar-link active d-flex align-items-center gap-3 p-2">
                <i class="bi bi-house fs-5"></i>
                <span class="link-text">Início</span>
            </a>
        </li>

        <li class="nav-item-custom">
            <a href="#submenu-agenda" data-bs-toggle="collapse" class="sidebar-link d-flex align-items-center gap-3 p-2">
                <i class="bi bi-calendar-event fs-5"></i>
                <span class="link-text">Agenda</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="collapse list-group list-group-flush" id="submenu-agenda">
                <li><a href="/psicologia/criar-agendamento" class="sidebar-link submenu-link p-2">Criar Agenda</a></li>
                <li><a href="/psicologia/consultar-agendamento" class="sidebar-link submenu-link p-2">Consultar Agendas</a></li>
            </ul>
        </li>

        <li class="nav-item-custom">
            <a href="#submenu-pacientes" data-bs-toggle="collapse" class="sidebar-link d-flex align-items-center gap-3 p-2">
                <i class="bi bi-people fs-5"></i>
                <span class="link-text">Pacientes</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="collapse list-group list-group-flush" id="submenu-pacientes">
                <li><a href="/psicologia/criar-paciente" class="sidebar-link submenu-link p-2">Criar Paciente</a></li>
                <li><a href="/psicologia/consultar-paciente" class="sidebar-link submenu-link p-2">Consultar Pacientes</a></li>
            </ul>
        </li>
        
        <li class="nav-item-custom">
            <a href="#submenu-config" data-bs-toggle="collapse" class="sidebar-link d-flex align-items-center gap-3 p-2">
                <i class="bi bi-gear fs-5"></i>
                <span class="link-text">Configurações</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <ul class="collapse list-group list-group-flush" id="submenu-config">
                <li><a href="/psicologia/criar-servico" class="sidebar-link submenu-link p-2">Serviços</a></li>
                <li><a href="/psicologia/criar-sala" class="sidebar-link submenu-link p-2">Salas</a></li>
                <li><a href="/psicologia/criar-horario" class="sidebar-link submenu-link p-2">Horários</a></li>
            </ul>
        </li>

        <li class="nav-item-custom">
            <a href="/psicologia/relatorios-agendamento" class="sidebar-link d-flex align-items-center gap-3 p-2">
                <i class="bi bi-bar-chart fs-5"></i>
                <span class="link-text">Relatórios</span>
            </a>
        </li>

        <li class="nav-item-custom mt-auto">
            <a href="/logout" class="sidebar-link link-logout d-flex align-items-center gap-3 p-2">
                <i class="bi bi-box-arrow-right fs-5"></i>
                <span class="link-text">Sair</span>
            </a>
        </li>
    </ul>
</nav>

<!-- OFFCANVAS MOBILE - ESQUERDA PARA DIREITA -->
<div class="offcanvas offcanvas-start d-lg-none mb-5" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header" style="background-color: var(--blue-color);">
        <h5 class="offcanvas-title text-white" id="offcanvasMenuLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body p-0" style="background-color: var(--light-color);">
        <ul class="list-group list-group-flush w-100">
        <!-- PÁGINA INICIAL - MENU AGENDA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-house"></i> Início
            </a>
        </li>


        <!-- INCLUIR AGENDAMENTO -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/criar-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-calendar-plus"></i> Criar Agenda
            </a>
        </li>


        <!-- CONSULTAR AGENDA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/consultar-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-pencil-square"></i> Agendas
            </a>
        </li>


        <!-- CADASTRAR PACIENTE -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/criar-paciente" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-person-add"></i> Criar Paciente
            </a>
        </li>

        
        <!-- CONSULTAR PACIENTE -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/consultar-paciente" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-people"></i> Pacientes
            </a>
        </li>


        <!-- CADASTRAR SERVIÇO -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/criar-servico" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-gear"></i> Serviços
            </a>
        </li>


        <!-- CADASTRAR SALA -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/criar-sala" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-door-open"></i> Salas
            </a>
        </li>


        <!-- HORÁRIOS -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/criar-horario" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-alarm"></i> Horários
            </a>
        </li>


        <!-- RELATÓRIO -->
        <li class="list-group-item rounded-1 p-0 overflow-hidden ">
            <a href="/psicologia/relatorios-agendamento" class="link-agendar d-flex align-items-center gap-2 p-1">
                <i class="bi bi-bar-chart"></i> Relatório
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('mainNavbar');
    const submenus = sidebar.querySelectorAll('.nav-item-custom');
    const logo = document.getElementById('logo-faesa');
    const storageKey = 'sidebarState';

    // Pré-carregar logos
    const logoExpandido = new Image();
    logoExpandido.src = "{{ asset('img/faesa_logo_expandido.png') }}";
    const logoRecolhido = new Image();
    logoRecolhido.src = "{{ asset('img/faesa_logo_recolhido.png') }}";

    function trocaLogo(src) {
        logo.style.opacity = 0;
        setTimeout(() => {
            logo.src = src;
            logo.style.opacity = 1;
        }, 200);
    }

    function verificaCollapsed() {
        if (sidebar.classList.contains('collapsed')) {
            trocaLogo(logoRecolhido.src);
        } else {
            trocaLogo(logoExpandido.src);
        }
    }

    // --- 1. Aplica estado salvo no carregamento ---
    const savedState = localStorage.getItem(storageKey);
    if (savedState === 'collapsed') {
        sidebar.classList.add('collapsed');
        verificaCollapsed();
    }

    // --- 2. Toggle da sidebar pelo botão --- 
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            verificaCollapsed();

            // Salva estado
            localStorage.setItem(storageKey, sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        });
    }

    // --- 3. Toggle dos submenus ---
    submenus.forEach(item => {
        const toggleLink = item.querySelector('a[data-bs-toggle="collapse"]');
        const submenu = item.querySelector('.collapse');

        if (toggleLink && submenu) {
            toggleLink.addEventListener('click', function (e) {
                e.preventDefault();

                // Se a sidebar estiver recolhida, expande
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    verificaCollapsed();
                    localStorage.setItem(storageKey, 'expanded');
                }

                // Fecha outros submenus
                submenus.forEach(otherItem => {
                    if (otherItem !== item) {
                        const otherSubmenu = otherItem.querySelector('.collapse');
                        if (otherSubmenu) {
                            otherSubmenu.style.display = 'none';
                            otherItem.querySelector('.arrow-icon')?.classList.remove('rotate');
                        }
                    }
                });

                // Alterna submenu clicado
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                    toggleLink.querySelector('.arrow-icon')?.classList.remove('rotate');
                } else {
                    submenu.style.display = 'block';
                    toggleLink.querySelector('.arrow-icon')?.classList.add('rotate');
                }
            });
        }
    });

    // --- 4. Fecha submenus ao clicar fora ---
    document.addEventListener('click', function (e) {
        submenus.forEach(item => {
            const toggleLink = item.querySelector('a[data-bs-toggle="collapse"]');
            const submenu = item.querySelector('.collapse');

            if (submenu && !item.contains(e.target)) {
                submenu.style.display = 'none';
                toggleLink.querySelector('.arrow-icon')?.classList.remove('rotate');
            }
        });
    });

    // --- 5. Atualiza o título da página ---
    const pageTitle = document.title;
    const pageTitleElement = document.getElementById('page-title');
    if (pageTitleElement) {
        pageTitleElement.textContent = pageTitle;
    }
});


</script>