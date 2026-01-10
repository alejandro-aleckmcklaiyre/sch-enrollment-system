<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ config('app.name', 'Enrollment System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-header h1 {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .register-header p {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-register {
            background-color: var(--secondary);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .btn-register:hover {
            background-color: #2980b9;
            color: white;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 2rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }
        
        .register-footer a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .alert {
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        
        .role-info {
            background-color: #f0f8ff;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Enrollment System</h1>
            <p>Create your account</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif




        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="mb-3">
                <label for="role" class="form-label">Register as</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" {{ old('role', $role) == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
                @error('role')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                       required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>



            <button type="submit" class="btn btn-register">Register</button>
        </form>

        <div class="register-footer">
            Already have an account? <a href="{{ route('login') }}">Login here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
