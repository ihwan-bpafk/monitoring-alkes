@extends('layouts.app')

@section('content')
<style>
    /* Custom Style untuk konsistensi UI BPAFK */
    .modal-body { background-color: #f8f9fa; }
    .card-custom { border-radius: 12px; border: none; }
    .table thead th { background-color: #f8f9fa; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .badge-stok { font-size: 0.85rem; padding: 0.5em 0.8em; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-0"><i class="bi bi-gift-fill me-2"></i>Monitoring Donasi & Stok Alat</h4>
            <p class="text-muted small mb-0">Manajemen inventaris stok masuk sebelum dialokasikan ke Rumah Sakit.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
                <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
            </a>

            <a href="{{ route('distributions.index') }}" class="btn btn-info shadow-sm fw-bold text-white">
                <i class="bi bi-truck me-2"></i>Menu Distribusi
            </a>

            <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold">
                <i class="bi bi-tools me-2"></i>Data Perbaikan
            </a>

            <button class="btn btn-primary shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTambahDonasi">
                <i class="bi bi-plus-lg me-1"></i>Tambah Donasi
            </button>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('donations.index') }}" method="GET" class="row g-2">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Pemberi Donasi</label>
                    <select name="filter_donatur" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Donatur --</option>
                        @foreach($list_donatur as $don)
                            <option value="{{ $don }}" {{ request('filter_donatur') == $don ? 'selected' : '' }}>{{ $don }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Nama Alat</label>
                    <select name="filter_alkes" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Alat --</option>
                        @foreach($list_alkes_donasi as $alk)
                            <option value="{{ $alk }}" {{ request('filter_alkes') == $alk ? 'selected' : '' }}>{{ $alk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Penerima</label>
                    <select name="filter_petugas" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Petugas --</option>
                        @foreach($list_petugas as $p)
                            <option value="{{ $p }}" {{ request('filter_petugas') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center" width="50">No</th>
                            <th>Pemberi Donasi</th>
                            <th>Identitas Alat</th>
                            <th class="text-center">Jumlah Donasi</th>
                            <th class="text-center">Sisa Stok</th>
                            <th>Diterima Oleh</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $d)
                        <tr>
                            <td class="ps-3 text-center text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-bold text-dark">{{ $d->pemberi_donasi }}</td>
                            <td>
                                <div class="text-primary fw-bold text-uppercase">{{ $d->nama_alkes }}</div>
                                <small class="text-muted">Merk: {{ $d->merek ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary px-3">{{ $d->jumlah_donasi }} Unit</span>
                                <div class="text-muted" style="font-size: 0.7rem;">Tgl: {{ \Carbon\Carbon::parse($d->tanggal_masuk)->format('d/m/Y') }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-stok {{ $d->sisa_stok > 0 ? 'bg-success' : 'bg-danger' }} shadow-sm">
                                    {{ $d->sisa_stok }} Unit
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $d->diterima_oleh }}</div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $d->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('donations.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus data donasi ini? Data distribusi terkait mungkin akan terpengaruh.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $d->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Donasi</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('donations.update', $d->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Pemberi Donasi</label>
                                                <input type="text" name="pemberi_donasi" class="form-control" value="{{ $d->pemberi_donasi }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Alat Kesehatan</label>
                                                <input type="text" name="nama_alkes" class="form-control" value="{{ $d->nama_alkes }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label small fw-bold">Merk</label>
                                                    <input type="text" name="merek" class="form-control" value="{{ $d->merek }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label small fw-bold">Jumlah Donasi</label>
                                                    <input type="number" name="jumlah_donasi" class="form-control" value="{{ $d->jumlah_donasi }}" required>
                                                    <small class="text-danger" style="font-size: 0.65rem;">*Mengubah ini akan meriset sisa stok.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="submit" class="btn btn-primary w-100 fw-bold">UPDATE DATA</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted italic">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Belum ada data donasi masuk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <div class="small text-muted">
                    Menampilkan {{ $donations->firstItem() ?? 0 }} - {{ $donations->lastItem() ?? 0 }} dari {{ $donations->total() }} data
                </div>
                <div>
                    {{ $donations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahDonasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Input Donasi Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('donations.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pemberi Donasi</label>
                        <input type="text" name="pemberi_donasi" class="form-control" placeholder="Contoh: Yayasan Bakti / PT. Sehat" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Nama Alat Kesehatan</label>
                        <input type="text" name="nama_alkes" class="form-control border-primary" placeholder="Contoh: Patient Monitor" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Merk</label>
                            <input type="text" name="merek" class="form-control" placeholder="Contoh: Philips / GE">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-danger">Jumlah Donasi</label>
                            <input type="number" name="jumlah_donasi" class="form-control border-danger" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Diterima Oleh (Petugas)</label>
                        <input type="text" name="diterima_oleh" class="form-control bg-light" value="{{ Auth::user()->name }}" required readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 pb-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow">SIMPAN DATA KE GUDANG</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection