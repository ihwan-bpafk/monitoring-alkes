@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">Detail Laporan Perbaikan</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6"><small class="text-muted d-block">Nama RS / Lokasi</small><strong>{{ $repair->nama_rs }} ({{ $repair->lokasi }})</strong></div>
                    <div class="col-6"><small class="text-muted d-block">Alat Kesehatan</small><strong>{{ $repair->nama_alkes }}</strong></div>
                    <div class="col-6"><small class="text-muted d-block">Kategori / Kondisi Awal</small><strong>{{ $repair->kategori }} - <span class="text-danger">{{ $repair->grade_kerusakan }}</span></strong></div>
                    <div class="col-6"><small class="text-muted d-block">Merek / Tipe</small><strong>{{ $repair->merek }} ({{ $repair->tipe_model }})</strong></div>
                    <div class="col-12"><hr></div>
                    <div class="col-md-6">
                        <h6>Update Progress:</h6>
                        <form action="{{ route('repairs.updateStatus', $repair->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-bold">Petugas</label>
                                <input type="text" name="petugas" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Status Baru</label>
                                <input type="text" name="status_perbaikan" class="form-control" value="{{ $repair->status_perbaikan }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Keterangan Perubahan</label>
                                <textarea name="keterangan_log" class="form-control" rows="2" placeholder="Apa yang sedang dikerjakan?"></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Rencana Tindak Lanjut (RTL)</label>
                                <textarea name="rtl" class="form-control" rows="2">{{ $repair->rtl }}</textarea>
                            </div>
                            <button class="btn btn-warning w-100 fw-bold shadow-sm">Simpan Update</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="small fw-bold d-block mb-2">Foto Kondisi Produk</label>
                        @if($repair->foto_kondisi)
                            <img src="{{ asset('storage/'.$repair->foto_kondisi) }}" class="img-fluid rounded shadow-sm border">
                        @else
                            <div class="bg-light border rounded p-5 text-muted small italic">Tidak ada foto</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-dark fw-bold">History Perubahan</div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($repair->histories as $h)
                    <div class="timeline-item mb-4 border-start border-3 border-primary ps-3">
                        <div class="small fw-bold text-primary">{{ $h->created_at->format('d/m/Y H:i') }}</div>
                        <div class="fw-bold">{{ $h->status_perbaikan }}</div>
                        <p class="small text-muted mb-1">{{ $h->keterangan_perubahan }}</p>
                        <small class="badge bg-light text-dark border">Oleh: {{ $h->user_nama }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection