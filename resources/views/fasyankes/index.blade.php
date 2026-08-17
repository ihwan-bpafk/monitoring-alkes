@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-primary fw-bold mb-0">
                <i class="bi bi-building me-2"></i>Master Data Fasyankes
            </h4>
            <p class="text-muted small mb-0">Kelola daftar Rumah Sakit dan Puskesmas</p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm fw-bold">
                <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
            </a>
            <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i>Tambah Fasyankes
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-secondary" width="5%">No</th>
                            <th class="py-3 text-secondary">Nama Fasyankes</th>
                            <th class="py-3 text-secondary">Jenis</th>
                            <th class="py-3 text-secondary">Lokasi / Kota</th>
                            <th class="px-4 py-3 text-secondary text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fasyankes as $index => $item)
                        <tr>
                            <td class="px-4 fw-bold text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $item->nama_fasyankes }}</td>
                            <td><span class="badge bg-info text-dark">{{ $item->jenis }}</span></td>
                            <td>{{ $item->lokasi ?? '-' }}</td>
                            <td class="px-4 text-center">
                                <div class="btn-group shadow-sm">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('fasyankes.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0">
                                    <div class="modal-header bg-primary py-2 text-white border-0">
                                        <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Fasyankes</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('fasyankes.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="small fw-bold">Nama Fasyankes</label>
                                                <input type="text" name="nama_fasyankes" class="form-control" value="{{ $item->nama_fasyankes }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold">Jenis</label>
                                                <select name="jenis" class="form-select" required>
                                                    <option value="RSUD" {{ $item->jenis == 'RSUD' ? 'selected' : '' }}>RSUD</option>
                                                    <option value="Dinkes" {{ $item->jenis == 'Dinkes' ? 'selected' : '' }}>Dinkes</option>
                                                    <option value="Puskesmas" {{ $item->jenis == 'Puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                                    <option value="Klinik" {{ $item->jenis == 'Klinik' ? 'selected' : '' }}>Klinik</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold">Lokasi / Kota</label>
                                                <input type="text" name="lokasi" class="form-control" value="{{ $item->lokasi }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top-0 py-2">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-folder-x fs-2 d-block mb-2 text-secondary"></i>
                                Belum ada data Fasyankes di wilayah ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary py-2 text-white border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Fasyankes</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('fasyankes.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Fasyankes</label>
                        <input type="text" name="nama_fasyankes" class="form-control" required placeholder="Contoh: RSUD Ruteng">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Jenis</label>
                        <select name="jenis" class="form-select" required>
                            <option value="RSUD">RSUD</option>
                            <option value="Dinkes">Dinkes</option>
                            <option value="Puskesmas">Puskesmas</option>
                            <option value="Klinik">Klinik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Lokasi / Kota</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Opsional">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 py-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
