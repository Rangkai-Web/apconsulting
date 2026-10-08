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
                'name_en' => 'Taxation Services',
                'description_id' => 'Kami mendampingi pemenuhan kewajiban pajak Anda melalui proses yang lebih terstruktur dan panduan yang mudah diterapkan.',
                'description_en' => 'Support your tax responsibilities with a more structured process and practical guidance.',
                'cta_text_id' => 'DISKUSIKAN LAYANAN PAJAK',
                'cta_text_en' => 'DISCUSS TAX SUPPORT',
                'features_id' => [
                    'Konsultasi perpajakan',
                    'Pelaporan pajak bulanan dan tahunan',
                    'Peninjauan kepatuhan pajak',
                    'Perencanaan pajak',
                    'Pendampingan pemeriksaan pajak',
                ],
                'features_en' => [
                    'Tax consultation',
                    'Monthly and annual tax reporting',
                    'Tax compliance review',
                    'Tax planning',
                    'Tax audit assistance',
                ],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_id' => 'Akuntansi & Pembukuan',
                'name_en' => 'Accounting & Bookkeeping',
                'description_id' => 'Bangun catatan keuangan terpercaya yang memberikan gambaran lebih jelas tentang operasional harian bisnis Anda.',
                'description_en' => 'Build reliable financial records that give your business a clearer view of its day-to-day operations.',
                'cta_text_id' => 'DISKUSIKAN DUKUNGAN AKUNTANSI',
                'cta_text_en' => 'DISCUSS ACCOUNTING SUPPORT',
                'features_id' => [
                    'Pencatatan transaksi keuangan',
                    'Laporan keuangan bulanan',
                    'Laporan keuangan tahunan',
                    'Rekonsiliasi bank',
                    'Penyesuaian jurnal akuntansi',
                ],
                'features_en' => [
                    'Financial transaction recording',
                    'Monthly financial statements',
                    'Annual financial statements',
                    'Bank reconciliation',
                    'Accounting journal adjustments',
                ],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_id' => 'Dukungan Administrasi Bisnis',
                'name_en' => 'Business Administration Support',
                'description_id' => 'Dapatkan dukungan fleksibel untuk pekerjaan administratif yang membantu bisnis Anda berjalan lebih terorganisir.',
                'description_en' => 'Get flexible support for administrative work that helps your business run in a more organized way.',
                'cta_text_id' => 'DISKUSIKAN DUKUNGAN BISNIS',
                'cta_text_en' => 'DISCUSS BUSINESS SUPPORT',
                'features_id' => [
                    'Administrasi perusahaan',
                    'Koordinasi dokumen',
                    'Tugas-tugas administratif',
                    'Dukungan kustom',
                ],
                'features_en' => [
                    'Corporate administration',
                    'Document coordination',
                    'Administrative tasks',
                    'Customized support',
                ],
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_id' => 'Layanan Keuangan Lainnya',
                'name_en' => 'Other Financial Services',
                'description_id' => 'Ubah informasi keuangan menjadi wawasan berguna untuk perencanaan, pengendalian, dan keputusan yang lebih baik.',
                'description_en' => 'Turn financial information into useful insight for planning, control, and better decisions.',
                'cta_text_id' => 'DISKUSIKAN DUKUNGAN KEUANGAN',
                'cta_text_en' => 'DISCUSS FINANCIAL SUPPORT',
                'features_id' => [
                    'Analisis laporan keuangan',
                    'Proyeksi keuangan',
                    'Manajemen arus kas',
                    'Penganggaran perusahaan',
                    'Evaluasi kinerja keuangan',
                ],
                'features_en' => [
                    'Financial statement analysis',
                    'Financial projections',
                    'Cash-flow management',
                    'Corporate budgeting',
                    'Financial performance evaluation',
                ],
                'sort_order' => 4,
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
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name_en' => $category['name_en']],
                $category
            );
        }
    }
}
