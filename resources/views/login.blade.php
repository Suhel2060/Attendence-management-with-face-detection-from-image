<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance System | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
            --accent-color: #2b5876;
            --input-focus: #88c3d8;
        }

        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
            transition: transform 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-5px);
        }

        .auth-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .auth-header::after {
            content: '✓';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.5rem;
            color: var(--accent-color);
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .auth-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .auth-subtitle {
            opacity: 0.9;
            font-weight: 400;
            font-size: 0.95rem;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-floating label {
            color: #64748b;
        }

        .form-control:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(136, 195, 216, 0.15);
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient::after {
            content: '➔';
            position: absolute;
            right: 1rem;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            padding-right: 2.5rem;
        }

        .btn-gradient:hover::after {
            opacity: 1;
            right: 1.5rem;
        }

        .auth-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        .auth-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <h1 class="auth-title">Attendance System</h1>
        <p class="auth-subtitle">Secure Access Portal</p>
    </div>
    
    <div class="auth-body">
        <form id="loginForm" class="needs-validation" novalidate>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                <label for="email"><i class="fas fa-id-card me-2"></i>Institutional Email</label>
                <div class="invalid-feedback">Please enter your registered email</div>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" placeholder="Password" required>
                <label for="password"><i class="fas fa-fingerprint me-2"></i>Access Code</label>
                <div class="invalid-feedback">Please enter your security code</div>
            </div>

            <button type="submit" class="btn btn-gradient w-100 py-2 mb-3">
                <i class="fas fa-unlock-alt me-2"></i>Authenticate
            </button>

            <div class="alert alert-danger mt-3 d-none" id="errorMessage" role="alert"></div>
        </form>
    </div>

    <div class="auth-footer">
        <a href="#forgot-password">Recover Access</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        const email = $('#email').val();
        const password = $('#password').val();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        $.ajax({
            url: '/login',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            contentType: 'application/json',
            data: JSON.stringify({ email, password }),
            success: function(response) {
                localStorage.setItem('token', response.token);
                window.location.href = '/dashboard';
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Authentication failed. Please verify your credentials.';
                $('#errorMessage').removeClass('d-none').text(errorMessage);
                $('#password').val('').focus();
            }
        });
    });

    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#errorMessage').addClass('d-none');
    });
});
</script>

</body>
</html>