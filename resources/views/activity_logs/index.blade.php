@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="mb-0 fw-bold text-primary">
            <i class="bi bi-journal-text me-2"></i>Log Aktivitas Pengguna
        </h2>
        <p class="text-muted mt-1 mb-0">Riwayat seluruh aktivitas (Login, Logout, Tambah, Ubah, Hapus)</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <form action="{{ route('activity_logs.index') }}" method="GET" class="d-flex justify-content-md-end w-100">
            <div class="input-group w-100 w-md-auto" style="max-width: 100%;">
                <input type="text" name="search" class="form-control" placeholder="Cari aktivitas, user, modul..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            </div>
            @if(request('search'))
                <a href="{{ route('activity_logs.index') }}" class="btn btn-outline-secondary ms-2" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="bg-primary text-white sticky-top shadow-sm" style="z-index: 2;">
                    <tr>
                        <th class="text-center bg-primary text-white" width="5%">No</th>
                        <th class="bg-primary text-white" width="15%">Waktu</th>
                        <th class="bg-primary text-white" width="15%">Pengguna</th>
                        <th class="bg-primary text-white" width="10%">Aksi</th>
                        <th class="bg-primary text-white" width="10%">Modul</th>
                        <th class="bg-primary text-white" width="25%">Keterangan</th>
                        <th class="bg-primary text-white" width="10%">IP & Lokasi</th>
                        <th class="text-center bg-primary text-white" width="10%">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-center">{{ $logs->firstItem() + $loop->index }}</td>
                            <td>
                                <div>{{ $log->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $log->user->name ?? 'Sistem / Guest' }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeColor = match(strtolower($log->action)) {
                                        'login' => 'info',
                                        'logout' => 'secondary',
                                        'create' => 'success',
                                        'update' => 'warning text-dark',
                                        'delete' => 'danger',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">{{ strtoupper($log->action) }}</span>
                            </td>
                            <td>{{ $log->model_type ?? '-' }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                <div><small class="fw-bold">{{ $log->ip_address }}</small></div>
                                <div><small class="text-muted">{{ $log->location ?? 'Unknown' }}</small></div>
                            </td>
                            <td class="text-center">
                                @if($log->before || $log->after)
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalDetail-{{ $log->id }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $logs->links('pagination::bootstrap-5') }}
</div>

<!-- Modals -->
@foreach ($logs as $log)
    @if($log->before || $log->after)
        <div class="modal fade" id="modalDetail-{{ $log->id }}" tabindex="-1" aria-labelledby="modalDetailLabel-{{ $log->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalDetailLabel-{{ $log->id }}">
                            <i class="bi bi-search me-2"></i>Detail Perubahan ({{ strtoupper($log->action) }})
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Modul:</strong> {{ $log->model_type ?? '-' }} (ID: {{ $log->model_id ?? '-' }})
                            </div>
                            <div class="col-md-6 text-md-end">
                                <strong>Waktu:</strong> {{ $log->created_at->format('d M Y H:i:s') }}
                            </div>
                        </div>
                        
                        <div class="row">
                            @if($log->before)
                                @php 
                                    $beforeData = is_string($log->before) ? json_decode($log->before, true) : $log->before; 
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-danger border-bottom pb-2">Data Sebelumnya (Before)</h6>
                                    <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                                        @if(is_iterable($beforeData))
                                            <table class="table table-sm table-hover mb-0">
                                                <tbody>
                                                    @foreach($beforeData as $key => $value)
                                                        <tr>
                                                            <td width="40%" class="fw-semibold bg-light text-capitalize text-muted">{{ str_replace('_', ' ', $key) }}</td>
                                                            <td class="text-danger">{{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="p-2 text-danger">{{ $beforeData }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            @if($log->after)
                                @php 
                                    $afterData = is_string($log->after) ? json_decode($log->after, true) : $log->after; 
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-success border-bottom pb-2">Data Baru (After)</h6>
                                    <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                                        @if(is_iterable($afterData))
                                            <table class="table table-sm table-hover mb-0">
                                                <tbody>
                                                    @foreach($afterData as $key => $value)
                                                        <tr>
                                                            <td width="40%" class="fw-semibold bg-light text-capitalize text-muted">{{ str_replace('_', ' ', $key) }}</td>
                                                            <td class="text-success">{{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="p-2 text-success">{{ $afterData }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="fw-bold border-bottom pb-2">Informasi Perangkat</h6>
                            <small class="text-muted">{{ $log->user_agent }}</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
