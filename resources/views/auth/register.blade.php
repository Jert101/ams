<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KofA AMS</title>
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
            padding: 20px;
        }
        
        .register-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            padding: 30px;
            margin: 20px auto;
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
        
        .btn {
            display: inline-block;
            padding: 12px 20px;
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
        
        .section-title {
            font-size: 16px;
            color: #991b1b;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #fee2e2;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .col {
            padding: 0 10px;
            flex: 1 0 100%;
        }
        
        @media (min-width: 768px) {
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        
        .error-text {
            color: #991b1b;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .required:after {
            content: "*";
            color: #991b1b;
            margin-left: 3px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <img src="{{ asset('kofa.png') }}" alt="KofA Logo">
            <div class="logo-text">AMS</div>
        </div>

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
        
        <h2 class="page-title">Create an Account</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="row">
                <!-- Left Column -->
                <div class="col col-md-6">
                    <h3 class="section-title">Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="name" class="required">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                        @error('name')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required>
                        @error('email')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}" class="form-control">
                        @error('date_of_birth')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col col-md-6">
                    <h3 class="section-title">Contact & Security</h3>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-control">
                        @error('address')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}" class="form-control" placeholder="e.g. 09123456789">
                        @error('mobile_number')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
            </div>
            
                    <div class="form-group">
                        <label for="password" class="required">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                            <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="required">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn">
                Register
                </button>
                
            <div class="links">
                    Already have an account? 
                <a href="{{ route('login') }}" style="font-weight: bold;">
                        Log in
                    </a>
            </div>
        </form>
    </div>
</body>
</html>
