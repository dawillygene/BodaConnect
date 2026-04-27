<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login - BodaConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: radial-gradient(ellipse at 10% 20%, rgba(56, 189, 248, 0.08) 0%, transparent 40%),
                        radial-gradient(ellipse at 90% 80%, rgba(124, 58, 237, 0.06) 0%, transparent 40%),
                        linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -20%;
            right: -10%;
            width: 70%;
            height: 70%;
            background: radial-gradient(circle, rgba(15, 118, 110, 0.03) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -15%;
            left: -5%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(30, 64, 175, 0.02) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }

        .glass-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            background: linear-gradient(135deg, rgba(255,255,255,0.75) 0%, rgba(255,255,255,0.5) 100%);
            border-radius: 2.5rem;
            box-shadow:
                0px 25px 50px -12px rgba(0, 0, 0, 0.15),
                0px 0px 0px 1px rgba(255, 255, 255, 0.5) inset,
                0px 0px 0px 1px rgba(15, 23, 42, 0.05);
            padding: 2.5rem 2rem;
            position: relative;
            z-index: 10;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .glass-card:hover {
            box-shadow:
                0px 30px 60px -15px rgba(0, 0, 0, 0.2),
                0px 0px 0px 1px rgba(255, 255, 255, 0.6) inset,
                0px 0px 0px 1px rgba(15, 23, 42, 0.08);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            width: 60%;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(15, 118, 110, 0.4), rgba(56, 189, 248, 0.4), transparent);
            border-radius: 0 0 2px 2px;
            opacity: 0.6;
        }

        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease;
        }

        .brand-icon svg {
            width: 5.5rem;
            height: 5.5rem;
            display: block;
        }

        .brand-icon:hover {
            transform: scale(1.05);
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .subtitle {
            font-size: 0.9375rem;
            color: #475569;
            font-weight: 400;
            margin-top: 0.25rem;
            letter-spacing: -0.01em;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 1rem;
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #b91c1c;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 1.25rem;
            animation: slideDown 0.3s ease;
        }

        .alert-error i {
            font-size: 1.1rem;
            color: #ef4444;
            flex-shrink: 0;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .input-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            letter-spacing: -0.01em;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .input-label i {
            font-size: 0.9rem;
            color: #0f766e;
            width: 16px;
            text-align: center;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-field {
            width: 100%;
            padding: 0.875rem 1rem;
            padding-left: 2.75rem;
            background: rgba(248, 250, 252, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(203, 213, 225, 0.5);
            border-radius: 1rem;
            font-size: 0.9375rem;
            color: #0f172a;
            font-weight: 500;
            transition: all 0.25s ease;
            box-shadow: 0px 1px 2px rgba(0,0,0,0.02);
            background: rgba(255,255,255,0.65);
        }

        .input-field:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0px 0px 0px 3px rgba(15, 118, 110, 0.1), 0px 4px 12px rgba(0,0,0,0.04);
            background: rgba(255,255,255,0.9);
        }

        .input-field::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            color: #64748b;
            font-size: 1rem;
            transition: color 0.2s;
            pointer-events: none;
            z-index: 2;
        }

        .input-wrapper:focus-within .input-icon-left {
            color: #0f766e;
        }

        .btn-primary {
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            border: none;
            border-radius: 1rem;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: -0.01em;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0px 8px 24px -8px rgba(15, 118, 110, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%);
            pointer-events: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            box-shadow: 0px 12px 28px -8px rgba(15, 118, 110, 0.5);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(1px) scale(0.99);
            box-shadow: 0px 4px 12px -4px rgba(15, 118, 110, 0.4);
        }

        .btn-primary i {
            font-size: 0.9rem;
            transition: transform 0.2s;
        }

        .btn-primary:hover i {
            transform: translateX(3px);
        }

        .footer-text {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.9rem;
            color: #475569;
            font-weight: 450;
        }

        .footer-link {
            color: #0f766e;
            font-weight: 600;
            text-decoration: none;
            position: relative;
            transition: color 0.2s;
            margin-left: 0.2rem;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 1.5px;
            background: #0f766e;
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.25s ease;
        }

        .footer-link:hover {
            color: #0d9488;
        }

        .footer-link:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        @media (max-width: 480px) {
            .glass-card {
                padding: 2rem 1.5rem;
                border-radius: 2rem;
            }

            .brand-icon svg {
                width: 4.75rem;
                height: 4.75rem;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="brand-icon">
            <x-logo-mark class="h-12 w-12" />
        </div>

        <h1>Login</h1>
        <p class="subtitle">Access your BodaConnect account.</p>

        @if ($errors->any())
            <div class="alert-error" id="errorAlert">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form class="form-container" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="input-group">
                <label class="input-label" for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" name="email" class="input-field" placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email">
                </div>
            </div>

            <div class="input-group">
                <label class="input-label" for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="input-wrapper">
                    <span class="input-icon-left"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" name="password" class="input-field" placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <span>Login</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <p class="footer-text">
            No account?
            <a href="{{ route('register') }}" class="footer-link">Register as customer</a>
        </p>
    </div>
</body>
</html>
