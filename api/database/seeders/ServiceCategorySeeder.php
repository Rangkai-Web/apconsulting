<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_id' => 'Layanan Perpajakan',
                'name_en' => 'Tax Services',
                'description_id' => 'Kami mendampingi pemenuhan kewajiban pajak Anda melalui proses yang lebih terstruktur, mulai dari kepatuhan bulanan hingga pendampingan pemeriksaan dan sengketa pajak.',
                'description_en' => 'From tax compliance and reviews to audit assistance and dispute resolution.',
                'cta_text_id' => 'DISKUSIKAN LAYANAN PAJAK',
                'cta_text_en' => 'DISCUSS TAX SUPPORT',
                'features_id' => [
                    'Konsultasi perpajakan',
                    'Pelaporan pajak bulanan dan tahunan',
                    'Peninjauan kepatuhan pajak (Tax Review)',
                    'Perencanaan pajak (Tax Planning)',
                    'Pendampingan pemeriksaan & sengketa pajak',
                ],
                'features_en' => [
                    'Tax consultation',
                    'Monthly and annual tax reporting',
                    'Tax compliance review',
                    'Tax planning',
                    'Tax audit & dispute resolution assistance',
                ],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_id' => 'Layanan Payroll Outsourcing',
                'name_en' => 'Payroll Outsourcing Services',
                'description_id' => 'Dukungan pengelolaan penggajian dan perhitungan PPh 21 karyawan agar administrasi payroll perusahaan Anda tetap terstruktur, tepat waktu, dan patuh aturan.',
                'description_en' => 'Payroll support to help keep your business’s salary administration and employee tax withholding structured.',
                'cta_text_id' => 'DISKUSIKAN LAYANAN PAYROLL',
                'cta_text_en' => 'DISCUSS PAYROLL SUPPORT',
                'features_id' => [
                    'Administrasi penggajian bulanan',
                    'Perhitungan & pemotongan PPh Pasal 21/26',
                    'Penyusunan slip gaji & laporan payroll',
                    'Pelaporan SPT Masa PPh 21 & bukti potong',
                ],
                'features_en' => [
                    'Monthly payroll administration',
                    'PPh 21/26 employee tax calculation & withholding',
                    'Payslip preparation & payroll reporting',
                    'Monthly & annual employee tax filing',
                ],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_id' => 'Layanan Akuntansi Pajak',
                'name_en' => 'Tax Accounting Services',
                'description_id' => 'Dukungan pembukuan dan akuntansi perpajakan untuk pencatatan keuangan yang lebih rapi, akurat, dan siap untuk rekonsiliasi fiskal.',
                'description_en' => 'Accounting and tax bookkeeping support for more organized records and accurate financial information.',
                'cta_text_id' => 'DISKUSIKAN DUKUNGAN AKUNTANSI',
                'cta_text_en' => 'DISCUSS ACCOUNTING SUPPORT',
                'features_id' => [
                    'Pencatatan transaksi keuangan & pembukuan',
                    'Penyusunan laporan keuangan bulanan & tahunan',
                    'Rekonsiliasi bank & penyesuaian jurnal',
                    'Rekonsiliasi fiskal untuk kebutuhan perpajakan',
                ],
                'features_en' => [
                    'Financial transaction recording & bookkeeping',
                    'Monthly & annual financial statements',
                    'Bank reconciliation & journal adjustments',
                    'Fiscal reconciliation for tax compliance',
                ],
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_id' => 'Pertanyaan Umum',
                'name_en' => 'General Inquiry',
                'description_id' => 'Hubungi kami untuk pertanyaan umum terkait kebutuhan bisnis Anda.',
                'description_en' => 'Contact us for general inquiries regarding your business needs.',
                'cta_text_id' => 'DISKUSIKAN KEBUTUHAN ANDA',
                'cta_text_en' => 'DISCUSS YOUR NEEDS',
                'features_id' => [],
                'features_en' => [],
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['sort_order' => $category['sort_order']],
                $category
            );
        }
    }
}
