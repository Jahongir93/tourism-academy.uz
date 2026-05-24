<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'HEMIS - Tourism Academy Samarkand')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    @stack('styles')
    
    <style>
        /* Custom styles */
        .sidebar-link:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.2);
            border-left: 3px solid #3B82F6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('layouts.error-handler')
    @yield('content')

    <script>
    // CSRF token avtomatik yangilash
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Har 30 daqiqada CSRF tokenni yangilash
    setInterval(function() {
        $.ajax({
            url: '/database/status',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.new_token) {
                    $('meta[name="csrf-token"]').attr('content', data.new_token);
                    $('input[name="_token"]').val(data.new_token);
                }
            },
            error: function(xhr) {
                // Silently fail - don't log errors
                if (xhr.status !== 401 && xhr.status !== 419) {
                    console.debug('CSRF token not refreshed');
                }
            }
        });
    }, 1800000);
    </script>

    @stack('scripts')
</body>
</html>