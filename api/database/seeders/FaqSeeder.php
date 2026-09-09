<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question_id' => 'Apa saja layanan utama yang ditawarkan oleh PCG?',
                'answer_id' => 'PCG menawarkan layanan konsultasi perpajakan, akuntansi & pembukuan, administrasi bisnis, serta konsultasi keuangan untuk berbagai skala usaha.',
                'question_en' => 'What core services does PCG provide?',
                'answer_en' => 'PCG provides professional tax consulting, accounting & bookkeeping, business administration support, and financial advisory services.',
                'category_id' => 'Umum',
                'category_en' => 'General',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question_id' => 'Bagaimana cara memulai konsultasi perpajakan dengan PCG?',
                'answer_id' => 'Anda dapat mengisi formulir kontak di website ini atau menghubungi kami langsung via WhatsApp. Tim profesional kami akan menjadwalkan sesi diskusi awal.',
                'question_en' => 'How can I start a tax consultation with PCG?',
                'answer_en' => 'You can fill out the contact form on this website or reach out directly via WhatsApp. Our professional team will schedule an initial discussion.',
                'category_id' => 'Layanan',
                'category_en' => 'Services',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question_id' => 'Apakah PCG melayani perusahaan berskala UMKM maupun Korporasi?',
                'answer_id' => 'Ya, kami melayani UMKM, startup, hingga perusahaan korporasi berskala nasional dan multinasional dengan solusi yang disesuaikan.',
                'question_en' => 'Does PCG serve both MSMEs and Corporate clients?',
                'answer_en' => 'Yes, we serve MSMEs, startups, as well as national and multinational corporations with tailored solutions.',
                'category_id' => 'Umum',
                'category_en' => 'General',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question_id' => $faq['question_id']], $faq);
        }
    }
}
