# AP Consulting — Company Profile

Monorepo: `api/` (Laravel 12/13 + Filament v5 admin) + `storefront/` (Nuxt 4 SPA).
Docs: `docs/api/company-profile.md`
Animation & Scroll Reveal blueprint: `docs/ANIMATION_BLUEPRINT.md`.

## Client Profile
**AP Consulting** merupakan kantor konsultan pajak berbasis hukum yang berpengalaman, profesional dan berintegritas dalam membantu klien (badan usaha ataupun perorangan) dalam memenuhi kewajiban perpajakannya secara efektif.
- **Komitmen**: Menjadi mitra perusahaan yang handal dan terpercaya dalam menyelesaikan permasalahan klien di bidang akuntansi dan perpajakan.
- **Tim**: Berpengalaman serta berwawasan luas dalam bidang perpajakan, dengan kerjasama tim yang solid, pelayanan optimal serta penyelesaian permasalahan yang konkrit.

## Stack
- Backend: Laravel 12/13, PHP 8.3+ (installed via Homebrew at `/opt/homebrew/bin/php`), SQLite (testing) / MySQL (local/production), PHPUnit for tests, Filament v5 for admin panel.
- Frontend: Nuxt 4.4.x, `ssr: false` (SPA only), Nuxt UI v4 component library, Pinia, Vitest for tests.
- Localization: Bilingual support (Bahasa Indonesia `id` and English `en`).

## Command Guidelines
- Backend Tests: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && cd api && php artisan test`
- Run Migrations: `export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && cd api && php artisan migrate`
- Frontend Tests: `cd storefront && npx vitest run`
- Frontend Dev: `cd storefront && npm run dev`
- Backend Dev: `cd api && php artisan serve`

## Conventions
- Multi-language support: Storefront translations are centralized in `storefront/app/composables/useLanguage.ts`.
- Content & Articles: Admin panel manages multilingual articles, team members, service categories, FAQs, and company settings via Filament.
- Contact inquiries: Submissions trigger WhatsApp redirect format with professional greeting for AP Consulting.
