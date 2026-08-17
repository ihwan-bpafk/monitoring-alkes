<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alkes;

class AlkesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alkesList = [
            'Doppler', 'Ultrasound Doppler', 'USG', 'Film viewer', 'Mesin Anaesthesi', 'X-Ray Panoramic', 'X-Ray General',
            'X-Ray Mammography', 'C-Arm', 'X-Ray Mobile', 'MRI', 'X-Ray CT Scan', 'Timbangan Bayi', 'UPS', 'Echocardiography',
            'Electrocardiograph (ECG)', 'ESWL', 'Operating Microscope', 'Operating Table', 'Arthroscopy', 'Defibrillator',
            'Auto Chemistry Analyzer', 'Centrifuge', 'Hematology Analyzer', 'Urine Analyzer', 'Micro Organism Analyzer',
            'Incubator Laboratory', 'Immunology Analyzer', 'Blood Bank', 'Plasma Extractor', 'Electromyography (EMG)',
            'Short Wave Diathermy', 'Micro Wave Diathermy', 'Treadmill', 'Suction Pump', 'Timbangan Mekanik', 'Patient Monitor',
            'Ventilator', 'Examination Lamp', 'Infusion Pump', 'Syringe Pump', 'Inkubator Bayi Transport', 'Auto Refractometer',
            'Slit Lamp', 'ENT Unit', 'Dental Unit', 'BABY INCUBATOR TRANSPORT', 'Inkubator Bayi', 'CPAP', 'Computed Radiography',
            'Electrosurgical Unit (ESU)', 'Fetal Doppler', 'Infra Red', 'Tensimeter Digital', 'Infant Warmer', 'Genset',
            'X-Ray Dental', 'Printer X-Ray', 'Bed Patient Electric', 'Microscope', 'Electro Stimulator', 'Ultrasound Therapy (UST)',
            'Mesin Pengering Laundry', 'mesin Setrika Laundry', 'Mesin Cuci Laundry', 'Endoscopy', 'Haemodialisa',
            'TLD Badge (Jumlah 24)', 'TLD Mata (Jumlah 8)', 'Centrifuge Refrigrator', 'Freezer', 'Thermo Sealer', 'Apheresis',
            'Timbangan Dewasa', 'Flowmeter', 'Blood Scale', 'Cathlab', 'Chemistry Analyzer', 'COOL TERAPHY', 'TENS',
            'Electroencephalograph (EEG)', 'Electrolyte Analyzer', 'Gynaecology Chair', 'INJECTOR', 'Laryngoscope',
            'Manual Bed Patient', 'Electrik sealer', 'FFP Thawer', 'Plasma Thawing', 'Platelet Agitator Incubator',
            'Freezer Darah', 'Freezer Darah (Kecil)', 'Medical Refrigerator', 'Tube sealer / Hand Sealer', 'Spirometer',
            'High Flow', 'Nebulizer', 'Suction Wall', 'Vein Viewer', 'Vasculiminator', 'Hepa Filter Portable',
            'Neo HIF Respiratory', 'Bio Safety Cabinet', 'Micropipet Fix Volume', 'Micropipet Variable Volume', 'Roller Mixer',
            'Rotator', 'Stirer', 'Convective Air Warming', 'CTG', 'Pulse Oximeter', 'Operating Lamp', 'Opthalmic Surgery',
            'Elektro Akupuntur', 'Traksi', 'Washer Disinfector', 'Steam Sterilizer', 'Phototherapy', 'Tempat Tidur Bayi',
            'Tempat Tidur Bayi Tindakan', 'Autoclave', 'Gynaecology Chair Electrik', 'Oxygen Concentrator', 'Portable Air Purifier',
            'Spirometer Anak', 'Stretcher', 'Ultrasonic Nebulizer', 'Biometri + USG Mata', 'Termometer Digital', 'Sterilisator',
            'Bedside Cabinet', 'Tonometer', 'USG Mata', 'Space Saving Chart', 'Blood Pressure Monitor', 'Bor Kecepatan Tinggi',
            'Washer', 'IPAL', 'Tabung Gas', 'Gait Exercise', 'Infrared heating cabinet', 'Dust collector', 'Socket Router',
            'Mesin vacuum', 'Mesin Bor Duduk', 'Mesin Heat Gun', 'Gergaji Jigsaw', 'Oscilating Tools', 'Gerinda Duduk',
            'Mesin Jahit Industr', 'STABILIZER 20kVa', 'Pengering Laundry', 'Mesin Setrika', 'patient simulator', 'Ambulance'
        ];

        // Hapus duplikat dan bersihkan spasi ekstra, lalu jadikan unik
        $uniqueAlkes = collect($alkesList)->map(function ($item) {
            return trim($item);
        })->unique(function ($item) {
            return strtolower($item);
        });

        foreach ($uniqueAlkes as $alkes) {
            Alkes::firstOrCreate(['nama_alkes' => $alkes]);
        }
    }
}
