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
    <h4 class="fw-bold text-primary"><i class="bi bi-tools me-2"></i>Monitoring Perbaikan Alkes</h4>
    
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
        </a>

        <a href="{{ route('repairs.report') }}" class="btn btn-success shadow-sm fw-bold">
            <i class="bi bi-file-earmark-excel me-1"></i>Reporting
        </a>
        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </button>
    </div>
</div>

{{-- <div class="row mb-3">
    <div class="col-md-5">
        <form action="{{ route('repairs.index') }}" method="GET" class="d-flex shadow-sm rounded">
            <input type="text" name="search" class="form-control border-0" placeholder="Cari RS, Alat, SN, atau Lokasi..." value="{{ request('search') }}">
            <button class="btn btn-white bg-white border-0 text-primary" type="submit">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('repairs.index') }}" class="btn btn-white bg-white border-0 text-danger">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>
</div> --}}

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('repairs.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="small fw-bold text-muted">Rumah Sakit</label>
                    <select name="nama_rs" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua RS --</option>
                        @foreach($list_rs as $rs)
                            <option value="{{ $rs }}" {{ request('nama_rs') == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted">Nama Alat</label>
                    <select name="nama_alkes" class="form-select form-select-sm select-search" onchange="this.form.submit()">
                        <option value="">-- Semua Alat --</option>
                        @foreach($list_alkes as $alkes)
                            <option value="{{ $alkes }}" {{ request('nama_alkes') == $alkes ? 'selected' : '' }}>{{ $alkes }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach($list_kategori as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted">Kondisi Awal</label>
                    <select name="grade_kerusakan" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach($list_grade as $grade)
                            <option value="{{ $grade }}" {{ request('grade_kerusakan') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted">Status Akhir</label>
                    <select name="status_perbaikan" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach($list_status as $status)
                            <option value="{{ $status }}" {{ request('status_perbaikan') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted">Respon Penyedia</label>
                    <select name="respon_penyedia" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach($list_respon as $respon)
                            <option value="{{ $respon }}" {{ request('respon_penyedia') == $respon ? 'selected' : '' }}>{{ $respon }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    @if(request()->anyFilled(['search', 'nama_rs', 'nama_alkes', 'kategori', 'grade_kerusakan', 'status_perbaikan', 'respon_penyedia']))
                        <a href="{{ route('repairs.index') }}" class="btn btn-outline-danger btn-sm px-3 shadow-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm fw-bold">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-3" width="50">No</th>
                        <th>RS / Lokasi / Petugas</th>
                        <th>Identitas Alat & Penyedia</th>
                        <th width="300">Log Progress Terbaru</th>
                        <th class="text-center">Berkas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairs as $r)
                    <tr>
                        <td class="ps-3 text-muted">
                            {{ $loop->iteration + ($repairs->currentPage() - 1) * $repairs->perPage() }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-uppercase">{{ $r->nama_rs ?? '-' }}</div>
                            <div class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $r->lokasi ?? '-' }}</div>
                            <div class="small mt-1 text-primary fw-bold"><i class="bi bi-person-badge"></i> {{ $r->input_by ?? 'System' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $r->nama_alkes ?? '-' }}</div>
                            <div class="badge bg-light text-dark border">{{ $r->kategori ?? '-' }}</div>
                            <small class="text-muted d-block">SN: <strong>{{ $r->serial_number ?? '-' }}</strong></small>
                            <small class="fw-bold text-dark d-block">Kondisi Awal Alkes: {{ $r->grade_kerusakan }}</small>
                            <small class="text-muted d-block">Merk/Model: {{ $r->merek }} / {{ $r->tipe_model }}</small>
                            <small class="text-dark d-block mt-1"><i class="bi bi-truck"></i> Penyedia: <strong>{{ $r->nama_penyedia ?? '-' }}</strong></small>
                            <small class="text-dark d-block mt-1">Respon Penyedia : {{ $r->respon_penyedia }}</small>
                            <small class="text-dark d-block mt-1">Tindakan Penyedia : {{ $r->tindakan_penyedia }}</small>
                        </td>
                        <td>
                            <div class="p-2 bg-light rounded border shadow-sm" style="max-height: 120px; overflow-y: auto; font-size: 0.8rem;">
                                @foreach($r->histories as $h)
                                <div class="mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary" style="font-size: 0.6rem;">{{ $h->status_perbaikan }}</span>
                                        <small class="text-muted" style="font-size: 0.65rem;">{{ $h->created_at->format('d/m/y H:i') }}</small>
                                    </div>
                                    <div class="text-dark fw-medium">{{ $h->keterangan_perubahan }}</div>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center">
                            @if($r->file_bap)
                                <a href="{{ asset('storage/'.$r->file_bap) }}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm py-0">
                                    <i class="bi bi-file-pdf"></i> BAP
                                </a>
                            @else
                                <span class="text-muted small italic">- No File -</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-info btn-sm text-white fw-bold px-2" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $r->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <button class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $r->id }}">Update</button>
                                <button class="btn btn-danger btn-sm px-2" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $r->id }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalUpdate{{ $r->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0">
                                <div class="modal-header bg-primary py-2 text-white border-0">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit & Update Data: {{ $r->nama_alkes }}</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('repairs.updateStatus', $r->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-sm p-3 mb-3 bg-light">
                                                    <h6 class="fw-bold border-bottom pb-2 text-primary">Identitas Unit</h6>
                                                    
                                                    <div class="mb-2">
                                                        <label class="small fw-bold">Nama Rumah Sakit</label>
                                                        <input type="text" name="nama_rs" class="form-control form-control-sm" value="{{ $r->nama_rs }}" readonly>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="small fw-bold">Lokasi / Kota</label>
                                                        <input type="text" name="lokasi" class="form-control form-control-sm" value="{{ $r->lokasi }}" readonly>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="small fw-bold">Nama Alkes</label>
                                                        <input type="text" name="nama_alkes" class="form-control form-control-sm" value="{{ $r->nama_alkes }}">
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-6 mb-2">
                                                            <label class="small fw-bold">Merek</label>
                                                            <input type="text" name="merek" class="form-control form-control-sm" value="{{ $r->merek }}">
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <label class="small fw-bold">Tipe/Model</label>
                                                            <input type="text" name="tipe_model" class="form-control form-control-sm" value="{{ $r->tipe_model }}">
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="small fw-bold">Serial Number (SN)</label>
                                                        <input type="text" name="serial_number" class="form-control form-control-sm" value="{{ $r->serial_number }}">
                                                    </div>
                                                    
                                                    <label class="small fw-bold d-block mt-3 mb-2">Foto Kondisi Saat Ini:</label>
                                                    <div class="row g-2">
                                                        @if($r->foto_kondisi && is_array($r->foto_kondisi))
                                                            @foreach($r->foto_kondisi as $path)
                                                                <div class="col-4">
                                                                    <img src="{{ asset('storage/'.$path) }}" class="img-fluid rounded border shadow-sm">
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="col-12 text-muted small p-2 text-center bg-white rounded border border-dashed">No Image</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="card border-0 shadow-sm p-3">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small fw-bold">Petugas Update</label>
                                                            <input type="text" name="input_by" class="form-control form-control-sm" value="{{ Auth::user()->name }}" required>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small fw-bold">Kategori Alkes</label>
                                                            <select name="kategori" class="form-select form-select-sm">
                                                                <option value="Elektromedik" {{ $r->kategori == 'Elektromedik' ? 'selected' : '' }}>Elektromedik</option>
                                                                <option value="Radiologi" {{ $r->kategori == 'Radiologi' ? 'selected' : '' }}>Radiologi</option>
                                                                <option value="Laboratorium" {{ $r->kategori == 'Laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                                                <option value="Penunjang" {{ $r->kategori == 'Penunjang' ? 'selected' : '' }}>Penunjang</option>
                                                                <option value="IGD" {{ $r->kategori == 'IGD' ? 'selected' : '' }}>IGD</option>
                                                                <option value="OK" {{ $r->kategori == 'OK' ? 'selected' : '' }}>OK</option>
                                                                <option value="ICU" {{ $r->kategori == 'ICU' ? 'selected' : '' }}>ICU</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small fw-bold">Kondisi Kontrak</label>
                                                            <select name="kondisi_kontrak" class="form-select">
                                                                <option value="Garansi" {{ (isset($r) && $r->kondisi_kontrak == 'Garansi') ? 'selected' : '' }}>Garansi</option>
                                                                <option value="Garansi Habis" {{ (isset($r) && $r->kondisi_kontrak == 'Garansi Habis') ? 'selected' : '' }}>Garansi Habis</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label class="small fw-bold">Kondisi Awal (Grade)</label>
                                                            <select name="grade_kerusakan" class="form-select form-select-sm">
                                                                <option value="Bisa Dipakai" {{ $r->grade_kerusakan == 'Bisa Dipakai' ? 'selected' : '' }}>Bisa Dipakai</option>
                                                                <option value="Rusak Ringan" {{ $r->grade_kerusakan == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                                <option value="Rusak Berat" {{ $r->grade_kerusakan == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="small fw-bold text-primary">Update Status Akhir</label>
                                                            <select name="status_perbaikan" class="form-select form-select-sm border-primary" required>
                                                                <option value="Bisa Dipakai" {{ $r->status_perbaikan == 'Bisa Dipakai' ? 'selected' : '' }}>Bisa Dipakai</option>
                                                                <option value="Dalam Proses Perbaikan" {{ $r->status_perbaikan == 'Dalam Proses Perbaikan' ? 'selected' : '' }}>Dalam Proses Perbaikan</option>
                                                                <option value="Harus di Ganti (BAP)" {{ $r->status_perbaikan == 'Harus di Ganti (BAP)' ? 'selected' : '' }}>Harus di Ganti (BAP)</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label class="small fw-bold">Nama Penyedia / Vendor</label>
                                                            <input type="text" name="nama_penyedia" class="form-control form-control-sm" value="{{ $r->nama_penyedia }}">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="small fw-bold">Komponen / Sparepart</label>
                                                            <select name="komponen" class="form-select form-select-sm">
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Power Supply">Power Supply</option>
                                                                <option value="Mainboard">Mainboard</option>
                                                                <option value="Aksesoris">Aksesoris</option>
                                                                <option value="Lainnya">Lainnya</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label class="small fw-bold">Respon Penyedia</label>
                                                            <select name="respon_penyedia" class="form-select form-select-sm">
                                                                <option value="Datang" {{ $r->respon_penyedia == 'Datang' ? 'selected' : '' }}>Datang</option>
                                                                <option value="Belum Datang" {{ $r->respon_penyedia == 'Belum Datang' ? 'selected' : '' }}>Belum Datang</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="small fw-bold">Tindakan Penyedia</label>
                                                            <textarea name="tindakan_penyedia" class="form-control form-control-sm" rows="1">{{ $r->tindakan_penyedia }}</textarea>
                                                        </div>

                                                        <div class="col-md-12 mb-2">
                                                            <label class="small fw-bold">Rencana Tindak Lanjut (RTL)</label>
                                                            <textarea name="rtl" class="form-control form-control-sm" rows="2">{{ $r->rtl }}</textarea>
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <label class="small fw-bold">Keterangan Tambahan / Log</label>
                                                            <textarea name="keterangan_lain" class="form-control form-control-sm" rows="2">{{ $r->keterangan_lain }}</textarea>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label class="small fw-bold text-danger"><i class="bi bi-file-pdf"></i> Ganti File BAP (PDF)</label>
                                                            <input type="file" name="file_bap" class="form-control form-control-sm" accept=".pdf">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="small fw-bold text-success"><i class="bi bi-camera"></i> Tambah Foto Baru</label>
                                                            <input type="file" name="foto_kondisi[]" class="form-control form-control-sm" multiple accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">SIMPAN PERUBAHAN</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalHapus{{ $r->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white py-2 border-0">
                                    <h6 class="modal-title small">Konfirmasi Hapus</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <p class="mb-0 text-muted">Hapus laporan <strong>{{ $r->nama_alkes }}</strong>?</p>
                                </div>
                                <div class="modal-footer justify-content-center border-0 pb-3">
                                    <form action="{{ route('repairs.destroy', $r->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-4 shadow-sm">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalDetail{{ $r->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-primary text-white py-3 border-0">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i> Rincian Laporan: {{ $r->nama_alkes }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        
                                        <div class="col-md-7">
                                            <div class="card border-0 shadow-sm mb-4 card-info-alkes">
                                                <div class="card-body">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Informasi Lokasi & Penginput</h6>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted small">Rumah Sakit</div>
                                                        <div class="col-sm-8 fw-bold">{{ $r->nama_rs ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted small">Kabupaten/Kota</div>
                                                        <div class="col-sm-8 fw-bold">{{ $r->lokasi ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted small">Petugas Input</div>
                                                        <div class="col-sm-8 text-dark">{{ $r->input_by ?? '-' }}</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-4 text-muted small">Tanggal Lapor</div>
                                                        <div class="col-sm-8 text-dark">{{ $r->tanggal_input ? \Carbon\Carbon::parse($r->tanggal_input)->format('d F Y') : '-' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cpu-fill me-2"></i>Identitas Alat Kesehatan</h6>
                                                    <div class="row g-3">
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Nama Alat</label>
                                                            <span class="fw-bold text-primary">{{ $r->nama_alkes ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Serial Number (SN)</label>
                                                            <span class="fw-bold text-dark">{{ $r->serial_number ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Merek / Brand</label>
                                                            <span>{{ $r->merek ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Tipe / Model</label>
                                                            <span>{{ $r->tipe_model ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Kategori</label>
                                                            <span class="badge bg-light text-dark border">{{ $r->kategori ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Kondisi Awal Alkes</label>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">{{ $r->grade_kerusakan ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Nama Penyedia</label>
                                                            <span class="text-info fw-bold">{{ $r->nama_penyedia ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="text-muted small d-block">Kondisi Kontrak</label>
                                                            <span>{{ $r->kondisi_kontrak ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="card border-0 shadow-sm mb-4">
                                                <div class="card-body">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Status & Respon Terkini</h6>
                                                    <div class="mb-3">
                                                        <label class="text-muted small d-block">Status</label>
                                                        <span class="badge bg-primary fs-6">{{ $r->status_perbaikan ?? '-' }}</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="text-muted small d-block">Komponen Rusak</label>
                                                        <div class="p-2 bg-light rounded border small">{{ $r->komponen ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <label class="text-muted small d-block">Respon Penyedia</label>
                                                            <span class="fw-bold">{{ $r->respon_penyedia ?? '-' }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="text-muted small d-block">Tindakan</label>
                                                            <span class="fw-bold">{{ $r->tindakan_penyedia ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="text-muted small d-block text-danger fw-bold">Rencana Tindak Lanjut (RTL)</label>
                                                        <p class="text-dark small border-start border-danger border-3 ps-2">{{ $r->rtl ?? '-' }}</p>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="text-muted small d-block">Keterangan Lain</label>
                                                        <small class="text-muted italic">{{ $r->keterangan_lain ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-medical me-2"></i>Dokumentasi Berkas</h6>
                                                    <div class="mb-3">
                                                        @if($r->file_bap)
                                                            <a href="{{ asset('storage/'.$r->file_bap) }}" target="_blank" class="btn btn-sm btn-outline-danger w-100 shadow-sm">
                                                                <i class="bi bi-file-pdf-fill me-2"></i>Buka Dokumen BAP (PDF/Word)
                                                            </a>
                                                        @else
                                                            <div class="p-2 border rounded bg-light text-center small text-muted italic">BAP belum diupload</div>
                                                        @endif
                                                    </div>
                                                    <label class="text-muted small d-block mb-2">Foto Kondisi Alat:</label>
                                                    <div class="row g-2">
                                                        @if($r->foto_kondisi && is_array($r->foto_kondisi))
                                                            @foreach($r->foto_kondisi as $path)
                                                                <div class="col-4">
                                                                    <a href="{{ asset('storage/'.$path) }}" target="_blank">
                                                                        <img src="{{ asset('storage/'.$path) }}" class="img-fluid rounded border shadow-sm" style="height: 70px; width: 100%; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="col-12 p-3 bg-light rounded text-center small text-muted border-dashed">Tidak ada foto.</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="fw-bold text-dark mb-4"><i class="bi bi-clock-history me-2"></i>Timeline History Perbaikan</h6>
                                                    <div class="position-relative ps-4 border-start ms-2">
                                                        @forelse($r->histories as $h)
                                                            <div class="mb-4 position-relative">
                                                                <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -31px; top: 5px; border: 2px solid white;"></div>
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="badge bg-primary shadow-sm" style="font-size: 0.7rem;">{{ $h->status_perbaikan }}</span>
                                                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $h->created_at->format('d/m/Y H:i') }} WIB</small>
                                                                </div>
                                                                <div class="bg-light p-2 rounded border small text-dark">
                                                                    {{ $h->keterangan_perubahan }}
                                                                    <div class="mt-1 text-muted fw-bold" style="font-size: 0.65rem;">Diperbarui oleh: {{ $h->user_nama }}</div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-center py-3 text-muted small">Belum ada riwayat perubahan.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light border-0 py-3">
                                    <button type="button" class="btn btn-secondary px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted small italic">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="small text-muted">
        Menampilkan {{ $repairs->firstItem() ?? 0 }} - {{ $repairs->lastItem() ?? 0 }} dari {{ $repairs->total() }} data
    </div>
    <div>
        {{ $repairs->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3 border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i> Input Laporan Perbaikan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('repairs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-12"><h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">1. Informasi Lokasi</h6></div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Petugas Input</label>
                            <input type="text" name="input_by" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold text-muted">Tanggal Input (Otomatis)</label>
                            <input type="date" class="form-control bg-light" value="{{ date('Y-m-d') }}" disabled>
                            <input type="hidden" name="tanggal_input" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold text-primary">Nama Rumah Sakit</label>
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

                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold text-muted">Lokasi (Kab/Kota)</label>
                            <input type="text" name="lokasi" class="form-control bg-light js-lokasi" readonly required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12"><h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">2. Identitas Alat & Penyedia</h6></div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Nama Alat Kesehatan</label>
                            <input type="text" name="nama_alkes" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Serial Number (SN)</label>
                            <input type="text" name="serial_number" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Nama Penyedia (Vendor)</label>
                            <input type="text" name="nama_penyedia" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Elektromedik">Elektromedik</option>
                                <option value="Radiologi">Radiologi</option>
                                <option value="Laboratorium">Laboratorium</option>
                                <option value="Penunjang">Penunjang</option>
                                <option value="IGD">IGD</option>
                                <option value="OK">OK</option>
                                <option value="ICU">ICU</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Merek</label>
                            <input type="text" name="merek" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Tipe/Model</label>
                            <input type="text" name="tipe_model" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Kondisi Awal Alkes</label>
                            <select name="grade_kerusakan" class="form-select">
                                <option value="Bisa Dipakai">Bisa Dipakai</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Kondisi Kontrak</label>
                            <select name="kondisi_kontrak" class="form-select">
                                <option value="Garansi" {{ (isset($r) && $r->kondisi_kontrak == 'Garansi') ? 'selected' : '' }}>Garansi</option>
                                <option value="Garansi Habis" {{ (isset($r) && $r->kondisi_kontrak == 'Garansi Habis') ? 'selected' : '' }}>Garansi Habis</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12"><h6 class="text-primary border-bottom pb-2 mb-3 fw-bold">3. Status, Respon & Berkas</h6></div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Update Status Alat</label>
                            <select name="status_perbaikan" class="form-select" required>
                                {{-- <option value="Berfungsi">Berfungsi</option> --}}
                                <option value="Bisa Dipakai">Bisa Dipakai</option>
                                <option value="Dalam Proses Perbaikan">Dalam Proses Perbaikan</option>
                                <option value="Harus di Ganti">Harus di Ganti (BAP)</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Komponen Rusak</label>
                            <select name="komponen" class="form-select form-select-sm">
                                <option value="">-- Pilih --</option>
                                <option value="Power Supply">Power Supply</option>
                                <option value="Mainboard">Mainboard</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Respon Penyedia</label>
                            <select name="respon_penyedia" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Datang">Datang</option>
                                <option value="Belum Datang">Belum Datang</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Tindakan Penyedia</label>
                            <textarea name="tindakan_penyedia" class="form-control" rows="1" placeholder="Analisa awal..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3 file-upload-wrapper">
                            <label class="form-label small fw-bold text-danger"><i class="bi bi-file-pdf"></i> Upload BAP (PDF)</label>
                            <input type="file" name="file_bap" class="form-control foto-input" accept=".pdf,.doc,.docx">
                        </div>
                        <div class="col-md-6 mb-3 file-upload-wrapper">
                            <label class="form-label small fw-bold text-success"><i class="bi bi-camera"></i> Foto Kondisi (Banyak)</label>
                            <input type="file" name="foto_kondisi[]" class="form-control foto-input" multiple accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Keterangan Lain-lain</label>
                            <textarea name="keterangan_lain" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    document.addEventListener('change', function (e) {
        // Cek apakah yang berubah adalah dropdown Nama RS
        if (e.target && e.target.classList.contains('js-nama-rs')) {
            const select = e.target;
            
            // Cari pembungkus terdekat (row) untuk menemukan input lokasi pasangannya
            const wrapper = select.closest('.row'); 
            const lokasiInput = wrapper.querySelector('.js-lokasi');

            // Ambil atribut data-lokasi dari option yang dipilih
            // Kita pakai select.options[select.selectedIndex] untuk vanilla JS
            const selectedOption = select.options[select.selectedIndex];
            const lokasiValue = selectedOption.getAttribute('data-lokasi');

            if (lokasiValue) {
                lokasiInput.value = lokasiValue;
            } else {
                lokasiInput.value = '';
            }
        }
    });

    // PENTING: Jika menggunakan Select2, tambahkan ini agar Select2 memicu event 'change' 
    // yang bisa dibaca oleh document.addEventListener di atas.
    $(document).ready(function() {
        $('.js-nama-rs').select2({
            theme: 'bootstrap-5',
            width: '100%',
            // Pastikan dropdownParent diarahkan ke modal terkait jika di dalam modal
            dropdownParent: $('.js-nama-rs').closest('.modal').length ? $('.js-nama-rs').closest('.modal') : null
        }).on('select2:select', function (e) {
            // Paksa trigger event change asli agar document.addEventListener menangkapnya
            this.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    $(document).ready(function() {
        $('.select-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Alat --',
            allowClear: true
        });
    });
</script>
@endsection