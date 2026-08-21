@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h4 class="text-primary fw-bold mb-0">
                <i class="bi bi-box-seam me-2"></i>Master Data Alkes
            </h4>
            <p class="text-muted small mb-0">Kelola daftar Alat Kesehatan (Alkes)</p>
        </div>
        
        <div class="d-grid d-sm-flex gap-2">
            <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i>Tambah Alkes
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form action="{{ route('alkes.index') }}" method="GET" class="d-flex align-items-center" style="max-width: 400px;">
                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari nama alkes..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary fw-bold"><i class="bi bi-search"></i> Cari</button>
                @if(request('search'))
                    <a href="{{ route('alkes.index') }}" class="btn btn-sm btn-outline-secondary ms-2 fw-bold"><i class="bi bi-x-circle"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-teal text-white align-middle">
                        <tr>
                            <th class="px-4 py-3 text-center" width="5%">No</th>
                            <th class="py-3">Nama Alkes</th>
                            <th class="px-4 py-3 text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alkes as $index => $item)
                        <tr>
                            <td class="px-4 fw-bold text-muted text-center">{{ $alkes->firstItem() + $index }}</td>
                            <td class="fw-bold">{{ $item->nama_alkes }}</td>
                            <td class="px-4 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('alkes.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger shadow-sm">
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
                                        <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Alkes</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('alkes.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="small fw-bold">Nama Alkes</label>
                                                <input type="text" name="nama_alkes" class="form-control" value="{{ $item->nama_alkes }}" required>
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
                            <td colspan="3" class="text-center py-4 text-muted">
                                <i class="bi bi-folder-x fs-2 d-block mb-2 text-secondary"></i>
                                Belum ada data Alkes.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $alkes->links() }}</div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary py-2 text-white border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Alkes</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('alkes.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="small fw-bold">Nama Alkes</label>
                        <input type="text" name="nama_alkes" class="form-control" required placeholder="Contoh: USG Doppler">
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
