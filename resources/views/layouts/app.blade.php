<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Alkes BPAFK</title>
    <link rel="icon" type="image/png" href="{{ asset('Logo.png') }}">
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

        .dropdown-menu {
            border-radius: 12px;
            padding: 8px;
        }

        .dropdown-item {
            color: var(--bs-primary);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .dropdown-item:hover,
        .dropdown-item:focus,
        .dropdown-item.active {
            background-color: var(--bs-primary);
            color: white !important;
        }

        .dropdown-item-danger {
            color: #dc3545 !important;
        }

        .dropdown-item-danger:hover,
        .dropdown-item-danger:focus,
        .dropdown-item-danger:active {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .navbar {
            border-bottom: 4px solid var(--bs-secondary);
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.95rem;
            padding-bottom: 0.4rem;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .navbar-nav .nav-link:hover {
            color: #ffffff;
            border-bottom: 2px solid rgba(255, 255, 255, 0.5);
        }

        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            border-bottom: 2px solid #ffffff;
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

        /* Override Select2 Bootstrap 5 theme highlight color */
        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: var(--bs-primary) !important;
            color: #fff !important;
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo BPAFK Medan.png') }}" alt="Logo" width="130" height="40"
                    class="d-inline-block align-top me-2">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-4 me-auto mb-2 mb-lg-0 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}"
                            href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('repairs.*') ? 'active fw-bold' : '' }}"
                            href="{{ route('repairs.index') }}">
                            Perbaikan
                        </a>
                    </li>
                    @if (auth()->check() && auth()->user()->role !== 2)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('donations.*') ? 'active fw-bold' : '' }}"
                                href="{{ route('donations.index') }}">
                                Stok Donasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('distributions.*') ? 'active fw-bold' : '' }}"
                                href="{{ route('distributions.index') }}">
                                Distribusi
                            </a>
                        </li>
                    @endif
                    @if (auth()->check() && in_array(auth()->user()->name, ['administrator', 'Prodis Alkes']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('fasyankes.*') || request()->routeIs('alkes.*') || request()->routeIs('activity_logs.*') ? 'active fw-bold' : '' }}"
                                href="#" id="masterDataDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Master Data
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="masterDataDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('fasyankes.*') ? 'active' : '' }}"
                                        href="{{ route('fasyankes.index') }}">
                                        Fasyankes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('alkes.*') ? 'active' : '' }}"
                                        href="{{ route('alkes.index') }}">
                                        Alkes
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}"
                                        href="{{ route('activity_logs.index') }}">
                                        Log Aktivitas
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center mt-3 mt-lg-0 pb-3 pb-lg-0">

                    <!-- PENYESUAIAN LOGO DITJEN FARMALKES MULAI DI SINI -->
                    <div class="d-none d-lg-flex align-items-center bg-white rounded px-2 py-1 me-3 shadow-sm">
                        <img src="{{ asset('img/Ditjen_Farmalkes_Logo.png') }}" alt="Logo Ditjen Farmalkes"
                            style="height: 35px; width: auto;" class="d-inline-block">
                    </div>
                    <!-- PENYESUAIAN LOGO DITJEN FARMALKES SELESAI -->

                    <!-- SELECT BENCANA -->
                    @auth
                        @php
                            $userBencanaId = Auth::user()->bencana_id;
                            if ($userBencanaId) {
                                $bencanas = \App\Models\Bencana::where('id', $userBencanaId)->get();
                            } else {
                                $bencanas = \App\Models\Bencana::where('is_active', true)->orderBy('id', 'desc')->get();
                            }
                            $activeBencanaId = session('active_bencana_id');
                        @endphp
                        @if ($bencanas->isNotEmpty())
                            <div class="me-3 d-flex align-items-center">
                                <form action="{{ route('switchBencana') }}" method="POST" class="m-0"
                                    id="bencana-form">
                                    @csrf
                                    <select name="bencana_id" id="bencana-select" class="form-select form-select-sm"
                                        style="min-width: 150px;">
                                        @foreach ($bencanas as $bencana)
                                            <option value="{{ $bencana->id }}"
                                                {{ $activeBencanaId == $bencana->id ? 'selected' : '' }}>
                                                {{ $bencana->nama_bencana }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                    @endauth
                    <!-- END SELECT BENCANA -->
                    <div class="dropdown">
                        <a href="#"
                            class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
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

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                            aria-labelledby="profileDropdown">
                            <li>
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item dropdown-item-danger py-2 fw-bold d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-2 fs-5"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
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

    <div class="container flex-grow-1">
        @yield('content')
    </div>

    <footer class="bg-white text-center py-3 mt-5 mt-auto border-top shadow-sm">
        <div class="container">
            <small class="text-muted fw-bold">
                &copy; {{ date('Y') }} Balai Pengamanan Fasilitas Kesehatan (BPAFK) Medan. All Rights Reserved.
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#bencana-select').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity
            }).on('select2:select', function(e) {
                $('#bencana-form').submit();
            });

            // Initialize global select2 filters if they exist
            $('.select2-filter').each(function() {
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $(this).data('placeholder') || '-- Pilih --',
                    allowClear: true
                }).on('select2:select', function(e) {
                    $(this).closest('form').submit();
                });
            });

            // Global Select2 init for modals (menggunakan event delegation agar mendukung AJAX pagination)
            $(document).on('shown.bs.modal', '.modal', function() {
                $(this).find('select:not(.select2-hidden-accessible)').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%',
                        placeholder: $(this).data('placeholder') || $(this).find(
                            'option:first').text() || '-- Pilih --',
                        allowClear: $(this).find('option:first').val() === '',
                    });
                });
            });
        });
    </script>
    <script>
        // Fitur Paginasi Seamless (Diem Aja)
        document.addEventListener('click', function(e) {
            let paginationLink = e.target.closest('.pagination a');
            if (!paginationLink) return;

            e.preventDefault();
            let url = paginationLink.href;
            let container = paginationLink.closest('.card-body');

            if (!container) return;

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');

                    // Cari secara spesifik card-body yang memiliki elemen tabel (table-responsive)
                    // untuk menghindari tertukarnya dengan card-body lain di halaman (seperti card statistik)
                    let newTable = doc.querySelector('.table-responsive');
                    let newContainer = newTable ? newTable.closest('.card-body') : null;

                    if (newContainer) {
                        // Swap HTML secara instan, tanpa animasi, tanpa pindah scroll
                        container.innerHTML = newContainer.innerHTML;
                        window.history.pushState({
                            path: url
                        }, '', url);
                    }
                })
                .catch(error => {
                    window.location.href = url;
                });
        });

        // Fitur Pencarian Seamless (AJAX)
        let searchTimeout;
        window.debounceSearch = function(form) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                form.dispatchEvent(new Event('submit', {
                    cancelable: true,
                    bubbles: true
                }));
            }, 500);
        };

        document.addEventListener('submit', function(e) {
            let form = e.target.closest('.ajax-search');
            if (!form) return;

            e.preventDefault();

            // Serialize form data to URL search params
            let url = new URL(form.action || window.location.href);
            let formData = new FormData(form);

            // Remove empty params to keep URL clean
            let params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }
            url.search = params.toString();

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');

                    let newTable = doc.querySelector('.table-responsive');
                    let oldTable = document.querySelector('.table-responsive');

                    if (newTable && oldTable) {
                        let newContainer = newTable.closest('.card-body');
                        let container = oldTable.closest('.card-body');

                        if (newContainer && container) {
                            container.innerHTML = newContainer.innerHTML;
                            window.history.pushState({
                                path: url.href
                            }, '', url.href);
                        }
                    }
                })
                .catch(error => {
                    window.location.href = url;
                });
        });

        // Tangani navigasi back/forward
        window.addEventListener('popstate', function() {
            window.location.reload();
        });
    </script>
</body>

</html>
