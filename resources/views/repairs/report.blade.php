@extends('layouts.app')

@section('content')
<style>
    /* Styling agar tabel tetap rapi di layar lebar */
    .table-preview-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .filter-card {
        border-radius: 12px;
        border: none;
        background: #ffffff;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="text-primary fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reporting Perbaikan Alkes</h4>
        <p class="text-muted small mb-0">Atur filter dan pratinjau data sebelum export ke Excel.</p>
    </div>
    <a href="{{ route('repairs.index') }}" class="btn btn-light border shadow-sm fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card filter-card shadow-sm mb-4">
    <div class="card-body p-4">
        <form id="filterForm" action="{{ route('repairs.export') }}" method="GET" target="_blank">
            <input type="hidden" name="bencana_id" value="{{ session('active_bencana_id') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Rumah Sakit</label>
                    <select name="nama_rs" class="form-select filter-input select2-custom">
                        <option value="">-- Semua --</option>
                        @foreach($list_rs as $nama => $lokasi)
                            <option value="{{ $nama }}" {{ request('nama_rs') == $nama ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Nama Alat</label>
                    <select name="nama_alkes" class="form-select filter-input select2-custom">
                        <option value="">-- Semua Alat --</option>
                        @foreach($list_alkes as $alkes)
                            <option value="{{ $alkes }}" {{ request('nama_alkes') == $alkes ? 'selected' : '' }}>
                                {{ $alkes }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Kategori</label>
                    <select name="kategori" class="form-select filter-input select2-custom">
                        <option value="">-- Semua --</option>
                        @foreach($list_kategori as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Kondisi Awal Alkes</label>
                    <select name="grade_kerusakan" class="form-select filter-input select2-custom">
                        <option value="">-- Semua --</option>
                        @foreach($list_grade as $grade)
                            <option value="{{ $grade }}" {{ request('grade_kerusakan') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Status Akhir</label>
                    <select name="status_perbaikan" class="form-select filter-input select2-custom">
                        <option value="">-- Semua Status --</option>
                        @foreach($list_status as $status)
                            <option value="{{ $status }}" {{ request('status_perbaikan') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Respon Penyedia</label>
                    <select name="respon_penyedia" class="form-select filter-input select2-custom">
                        <option value="">-- Semua Respon --</option>
                        @foreach($list_respon as $respon)
                            <option value="{{ $respon }}" {{ request('respon_penyedia') == $respon ? 'selected' : '' }}>{{ $respon }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success fw-bold shadow-sm px-4">
                        <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm table-preview-container">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i>Pratinjau Hasil Export (Live)</h6>
        <span class="badge bg-light text-primary border" id="data-count">Total Data: {{ $repairs->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto; overflow-x: auto;">
            <table class="table table-hover mb-0" style="min-width: 2000px; font-size: 0.8rem;">
                <thead class="text-center align-middle" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th width="50" class="ps-3">No</th>
                        <th>Tanggal Input</th>
                        <th>Rumah Sakit</th>
                        <th>Lokasi</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Serial Number</th>
                        <th>Merek</th>
                        <th>Model/Tipe</th>
                        <th>Penyedia</th>
                        <th>Kontrak</th>
                        <th>Kondisi Awal Alkes</th>
                        <th>Respon Penyedia</th>
                        <th>Tindakan Penyedia</th>
                        <th>Status Akhir</th>
                        <th>Komponen Rusak</th>
                        <th>RTL</th>
                        <th>Keterangan Lain</th>
                    </tr>
                </thead>
                <tbody id="previewTableBody">
                    @include('repairs._report_rows', ['repairs' => $repairs])
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.filter-input');
        const tableBody = document.getElementById('previewTableBody');

        inputs.forEach(input => {
            // "input" event akan mendeteksi setiap ketikan/perubahan select
            input.addEventListener('input', debounceFetch);
        });

        // Inisialisasi Select2 untuk tampilan yang konsisten dengan halaman lain
        $('.select2-custom').select2({
            theme: 'bootstrap-5'
        }).on('change', function() {
            debounceFetch();
        });

        let timeout = null;
        function debounceFetch() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchData();
            }, 500); 
        }

        function fetchData() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData).toString();
            
            // Efek Loading
            tableBody.style.opacity = '0.5';

            fetch(`{{ route('repairs.reportPreview') }}?${params}`)
                .then(response => response.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    tableBody.style.opacity = '1';
                    
                    // Update counter data sederhana (opsional)
                    const rowCount = tableBody.querySelectorAll('tr[data-row]').length;
                    document.getElementById('data-count').innerText = 'Data Ditemukan: ' + rowCount;
                });
        }
    });
</script>
@endsection