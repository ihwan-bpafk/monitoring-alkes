<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Monitoring Alkes BPAFK</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: url("{{ asset('img/bg_login.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            background-color: #f4f7f6;
            /* Fallback color */
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background-color: #047d79;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .btn-primary {
            background-color: #047d79;
            border: none;
            padding: 12px;
            font-weight: bold;
        }

        .btn-primary:hover,
        .btn-primary:active,
        .btn-primary:focus,
        .btn-primary:active:focus {
            background-color: #035f5c !important;
            border-color: #035f5c !important;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: #047d79;
            box-shadow: 0 0 0 0.25rem rgba(4, 125, 121, 0.1);
        }

        .logo-box {
            background: white;
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="login-header">
                        <div class="logo-box shadow-sm mb-3">
                            <img src="{{ asset('img/Ditjen_Farmalkes_Logo.png') }}" width="150" alt="Logo BPAFK">
                        </div>
                        <h5 class="mb-0 fw-bold">SISTEM MONITORING PERBAIKAN</h5>
                        <small class="opacity-75">BPAFK Medan - Kemenkes RI</small>
                    </div>
                    <div class="card-body p-5">
                        @if ($errors->any())
                            <div class="alert alert-danger small">{{ $errors->first() }}</div>
                        @endif

                        <form action="{{ url('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Masukkan username" required autofocus>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Masukkan password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3 shadow-sm">MASUK KE SISTEM</button>

                            <div class="text-center">
                                <img src="{{ asset('img/logo BPAFK Medan.png') }}" width="120" alt="Logo Ditjen">
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-4 text-muted small" style="text-shadow: 0 0 5px white, 0 0 10px white;">&copy; 2026 BPAFK Medan. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</body>

</html>
