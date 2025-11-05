<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Painel Administrativo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        /* Improved modern design with better colors and spacing */
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #3b82f6;
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Sidebar Styles */
        .vertical-menu {
            width: 260px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .vertical-menu::-webkit-scrollbar {
            width: 6px;
        }

        .vertical-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .vertical-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .navbar-brand-box {
            padding: 24px 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-lg img {
            max-width: 150px;
            height: auto;
        }

        .logo-lg {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }

        #sidebar-menu {
            padding: 20px 0;
        }

        #sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sidebar-menu .menu-title {
            padding: 16px 20px 8px;
            color: #94a3b8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        #sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        #sidebar-menu li a:hover {
            background: var(--sidebar-hover);
            color: #ffffff;
            border-left-color: var(--sidebar-active);
        }

        #sidebar-menu li a.active {
            background: var(--sidebar-hover);
            color: #ffffff;
            border-left-color: var(--sidebar-active);
        }

        #sidebar-menu li a i {
            margin-right: 12px;
            font-size: 20px;
            width: 20px;
            text-align: center;
        }

        #sidebar-menu .sub-menu {
            display: none;
            background: rgba(0, 0, 0, 0.2);
        }

        #sidebar-menu .sub-menu.show {
            display: block;
        }

        #sidebar-menu .sub-menu li a {
            padding-left: 56px;
            font-size: 13px;
            font-weight: 400;
        }

        #sidebar-menu .has-arrow::after {
            content: '\f107';
            font-family: 'boxicons';
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 18px;
        }

        #sidebar-menu .has-arrow.active::after {
            transform: rotate(180deg);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .page-content {
            padding: 24px;
            flex: 1;
        }

        /* Page Title */
        .page-title-box {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title-box h4 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 700;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .breadcrumb-item a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
        }

        /* Cards */
        .card {
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--card-shadow-hover);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-body {
            padding: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }

        /* Stat Cards */
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: #ffffff;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: var(--success-color);
            color: #ffffff;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger-color);
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-warning {
            background: var(--warning-color);
            color: #ffffff;
        }

        .btn-warning:hover {
            background: #d97706;
            transform: translateY(-1px);
        }

        .btn-info {
            background: var(--info-color);
            color: #ffffff;
        }

        .btn-info:hover {
            background: #0891b2;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-group .btn {
            margin: 0;
        }

        /* Tables */
        .table {
            font-size: 14px;
            color: #334155;
        }

        .table thead th {
            background-color: #f8fafc;
            border-bottom: 2px solid var(--border-color);
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
            padding: 16px;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            font-size: 12px;
            border-radius: 6px;
        }

        .bg-success {
            background-color: var(--success-color) !important;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
        }

        .bg-info {
            background-color: var(--info-color) !important;
        }

        .bg-secondary {
            background-color: #64748b !important;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert i {
            font-size: 20px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Forms */
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Modal */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-weight: 700;
            color: #0f172a;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 16px 24px;
            border-radius: 0 0 12px 12px;
        }

        .modal.show {
            display: block;
            background: rgba(0, 0, 0, 0.5);
        }

        /* Avatar */
        .avatar-xs, .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .bg-soft-primary {
            background-color: #dbeafe;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-soft-warning {
            background-color: #fef3c7;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        /* Footer */
        footer {
            background-color: #ffffff;
            padding: 20px 24px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .vertical-menu {
                left: -260px;
            }

            .main-content {
                margin-left: 0;
            }

            .vertical-menu.show {
                left: 0;
            }

            .page-content {
                padding: 16px;
            }
        }

        /* Utilities */
        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .d-grid {
            display: grid;
        }

        .gap-3 {
            gap: 1rem;
        }
    </style>

    @yield('css')
</head>

<body data-sidebar="dark">

    <div class="vertical-menu">
        <div class="navbar-brand-box">
            <a href="/" class="logo logo-light">
                <span class="logo-lg">
                    Admin Panel
                </span>
            </a>
        </div>

        <div data-simplebar class="h-100">
            <div id="sidebar-menu">
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="menu-title">Administração</li>

                    <li>
                        <a href="/admin/dashboard" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span>Dashboard Admin</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-user"></i>
                            <span>Usuários</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="/users">Listar Usuários</a></li>
                            {{-- <li><a href="/users/create">Cadastrar Usuário</a></li> --}}
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-message-square-x"></i>
                            <span>Palavras Banidas</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="/banned-words">Listar Palavras</a></li>
                            <li><a href="/banned-words/create">Cadastrar Palavra</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="/reports" class="waves-effect">
                            <i class="bx bx-flag"></i>
                            <span>Reports de Posts</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @yield('listagem')
            </div>
        </div>

        <footer>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        {{ date('Y') }} © Sua Região.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Desenvolvido por <a href="#" target="_blank">Web Agência</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasArrowLinks = document.querySelectorAll('.has-arrow');
            
            hasArrowLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parent = this.parentElement;
                    const subMenu = parent.querySelector('.sub-menu');
                    
                    if (subMenu) {
                        if (subMenu.classList.contains('show')) {
                            subMenu.classList.remove('show');
                            this.classList.remove('active');
                        } else {
                            document.querySelectorAll('.sub-menu').forEach(menu => {
                                menu.classList.remove('show');
                            });
                            document.querySelectorAll('.has-arrow').forEach(arrow => {
                                arrow.classList.remove('active');
                            });
                            
                            subMenu.classList.add('show');
                            this.classList.add('active');
                        }
                    }
                });
            });

            // Initialize DataTables
            if ($.fn.DataTable && $('#datatable-buttons').length) {
                $('#datatable-buttons').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                    }
                });
            }

            // Initialize Select2
            if ($.fn.select2 && $('.select2').length) {
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });
            }

            // Modal functionality
            document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-bs-target');
                    const modal = document.querySelector(targetId);
                    if (modal) {
                        modal.classList.add('show');
                    }
                });
            });

            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(closeBtn => {
                closeBtn.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) {
                        modal.classList.remove('show');
                    }
                });
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('show');
                    }
                });
            });
        });
    </script>

    @yield('script')

</body>

</html>
