<!DOCTYPE html>
<html lang="pt-br">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
    <style>

        body {
            /* font-family: Arial, sans-serif; */
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #5CA4EA;   /* #107ed8  #5CA4EA */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            width: 280px;
            background-color: #fff;
            padding: 70px 50px;
            border-radius: 10px;
            box-shadow: 3px 3px 13px #000;
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */
        }

        .container img {
            display: block;
            margin: 0 auto 20px;
            width: 190px;
            margin-bottom: 70px;
        }

        label {
            font-size: 18px;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 2px;
            margin: 15px 0px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border: 0 none;
            outline: 0;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 281px;
            padding: 15px;
            margin-top:15px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            font-size: 15px;
            height: 50px;

        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        hr {
            color: black;
            margin-top: -10px;
        }
        .hr {
            padding-bottom: 20px;
        }

        .error {
            color: red;
            margin-top: 5px;
        }

        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .forgot-password-link {
            margin-top: 20px;
        }

        .forgot-password-link a {
            text-decoration: none;
            color: #007bff;
            font-size: 15px;
        }

        .forgot-password-link a:hover {
            text-decoration: underline;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 10px 40px 10px 5px;
            font-size: 16px;
            border: none;
            border-bottom: 1px solid #ccc;
            transition: all 0.3s ease;
            background: transparent;
        }

        .input-group label {
            position: absolute;
            top: 10px;
            left: 5px;
            font-size: 16px;
            color: gray;
            pointer-events: none;
            transition: 0.3s ease all;
        }

        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            top: -10px;
            font-size: 12px;
            color: #2596be;
        }

        .input-group input:focus {
            border-bottom: 2px solid #2596be;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #999;
            user-select: none;
        }



        /* ESQUECEU A SENHA? */
        .forgot-password-link a {
            display: inline-block;
            color: #2596be;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .forgot-password-link a:hover {
            color:rgb(6, 91, 128);
            transform: translateX(5px);
        }



        /* BOÃO SUBMETER FORMULÁRIO */
        input[type="submit"] {
            background-color: #2596be;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        input[type="submit"]:hover {
            background-color: #1b6e91;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        input[type="submit"]:active {
            transform: translateY(1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }


        /* ===== ESTILOS ADICIONADOS PARA O SELETOR DE TIPO DE USUÁRIO ===== */
        .select-container {
            position: relative;
            margin-bottom: 20px;
        }

        .select-container select {
            width: 100%;
            padding: 10px 5px;
            font-size: 16px;
            border: none;
            border-bottom: 1px solid #ccc;
            background-color: transparent;
            appearance: none; /* Remove a seta padrão do navegador */
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            color: gray; /* Cor do texto do placeholder */
        }
        
        /* Muda a cor do texto quando uma opção válida é selecionada */
        .select-container select:valid {
            color: #000;
        }

        /* Adiciona uma seta para baixo personalizada */
        .select-container::after {
            content: '\25BC';
            font-size: 16px;
            color: #999;
            position: absolute;
            right: 10px;
            top: 10px;
            pointer-events: none; /* Permite clicar através do ícone */
        }

        .select-container select:focus {
            outline: none;
            border-bottom: 2px solid #2596be;
        }
        /* ===== FIM DOS ESTILOS ADICIONADOS ===== */


    </style>



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