@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-teal mb-0"><i class="bi bi-truck me-2"></i>Distribusi Alat Donasi</h4>
            <p class="text-muted small mb-0">Kelola pengiriman alat kesehatan dari BPAFK ke Rumah Sakit tujuan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
                <i class="bi bi-speedometer2 me-2 text-teal"></i>Dashboard
            </a>
            <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold px-3">
                <i class="bi bi-tools me-2"></i>Data Perbaikan
            </a>
            <a href="{{ route('donations.index') }}" class="btn btn-success shadow-sm fw-bold">
                <i class="bi bi-box-seam me-2"></i>Stok Donasi
            </a>
            <a href="{{ route('distributions.export', request()->all()) }}" class="btn btn-success shadow-sm fw-bold">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
            <button class="btn btn-primary shadow-sm fw-bold px-3 bg-teal border-teal" data-bs-toggle="modal" data-bs-target="#modalTambahDistribusi">
                <i class="bi bi-plus-lg me-1"></i>Input Distribusi
            </button>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('distributions.index') }}" method="GET" class="row g-2">
                <div class="col-md-2">
                    <label class="small fw-bold">RS Tujuan</label>
                    <select name="filter_rs" class="form-select select2-filter" onchange="this.form.submit()">
                        <option value="">-- RS --</option>
                        @foreach($list_rs_master as $rs)
                            <option value="{{ $rs }}" {{ request('filter_rs') == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold">Nama Alat</label>
                    <select name="filter_alkes" class="form-select select2-filter" onchange="this.form.submit()">
                        <option value="">-- Semua Alat --</option>
                        @foreach($list_alkes_dist as $name)
                            <option value="{{ $name }}" {{ request('filter_alkes') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold">Pemberi Donasi</label>
                    <select name="filter_pemberi" class="form-select select2-filter" onchange="this.form.submit()">
                        <option value="">-- Semua Pemberi --</option>
                        @foreach($list_pemberi as $pemberi)
                            <option value="{{ $pemberi }}" {{ request('filter_pemberi') == $pemberi ? 'selected' : '' }}>{{ $pemberi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold">Status</label>
                    <select name="filter_status" class="form-select select2-filter" onchange="this.form.submit()">
                        <option value="">-- Status --</option>
                        @foreach($list_status as $st)
                            <option value="{{ $st }}" {{ request('filter_status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-white text-center bg-teal">
                        <tr>
                            <th width="50">No</th>
                            <th class="text-start">Nama Alat</th>
                            <th class="text-start">Rumah Sakit Tujuan</th>
                            <th>Jumlah</th>
                            <th>Tanggal Kirim</th>
                            <th>Status</th>
                            <th>Pengirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($distributions as $dist)
                        <tr>
                            <td>{{ ($distributions->currentPage() - 1) * $distributions->perPage() + $loop->iteration }}</td>
                            <td class="text-start">
                                <div class="fw-bold text-teal">{{ $dist->donation->nama_alkes }}</div>
                                <small class="text-muted">Pemberi: {{ $dist->donation->pemberi_donasi }}</small>
                            </td>
                            <td class="text-start fw-bold text-dark">{{ $dist->nama_rs }}</td>
                            <td><span class="badge bg-info text-dark">{{ $dist->jumlah_distribusi }} Unit</span></td>
                            <td>{{ \Carbon\Carbon::parse($dist->tanggal_distribusi)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $dist->status == 'Diterima RS' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $dist->status }}
                                </span>
                            </td>
                            <td><small>{{ $dist->petugas_pengirim }}</small></td>
                            <td>
                                @if(auth()->user()->role === 1 || auth()->user()->role === 2)
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-sm btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $dist->id }}" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $dist->id }}" title="Batalkan Distribusi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </td>
                        </tr>
                        <div class="modal fade" id="modalEdit{{ $dist->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Distribusi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('distributions.update', $dist->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PATCH')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="small fw-bold">Alat Kesehatan</label>
                                                <input type="text" class="form-control bg-light" value="{{ $dist->donation->nama_alkes }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold">RS Tujuan</label>
                                                <select name="nama_rs" class="form-select select2-edit" required>
                                                    @foreach($list_rs_master as $rs)
                                                        <option value="{{ $rs }}" {{ $dist->nama_rs == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                                                    @endforeach
                                                    <option value="Dinkes Tamiang" {{ $dist->nama_rs == "Dinkes Tamiang" ? 'selected' : '' }}>Dinkes Tamiang</option>
                                                    <option value="RSUD Cut Meutia" {{ $dist->nama_rs == "RSUD Cut Meutia" ? 'selected' : '' }}>RSUD Cut Meutia</option>
                                                    <option value="Dinkes Kab Bireuen" {{ $dist->nama_rs == "Dinkes Kab Bireuen" ? 'selected' : '' }}>Dinkes Kab Bireuen</option>
                                                    <option value="Dinkes Prov. Aceh" {{ $dist->nama_rs == "Dinkes Prov. Aceh" ? 'selected' : '' }}>Dinkes Prov. Aceh</option>
                                                    <option value="RSUD H. Sahudin Kutacane" {{ $dist->nama_rs == "RSUD H. Sahudin Kutacane" ? 'selected' : '' }}>RSUD H. Sahudin Kutacane</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="small fw-bold">Jumlah Distribusi</label>
                                                    <input type="number" name="jumlah_distribusi" class="form-control" value="{{ $dist->jumlah_distribusi }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="small fw-bold">Tanggal</label>
                                                    <input type="date" name="tanggal_distribusi" class="form-control" value="{{ $dist->tanggal_distribusi }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold">Update Status Pengiriman</label>
                                                <select name="status" class="form-select border-warning" required>
                                                    <option value="Dikirim" {{ $dist->status == 'Dikirim' ? 'selected' : '' }}>Dikirim (Proses Perjalanan)</option>
                                                    <option value="Diterima RS" {{ $dist->status == 'Diterima RS' ? 'selected' : '' }}>Diterima RS (Sudah Sampai)</option>
                                                    <option value="Alokasi" {{ $dist->status == 'Alokasi' ? 'selected' : '' }}>Alokasi</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold">Ganti Berita Acara (Opsional)</label>
                                                <input type="file" name="file_ba" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0">
                                            <button type="submit" class="btn btn-primary w-100 fwwhitebold">UPDATE DATA</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="modalHapus{{ $dist->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                                    <div class="modal-header bg-danger text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Pembatalan</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <div class="modal-body p-4 text-center">
                                        <i class="bi bi-x-circle text-danger mb-3" style="font-size: 3rem;"></i>
                                        <h5 class="fw-bold text-dark">Apakah Anda yakin?</h5>
                                        <p class="text-muted">
                                            Data pengiriman <strong>{{ $dist->jumlah_distribusi }} unit {{ $dist->donation->nama_alkes }}</strong> ke <strong>{{ $dist->nama_rs }}</strong> akan dihapus.
                                        </p>
                                        <div class="alert alert-warning border-0 small">
                                            <i class="bi bi-info-circle me-1"></i> <strong>SINKRONISASI STOK:</strong> 
                                            Sistem akan mengembalikan {{ $dist->jumlah_distribusi }} unit ke stok gudang Donasi secara otomatis.
                                        </div>
                                    </div>

                                    <div class="modal-footer bg-light border-0" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                        
                                        <form action="{{ route('distributions.destroy', $dist->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">
                                                YA, HAPUS & KEMBALIKAN STOK
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $distributions->links() }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahDistribusi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-send-plus me-2"></i>Kirim Alat ke RS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('distributions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-dark">Pilih Alat (Stok Tersedia)</label>
                        <select name="donation_id" class="form-select select2-tambah" required>
                            <option value="">-- Cari Nama Alat --</option>
                            @foreach($availableDonations as $avail)
                                <option value="{{ $avail->id }}">
                                    {{ $avail->nama_alkes }} (Sisa: {{ $avail->sisa_stok }} | Pemberi: {{ $avail->pemberi_donasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-primary">Rumah Sakit Tujuan</label>
                        <select name="nama_rs" class="form-select select2-tambah" required>
                            <option value="">-- Cari Nama RS --</option>
                            @foreach($list_rs_master as $rs)
                                <option value="{{ $rs }}">{{ $rs }}</option>
                            @endforeach
                            <option value="Dinkes Tamiang">Dinkes Tamiang</option>
                            <option value="RSUD Cut Meutia">RSUD Cut Meutia</option>
                            <option value="Dinkes Kab Bireuen">Dinkes Kab Bireuen</option>
                            <option value="Dinkes Prov. Aceh">Dinkes Prov. Aceh</option>
                            <option value="RSUD H. Sahudin Kutacane">RSUD H. Sahudin Kutacane</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-danger">Jumlah Dikirim</label>
                            <input type="number" name="jumlah_distribusi" class="form-control border-danger" min="1" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Tanggal Distribusi</label>
                            <input type="date" name="tanggal_distribusi" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Status Pengiriman</label>
                        <select name="status" class="form-select border-primary" required>
                            <option value="Dikirim" selected>Dikirim (Proses Perjalanan)</option>
                            <option value="Diterima RS">Diterima RS (Sudah Sampai)</option>
                            <option value="Alokasi">Alokasi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">File Berita Acara (PDF/JPG/PNG)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-file-earmark-arrow-up"></i></span>
                            <input type="file" name="file_ba" class="form-control">
                        </div>
                        <div class="form-text small text-muted">Maksimal 2MB.</div>
                    </div>

                    <div class="mb-0">
                        <label class="small fw-bold">Keterangan / No. Surat</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Pengiriman tahap 1..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white px-4 fw-bold shadow-sm bg-primary">
                        <i class="bi bi-check-circle me-1"></i>PROSES DISTRIBUSI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2-dist').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalTambahDistribusi'),
            width: '100%',
            placeholder: "-- Ketik Nama Alat --"
        });
    });
    $(document).ready(function() {
        $('.select2-filter').select2({ theme: 'bootstrap-5' });
        $('.select2-tambah').select2({ 
            theme: 'bootstrap-5', 
            dropdownParent: $('#modalTambahDistribusi') 
        });
    });
    </script>
@endsection