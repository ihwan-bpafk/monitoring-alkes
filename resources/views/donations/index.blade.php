@extends('layouts.app')

@section('content')
<style>
    /* Konsistensi UI */
    .modal-dialog-scrollable .modal-content { max-height: 95vh; }
    .modal-body { background-color: #f8f9fa; overflow-y: auto !important; }
    .card-stok-utama { border-left: 5px solid #198754; }
    .table thead th { vertical-align: middle; text-align: center; }
    .select2-container { width: 100% !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-primary mb-0"><i class="bi bi-gift-fill me-2"></i>Monitoring Donasi & Stok Alat</h4>
        <p class="text-muted small mb-0">Manajemen inventaris donasi masuk dan distribusi ke RS.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
        </a>
        <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold">
            <i class="bi bi-tools me-2"></i>Data Perbaikan
        </a>
        <button class="btn btn-primary shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTambahDonasi">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white fw-bold py-3 border-0">
        <i class="bi bi-filter-left me-2 text-primary"></i>Filter & Rekap Data
    </div>
    <div class="card-body pt-0">
        <form action="{{ route('donations.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Rumah Sakit</label>
                <select name="filter_rs" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua RS Tujuan --</option>
                    @foreach($list_rs as $rs)
                        <option value="{{ $rs }}" {{ request('filter_rs') == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Pihak Donatur</label>
                <select name="filter_donatur" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Donatur --</option>
                    @foreach($list_donatur as $don)
                        <option value="{{ $don }}" {{ request('filter_donatur') == $don ? 'selected' : '' }}>{{ $don }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Jenis Alat</label>
                <select name="filter_alkes" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Nama Alat --</option>
                    @foreach($list_alkes_donasi as $alk)
                        <option value="{{ $alk }}" {{ request('filter_alkes') == $alk ? 'selected' : '' }}>{{ $alk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset Filter</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-3" width="50">No</th>
                        <th class="text-start">Identitas Alat</th>
                        <th>Donasi Masuk</th>
                        <th>Distribusi (Ke RS)</th>
                        <th>Sisa Stok</th>
                        <th>Berkas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $d)
                    <tr>
                        <td class="ps-3 text-center text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark text-uppercase">{{ $d->nama_alkes }}</div>
                            <small class="text-muted d-block">{{ $d->merek ?? '-' }} | {{ $d->tipe_model ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $d->donatur }}</div>
                            <div class="small">Total: <strong>{{ $d->jumlah_total_donasi }}</strong> unit</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Diterima: {{ $d->tanggal_terima_donatur ? \Carbon\Carbon::parse($d->tanggal_terima_donatur)->format('d/m/Y') : '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $d->nama_rs }}</div>
                            <div class="small">Keluar: <span class="badge bg-warning text-dark">{{ $d->jumlah }} Unit</span></div>
                            <div class="mt-1"><span class="badge {{ $d->status == 'Diterima' ? 'bg-info' : 'bg-light text-dark border' }} py-1">{{ $d->status ?? 'Proses' }}</span></div>
                        </td>
                        <td class="text-center">
                            <h5 class="mb-0">
                                <span class="badge {{ $d->sisa_stok > 0 ? 'bg-primary' : 'bg-danger' }} shadow-sm">
                                    {{ $d->sisa_stok }}
                                </span>
                            </h5>
                            <small class="text-muted" style="font-size: 0.65rem;">Unit Tersisa</small>
                        </td>
                        <td class="text-center">
                            @if($d->file_donasi)
                                <a href="{{ asset('storage/'.$d->file_donasi) }}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            @else
                                <span class="text-muted small italic">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $d->id }}"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $d->id }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalUpdate{{ $d->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white py-3">
                                    <h5 class="modal-title fw-bold">Edit Data: {{ $d->nama_alkes }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('donations.update', $d->id) }}" method="POST" enctype="multipart/form-data" class="form-hitung">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Nama Alat Kesehatan</label>
                                                <select name="nama_alkes" class="form-select select2-edit" required>
                                                    @foreach($list_alkes as $alkes)
                                                        <option value="{{ $alkes }}" {{ $d->nama_alkes == $alkes ? 'selected' : '' }}>{{ $alkes }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-primary">Pemberi Donasi (Donatur)</label>
                                                <input type="text" name="donatur" class="form-control" value="{{ $d->donatur }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-muted">Tanggal Terima dari Donatur</label>
                                                <input type="date" name="tanggal_terima_donatur" class="form-control" value="{{ $d->tanggal_terima_donatur }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-primary">Stok Awal Donasi</label>
                                                <input type="number" name="jumlah_total_donasi" class="form-control border-primary input-total" value="{{ $d->jumlah_total_donasi }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-warning">Jumlah Keluar (Ke RS)</label>
                                                <input type="number" name="jumlah" class="form-control border-warning input-keluar" value="{{ $d->jumlah }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-danger">Sisa Stok (Otomatis)</label>
                                                <input type="number" name="sisa_stok" class="form-control border-danger bg-light input-sisa" value="{{ $d->sisa_stok }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Tujuan Rumah Sakit</label>
                                                <input type="text" name="nama_rs" class="form-control text-uppercase" value="{{ $d->nama_rs }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Status Distribusi</label>
                                                <select name="status" class="form-select">
                                                    <option value="Dikirim" {{ $d->status == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                    <option value="Diterima" {{ $d->status == 'Diterima' ? 'selected' : '' }}>Diterima RS</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold text-danger">Ganti Berkas (PDF/JPG)</label>
                                                <input type="file" name="file_donasi" class="form-control border-danger">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">SIMPAN PERUBAHAN</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalHapus{{ $d->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white py-2">
                                    <h6 class="modal-title small">Konfirmasi Hapus</h6>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <p class="mb-0 text-muted small">Hapus data donasi alat:</p>
                                    <h6 class="fw-bold">{{ $d->nama_alkes }}</h6>
                                </div>
                                <div class="modal-footer justify-content-center border-0 pb-3">
                                    <form action="{{ route('donations.destroy', $d->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm px-4 shadow-sm">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted small italic">Data donasi tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center justify-content-md-between align-items-center p-3 border-top bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
            <div class="small text-muted d-none d-md-block">
                Halaman {{ $donations->currentPage() }} dari {{ $donations->lastPage() }}
            </div>
            <div>
                {{ $donations->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahDonasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Input Alat Donasi Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data" class="form-hitung">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Petugas Input</label>
                            <input type="text" name="input_by" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tujuan Rumah Sakit</label>
                            <select name="nama_rs" class="form-select js-nama-rs select2-insidelop" required>
                                <option value="">-- Pilih RS Tujuan --</option>
                                <option value="RSUD Muda Sedia Aceh Tamiang">RSUD Muda Sedia Aceh Tamiang</option>
                                <option value="RSUD Sultan Abdul Aziz Syah">RSUD Sultan Abdul Aziz Syah</option>
                                <option value="RSUD Langsa">RSUD Langsa</option>
                                <option value="RSUD Muyang Kute">RSUD Muyang Kute</option>
                                <option value="RSUD Zubir Mahmud">RSUD Zubir Mahmud</option>
                                <option value="RSUD Fauziah Bireuen">RSUD Fauziah Bireuen</option>
                                <option value="RSUD Datu Beru">RSUD Datu Beru</option>
                                <option value="RSUD Tanjung Pura">RSUD Tanjung Pura</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-primary">Nama Alat Kesehatan</label>
                            <select name="nama_alkes" class="form-select select2-insidelop border-primary" required>
                                <option value="">-- Pilih atau Ketik Alat Baru --</option>
                                @foreach($list_alkes as $alkes)
                                    <option value="{{ $alkes }}">{{ $alkes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">Tgl Terima Donatur</label>
                            <input type="date" name="tanggal_terima_donatur" class="form-control border-danger" value="{{ date('Y-m-d') }}" required>
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
                            <label class="form-label small fw-bold">Donatur / Pemberi</label>
                            <input type="text" name="donatur" class="form-control" required placeholder="Nama Yayasan/PT">
                        </div>
                        
                        <div class="col-md-12">
                            <div class="p-3 rounded border bg-white card-stok-utama shadow-sm">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-primary">Jumlah Total Donasi</label>
                                        <input type="number" name="jumlah_total_donasi" class="form-control border-primary input-total" value="0" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-warning">Jumlah Keluar (Ke RS)</label>
                                        <input type="number" name="jumlah" class="form-control border-warning input-keluar" value="0" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-danger">Sisa Stok (Gudang)</label>
                                        <input type="number" name="sisa_stok" class="form-control border-danger bg-light input-sisa" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Distribusi</label>
                            <select name="status" class="form-select">
                                <option value="Dikirim">Dikirim ke RS</option>
                                <option value="Diterima">Diterima RS</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-info">Berkas Pendukung (PDF/IMG)</label>
                            <input type="file" name="file_donasi" class="form-control border-info" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Keterangan Lain</label>
                            <textarea name="keterangan_lain" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">SIMPAN DATA STOK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk Modal Tambah & Update
    $('.select2-insidelop, .select2-edit').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('.modal'), // Agar dropdown muncul di atas modal
        tags: true, 
        placeholder: "-- Pilih atau Ketik Baru --"
    });

    // LOGIKA KALKULASI STOK OTOMATIS
    // Mendengarkan perubahan pada input total dan input keluar
    $(document).on('input', '.input-total, .input-keluar', function() {
        let form = $(this).closest('form');
        let total = parseInt(form.find('.input-total').val()) || 0;
        let keluar = parseInt(form.find('.input-keluar').val()) || 0;
        
        let sisa = total - keluar;
        
        // Update field sisa stok
        form.find('.input-sisa').val(sisa);

        // Validasi Sederhana: Jangan biarkan keluar > total
        if (keluar > total) {
            form.find('.input-keluar').addClass('is-invalid');
        } else {
            form.find('.input-keluar').removeClass('is-invalid');
        }
    });
});
</script>
@endsection