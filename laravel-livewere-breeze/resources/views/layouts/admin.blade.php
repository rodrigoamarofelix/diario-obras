<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SGC - Gestão de Contratos - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css?v={{ time() }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Mobile Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive.css') }}">

    @livewireStyles

    <!-- Custom CSS para responsividade mobile -->
    <style>
        .brand-image {
            background-image: none !important;
        }
        .user-panel .image img {
            background-image: none !important;
        }
        .brand-image::before {
            content: "\f1ad"; /* Font Awesome building icon */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 33px;
            color: #007bff;
            opacity: 0.8;
        }
        /* Removido o ::before do user-panel para evitar ícone duplicado */
        .user-panel .info a {
            font-size: 14px !important;
            font-weight: normal !important;
        }

        /* === RESPONSIVIDADE MOBILE === */

        /* Sidebar responsiva */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar-open .sidebar {
                transform: translateX(0);
            }
            .main-sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh !important;
                z-index: 1040;
            }
            .content-wrapper {
                margin-left: 0 !important;
            }
            .navbar {
                margin-left: 0 !important;
            }
        }

        /* === CORREÇÕES ESPECÍFICAS PARA SMALL BOXES === */
        .dark-mode .small-box {
            background-color: #2d2d2d !important;
            border: 1px solid #404040 !important;
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner {
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner h3 {
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner p {
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner span {
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner div {
            color: #ffffff !important;
        }

        /* === CORREÇÕES ESPECÍFICAS PARA NAVBAR === */
        .dark-mode .main-header {
            background-color: #2d2d2d !important;
            border-bottom-color: #404040 !important;
        }

        .dark-mode .main-header .navbar {
            background-color: #2d2d2d !important;
        }

        .dark-mode .main-header .navbar-nav .nav-link {
            color: #ffffff !important;
        }

        .dark-mode .main-header .navbar-nav .nav-link:hover {
            color: #007bff !important;
        }

        .dark-mode .main-header .navbar-nav .nav-item .dropdown-toggle {
            color: #ffffff !important;
        }

        .dark-mode .main-header .navbar-nav .nav-item .dropdown-toggle:hover {
            color: #007bff !important;
        }

        .dark-mode .main-header .navbar-nav .nav-item .dropdown-menu {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
        }

        .dark-mode .main-header .navbar-nav .nav-item .dropdown-menu .dropdown-item {
            color: #ffffff !important;
        }

        .dark-mode .main-header .navbar-nav .nav-item .dropdown-menu .dropdown-item:hover {
            background-color: #404040 !important;
            color: #ffffff !important;
        }

        /* CORREÇÃO ESPECÍFICA PARA text-gray-800 */
        .dark-mode .text-gray-800 {
            color: #ffffff !important;
        }

        .dark-mode h5.text-gray-800 {
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner h5.text-gray-800 {
            color: #ffffff !important;
        }

        .dark-mode .small-box .inner h5 {
            color: #ffffff !important;
        }

        /* Forçar todos os h5 em small boxes */
        .dark-mode .small-box h5 {
            color: #ffffff !important;
        }

        /* CORREÇÃO ESPECÍFICA PARA NÚMEROS */
        .dark-mode .small-box .inner h3,
        .dark-mode .small-box .inner p,
        .dark-mode .small-box .inner span,
        .dark-mode .small-box .inner div,
        .dark-mode .small-box .inner strong,
        .dark-mode .small-box .inner b {
            color: #ffffff !important;
        }

        /* Forçar todos os elementos de texto */
        .dark-mode .small-box .inner * {
            color: #ffffff !important;
        }

        /* Específico para números grandes */
        .dark-mode .small-box .inner h3[style*="color"],
        .dark-mode .small-box .inner p[style*="color"],
        .dark-mode .small-box .inner span[style*="color"] {
            color: #ffffff !important;
        }

        /* Cards responsivos */
        @media (max-width: 576px) {
            .small-box {
                margin-bottom: 15px;
            }
            .small-box .inner h3 {
                font-size: 1.5rem !important;
            }
            .small-box .inner p {
                font-size: 0.9rem !important;
            }
        }

        /* Tabelas responsivas */
        @media (max-width: 768px) {
            .table-responsive {
                border: none;
            }
            .table {
                font-size: 0.85rem;
            }
            .table th,
            .table td {
                padding: 0.5rem 0.3rem;
            }
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        /* Formulários responsivos */
        @media (max-width: 576px) {
            .form-group {
                margin-bottom: 1rem;
            }
            .form-control {
                font-size: 16px; /* Evita zoom no iOS */
            }
            .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Modais responsivos */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            .modal-content {
                border-radius: 0.5rem;
            }
        }

        /* Dashboard responsivo */
        @media (max-width: 768px) {
            .content-header h1 {
                font-size: 1.5rem;
            }
            .breadcrumb {
                font-size: 0.85rem;
            }
        }

        /* Cards de workflow responsivos */
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }
            .card-title {
                font-size: 1.1rem;
            }
        }

        /* Botões de ação responsivos */
        @media (max-width: 576px) {
            .btn-group-vertical .btn {
                margin-bottom: 0.25rem;
            }
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        /* Notificações responsivas */
        @media (max-width: 576px) {
            .dropdown-menu {
                right: 0;
                left: auto;
                min-width: 250px;
            }
        }

        /* Melhorias gerais para mobile */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            .row {
                margin-left: -5px;
                margin-right: -5px;
            }
            .col-md-3,
            .col-md-4,
            .col-md-6,
            .col-md-8,
            .col-md-9,
            .col-md-12 {
                padding-left: 5px;
                padding-right: 5px;
            }
        }

        /* Touch-friendly buttons */
        @media (max-width: 768px) {
            .btn {
                min-height: 44px; /* Tamanho mínimo recomendado para touch */
                min-width: 44px;
            }
            .nav-link {
                padding: 0.75rem 1rem;
            }
        }

        /* Melhorias para landscape mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .small-box .inner h3 {
                font-size: 1.2rem !important;
            }
            .small-box .inner p {
                font-size: 0.8rem !important;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Toggle de Tema AdminLTE -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button" title="Configurações e Temas" data-toggle="tooltip" data-placement="bottom">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li>

                <!-- Notificações -->
                <li class="nav-item">
                    <livewire:notificacao-component />
                </li>

                <!-- User Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <i class="fas fa-user mr-2"></i> Perfil
                        </a>
                        <a class="dropdown-item" href="{{ route('two-factor.index') }}">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Autenticação 2FA
                            @if(auth()->user()->hasTwoFactorEnabled())
                                <span class="badge badge-success ml-2">Ativo</span>
                            @else
                                <span class="badge badge-warning ml-2">Inativo</span>
                            @endif
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="logout()">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sair
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link">
                <i class="fas fa-building brand-image" style="font-size: 33px; color: #007bff; opacity: .8"></i>
                <span class="brand-text font-weight-light" style="font-size: 16px;">SGC - Gestão de Contratos</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle" style="font-size: 40px; color: #6c757d;"></i>
                    </div>
                    <div class="info">
                        <a href="{{ route('profile') }}" class="d-block" style="font-size: 14px; font-weight: normal;">{{ Auth::user()->name }}</a>
                        <small class="text-muted">{{ Auth::user()->profile_name }}</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Usuários -->
                        @if(auth()->user()->canManageUsers())
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuários</p>
                            </a>
                        </li>
                        @endif

                        <!-- Lotações -->
                        <li class="nav-item">
                            <a href="{{ route('lotacao.index') }}" class="nav-link {{ request()->routeIs('lotacao.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Lotações</p>
                            </a>
                        </li>

                        <!-- Pessoas -->
                        <li class="nav-item">
                            <a href="{{ route('pessoa.index') }}" class="nav-link {{ request()->routeIs('pessoa.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Pessoas</p>
                            </a>
                        </li>

                        <!-- Contratos -->
                        <li class="nav-item">
                            <a href="{{ route('contrato.index') }}" class="nav-link {{ request()->routeIs('contrato.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>Contratos</p>
                            </a>
                        </li>

                        <!-- Catálogos -->
                        <li class="nav-item">
                            <a href="{{ route('catalogo.index') }}" class="nav-link {{ request()->routeIs('catalogo.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Catálogos</p>
                            </a>
                        </li>

                        <!-- Medições -->
                        <li class="nav-item">
                            <a href="{{ route('medicao.index') }}" class="nav-link {{ request()->routeIs('medicao.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Medições</p>
                            </a>
                        </li>

                        <!-- Pagamentos -->
                        <li class="nav-item">
                            <a href="{{ route('pagamento.index') }}" class="nav-link {{ request()->routeIs('pagamento.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>Pagamentos</p>
                            </a>
                        </li>

                        <!-- Relatórios -->
                        <li class="nav-item">
                            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Relatórios</p>
                            </a>
                        </li>

                        <!-- Exportação -->
                        <li class="nav-item">
                            <a href="{{ route('exports.index') }}" class="nav-link {{ request()->routeIs('exports.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-download"></i>
                                <p>Exportação</p>
                            </a>
                        </li>

                        <!-- Backup -->
                        <li class="nav-item">
                            <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-database"></i>
                                <p>Backup</p>
                            </a>
                        </li>

                        <!-- Autenticação 2FA -->
                        <li class="nav-item">
                            <a href="{{ route('two-factor.index') }}" class="nav-link {{ request()->routeIs('two-factor.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>
                                    Autenticação 2FA
                                    @if(auth()->user()->hasTwoFactorEnabled())
                                        <span class="badge badge-success ml-2">Ativo</span>
                                    @else
                                        <span class="badge badge-warning ml-2">Inativo</span>
                                    @endif
                                </p>
                            </a>
                        </li>

                        <!-- Busca Avançada -->
                        <li class="nav-item">
                            <a href="{{ route('search.index') }}" class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-search"></i>
                                <p>Busca Avançada</p>
                            </a>
                        </li>

                        <!-- Workflow de Aprovação -->
                        <li class="nav-item">
                            <a href="{{ route('workflow.index') }}" class="nav-link {{ request()->routeIs('workflow.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Workflow</p>
                            </a>
                        </li>

                        <!-- Auditoria -->
                        <li class="nav-item {{ request()->routeIs('auditoria.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>
                                    Auditoria
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.index') }}" class="nav-link {{ request()->routeIs('auditoria.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Todas as Auditorias</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.pessoas') }}" class="nav-link {{ request()->routeIs('auditoria.pessoas') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pessoas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.responsaveis') }}" class="nav-link {{ request()->routeIs('auditoria.responsaveis') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Responsáveis Contratos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.contratos') }}" class="nav-link {{ request()->routeIs('auditoria.contratos') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Contratos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.lotacoes') }}" class="nav-link {{ request()->routeIs('auditoria.lotacoes') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Lotações</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('auditoria.usuarios') }}" class="nav-link {{ request()->routeIs('auditoria.usuarios') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Usuários</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Perfil -->
                        <li class="nav-item">
                            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Perfil</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5><i class="fas fa-cog"></i> Configurações</h5>
                <hr>

                <!-- Toggle de Tema -->
                <div class="form-group">
                    <label><i class="fas fa-palette"></i> Tema da Interface</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-light active" id="light-theme" title="Modo Claro">
                            <input type="radio" name="theme" value="light" checked>
                            <i class="fas fa-sun"></i> Claro
                        </label>
                        <label class="btn btn-outline-light" id="dark-theme" title="Modo Escuro">
                            <input type="radio" name="theme" value="dark">
                            <i class="fas fa-moon"></i> Escuro
                        </label>
                    </div>
                    <small class="form-text text-muted">
                        Escolha entre o tema claro ou escuro para melhor experiência visual.
                    </small>
                </div>

                <hr>

                <!-- Informações do Sistema -->
                <div class="form-group">
                    <label><i class="fas fa-info-circle"></i> Informações</label>
                    <div class="small text-muted">
                        <p><strong>Sistema:</strong> SGC - Gestão de Contratos</p>
                        <p><strong>Versão:</strong> 1.0.0</p>
                        <p><strong>Framework:</strong> Laravel + AdminLTE</p>
                    </div>
                </div>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Versão</b> 1.0.0
            </div>
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ config('app.name', 'Laravel') }}</a>.</strong>
            Todos os direitos reservados.
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <!-- Correção de Conflitos JavaScript -->
    <script src="{{ asset('js/sgc-conflicts-fix.js') }}"></script>

    <!-- Lazy Loading Service -->
    <script src="{{ asset('js/lazy-loading.js') }}"></script>

    @livewireScripts

    <script>
        // === CORREÇÃO DE CONFLITOS JAVASCRIPT ===

        // Verificar se Alpine.js já foi carregado
        if (typeof window.Alpine === 'undefined') {
            console.log('Alpine.js não detectado, carregando...');
        } else {
            console.log('Alpine.js já carregado, evitando conflitos...');
        }

        // Aguardar o Livewire estar pronto (nova sintaxe)
        document.addEventListener('livewire:init', function () {
            console.log('Livewire inicializado com sucesso!');
        });

        // === SISTEMA DE TEMA NATIVO ADMINLTE ===

        // Carregar tema salvo
        const savedTheme = localStorage.getItem('adminlte-theme') || 'light';
        document.body.classList.add(savedTheme === 'dark' ? 'dark-mode' : 'light-mode');

        // Função para aplicar tema com correções específicas
        function applyTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.remove('light-mode');
                document.body.classList.add('dark-mode');

                // Forçar cores específicas para elementos problemáticos
                setTimeout(() => {
                    // Elementos gerais do dashboard
                    const problemElements = document.querySelectorAll('.content-wrapper .content h1, .content-wrapper .content h2, .content-wrapper .content h3, .content-wrapper .content h4, .content-wrapper .content h5, .content-wrapper .content h6, .content-wrapper .content p, .content-wrapper .content span, .content-wrapper .content div, .content-wrapper .content .card-body');
                    problemElements.forEach(element => {
                        element.style.color = '#ffffff !important';
                    });

                    // ESPECÍFICO PARA SMALL BOXES - Forçar texto branco
                    const smallBoxElements = document.querySelectorAll('.small-box, .small-box .inner, .small-box .inner h3, .small-box .inner h5, .small-box .inner p, .small-box .inner span, .small-box .inner div');
                    smallBoxElements.forEach(element => {
                        element.style.color = '#ffffff !important';
                        element.style.backgroundColor = '#2d2d2d !important';
                    });

                    // CORREÇÃO ESPECÍFICA PARA text-gray-800
                    const grayElements = document.querySelectorAll('.text-gray-800, h5.text-gray-800, .small-box .inner h5.text-gray-800, .small-box .inner h5, .small-box h5');
                    grayElements.forEach(element => {
                        element.style.color = '#ffffff !important';
                    });

                    // CORREÇÃO ESPECÍFICA PARA NAVBAR
                    const navbarElements = document.querySelectorAll('.main-header, .main-header .navbar, .main-header .navbar-nav .nav-link, .main-header .navbar-nav .nav-item .dropdown-toggle');
                    navbarElements.forEach(element => {
                        element.style.backgroundColor = '#2d2d2d !important';
                        element.style.color = '#ffffff !important';
                    });

                    // Forçar cor branca nos links da navbar
                    const navLinks = document.querySelectorAll('.main-header .navbar-nav .nav-link, .main-header .navbar-nav .nav-item .dropdown-toggle');
                    navLinks.forEach(element => {
                        element.style.color = '#ffffff !important';
                    });

                    // Forçar fundo escuro em elementos específicos
                    const backgroundElements = document.querySelectorAll('.content-wrapper .content, .content-wrapper .content .container-fluid, .content-wrapper .content .row, .content-wrapper .content .col');
                    backgroundElements.forEach(element => {
                        element.style.backgroundColor = '#1a1a1a !important';
                    });

                    // Forçar fundo escuro nas small boxes
                    const smallBoxBackgrounds = document.querySelectorAll('.small-box');
                    smallBoxBackgrounds.forEach(element => {
                        element.style.backgroundColor = '#2d2d2d !important';
                        element.style.borderColor = '#404040 !important';
                    });
                }, 100);
            } else {
                document.body.classList.remove('dark-mode');
                document.body.classList.add('light-mode');

                // Remover estilos forçados no modo claro
                setTimeout(() => {
                    const problemElements = document.querySelectorAll('.content-wrapper .content h1, .content-wrapper .content h2, .content-wrapper .content h3, .content-wrapper .content h4, .content-wrapper .content h5, .content-wrapper .content h6, .content-wrapper .content p, .content-wrapper .content span, .content-wrapper .content div, .content-wrapper .content .card-body');
                    problemElements.forEach(element => {
                        element.style.color = '';
                    });

                    // Remover estilos das small boxes
                    const smallBoxElements = document.querySelectorAll('.small-box, .small-box .inner, .small-box .inner h3, .small-box .inner h5, .small-box .inner p, .small-box .inner span, .small-box .inner div');
                    smallBoxElements.forEach(element => {
                        element.style.color = '';
                        element.style.backgroundColor = '';
                    });

                    // Remover estilos dos elementos text-gray-800
                    const grayElements = document.querySelectorAll('.text-gray-800, h5.text-gray-800, .small-box .inner h5.text-gray-800, .small-box .inner h5, .small-box h5');
                    grayElements.forEach(element => {
                        element.style.color = '';
                    });

                    // Remover estilos da navbar
                    const navbarElements = document.querySelectorAll('.main-header, .main-header .navbar, .main-header .navbar-nav .nav-link, .main-header .navbar-nav .nav-item .dropdown-toggle');
                    navbarElements.forEach(element => {
                        element.style.backgroundColor = '';
                        element.style.color = '';
                    });

                    // Remover estilos dos links da navbar
                    const navLinks = document.querySelectorAll('.main-header .navbar-nav .nav-link, .main-header .navbar-nav .nav-item .dropdown-toggle');
                    navLinks.forEach(element => {
                        element.style.color = '';
                    });

                    const backgroundElements = document.querySelectorAll('.content-wrapper .content, .content-wrapper .content .container-fluid, .content-wrapper .content .row, .content-wrapper .content .col');
                    backgroundElements.forEach(element => {
                        element.style.backgroundColor = '';
                    });

                    const smallBoxBackgrounds = document.querySelectorAll('.small-box');
                    smallBoxBackgrounds.forEach(element => {
                        element.style.backgroundColor = '';
                        element.style.borderColor = '';
                    });
                }, 100);
            }
        }

        // Aplicar tema inicial
        applyTheme(savedTheme);

        // Atualizar botões do control sidebar
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Adicionar tooltips aos botões de tema
            $('#light-theme').attr('data-toggle', 'tooltip').attr('title', 'Ativar Modo Claro');
            $('#dark-theme').attr('data-toggle', 'tooltip').attr('title', 'Ativar Modo Escuro');
            $('[data-toggle="tooltip"]').tooltip();

            const lightBtn = document.getElementById('light-theme');
            const darkBtn = document.getElementById('dark-theme');

            if (lightBtn && darkBtn) {
                if (savedTheme === 'dark') {
                    darkBtn.classList.add('active');
                    lightBtn.classList.remove('active');
                } else {
                    lightBtn.classList.add('active');
                    darkBtn.classList.remove('active');
                }

                // Event listeners para mudança de tema
                lightBtn.addEventListener('click', function() {
                    applyTheme('light');
                    localStorage.setItem('adminlte-theme', 'light');
                    lightBtn.classList.add('active');
                    darkBtn.classList.remove('active');
                });

                darkBtn.addEventListener('click', function() {
                    applyTheme('dark');
                    localStorage.setItem('adminlte-theme', 'dark');
                    darkBtn.classList.add('active');
                    lightBtn.classList.remove('active');
                });
            }
        });

        // Prevenir múltiplas inicializações do Alpine
        if (window.Alpine && !window.Alpine._initialized) {
            console.log('Alpine.js já carregado, evitando reinicialização...');
        }

        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '/logout';
            }
        }

        // === RESPONSIVIDADE MOBILE ===

        // Toggle sidebar em mobile
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }

        // Fechar sidebar ao clicar fora (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.main-sidebar');
            const toggleBtn = document.querySelector('[data-widget="pushmenu"]');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    document.body.classList.remove('sidebar-open');
                }
            }
        });

        // Detectar mudança de orientação
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                if (window.innerWidth > 768) {
                    document.body.classList.remove('sidebar-open');
                }
            }, 100);
        });

        // Detectar redimensionamento da janela
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.body.classList.remove('sidebar-open');
            }
        });

        // Melhorar experiência touch
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
        }

        // Prevenir zoom duplo toque em iOS
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Melhorar performance em mobile
        if (window.innerWidth <= 768) {
            // Lazy loading para imagens
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    </script>

    @yield('scripts')
</body>
</html>
