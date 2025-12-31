@forelse($repairs as $index => $r)
<tr data-row>
    <td class="text-center bg-light">{{ $index + 1 }}</td>
    <td>{{ $r->tanggal_input ?? '-' }}</td>
    <td class="fw-bold">{{ $r->nama_rs ?? '-' }}</td>
    <td>{{ $r->lokasi ?? '-' }}</td>
    <td class="text-primary fw-bold">{{ $r->nama_alkes ?? '-' }}</td>
    <td>{{ $r->kategori ?? '-' }}</td>
    <td><code>{{ $r->serial_number ?? '-' }}</code></td>
    <td>{{ $r->merek ?? '-' }}</td>
    <td>{{ $r->tipe_model ?? '-' }}</td>
    <td>{{ $r->nama_penyedia ?? '-' }}</td>
    <td>{{ $r->kondisi_kontrak ?? '-' }}</td>
    <td>
        @if($r->grade_kerusakan == 'Rusak Berat')
            <span class="badge bg-danger text-white">{{ $r->grade_kerusakan }}</span>
        @else
            <span class="badge bg-warning text-dark">{{ $r->grade_kerusakan }}</span>
        @endif
    </td>
    <td>{{ $r->respon_penyedia ?? '-' }}</td>
    <td>{{ $r->tindakan_penyedia ?? '-' }}</td>
    <td><span class="badge bg-primary px-3">{{ $r->status_perbaikan ?? '-' }}</span></td>
    <td>{{ $r->komponen ?? '-' }}</td>
    <td class="text-danger fw-bold">{{ $r->rtl ?? '-' }}</td>
    <td class="text-muted">{{ Str::limit($r->keterangan_lain, 50) }}</td>
</tr>
@empty
<tr>
    <td colspan="18" class="text-center py-5">
        <div class="text-muted">
            <i class="bi bi-search fs-1 d-block mb-2"></i>
            Data tidak ditemukan dengan kriteria filter tersebut.
        </div>
    </td>
</tr>
@endforelse