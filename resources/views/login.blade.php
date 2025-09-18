<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/login/app.css'])
</head>

<body>

    <div class="container">
        
        <img src="{{ asset('faesa.png') }}" alt="Logo">
        
        <form action="{{ route('loginPOST') }}" method="POST">
            @csrf

            <div class="input-group">
                <input type="text" id="login" name="login" required placeholder=" ">
                <label for="login">Usuário</label>
            </div>

            
            @error('login')
            <div class="error">{{ $message }}</div>
            @enderror
            

            <div class="input-group">
                <input type="password" id="senha" name="senha" required placeholder=" ">
                <label for="senha">Senha</label>
                <span class="toggle-password" onclick="togglePasswordVisibility(this)">
                    <i class="bi bi-eye"></i>
                </span>
            </div>
            
            
            @error('senha')
            <div class="error">{{ $message }}</div>
            @enderror
            
            <div class="select-container">
                <select id="tipo_usuario" name="tipo_usuario" required>
                    <option value="usuario" selected>Administrador</option>
                    <option value="aluno">Aluno</option>
                    <option value="professor">Professor</option>
                </select>
            </div>
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            
            <input type="submit" value="Entrar">

            
            <div class="forgot-password-link">
                <a href="https://acesso.faesa.br/#/auth-user/forgot-password">Esqueceu a senha?</a>
            </div>



        </form>
    </div>

</body>

    <script>
        function togglePasswordVisibility(element) {
            const input = document.getElementById("senha");
            const icon = element.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>



</html>