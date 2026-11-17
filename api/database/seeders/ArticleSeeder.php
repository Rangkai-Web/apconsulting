<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title_id' => 'Usaha Baru Buka, Harus Langsung Bayar Pajak?',
                'slug_id' => 'new-business-do-you-need-to-pay-tax-right-away',
                'excerpt_id' => 'Pajak merupakan bagian penting dari keberlanjutan ekonomi negara, dan pemahaman mengenai kewajiban pajak membantu bisnis baru tetap patuh aturan.',
                'content_id' => '<p>Mendirikan usaha baru adalah langkah besar bagi setiap wirausahawan. Namun, salah satu pertanyaan yang paling sering diajukan adalah: <strong>apakah usaha yang baru berdiri harus langsung membayar pajak?</strong></p><h3>1. Kewajiban Administrasi Terlebih Dahulu</h3><p>Pada tahap awal pendirian, fokus utama wajib pajak badan atau perorangan adalah pemenuhan kewajiban administrasi, yaitu memiliki Nomor Pokok Wajib Pajak (NPWP) dan mengukuhkan status perpajakan yang sesuai.</p><h3>2. Pajak Dihitung Berdasarkan Penghasilan</h3><p>Secara umum, Pajak Penghasilan (PPh) dikenakan atas penghasilan kena pajak atau peredaran bruto (omzet). Jika usaha baru Anda belum menghasilkan penjualan atau masih dalam tahap persiapan, kewajiban penyetoran PPh final UMKM (0,5%) belum timbul, namun kewajiban pelaporan SPT nihil tetap harus dipenuhi.</p><h3>3. Pentingnya Konsultasi Sejak Dini</h3><p>Merencanakan kepatuhan pajak sejak awal berdirinya usaha akan menyelamatkan bisnis Anda dari risiko sanksi denda administrasi di kemudian hari.</p>',
                'category_id' => 'Layanan Perpajakan',
                'title_en' => 'New Business: Do You Need to Pay Tax Right Away?',
                'slug_en' => 'new-business-do-you-need-to-pay-tax-right-away',
                'excerpt_en' => 'Taxation is an essential part of national economic sustainability. Understanding tax obligations from day one helps newly established businesses stay compliant.',
                'content_en' => '<p>Starting a new business is an exciting milestone. However, one of the most common questions entrepreneurs ask is: <strong>does a newly established business have to pay taxes immediately?</strong></p><h3>1. Administrative Compliance First</h3><p>In the beginning, your primary focus should be administrative compliance—obtaining your Tax Identification Number (NPWP) and setting up your tax credentials properly.</p><h3>2. Taxes Are Based on Revenue</h3><p>Income tax is generally levied on net income or gross revenue. If your business hasn’t generated sales yet, tax payments may not be due, but regular nil filing reports must still be submitted.</p>',
                'category_en' => 'Tax Services',
                'author_name' => 'Andry Phangkawira, S.E., Ak., BKP.',
                'cover_image' => null,
                'read_time' => 5,
                'is_published' => true,
                'published_at' => '2023-12-18',
                'views_count' => 142,
            ],
            [
                'title_id' => 'Padankan NIK dan NPWP Sebelum Akhir Tahun 2023!',
                'slug_id' => 'padankan-nik-npwp',
                'excerpt_id' => 'Mulai tanggal 14 Juli 2022, sistem perpajakan di Indonesia telah mengalami perubahan signifikan melalui implementasi pemadanan NIK dan NPWP.',
                'content_id' => '<p>Direktorat Jenderal Pajak (DJP) terus mendorong wajib pajak orang pribadi untuk melakukan integrasi Nomor Induk Kependudukan (NIK) menjadi Nomor Pokok Wajib Pajak (NPWP) 16 digit.</p><h3>Mengapa Pemadanan Ini Penting?</h3><p>Integrasi NIK-NPWP bertujuan untuk menyederhanakan administrasi perpajakan dan mendukung implementasi Core Tax Administration System (CTAS).</p><h3>Bagaimana Cara Memadankan NIK-NPWP?</h3><ol><li>Login ke portal DJP Online (djponline.pajak.go.id).</li><li>Masuk ke menu Profil.</li><li>Masukkan 16 digit NIK sesuai KTP dan lakukan validasi.</li><li>Simpan data yang telah tervalidasi.</li></ol>',
                'category_id' => 'Layanan Perpajakan',
                'title_en' => 'Link Your NIK and NPWP Before the End of 2023!',
                'slug_en' => 'padankan-nik-npwp',
                'excerpt_en' => 'Since July 14, 2022, Indonesia\'s taxation system has undergone significant digital integration through NIK-NPWP validation.',
                'content_en' => '<p>The Directorate General of Taxes (DGT) continues to urge individual taxpayers to integrate their National Identification Number (NIK) with their 16-digit NPWP.</p><h3>Why Is This Integration Crucial?</h3><p>The single identity system simplifies tax administration and powers the nationwide Core Tax Administration System.</p>',
                'category_en' => 'Tax Services',
                'author_name' => 'Andry Phangkawira, S.E., Ak., BKP.',
                'cover_image' => null,
                'read_time' => 4,
                'is_published' => true,
                'published_at' => '2023-12-15',
                'views_count' => 98,
            ],
            [
                'title_id' => 'Konsultan Pajak SPT Tahunan Tangerang Jadi Lebih Mudah',
                'slug_id' => 'konsultan-pajak-spt-tahunan',
                'excerpt_id' => 'Konsultan Pajak SPT Tahunan Tangerang — Masyarakat ataupun Perusahaan harus memiliki kesadaran wajib pajak untuk pelaporan tahunan tepat waktu.',
                'content_id' => '<p>Menjelang batas akhir pelaporan SPT Tahunan, banyak wajib pajak pribadi maupun badan di wilayah Tangerang dan Jakarta Barat menghadapi kendala teknis dan rekonsiliasi fiskal.</p><h3>Dukungan Konsultan Pajak Resmi</h3><p>Bekerja sama dengan konsultan pajak terdaftar memastikan seluruh dokumen laporan keuangan, bukti potong (1721-A1/A2), dan lampiran SPT dihitung dengan cermat sesuai ketentuan UU Perpajakan yang berlaku.</p>',
                'category_id' => 'Layanan Perpajakan',
                'title_en' => 'Annual Tax Return (SPT) Consultants in Tangerang',
                'slug_en' => 'konsultan-pajak-spt-tahunan',
                'excerpt_en' => 'Practical guidance for individuals and corporate taxpayers in Tangerang on preparing accurate annual SPT filings with licensed consultants.',
                'content_en' => '<p>As annual tax return deadlines approach, businesses and individuals in Tangerang and Greater Jakarta benefit greatly from structured fiscal reconciliation and expert compliance assistance.</p>',
                'category_en' => 'Tax Services',
                'author_name' => 'Andry Phangkawira, S.E., Ak., BKP.',
                'cover_image' => null,
                'read_time' => 5,
                'is_published' => true,
                'published_at' => '2023-10-18',
                'views_count' => 85,
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug_id' => $article['slug_id']],
                $article
            );
        }
    }
}
