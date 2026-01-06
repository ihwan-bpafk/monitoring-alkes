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
    <h4 class="fw-bold text-primary"><i class="bi bi-gift me-2"></i>Monitoring Donasi Alkes</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
        </a>
        <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold px-3">
            <i class="bi bi-tools me-2"></i>Data Perbaikan
        </a>
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
                        <th class="bg-primary text-white">Donatur</th>
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
                            <div class="small text-primary fw-bold">{{ $d->input_by }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $d->nama_alkes }}</div>
                            <small class="badge bg-info text-dark">{{ $d->jumlah }} Unit</small>
                        </td>
                        <td>
                            <div class="fw-bold text-success">{{ $d->donatur }}</div>
                        </td>
                        <td class="text-center">
                            @if($d->file_donasi)
                                <a href="{{ asset('storage/'.$d->file_donasi) }}" target="_blank" class="btn btn-sm btn-outline-danger py-0">
                                    <i class="bi bi-file-earmark-text"></i> PDF
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $d->id }}"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $d->id }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalUpdate{{ $d->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Edit Data Donasi</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('donations.update', $d->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Nama Alat</label>
                                                <input type="text" name="nama_alkes" class="form-control" value="{{ $d->nama_alkes }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Jumlah</label>
                                                <input type="number" name="jumlah" class="form-control" value="{{ $d->jumlah }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">File Baru (Biarkan kosong jika tidak ganti)</label>
                                                <input type="file" name="file_donasi" class="form-control" accept=".pdf,.jpg,.png">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary px-4">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalHapus{{ $d->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0">
                                <div class="modal-header bg-danger text-white py-2">
                                    <h6 class="modal-title">Hapus Data?</h6>
                                </div>
                                <div class="modal-body text-center py-4">
                                    Apakah Anda yakin ingin menghapus data donasi <strong>{{ $d->nama_alkes }}</strong>?
                                </div>
                                <div class="modal-footer justify-content-center border-0">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                    <form action="{{ route('donations.destroy', $d->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr><td colspan="6" class="text-center py-5">Data donasi belum tersedia.</td></tr>
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
                            <select name="nama_rs" class="form-select js-nama-rs select2-insidelop" required>
                                <option value="" data-lokasi="">-- Pilih Rumah Sakit --</option>
                                <option value="RSUD Muda Sedia Aceh Tamiang" data-lokasi="Kab. Aceh Tamiang">RSUD Muda Sedia Aceh Tamiang</option>
                                <option value="RSUD Sultan Abdul Aziz Syah" data-lokasi="Kota Peureulak">RSUD Sultan Abdul Aziz Syah</option>
                                <option value="RSUD Langsa" data-lokasi="Kota Langsa">RSUD Langsa</option>
                                <option value="RSUD Muyang Kute" data-lokasi="Bener Meriah">RSUD Muyang Kute</option>
                                <option value="RSUD Zubir Mahmud" data-lokasi="Kab Aceh Timur/Idi Timur">RSUD Zubir Mahmud</option>
                                <option value="RSUD Fauziah Bireuen" data-lokasi="Bireuen">RSUD Fauziah Bireuen</option>
                                <option value="RSUD Datu Beru" data-lokasi="Takengon">RSUD Datu Beru</option>
                                <option value="RSUD Tanjung Pura" data-lokasi="Kab. Langkat/Tanjung Pura">RSUD Tanjung Pura</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-primary">Nama Alat Kesehatan</label>
                            <select name="nama_alkes" class="form-select select2-insidelop border-primary" required>
                                <option value="">-- Pilih Alat Kesehatan --</option>
                                @foreach($list_alkes as $alkes)
                                    <option value="{{ $alkes }}">{{ $alkes }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">Pilih alat yang sudah terdaftar di sistem.</div>
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
<script>
    $(document).ready(function() {
    $('.select2-insidelop').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalTambahDonasi'), // Penting agar select2 muncul di dalam modal
        tags: true, // Izinkan user mengetik nama alat baru yang belum ada di list
        placeholder: "-- Pilih atau Ketik Alat Baru --"
    });
});
</script>
@endsection