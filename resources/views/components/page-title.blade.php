@vite(['resources/css/app.css', 'resources/js/app.js'])

<div class="col-12 text-center mb-1 d-none d-md-block page-title-sticky">

    <div class="d-flex flex-row justify-content-between align-items-center">
        
        <p class="p-0 mt-2 mb-1 text-start" style="font-size: 25px;">
            
            <i class="bi bi-list d-none d-lg-inline" id="btnToggleNavbar" style="cursor: pointer;"></i>

            <strong id='page-title'></strong>
            
        </p>

		<div class="d-flex flex-row gap-2 justify-content-between align-items-center">

            <!-- BUTTON ADICIONADO NA VIEW -->
            {{ $slot }}

            <!-- ICONE DO PERFIL DO USUARIO -->
            <div class="profile-container" style="position: relative;">
                <i class="bi bi-person-circle fs-2" id="profile" style="cursor: pointer;"></i> 
            </div>

        </div>        
        
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Elementos da página e a chave de armazenamento
    const btnToggle = document.getElementById("btnToggleNavbar");
    const navbar = document.getElementById("mainNavbar");
    const storageKey = 'sidebarState'; // Chave para salvar no localStorage

    // Verifica se os elementos essenciais existem
    if (!btnToggle || !navbar) {
        console.error("Botão de toggle ou a navbar não foram encontrados.");
        return;
    }

    // 1. AO CARREGAR A PÁGINA: Aplica o estado salvo
    const savedState = localStorage.getItem(storageKey);
    if (savedState === 'collapsed') {
        navbar.classList.add('collapsed');
        verificaCollapsed()
        
        // Se a página já carrega com a navbar recolhida,
        // o calendário precisa ser renderizado no tamanho correto.
        // Um pequeno timeout garante que o layout se ajustou.
        setTimeout(() => {
            if (typeof renderCalendar === 'function') {
                renderCalendar();
            }
        }, 50); 
    }

    // 2. AO CLICAR NO BOTÃO: Alterna o estado, salva e atualiza o calendário
    btnToggle.addEventListener("click", function () {
        navbar.classList.toggle("collapsed");
        verificaCollapsed()

        // Salva o novo estado no localStorage
        if (navbar.classList.contains("collapsed")) {
            localStorage.setItem(storageKey, 'collapsed');
        } else {
            localStorage.setItem(storageKey, 'expanded');
        }

        // Atualiza o tamanho do calendário depois da animação da sidebar
        setTimeout(() => {
            if (typeof renderCalendar === 'function') {
                renderCalendar();
            }
        }, 300); // 300ms para corresponder à transição do CSS
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageTitle = document.title; // pega o <title> do <head>
        document.getElementById('page-title').textContent = pageTitle;
    });
</script>


<script>
const navbar = document.getElementById("mainNavbar");
const logo = document.getElementById("logo-faesa");

// Pré-carregar as duas versões do logo
const logoExpandido = new Image();
logoExpandido.src = "{{ asset('img/faesa_logo_expandido.png') }}";
const logoRecolhido = new Image();
logoRecolhido.src = "{{ asset('img/faesa_logo_recolhido.png') }}";

function trocaLogo(src) {
    // Fade out
    logo.style.opacity = 0;
    setTimeout(() => {
        logo.src = src;
        // Fade in
        logo.style.opacity = 1;
    }, 200);
}

function verificaCollapsed() {
    if (navbar.classList.contains("collapsed")) {
        // Navbar está fechada -> usa logo recolhido
        trocaLogo(logoRecolhido.src);
    } else {
        // Navbar aberta -> usa logo expandido
        trocaLogo(logoExpandido.src);
    }
};
</script>