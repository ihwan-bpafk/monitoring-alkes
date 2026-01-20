@extends('layouts.app')

@section('content')
<style>
    /* Custom Style untuk konsistensi UI BPAFK */
    .modal-body { background-color: #f8f9fa; }
    .card-custom { border-radius: 12px; border: none; }
    .table thead th { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .badge-stok { font-size: 0.85rem; padding: 0.5em 0.8em; }
    /* Warna Teal Khas */
    .bg-teal { background-color: #047d79 !important; }
    .text-teal { color: #047d79 !important; }
    .border-teal { border-color: #047d79 !important; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-teal mb-0"><i class="bi bi-gift-fill me-2"></i>Monitoring Donasi & Stok Alat</h4>
            <p class="text-muted small mb-0">Manajemen inventaris stok masuk sebelum dialokasikan ke Rumah Sakit.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
                <i class="bi bi-speedometer2 me-2 text-teal"></i>Dashboard
            </a>

            <a href="{{ route('distributions.index') }}" class="btn btn-info shadow-sm fw-bold text-white">
                <i class="bi bi-truck me-2"></i>Menu Distribusi
            </a>

            <a href="{{ route('repairs.index') }}" class="btn btn-warning shadow-sm fw-bold">
                <i class="bi bi-tools me-2"></i>Data Perbaikan
            </a>
            <a href="{{ route('donations.export', request()->all()) }}" class="btn btn-success shadow-sm fw-bold">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
            {{-- Hanya Admin (1) dan Petugas (2) yang bisa menambah data --}}
            @if(auth()->user()->role === 1 || auth()->user()->role === 2)
            <button class="btn btn-primary shadow-sm fw-bold px-3 bg-teal border-teal" data-bs-toggle="modal" data-bs-target="#modalTambahDonasi">
                <i class="bi bi-plus-lg me-1"></i>Tambah Donasi
            </button>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('donations.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Pemberi Donasi</label>
                    <select name="filter_pemberi" class="form-select form-select-sm select2-filter" onchange="this.form.submit()">
                        <option value="">-- Cari Pemberi --</option>
                        @foreach($list_pemberi as $p)
                            <option value="{{ $p }}" {{ request('filter_pemberi') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Nama Alat</label>
                    <select name="filter_alkes" class="form-select form-select-sm select2-filter" onchange="this.form.submit()">
                        <option value="">-- Cari Nama Alat --</option>
                        @foreach($list_alkes_donasi as $alk)
                            <option value="{{ $alk }}" {{ request('filter_alkes') == $alk ? 'selected' : '' }}>{{ $alk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Penerima</label>
                    <select name="filter_petugas" class="form-select form-select-sm select2-filter" onchange="this.form.submit()">
                        <option value="">-- Cari Penerima --</option>
                        @foreach($list_penerima as $p)
                            <option value="{{ $p }}" {{ request('filter_petugas') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold text-muted">Status Stok</label>
                    <select name="filter_stok" class="form-select select2-filter" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="tersedia" {{ request('filter_stok') == 'tersedia' ? 'selected' : '' }}>Tersedia (Sisa > 0)</option>
                        <option value="habis" {{ request('filter_stok') == 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
                    </select>
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
                    <thead class="text-white text-center bg-teal">
                        <tr>
                            <th width="50">No</th>
                            <th class="text-start">Pemberi Donasi</th>
                            <th class="text-start">Nama Alat / Merk</th>
                            <th>Stok Sisa</th>
                            <th>Status Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($donations as $d)
                        <tr>
                            <td>{{ ($donations->currentPage() - 1) * $donations->perPage() + $loop->iteration }}</td>
                            <td class="text-start fw-bold text-dark">{{ $d->pemberi_donasi }}</td>
                            <td class="text-start">
                                <div class="text-teal fw-bold">{{ $d->nama_alkes }}</div>
                                <small class="text-muted">{{ $d->merek ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $d->sisa_stok > 0 ? 'bg-success' : 'bg-danger' }} badge-stok">
                                    {{ $d->sisa_stok }} / {{ $d->jumlah_donasi }} Unit
                                </span>
                            </td>
                            <td>
                                @if($d->distributions->isNotEmpty())
                                    {{-- Jika sudah didistribusikan --}}
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-info text-white mb-2" style="font-size: 0.75rem;">
                                            <i class="bi bi-truck me-1"></i> Terdistribusi
                                        </span>
                                        
                                        <div class="text-start w-100 px-2">
                                            @php
                                                // Kelompokkan distribusi berdasarkan Nama RS dan jumlahkan unitnya
                                                $summaryDistribusi = $d->distributions->groupBy('nama_rs')->map(function ($row) {
                                                    return $row->sum('jumlah_distribusi');
                                                });
                                            @endphp

                                            @foreach($summaryDistribusi as $rs => $total)
                                                <div class="d-flex justify-content-between border-bottom mb-1 pb-1" style="font-size: 0.7rem;">
                                                    <span class="text-muted fw-bold">{{ $rs }}</span>
                                                    <span class="badge bg-light text-dark border ms-2">{{ $total }} Unit</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- Jika masih di posisi awal (Gudang/BPAFK) --}}
                                    <span class="badge border text-teal border-teal">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $d->status_akhir }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    @if(auth()->user()->role === 1 || auth()->user()->role === 2)
                                    <button class="btn btn-sm btn-outline-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLog{{ $d->id }}" title="Lihat Riwayat">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    
                                    <button class="btn btn-sm text-white shadow-sm bg-teal" data-bs-toggle="modal" data-bs-target="#modalUpdate{{ $d->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHapusDonasi{{ $d->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalLog{{ $d->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                                    <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-journal-text me-2"></i>Riwayat Pergerakan Alat</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <h6 class="fw-bold text-teal text-uppercase">{{ $d->nama_alkes }} - {{ $d->merek }}</h6>
                                            <p class="small text-muted">Sumber: {{ $d->pemberi_donasi }}</p>
                                        </div>

                                        <div class="timeline-container">
                                            @forelse($d->logs as $log)
                                            <div class="d-flex mb-4">
                                                <div class="me-3 text-center" style="width: 50px;">
                                                    <div class="bg-teal rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow" style="width: 35px; height: 35px;">
                                                        <i class="bi bi-check2"></i>
                                                    </div>
                                                    <div class="vr h-100 mt-2" style="width: 2px; background-color: #dee2e6;"></div>
                                                </div>
                                                <div class="card card-body border-0 shadow-sm p-3 bg-white w-100">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge bg-teal">{{ $log->status }}</span>
                                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $log->created_at->format('d M Y, H:i') }}</small>
                                                    </div>
                                                    <p class="mb-1 small text-dark">{{ $log->catatan ?? 'Tidak ada catatan tambahan.' }}</p>
                                                    <div class="d-flex align-items-center mt-2 border-top pt-2">
                                                        <i class="bi bi-person-circle text-muted me-2"></i>
                                                        <small class="text-muted">Petugas: <strong>{{ $log->diupdate_oleh }}</strong></small>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="text-center py-4">
                                                <i class="bi bi-info-circle fs-2 text-muted"></i>
                                                <p class="text-muted mt-2">Belum ada riwayat update status.</p>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0">
                                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalUpdate{{ $d->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                                    <div class="modal-header text-white bg-teal" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-arrow-repeat me-2"></i>Update Status Akhir
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="{{ route('donations.updateStatus', $d->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body p-4">
                                            <div class="mb-4 p-3 bg-light rounded border-start border-4 border-teal">
                                                <label class="small text-muted d-block">Nama Alat Kesehatan:</label>
                                                <span class="fw-bold text-dark">{{ $d->nama_alkes }}</span>
                                                <hr class="my-2">
                                                <label class="small text-muted d-block">Status Saat Ini:</label>
                                                <span class="badge bg-teal">{{ $d->status_akhir }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Jumlah Total Donasi (Unit)</label>
                                                <input type="number" name="jumlah_donasi" class="form-control" value="{{ $d->jumlah_donasi }}" min="1" required>
                                                <div class="form-text mt-1 small text-muted">
                                                    <i class="bi bi-info-circle me-1"></i> Saat ini terpakai: <strong>{{ $d->jumlah_donasi - $d->sisa_stok }} Unit</strong>.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Status/Posisi Baru</label>
                                                <select name="status_akhir" class="form-select select2-filter">
                                                    @php
                                                        $options = ['BPAFK Medan', 'BPAFK Jakarta', 'IFP', 'PUSKRIS', 'DINKES aceh', 'Dinkes sumut', 'RS lainnya', 'Vendor'];
                                                    @endphp
                                                    
                                                    <option value="-">-- Reset Status --</option>
                                                    @foreach($options as $opt)
                                                        <option value="{{ $opt }}" {{ $d->status_akhir == $opt ? 'selected' : '' }}>
                                                            {{ $opt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Catatan Tambahan</label>
                                                <textarea name="catatan" class="form-control" rows="3" placeholder="Alasan perubahan status..."></textarea>
                                            </div>

                                            <div class="d-flex align-items-center p-2 rounded shadow-sm bg-white border">
                                                <i class="bi bi-person-check-fill fs-4 me-3 text-teal"></i>
                                                <div>
                                                    <div class="small text-muted" style="font-size: 0.7rem;">Petugas Update:</div>
                                                    <div class="fw-bold text-teal">{{ Auth::user()->name }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer bg-light border-0" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                            <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn text-white px-4 fw-bold shadow-sm bg-teal">SIMPAN PERUBAHAN</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalHapusDonasi{{ $d->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                                    <div class="modal-header bg-danger text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Data Master</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 text-center">
                                        <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                                        <h5 class="fw-bold text-dark">Hapus Donasi Ini?</h5>
                                        <p class="text-muted small">
                                            Menghapus <strong>{{ $d->nama_alkes }}</strong> ({{ $d->pemberi_donasi }}) akan menghapus <strong>SELURUH</strong> data distribusi dan log riwayat terkait secara permanen.
                                        </p>
                                        <div class="alert alert-warning border-0 small">
                                            <i class="bi bi-info-circle me-1"></i> Data yang sudah dihapus tidak dapat dikembalikan.
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                        <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('donations.destroy', $d->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">YA, HAPUS SEMUA</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
            <div class="modal-header bg-teal text-white">
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
                        <label class="form-label small fw-bold text-teal">Pilih Nama Alat Kesehatan</label>
                        <select name="nama_alkes" class="form-select select2-tambah" required>
                            <option value="">-- Ketik untuk mencari alat --</option>
                            @foreach($list_alkes_master as $alkes)
                                <option value="{{ $alkes }}">{{ $alkes }}</option>
                            @endforeach
                            <option value="Antropometri Set">Antropometri Set</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Merk</label>
                            <input type="text" name="merek" class="form-control" placeholder="Contoh: Philips / GE">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-danger">Jumlah Donasi</label>
                            <input type="number" name="jumlah_donasi" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Status / Lokasi Alat</label>
                        <select name="status_akhir" class="form-select select2-tambah" required>
                            <option value="BPAFK Medan" selected>BPAFK Medan</option>
                            <option value="BPAFK Jakarta">BPAFK Jakarta</option>
                            <option value="IFP">IFP</option>
                            <option value="PUSKRIS">PUSKRIS</option>
                            <option value="DINKES aceh">DINKES aceh</option>
                            <option value="Dinkes sumut">Dinkes sumut</option>
                            <option value="RS lainnya">RS lainnya</option>
                            <option value="Vendor">Vendor</option>
                        </select>
                        <div class="form-text small">Tentukan lokasi awal penyimpanan alat setelah diterima.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Diterima Oleh (Petugas)</label>
                        <input type="text" name="diterima_oleh" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 pb-4">
                    <button type="submit" class="btn text-white w-100 fw-bold shadow-sm bg-teal">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // 1. Inisialisasi Select2 untuk SEMUA Filter di atas tabel
    $('.select2-filter').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true
    });

    // 2. Inisialisasi Select2 khusus untuk Modal Tambah Donasi
    $('.select2-tambah').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalTambahDonasi'), // Wajib agar bisa diketik di dalam modal
        width: '100%',
        placeholder: "-- Pilih Alat dari Master Repair --",
        allowClear: true
    });

    // 3. Inisialisasi Select2 untuk Modal Update Status (Jika ada dropdown di sana)
    // Gunakan class khusus jika Ahmad ingin status akhir juga berupa pilihan dropdown
});
</script>
@endsection