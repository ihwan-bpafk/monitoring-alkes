@extends('layouts.app')

@section('content')
<style>
    /* Fix Modal Scroll & Layout */
    .modal-dialog-scrollable .modal-content {
        max-height: 95vh;
    }
    .modal-body {
        overflow-y: auto !important;
        background-color: #f8f9fa;
    }
    /* Style List Nama File */
    .file-name-list li {
        padding: 6px 10px;
        border-radius: 6px;
        background: #ffffff;
        border: 1px solid #dee2e6;
        margin-bottom: 5px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .card-info-alkes {
        border-left: 4px solid #0d6efd;
    }
    .pagination { margin-bottom: 0; }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary"><i class="bi bi-tools me-2"></i>Monitoring Donasi Alkes</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
        </a>

        <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold px-3">
            <i class="bi bi-tools me-2"></i>Data Perbaikan
        </a>

        {{-- <a href="{{ route('repairs.report') }}" class="btn btn-success shadow-sm fw-bold">
            <i class="bi bi-file-earmark-excel me-1"></i>Reporting
        </a> --}}

        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahDonasi">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </button>
    </div>
</div>
<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-3 bg-primary text-white" width="50">No</th>
                        <th class="bg-primary text-white">RS / Petugas</th>
                        <th class="bg-primary text-white">Identitas Alat</th>
                        <th class="bg-primary text-white">Donatur & Keterangan</th>
                        <th class="bg-primary text-white text-center">Berkas</th>
                        <th class="bg-primary text-white text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $d)
                    <tr>
                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark text-uppercase">{{ $d->nama_rs }}</div>
                            <div class="small text-primary fw-bold"><i class="bi bi-person-badge"></i> {{ $d->input_by }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $d->nama_alkes }}</div>
                            <small class="text-muted d-block">Merk/Model: {{ $d->merek ?? '-' }} / {{ $d->tipe_model ?? '-' }}</small>
                            <small class="badge bg-info text-dark">Jumlah: {{ $d->jumlah }} Unit</small>
                        </td>
                        <td>
                            <div class="fw-bold text-success"><i class="bi bi-box-seam me-1"></i>{{ $d->donatur }}</div>
                            <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $d->keterangan_lain ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            @if($d->file_donasi)
                                <a href="{{ asset('storage/'.$d->file_donasi) }}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm py-0">
                                    <i class="bi bi-file-earmark-text"></i> Lihat Berkas
                                </a>
                            @else
                                <span class="text-muted small italic">- Kosong -</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-info btn-sm text-white fw-bold px-2" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $d->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-sm fw-bold px-2" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $d->id }}"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-danger btn-sm px-2" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $d->id }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    {{-- [Modal Update Code similar to Modal Tambah below but with values] --}}

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted small italic">Data donasi belum tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahDonasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-gift me-2"></i>Input Alat Donasi Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Petugas Input</label>
                            <input type="text" name="input_by" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Rumah Sakit</label>
                            <input type="text" name="nama_rs" class="form-control" placeholder="Tujuan Donasi" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Nama Alat Kesehatan</label>
                            <input type="text" name="nama_alkes" class="form-control border-primary" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Merek</label>
                            <input type="text" name="merek" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe/Model</label>
                            <input type="text" name="tipe_model" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jumlah (Unit)</label>
                            <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">Donatur / Pemberi</label>
                            <input type="text" name="donatur" class="form-control border-success" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Upload Berkas (PDF/IMG)</label>
                            <input type="file" name="file_donasi" class="form-control border-danger" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Keterangan Lain</label>
                            <textarea name="keterangan_lain" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection