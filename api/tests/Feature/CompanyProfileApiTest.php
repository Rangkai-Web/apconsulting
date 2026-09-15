<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Inquiry;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_fetch_faqs_in_indonesian_and_english(): void
    {
        $responseId = $this->getJson('/api/faqs?lang=id');
        $responseId->assertStatus(200)
            ->assertJsonPath('data.0.question', 'Apa saja layanan utama yang ditawarkan oleh PCG?');

        $responseEn = $this->getJson('/api/faqs?lang=en');
        $responseEn->assertStatus(200)
            ->assertJsonPath('data.0.question', 'What core services does PCG provide?');
    }

    public function test_can_fetch_articles_in_indonesian_and_english(): void
    {
        Article::create([
            'title_id' => 'Artikel Uji Coba',
            'slug_id' => 'artikel-uji-coba',
            'excerpt_id' => 'Ringkasan artikel',
            'content_id' => '<p>Konten artikel</p>',
            'title_en' => 'Test Article',
            'slug_en' => 'test-article',
            'excerpt_en' => 'Test excerpt',
            'content_en' => '<p>Test content</p>',
            'category_id' => 'Perpajakan',
            'category_en' => 'Taxation',
            'author_name' => 'Admin PCG',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $responseId = $this->getJson('/api/articles?lang=id');
        $responseId->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Artikel Uji Coba');

        $responseEn = $this->getJson('/api/articles?lang=en');
        $responseEn->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Test Article');
    }

    public function test_can_fetch_article_detail_by_slug_id_or_en(): void
    {
        $article = Article::create([
            'title_id' => 'Detail Uji Coba',
            'slug_id' => 'detail-uji-coba',
            'content_id' => '<p>Konten detail</p>',
            'title_en' => 'Detail Test Article',
            'slug_en' => 'detail-test-article',
            'content_en' => '<p>Detail content</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Query with ID slug & lang=id
        $responseId = $this->getJson('/api/articles/detail-uji-coba?lang=id');
        $responseId->assertStatus(200)
            ->assertJsonPath('data.title', 'Detail Uji Coba');

        // Query with EN slug & lang=en
        $responseEn = $this->getJson('/api/articles/detail-test-article?lang=en');
        $responseEn->assertStatus(200)
            ->assertJsonPath('data.title', 'Detail Test Article');

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'views_count' => 2,
        ]);
    }

    public function test_can_fetch_team_members(): void
    {
        Team::create([
            'name' => 'John Doe',
            'position' => 'Senior Partner',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/team');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'name', 'position', 'is_active'],
                ],
            ]);
    }

    public function test_can_fetch_company_settings(): void
    {
        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'company' => ['logo'],
                    'contact' => ['email', 'phone', 'whatsapp', 'address', 'maps_embed_url'],
                    'social' => ['linkedin', 'instagram', 'facebook', 'twitter', 'youtube'],
                ],
            ]);
    }

    public function test_can_submit_contact_form(): void
    {
        $payload = [
            'full_name' => 'Adi Primanto',
            'email' => 'adi@example.com',
            'phone' => '+62812345678',
            'company' => 'PT Test Indonesia',
            'service_interest' => 'Taxation Services',
            'message' => 'Saya ingin berkonsultasi mengenai pajak perusahaan.',
        ];

        $response = $this->postJson('/api/contact', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'message', 'whatsapp_url', 'data']);

        $this->assertDatabaseHas('inquiries', [
            'email' => 'adi@example.com',
            'full_name' => 'Adi Primanto',
            'status' => 'unread',
        ]);
    }

    public function test_can_fetch_service_categories(): void
    {
        $response = $this->getJson('/api/service-categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'name_id', 'name_en', 'sort_order', 'is_active'],
                ],
            ]);
    }
}
