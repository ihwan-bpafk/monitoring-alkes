namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlkesSummaryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    // Header Tabel Excel
    public function headings(): array
    {
        return [
            'Nama Alat Kesehatan',
            'Total Unit (Aset)',
            'Bisa Dipakai',
            'Dalam Proses',
            'Harus Ganti (BAP)',
            'Alokasi (Rencana)',
            'Distribusi (Terkirim)',
            'Kebutuhan'
        ];
    }

    // Pemetaan Data per Baris
    public function map($item): array
    {
        return [
            $item->nama_alkes,
            $item->jumlah,
            $item->bisa_dipakai,
            $item->proses,
            $item->ganti,
            $item->alokasi,
            $item->distribusi,
            $item->kebutuhan > 0 ? 'Butuh ' . $item->kebutuhan . ' Unit' : 'Terpenuhi'
        ];
    }
}