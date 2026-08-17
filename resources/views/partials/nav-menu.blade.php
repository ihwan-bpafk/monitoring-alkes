<style>
    .btn-nav-modern {
        background-color: #ffffff;
        color: var(--bs-primary);
        border: 1px solid var(--bs-primary);
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    .btn-nav-modern:hover {
        background-color: var(--bs-primary);
        color: #ffffff;
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .btn-nav-modern:hover i {
        color: #ffffff !important;
    }
    .btn-nav-modern.active {
        background-color: var(--bs-primary);
        color: #ffffff;
        border-color: var(--bs-primary);
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    }
    .btn-nav-modern.active i {
        color: #ffffff !important;
    }
    .btn-nav-modern i {
        color: var(--bs-primary);
        transition: all 0.2s;
    }
</style>

<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('dashboard') }}" class="btn btn-nav-modern {{ isset($active) && $active == 'dashboard' ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </a>
    <a href="{{ route('repairs.index') }}" class="btn btn-nav-modern {{ isset($active) && $active == 'repairs' ? 'active' : '' }}">
        <i class="bi bi-tools me-2"></i>Data Perbaikan
    </a>
    <a href="{{ route('donations.index') }}" class="btn btn-nav-modern {{ isset($active) && $active == 'donations' ? 'active' : '' }}">
        <i class="bi bi-box-seam me-2"></i>Stok Donasi
    </a>
    <a href="{{ route('distributions.index') }}" class="btn btn-nav-modern {{ isset($active) && $active == 'distributions' ? 'active' : '' }}">
        <i class="bi bi-truck me-2"></i>Distribusi
    </a>
    @if (auth()->check() && auth()->user()->name === 'Administrator')
        <a href="{{ route('fasyankes.index') }}" class="btn btn-nav-modern {{ isset($active) && $active == 'fasyankes' ? 'active' : '' }}">
            <i class="bi bi-building me-2"></i>Fasyankes
        </a>
    @endif
</div>
