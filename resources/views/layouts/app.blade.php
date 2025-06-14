<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Attendance System')</title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #4a90e2;
            --hover-color: #3b7cc4;
            --bg-gradient: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.95);
        }

        .content {
            flex: 1;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .logout {
            background: linear-gradient(90deg, rgba(255,255,255,0.9) 0%, rgba(245,245,245,0.9) 100%);
            padding: 1rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            backdrop-filter: blur(8px);
        }

        .user-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .user-badge i {
            font-size: 1.2rem;
        }

        #logoutBtn {
            background: linear-gradient(135deg, var(--primary-color), var(--hover-color));
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        #logoutBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        .main-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            min-height: calc(100vh - 120px);
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }
            
            .logout {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .user-badge {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        @include('layouts.sidebar')

        <div class="content">
            <!-- Logout Section -->
            <div class="logout d-flex justify-content-between align-items-center">
                <div class="user-badge">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ $usr->name ?? $usr->email }}</span>
                </div>
                <div>
                    <button id="logoutBtn" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                    <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->


    <script>
        $('#logoutBtn').click(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Ready to Leave?',
                text: "Please confirm your logout request",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4a90e2',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel',
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: 'rgba(0, 0, 0, 0.1)'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#logoutForm').submit();
                }
            });
        });
    </script>
</body>
</html>