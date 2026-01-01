@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-primary fw-bold mb-0">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard Monitoring Alkes
            </h4>
            <p class="text-muted small mb-0">BPAFK Medan</p>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('repairs.index') }}" class="btn btn-primary shadow-sm fw-bold px-3">
                <i class="bi bi-tools me-2"></i>Data Perbaikan
            </a>

            <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
                <select name="nama_rs" class="form-select shadow-sm border-primary" onchange="this.form.submit()" style="min-width: 220px;">
                    <option value="">-- Semua Rumah Sakit --</option>
                    @foreach($list_rs as $rs)
                        <option value="{{ $rs }}" {{ $selected_rs == $rs ? 'selected' : '' }}>{{ $rs }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

   <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Laporan Monitoring</h6>
                        <h2 class="fw-bold mb-0">
                            {{ $totalData }} 
                            <small class="fs-6 fw-normal">
                                Unit Alat 
                                @if($selected_rs)
                                    — <span class="fw-bold">{{ $selected_rs }}</span>
                                @else
                                    — <span class="opacity-75">(Semua Rumah Sakit)</span>
                                @endif
                            </small>
                        </h2>
                    </div>
                    <i class="bi bi-clipboard-data fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 border-0">
                    <i class="bi bi-activity me-2 text-primary"></i>Kondisi Awal Alkes
                </div>
                <div class="card-body">
                    <canvas id="chartAwal" style="max-height: 250px;"></canvas>
                    <div id="legendAwal" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 border-0 d-flex justify-content-between">
                    <span><i class="bi bi-person-check me-2 text-primary"></i>Respon Penyedia</span>
                    <span class="badge bg-light text-dark border small fw-normal">Total alat yg ada Vendor: {{ $totalWithVendor }}</span>
                </div>
                <div class="card-body">
                    <canvas id="chartRespon" style="max-height: 250px;"></canvas>
                    <div id="legendRespon" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 border-0">
                    <i class="bi bi-check-circle me-2 text-primary"></i>Kondisi Akhir Alkes
                </div>
                <div class="card-body">
                    <canvas id="chartAkhir" style="max-height: 250px;"></canvas>
                    <div id="legendAkhir" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Konfigurasi Global
    const totalDataGlobal = {{ $totalData }};
    const totalWithVendor = {{ $totalWithVendor }};

    // Fungsi Penentu Warna Sesuai Permintaan
    function getColorByLabel(label) {
        const text = label.toLowerCase();
        
        // Hijau: Bisa dipakai, Selesai, Datang
        if (text.includes('bisa dipakai') || text.includes('selesai') || (text.includes('datang') && !text.includes('belum'))) {
            return '#198754'; 
        }
        // Oranye: Rusak ringan, Dalam proses
        if (text.includes('ringan') || text.includes('proses')) {
            return '#fd7e14'; 
        }
        // Merah: Rusak berat, Harus diganti (BAP)
        if (text.includes('berat') || text.includes('ganti') || text.includes('bap') || text.includes('belum')) {
            return '#dc3545'; 
        }
        // Hitam: Lainnya / Tidak diketahui / -
        return '#212529'; 
    }

    // Fungsi Render Chart
    function renderDoughnut(ctxId, labels, values) {
        const bgColors = labels.map(label => getColorByLabel(label));
        const ctx = document.getElementById(ctxId).getContext('2d');
        
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: bgColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '75%'
            }
        });
    }

    // Fungsi Render Legend Custom dengan Persentase
    function renderLegend(containerId, labels, values, isVendor = false) {
        const container = document.getElementById(containerId);
        const currentTotal = isVendor ? totalWithVendor : totalDataGlobal;
        
        let html = '<ul class="list-group list-group-flush">';
        labels.forEach((label, i) => {
            const color = getColorByLabel(label);
            const percentage = currentTotal > 0 ? ((values[i] / currentTotal) * 100).toFixed(1) : 0;
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-1 small">
                    <span>
                        <i class="bi bi-circle-fill me-2" style="color: ${color}; font-size: 0.7rem;"></i>
                        ${label}
                    </span>
                    <span class="fw-bold">${values[i]} <span class="text-muted fw-normal">(${percentage}%)</span></span>
                </li>`;
        });
        html += '</ul>';
        container.innerHTML = html;
    }

    // --- EKSEKUSI DATA ---

    // 1. Kondisi Awal Alkes
    const awalLabels = {!! json_encode($gradeData->pluck('grade_kerusakan')) !!};
    const awalValues = {!! json_encode($gradeData->pluck('total')) !!};
    renderDoughnut('chartAwal', awalLabels, awalValues);
    renderLegend('legendAwal', awalLabels, awalValues);

    // 2. Respon Penyedia
    const responLabels = {!! json_encode($responData->pluck('respon_penyedia')) !!};
    const responValues = {!! json_encode($responData->pluck('total')) !!};
    renderDoughnut('chartRespon', responLabels, responValues);
    renderLegend('legendRespon', responLabels, responValues, true); // true karena hitung dari total vendor

    // 3. Kondisi Akhir Alkes
    const akhirLabels = {!! json_encode($statusData->pluck('status_perbaikan')) !!};
    const akhirValues = {!! json_encode($statusData->pluck('total')) !!};
    renderDoughnut('chartAkhir', akhirLabels, akhirValues);
    renderLegend('legendAkhir', akhirLabels, akhirValues);
</script>

<style>
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); }
    .list-group-item { background: transparent; }
</style>
@endsection