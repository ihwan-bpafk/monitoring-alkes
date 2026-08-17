<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Monitoring Alkes BPAFK</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --bs-primary: #047d79;
            --bs-primary-rgb: 4, 125, 121;
            --bs-secondary: #d0db02;
            --bs-secondary-rgb: 208, 219, 2;
        }

        .bg-primary {
            background-color: var(--bs-primary) !important;
        }

        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover {
            background-color: #035f5c !important;
        }

        .navbar {
            border-bottom: 4px solid var(--bs-secondary);
        }

        .timeline {
            border-left: 3px solid var(--bs-primary);
            padding-left: 20px;
            position: relative;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -9px;
            top: 5px;
            width: 15px;
            height: 15px;
            background: var(--bs-primary);
            border-radius: 50%;
        }

        .table thead tr th {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: 95vh;
        }

        .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        .modal-body .card {
            height: auto !important;
            margin-bottom: 15px;
        }

        .select2-container--bootstrap-5 .select2-selection__rendered,
        .select2-container--bootstrap-5 .select2-results__option,
        .select2-container--bootstrap-5 .select2-search__field,
        .select2-container--bootstrap-5 .select2-selection__placeholder {
            font-size: 0.8rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single:focus,
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #047d79 !important;
            box-shadow: 0 0 0 0.25rem rgba(4, 125, 121, 0.25) !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted,
        .select2-container--bootstrap-5 .select2-results__option--selected,
        .select2-container--bootstrap-5 .select2-results__option:hover {
            background-color: #047d79 !important;
            color: white !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #047d79 !important;
            max-height: 800px !important;
        }

        .select2-container--bootstrap-5 .select2-selection__clear {
            color: #047d79 !important;
        }

        .select2-container--bootstrap-5 .select2-results__options {
            max-height: 440px !important;
            overflow-y: auto;
        }

        .pagination .page-link {
            color: #047d79;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background-color: #047d79 !important;
            border-color: #047d79 !important;
            color: white !important;
        }

        .pagination .page-link:hover {
            color: white;
            background-color: #035f5c;
            border-color: #035f5c;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.25rem rgba(4, 125, 121, 0.25);
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('repairs.index') }}">
                <img src="{{ asset('img/logo BPAFK Medan.png') }}" alt="Logo" width="130" height="40"
                    class="d-inline-block align-top me-2">
                <span class="d-none d-md-inline">BPAFK - Monitoring Alkes</span>
            </a>

            <div class="d-flex align-items-center">

                <!-- PENYESUAIAN LOGO DITJEN FARMALKES MULAI DI SINI -->
                <div class="d-none d-lg-flex align-items-center bg-white rounded px-2 py-1 me-3 shadow-sm">
                    <img src="{{ asset('img/Ditjen_Farmalkes_Logo.png') }}" alt="Logo Ditjen Farmalkes"
                        style="height: 35px; width: auto;" class="d-inline-block">
                </div>
                <!-- PENYESUAIAN LOGO DITJEN FARMALKES SELESAI -->

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end me-2 d-none d-sm-block">
                            <small class="d-block lh-1 fw-bold">{{ Auth::user()->name }}</small>
                            <small style="font-size: 0.7rem;" class="text-white-50">Username:
                                {{ Auth::user()->username }}</small>
                        </div>
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 35px; height: 35px;">
                            <i class="bi bi-person-fill text-primary fs-5"></i>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="profileDropdown">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item text-danger py-2 fw-bold d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-2 fs-5"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    @if (config('app.data_locked'))
        <div
            class="alert {{ auth()->user()->username === 'admin' ? 'alert-info' : 'alert-danger' }} border-0 shadow-sm rounded-0 mb-4 py-1">
            <div class="container-fluid d-flex align-items-center justify-content-center">
                <i class="bi bi-info-circle-fill me-2"></i>
                @if (auth()->user()->username === 'admin')
                    <small><strong>MODE LAPORAN AKTIF:</strong> Anda tetap bisa input/edit karena login sebagai Admin
                        Utama.</small>
                @else
                    <small><strong>SISTEM TERKUNCI:</strong> Input/Update dinonaktifkan sementara untuk Validasi
                        Laporan.</small>
                @endif
            </div>
        </div>
    @endif

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>

</html>
