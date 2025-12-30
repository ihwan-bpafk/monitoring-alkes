@extends('layouts.app')
@section('content')
<div class="card shadow-sm mb-5">
    <div class="card-header bg-white font-weight-bold">Form Input Perbaikan</div>
    <div class="card-body">
        <form action="{{ route('repairs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Tanggal Input</label>
                    <input type="date" name="tanggal_input" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Nama Rumah Sakit</label>
                    <input type="text" name="nama_rs" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Lokasi (Kab/Kota)</label>
                    <input type="text" name="lokasi" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Nama Alat Kesehatan</label>
                    <input type="text" name="nama_alkes" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Kategori</label>
                    <input type="text" name="kategori" class="form-control" placeholder="Contoh: Life Support">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Merek/Brand</label>
                    <input type="text" name="merek" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Tipe/Model</label>
                    <input type="text" name="tipe_model" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Nama Penyedia</label>
                    <input type="text" name="nama_penyedia" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Grade Kerusakan</label>
                    <select name="grade_kerusakan" class="form-select">
                        <option value="Ringan">Ringan</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Berat">Berat</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Kondisi Kontrak</label>
                    <input type="text" name="kondisi_kontrak" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Status Awal</label>
                    <input type="text" name="status_perbaikan" class="form-control" value="Laporan Diterima">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Foto Kondisi</label>
                    <input type="file" name="foto_kondisi" class="form-control">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Keterangan Lain</label>
                    <textarea name="keterangan_lain" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Data</button>
        </form>
    </div>
</div>
@endsection