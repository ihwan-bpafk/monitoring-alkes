@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h4 class="text-primary fw-bold mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Monitoring Alkes
                </h4>
            </div>
            <div class="d-flex w-100 w-md-auto justify-content-md-end">
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2 w-100">
                    <input type="hidden" name="bencana_id" value="{{ session('active_bencana_id') }}">
                    <select name="nama_rs" class="form-select shadow-sm border-primary select2-filter"
                        style="min-width: 200px;">
                        <option value="">-- Semua Rumah Sakit --</option>
                        @foreach ($list_rs as $rs)
                            <option value="{{ $rs }}" {{ $selected_rs == $rs ? 'selected' : '' }}>
                                {{ $rs }}</option>
                        @endforeach
                    </select>

                    <select name="kategori" class="form-select shadow-sm border-primary select2-filter"
                        style="min-width: 180px;">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($list_kategori as $kat)
                            <option value="{{ $kat }}" {{ $selected_kategori == $kat ? 'selected' : '' }}>
                                {{ $kat }}</option>
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
                                    @if ($selected_rs)
                                        — <span class="fw-bold">{{ $selected_rs }}</span>
                                    @else
                                        — <span class="opacity-75">(Semua Rumah Sakit)</span>
                                    @endif
                                    @if ($selected_kategori)
                                        — <span class="fw-bold">{{ $selected_kategori }}</span>
                                    @else
                                        — <span class="opacity-75">(Semua Kategori)</span>
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
                        <span class="badge bg-light text-dark border small fw-normal">Total alat yg rusak:
                            {{ $totalWithVendor }}</span>
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

    <div class="row mt-4 mb-5">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-0">
                    <i class="bi bi-stack me-2 text-primary"></i>Ringkasan Inventaris Alat
                    <small class="text-muted fw-normal">(Berdasarkan Filter)</small>
                </div>
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-white fw-bold py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div class="text-primary"><i class="bi bi-grid-3x3-gap me-2"></i>Rincian Kondisi Akhir per Jenis
                            Alat</div>
                        <a href="{{ route('dashboard.exportExcel', request()->all()) }}"
                            class="btn btn-sm btn-outline-success fw-bold px-3 shadow-sm rounded-pill">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th class="ps-4 text-start">Nama Alat Kesehatan</th>
                                        <th>Total Unit</th>
                                        <th>Bisa Dipakai</th>
                                        <th>Proses</th>
                                        <th>Harus Ganti (BAP)</th>
                                        <th>Alokasi</th>
                                        <th>Distribusi</th>
                                        <th class="bg-dark text-white">Kebutuhan</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse($alkesSummary as $item)
                                        <tr>
                                            <td class="ps-4 text-start fw-bold text-dark">{{ $item->nama_alkes }}</td>
                                            <td><span
                                                    class="badge bg-primary rounded-pill px-3 shadow-sm">{{ $item->jumlah }}</span>
                                            </td>
                                            <td
                                                class="{{ $item->bisa_dipakai > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                                                {{ $item->bisa_dipakai }}</td>
                                            <td
                                                class="{{ $item->proses > 0 ? 'text-warning text-dark fw-bold' : 'text-muted' }}">
                                                {{ $item->proses }}</td>
                                            <td class="{{ $item->ganti > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $item->ganti }}</td>
                                            <td
                                                class="{{ $item->total_alokasi > 0 ? 'text-primary fw-bold' : 'text-muted' }}">
                                                {{ $item->total_alokasi }}</td>
                                            <td
                                                class="{{ $item->total_distribusi > 0 ? 'text-info fw-bold' : 'text-muted' }}">
                                                {{ $item->total_distribusi }}</td>
                                            <td class="bg-light">
                                                @if ($item->kebutuhan > 0)
                                                    <span class="badge bg-danger shadow-sm">Butuh {{ $item->kebutuhan }}
                                                        Lagi</span>
                                                @else
                                                    <span class="text-success small fw-bold"><i class="bi bi-check-all"></i>
                                                        Terpenuhi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">Data tidak ditemukan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $alkesSummary->links() }}</div>
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
            if (text.includes('bisa dipakai') || text.includes('selesai') || (text.includes('datang') && !text.includes(
                    'belum'))) {
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

        // Plugin untuk Teks di Tengah Donut Chart
        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut' || !chart.config.options.plugins.centerText) return;
                const ctx = chart.ctx;
                const width = chart.width;
                const height = chart.height;
                const text = chart.config.options.plugins.centerText.text;

                ctx.restore();
                ctx.font = 'bold 2rem "Nunito", sans-serif';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#047d79';
                const textX = Math.round((width - ctx.measureText(text).width) / 2);
                const textY = height / 2;
                ctx.fillText(text, textX, textY);

                ctx.font = '0.9rem "Nunito", sans-serif';
                ctx.fillStyle = '#6c757d';
                const label = 'Alat';
                const labelX = Math.round((width - ctx.measureText(label).width) / 2);
                ctx.fillText(label, labelX, textY + 25);
                ctx.save();
            }
        };

        // Fungsi Render Chart
        function renderDoughnut(ctxId, labels, values, totalText) {
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
                        legend: {
                            display: false
                        },
                        centerText: {
                            text: totalText.toString()
                        }
                    },
                    cutout: '75%'
                },
                plugins: [centerTextPlugin]
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
        renderDoughnut('chartAwal', awalLabels, awalValues, totalDataGlobal);
        renderLegend('legendAwal', awalLabels, awalValues);

        // 2. Respon Penyedia
        const responLabels = {!! json_encode($responData->pluck('respon_penyedia')) !!};
        const responValues = {!! json_encode($responData->pluck('total')) !!};
        renderDoughnut('chartRespon', responLabels, responValues, totalWithVendor);
        renderLegend('legendRespon', responLabels, responValues, true); // true karena hitung dari total vendor

        // 3. Kondisi Akhir Alkes
        const akhirLabels = {!! json_encode($statusData->pluck('status_perbaikan')) !!};
        const akhirValues = {!! json_encode($statusData->pluck('total')) !!};
        renderDoughnut('chartAkhir', akhirLabels, akhirValues, totalDataGlobal);
        renderLegend('legendAkhir', akhirLabels, akhirValues);
    </script>

    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .list-group-item {
            background: transparent;
        }
    </style>
@endsection
