@extends('layouts.app')

@section('content')
<style>
    /* Tetap gunakan style kamu yang lama */
    .modal-body { background-color: #f8f9fa; }
    .select2-container { width: 100% !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary"><i class="bi bi-gift me-2"></i>Monitoring Donasi Alkes</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
        </a>
        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahDonasi">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body">
        <form action="{{ route('donations.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="small fw-bold">Filter Rumah Sakit</label>
                <select name="filter_rs" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua RS --</option>
                    @foreach($list_rs as $rs)
                        <option value="{{ $rs }}" {{ request('filter_rs') == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Filter Donatur</label>
                <select name="filter_donatur" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Donatur --</option>
                    @foreach($list_donatur as $don)
                        <option value="{{ $don }}" {{ request('filter_donatur') == $don ? 'selected' : '' }}>{{ $don }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Filter Nama Alat</label>
                <select name="filter_alkes" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Alat --</option>
                    @foreach($list_alkes_donasi as $alk)
                        <option value="{{ $alk }}" {{ request('filter_alkes') == $alk ? 'selected' : '' }}>{{ $alk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="{{ route('donations.index') }}" class="btn btn-secondary btn-sm w-100">Reset Filter</a>
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
                        <th class="ps-3">No</th>
                        <th>RS / Petugas</th>
                        <th>Identitas Alat</th>
                        <th>Donatur / Tgl Terima</th> <th class="text-center">Berkas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $d)
                    <tr>
                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $d->nama_rs }}</div>
                            <div class="small text-muted">Oleh: {{ $d->input_by }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $d->nama_alkes }}</div>
                            <small class="text-muted">{{ $d->merek }} / {{ $d->tipe_model }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-success">{{ $d->donatur }}</div>
                            <div class="small text-muted"><i class="bi bi-calendar-check me-1"></i>{{ $d->tanggal_diterima ? \Carbon\Carbon::parse($d->tanggal_diterima)->format('d/m/Y') : '-' }}</div>
                        </td>
                        ...
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5">Data tidak ditemukan.</td></tr>
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
                            <label class="form-label small fw-bold text-danger">Tanggal Diterima RS</label>
                            <input type="date" name="tanggal_diterima" class="form-control border-danger" required value="{{ date('Y-m-d') }}">
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
                            <label class="form-label small fw-bold text-info">Upload Berkas (PDF/IMG)</label>
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
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection