@extends('layouts.attendence')

@section('title', 'Attendance Portal')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    
    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --secondary: #7209b7;
        --success: #06d6a0;
        --danger: #ef476f;
        --light: #f8f9fa;
        --dark: #212529;
        --gray: #6c757d;
        --light-gray: #e9ecef;
        --border-radius: 16px;
        --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }
    
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        color: var(--dark);
    }
    
    .container {
        display: flex;
        max-width: 1200px;
        width: 100%;
        height: 90vh;
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
    }
    
    /* Login Section */
    .login-section {
        flex: 1;
        background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
        color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .login-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
    }
    
    .logo {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .logo-icon {
        font-size: 32px;
        margin-right: 12px;
    }
    
    .logo-text {
        font-size: 24px;
        font-weight: 700;
    }
    
    .welcome-text {
        margin-bottom: 40px;
        z-index: 2;
    }
    
    .welcome-text h1 {
        font-size: 32px;
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .welcome-text p {
        opacity: 0.85;
        font-weight: 300;
    }
    
    .features {
        margin-top: 30px;
        z-index: 2;
    }
    
    .feature {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .feature i {
        background: rgba(255, 255, 255, 0.15);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 16px;
    }
    
    /* Auth Section */
    .auth-section {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: white;
    }
    
    .auth-card {
        max-width: 450px;
        width: 100%;
        margin: 0 auto;
    }
    
    .auth-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .auth-header h2 {
        font-size: 28px;
        color: var(--primary);
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .auth-header p {
        color: var(--gray);
    }
    
    .form-floating {
        margin-bottom: 20px;
        position: relative;
    }
    
    .form-control {
        height: 56px;
        border-radius: 12px;
        padding: 0 20px;
        font-size: 16px;
        border: 2px solid #e2e8f0;
        transition: var(--transition);
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
    }
    
    .form-floating label {
        padding: 0 20px;
        display: flex;
        align-items: center;
        color: var(--gray);
    }
    
    .form-floating i {
        margin-right: 10px;
        color: var(--primary);
    }
    
    .btn {
        height: 56px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    .btn-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }
    
    .btn-gradient:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #5f0b9e 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }
    
    .btn-outline {
        border: 2px solid var(--primary);
        color: var(--primary);
        background: transparent;
    }
    
    .btn-outline:hover {
        background: rgba(67, 97, 238, 0.05);
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 25px;
        color: var(--gray);
    }
    
    .auth-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .container {
            flex-direction: column;
            height: auto;
        }
    }

    /* Animation */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(67, 97, 238, 0); }
        100% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0); }
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: 0.2em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        -webkit-animation: spinner-border .75s linear infinite;
        animation: spinner-border .75s linear infinite;
    }

    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }
</style>
</head>
<body>
<div class="container">
    <!-- Login Section -->
    <div class="login-section">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-fingerprint"></i>
            </div>
            <div class="logo-text">SecureAttend</div>
        </div>
        
        <div class="welcome-text">
            <h1>Welcome Back</h1>
            <p>Secure and reliable attendance tracking system</p>
        </div>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <h4>Secure Attendance</h4>
                    <p>End-to-end encrypted data protection</p>
                </div>
            </div>
            <div class="feature">
                <i class="fas fa-bolt"></i>
                <div>
                    <h4>Instant Attendance</h4>
                    <p>Real-time Image Capture</p>
                </div>
            </div>
            <div class="feature">
                <i class="fas fa-chart-line"></i>
                <div>
                    <h4>Attendance History</h4>
                    <p>View your attendance history</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Auth Section -->
    <div class="auth-section">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Sign In</h2>
                <p>Enter your credentials to access the system</p>
            </div>
            
            <form id="loginForm" class="needs-validation" novalidate>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                    <label for="email"><i class="fas fa-id-card"></i> Institutional Email</label>
                    <div class="invalid-feedback">Please enter your registered email</div>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock"></i> Access Code</label>
                    <div class="invalid-feedback">Please enter your security code</div>
                </div>
                
                <button type="submit" class="btn btn-gradient w-100 py-2 mb-3 pulse">
                    <i class="fas fa-unlock-alt me-2"></i>Authenticate
                </button>
                
                <div class="alert alert-danger d-none" id="errorMessage" role="alert"></div>
            </form>
            
            <div class="auth-footer">
                <a href="/attendencethis"><i class="fas fa-camera me-1"></i> Mark Attendance</a>
            </div>
        </div>
    </div>
</div>

<script>
   $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#loginForm').submit(function (e) {
        e.preventDefault();
        const email = $('#email').val();
        const password = $('#password').val();

        if (!email || !password) {
            showNotification("Please fill in all fields", "error");
            return;
        }

        const btn = $(this).find('button[type="submit"]');
        btn.addClass('disabled');
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Authenticating');

        $.ajax({
            url: '/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ email, password }),
            success: function (response) {
                    showNotification("Login successful", "success");
                    window.location.href = '/dashboard';
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Authentication failed.';
                showNotification(errorMessage, "error");
                $('#password').val('').focus();
            },
            complete: function () {
                btn.removeClass('disabled');
                btn.html('<i class="fas fa-unlock-alt me-2"></i>Authenticate');
            }
        });
    });

    function showNotification(message, type) {
        const alert = document.getElementById('errorMessage');
        alert.textContent = message;
        alert.classList.remove('d-none', 'alert-success', 'alert-danger');
        alert.classList.add(type === "success" ? 'alert-success' : 'alert-danger');

        setTimeout(() => {
            alert.classList.add('d-none');
        }, 5000);
    }
});

</script>
@endsection
