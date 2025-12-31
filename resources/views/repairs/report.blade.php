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
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary">Rumah Sakit</label>
                    <input type="text" name="nama_rs" class="form-control form-control-sm filter-input" placeholder="Cari RS...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary">Nama Alat</label>
                    <input type="text" name="nama_alkes" class="form-control form-control-sm filter-input" placeholder="Cari Alat...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary">Grade</label>
                    <select name="grade_kerusakan" class="form-select form-select-sm filter-input">
                        <option value="">-- Semua Grade --</option>
                        <option value="Bisa Dipakai">Bisa Dipakai</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary">Status Akhir</label>
                    <select name="status_perbaikan" class="form-select form-select-sm filter-input">
                        <option value="">-- Semua Status --</option>
                        <option value="Selesai Diperbaiki">Selesai Diperbaiki</option>
                        <option value="Dalam Proses">Dalam Proses Perbaikan</option>
                        <option value="Harus Diganti">Harus Diganti (BAP)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary">Respon Penyedia</label>
                    <select name="respon_penyedia" class="form-select form-select-sm filter-input">
                        <option value="">-- Semua Respon --</option>
                        <option value="Datang">Datang</option>
                        <option value="Belum Datang">Belum Datang</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm py-1">
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
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-hover mb-0" style="min-width: 2000px; font-size: 0.8rem;">
                <thead class="bg-light text-center align-middle">
                    <tr>
                        <th width="50" class="bg-light sticky-left">No</th>
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
                        <th>Grade Kerusakan</th>
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