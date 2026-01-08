@extends('layouts.app')

@section('content')
<style>
    .modal-body { background-color: #f8f9fa; }
    .select2-container { width: 100% !important; }
    .badge-stok { font-size: 0.8rem; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-0"><i class="bi bi-truck me-2"></i>Distribusi & Alokasi Alkes</h4>
            <p class="text-muted small mb-0">Penyaluran alat kesehatan dari gudang donasi ke Rumah Sakit.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
                <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
            </a>
            <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold px-3">
                <i class="bi bi-tools me-2"></i>Data Perbaikan
            </a>
            <a href="{{ route('donations.index') }}" class="btn btn-success shadow-sm fw-bold">
                <i class="bi bi-box-seam me-2"></i>Stok Donasi
            </a>
            @if(auth()->user()->role === 1 || auth()->user()->role === 2)
            <button class="btn btn-primary shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalDistribusi">
                <i class="bi bi-plus-lg me-1"></i>Input Distribusi
            </button>
            @endif
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('distributions.index') }}" method="GET" class="row g-2">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Rumah Sakit Tujuan</label>
                    <select name="filter_rs" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua RS --</option>
                        @foreach($list_rs as $rs)
                            <option value="{{ $rs }}" {{ request('filter_rs') == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Nama Alat</label>
                    <select name="filter_alkes" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Alat --</option>
                        @foreach($list_alkes as $alk)
                            <option value="{{ $alk }}" {{ request('filter_alkes') == $alk ? 'selected' : '' }}>{{ $alk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Status</label>
                    <select name="filter_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        @foreach($list_status as $st)
                            <option value="{{ $st }}" {{ request('filter_status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-3" width="50">No</th>
                            <th class="text-start">Identitas Alat</th>
                            <th>Sumber Donasi</th>
                            <th>Tujuan (RS)</th>
                            <th>Jumlah Distribusi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributions as $dist)
                        <tr>
                            <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                            <td class="text-start">
                                <div class="fw-bold text-primary text-uppercase">{{ $dist->donation->nama_alkes }}</div>
                                <small class="text-muted">{{ $dist->donation->merek ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $dist->donation->pemberi_donasi }}</div>
                                <small class="text-muted">Total Awal: {{ $dist->donation->jumlah_donasi }} | Sisa: <strong>{{ $dist->donation->sisa_stok }}</strong></small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $dist->nama_rs }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Tgl: {{ \Carbon\Carbon::parse($dist->tanggal_distribusi)->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark px-3 py-2">
                                    {{ $dist->jumlah_distribusi }} Unit
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $dist->status == 'Diterima' ? 'bg-success' : 'bg-info' }}">
                                    {{ $dist->status }}
                                </span>
                            </td>
                            <td>
                                @if(auth()->user()->role === 1 || auth()->user()->role === 2)
                                <form action="{{ route('distributions.destroy', $dist->id) }}" method="POST" onsubmit="return confirm('Hapus data distribusi? Stok akan dikembalikan otomatis.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted italic">Belum ada riwayat distribusi barang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $distributions->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDistribusi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i>Form Alokasi Distribusi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('distributions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Pilih Sumber Donasi (Alat & Sisa Stok)</label>
                            <select name="donation_id" class="form-select select2-source" required>
                                <option value="">-- Cari Alat dengan Sisa Stok > 0 --</option>
                                @foreach($donations_available as $don)
                                    <option value="{{ $don->id }}" data-sisa="{{ $don->sisa_stok }}">
                                        {{ $don->nama_alkes }} - {{ $don->pemberi_donasi }} (Sisa: {{ $don->sisa_stok }} Unit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Alokasi Distribusi (Nama RS)</label>
                            <input type="text" name="nama_rs" class="form-control" placeholder="Contoh: RSUD Sultan Abdul Aziz Syah" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">Jumlah Distribusi</label>
                            <input type="number" name="jumlah_distribusi" class="form-control border-danger" min="1" required>
                            <div id="stok-warning" class="form-text text-danger d-none small mt-1">
                                <i class="bi bi-exclamation-triangle"></i> Melebihi sisa stok!
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Distribusi</label>
                            <input type="date" name="tanggal_distribusi" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Petugas Pengirim</label>
                            <input type="text" name="petugas_pengirim" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Keterangan / Catatan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" id="btn-submit">PROSES DISTRIBUSI</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2-source').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalDistribusi'),
        placeholder: "-- Cari Alat --"
    });

    // Validasi Real-time Jumlah Distribusi vs Sisa Stok
    $('input[name="jumlah_distribusi"]').on('input', function() {
        let selectedOption = $('.select2-source').find(':selected');
        let sisaStok = parseInt(selectedOption.data('sisa')) || 0;
        let inputJumlah = parseInt($(this).val()) || 0;

        if (inputJumlah > sisaStok) {
            $(this).addClass('is-invalid');
            $('#stok-warning').removeClass('d-none');
            $('#btn-submit').attr('disabled', true);
        } else {
            $(this).removeClass('is-invalid');
            $('#stok-warning').addClass('d-none');
            $('#btn-submit').attr('disabled', false);
        }
    });

    // Reset validasi saat ganti pilihan alat
    $('.select2-source').on('change', function() {
        $('input[name="jumlah_distribusi"]').val('').removeClass('is-invalid');
        $('#stok-warning').addClass('d-none');
    });
});
</script>
@endsection