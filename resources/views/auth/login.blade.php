<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - webToko</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin-bottom: 15px;
            font-size: 35px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        h1 {
            color: #1a1a1a;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #888;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .alerts {
            margin-bottom: 25px;
        }

        .error-box {
            background: #fee;
            border-left: 4px solid #d32f2f;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 6px;
            animation: slideIn 0.3s ease-out;
        }

        .error-box p {
            font-size: 13px;
            margin: 5px 0;
        }

        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
            padding: 12px 15px;
            border-radius: 6px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            width: 18px;
            text-align: center;
            pointer-events: none;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 13px 15px 13px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: #f9f9f9;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #999;
        }

        .form-group:last-of-type {
            margin-bottom: 30px;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .btn:hover {
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: #999;
            font-size: 12px;
        }

        .footer-text strong {
            color: #667eea;
        }

        /* Loading state */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 40px 30px;
            }

            h1 {
                font-size: 26px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <h1>webToko</h1>
                <p class="subtitle">Studio Photo Management System</p>
            </div>

            <!-- Alerts -->
            <div class="alerts">
                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <p><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-box">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Login Form -->
            <form action="{{ route('authenticate') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user" style="margin-right: 5px;"></i>Username
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-circle input-icon"></i>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock" style="margin-right: 5px;"></i>Password
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>Login
                </button>
            </form>

            <!-- Footer -->
            <div class="footer-text">
                <strong>webToko</strong> &copy; 2026 - All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
