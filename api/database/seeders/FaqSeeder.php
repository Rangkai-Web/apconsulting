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
                'question_id' => 'Apa saja layanan utama yang ditawarkan oleh AP Consulting?',
                'answer_id' => 'AP Consulting menawarkan layanan konsultasi perpajakan berbasis hukum, akuntansi & pembukuan, serta pendampingan penyelesaian masalah perpajakan bagi klien badan usaha maupun perorangan.',
                'question_en' => 'What core services does AP Consulting provide?',
                'answer_en' => 'AP Consulting provides legal-based tax consulting, accounting & bookkeeping, and tax dispute resolution & compliance services for corporate and individual clients.',
                'category_id' => 'Umum',
                'category_en' => 'General',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question_id' => 'Bagaimana cara memulai konsultasi perpajakan dengan AP Consulting?',
                'answer_id' => 'Anda dapat mengisi formulir kontak di website ini atau menghubungi kami langsung via WhatsApp. Tim konsultan kami yang berpengalaman akan segera menjadwalkan sesi diskusi awal untuk memahami kebutuhan perpajakan Anda.',
                'question_en' => 'How can I start a tax consultation with AP Consulting?',
                'answer_en' => 'You can fill out the contact form on this website or reach out directly via WhatsApp. Our experienced consulting team will schedule an initial consultation to understand your tax requirements.',
                'category_id' => 'Layanan',
                'category_en' => 'Services',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question_id' => 'Apakah AP Consulting melayani badan usaha maupun perorangan?',
                'answer_id' => 'Ya, kami berpengalaman, profesional, dan berintegritas dalam membantu klien badan usaha maupun perorangan untuk memenuhi kewajiban perpajakannya secara efektif dan solutif.',
                'question_en' => 'Does AP Consulting serve both corporate and individual clients?',
                'answer_en' => 'Yes, our team is experienced, professional, and dedicated to helping both corporate entities and individuals fulfill their tax obligations effectively and compliantly.',
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
