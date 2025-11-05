<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Sua Região</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            background: #fff !important;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            height: 100%;
        }
        
        .auth-wrapper {
            max-width: 420px;
            margin: 0 auto;
            padding: 32px 18px 0 18px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff !important;
        }
        
        .auth-logo img {
            display: block;
            margin: 0 auto 18px auto;
            max-width: 220px;
            height: auto;
        }
        
        .auth-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #111;
            text-align: center;
        }
        
        .auth-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-bottom: 18px;
        }
        
        .auth-input {
            width: 100%;
            padding: 14px 18px;
            font-size: 1.1rem;
            border: 2px solid #111;
            border-radius: 28px;
            outline: none;
            margin-bottom: 0;
            background: #fff;
            color: #222;
        }
        
        .auth-input:focus {
            border-color: #2196f3;
            box-shadow: none;
        }
        
        .auth-btn {
            width: 100%;
            padding: 14px 0;
            background: #2196f3;
            color: #fff;
            font-size: 1.15rem;
            font-weight: 700;
            border: none;
            border-radius: 28px;
            margin-top: 8px;
            margin-bottom: 0;
            cursor: pointer;
            box-shadow: none;
            transition: background 0.3s;
        }
        
        .auth-btn:hover {
            background: #1976d2;
        }
        
        .auth-btn-outline {
            width: 100%;
            padding: 14px 0;
            background: #fff;
            color: #2196f3;
            font-size: 1.15rem;
            font-weight: 700;
            border: 2px solid #2196f3;
            border-radius: 28px;
            margin-top: 8px;
            margin-bottom: 0;
            cursor: pointer;
            box-shadow: none;
            transition: all 0.3s;
        }
        
        .auth-btn-outline:hover {
            background: #2196f3;
            color: #fff;
        }
        
        .auth-links {
            width: 100%;
            text-align: center;
            margin: 18px 0 0 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }
        
        .auth-divider {
            color: #666;
            font-size: 1rem;
            margin: 8px 0;
        }
        
        .auth-link {
            background: none;
            border: none;
            color: #111;
            font-size: 1.05rem;
            font-weight: 500;
            text-decoration: underline;
            cursor: pointer;
            margin-top: 8px;
        }
        
        .auth-link:hover {
            color: #2196f3;
        }
        
        .auth-footer {
            width: 100%;
            margin-top: 32px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .auth-footer-link {
            color: #2196f3;
            font-size: 1rem;
            text-decoration: underline;
            font-weight: 500;
        }
        
        .auth-footer-link:hover {
            color: #1976d2;
        }
        
        .auth-copyright {
            width: 100%;
            text-align: center;
            color: #888;
            font-size: 0.95rem;
            margin-top: 8px;
        }
        
        .alert {
            border-radius: 28px;
            margin-bottom: 18px;
        }
        
        @media (max-width: 480px) {
            .auth-wrapper {
                padding: 24px 12px;
            }
            .auth-input, .auth-btn, .auth-btn-outline {
                width: 100%;
                max-width: 100%;
                font-size: 1rem;
                padding: 12px;
            }
            .auth-title {
                font-size: 1.2rem;
            }
            .auth-footer {
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-logo">
            <img src="{{ asset('images/logo-DOyri49F.png') }}" alt="Sua Região Logo" />
        </div>
        
        <h2 class="auth-title">Faça login para continuar</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger w-100">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        
        @if (session('success'))
            <div class="alert alert-success w-100">
                {{ session('success') }}
            </div>
        @endif
        
        <form class="auth-form" method="POST" action="login">
            @csrf
            
            <input 
                type="email" 
                name="email" 
                placeholder="Email" 
                class="auth-input" 
                value="{{ old('email') }}"
                required 
                autofocus
            />
            
            <input 
                type="password" 
                name="password" 
                placeholder="Senha" 
                class="auth-input" 
                required
            />
            
            <button type="submit" class="auth-btn">Entrar</button>
        </form>
        
        <div class="auth-links">
            <span class="auth-divider">ou</span>
            <a href="/register" class="auth-btn-outline text-decoration-none">Crie sua conta</a>
            <a href="/password/request" class="auth-link text-decoration-none">Esqueci minha senha</a>
        </div>
        
        <div class="auth-footer">
            <a href="#" class="auth-footer-link">Termos de Uso</a>
            <a href="#" class="auth-footer-link">Política de Privacidade</a>
            <div class="auth-copyright">© 2025 Sua Região. Todos os direitos reservados.</div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
