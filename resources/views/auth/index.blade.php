<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KofA AMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #991b1b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-container img {
            width: 80px;
            height: 80px;
        }
        
        .logo-text {
            color: #eab308;
            font-weight: bold;
            font-size: 20px;
            margin-top: 10px;
        }
        
        .page-title {
            color: #991b1b;
            font-size: 24px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #991b1b;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-check input {
            margin-right: 8px;
        }
        
        .form-check label {
            font-size: 14px;
            color: #333;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #991b1b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }
        
        .btn:hover {
            background-color: #7f1d1d;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            background-color: transparent;
            color: #666;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 20px;
            text-decoration: none;
        }
        
        .back-btn:hover {
            background-color: #f9f9f9;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #991b1b;
            text-decoration: none;
            font-size: 14px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="{{ asset('kofa.png') }}" alt="KofA Logo">
            <div class="logo-text">AMS</div>
        </div>
        
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops! Something went wrong.</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <a href="{{ url('/') }}" class="back-btn">
            ← Back
        </a>
        
        <h2 class="page-title">Welcome Back!</h2>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html> 