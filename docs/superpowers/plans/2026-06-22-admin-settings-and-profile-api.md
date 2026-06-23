# Store Settings (Filament & API) + Member Profile API Implementation Plan

**Goal:** Create a custom settings page in Filament to manage store information, expose this information via a public REST API (`GET /api/settings`), implement authenticated profile endpoints (`GET /api/profile`, `PUT /api/profile`, `PUT /api/profile/password`), and connect the storefront footer to display real settings data dynamically while removing the Google Maps iframe.

---

## User Review Required

> [!IMPORTANT]
> **Google Maps Embed Removal**
> As requested, we have excluded `google_maps_embed_url` from the database migration, the Filament Settings form, the API response, and completely removed the Google Maps iframe from the storefront footer (`AppFooter.vue`).

> [!NOTE]
> **Database Seeding**
> We will add a default `StoreSetting` row directly inside `DatabaseSeeder.php` via `firstOrCreate` to guarantee that at least one settings record exists on seed and prevent the page/API from throwing 500 errors when fetching settings before any admin setup has occurred.

---

## Open Questions

None. The requirements for removing the Google Maps embed, setting up the custom Filament page, and building the Profile APIs are fully understood.

---

## Proposed Changes

### 1. Database & Models

#### [MODIFY] [StoreSetting.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/app/Models/StoreSetting.php)
- Update model to add `phone`, `whatsapp_number`, `instagram_url`, and `operating_hours` to the `#[Fillable]` attribute.
- Retain the existing `contact` field to avoid breaking any other potential areas.

#### [NEW] [2026_06_22_164500_add_fields_to_store_settings_table.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/database/migrations/2026_06_22_164500_add_fields_to_store_settings_table.php)
- Add nullable string columns for `phone`, `whatsapp_number`, `instagram_url`, and `operating_hours` to `store_settings`.
- Do NOT add `google_maps_embed_url`.

#### [MODIFY] [DatabaseSeeder.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/database/seeders/DatabaseSeeder.php)
- Add a seeding operation to create a default `StoreSetting` row with initial contact/social details if none exists.

---

### 2. Filament Admin Settings Page

#### [NEW] [SettingsPage.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/app/Filament/Pages/SettingsPage.php)
- Create a custom Filament page implementing `HasForms` and using `InteractsWithForms`.
- Configure the form to bind to `StoreSetting::first()`.
- Inputs:
  - `store_name` (TextInput, required)
  - `logo` (FileUpload, using `public_uploads` disk under `settings/` directory)
  - `address` (Textarea)
  - `phone` (TextInput)
  - `whatsapp_number` (TextInput, with helperText for format guidelines)
  - `instagram_url` (TextInput, url)
  - `operating_hours` (TextInput)
  - `rajaongkir_origin_district_id` (Select, pulling from `RajaongkirDistrict::all()`)
- Form submission will save changes back to the single `store_settings` row.

#### [NEW] [settings-page.blade.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/resources/views/filament/pages/settings-page.blade.php)
- Blade template rendering Filament panel components: `<x-filament-panels::page>`, `<x-filament-panels::form>`, and `<x-filament-panels::form.actions>`.

---

### 3. API Controllers & Routes

#### [NEW] [SettingsResource.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/app/Http/Resources/Api/SettingsResource.php)
- Transform `StoreSetting` model into public JSON format.
- Exclude internal IDs, RajaOngkir district IDs, and timestamps.
- Return the full URL path of the store logo via `Storage::disk('public_uploads')->url($this->logo)`.

#### [NEW] [SettingsController.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/app/Http/Controllers/Api/SettingsController.php)
- `show()`: return the single `StoreSetting` wrapped in `SettingsResource`.

#### [NEW] [ProfileController.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/app/Http/Controllers/Api/ProfileController.php)
- Implement endpoints:
  - `show()`: get current user details (`id`, `name`, `email`, `phone`).
  - `update()`: edit `name` and `phone`. (Email change is omitted per design).
  - `updatePassword()`: validate `current_password` via manual hash check (robust across guards), and update `password` using `Hash::make()`.

#### [MODIFY] [api.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/routes/api.php)
- Register `GET /api/settings` as a public route.
- Register `GET /api/profile`, `PUT /api/profile`, and `PUT /api/profile/password` under the existing `auth:sanctum` group.

---

### 4. Storefront Integration

#### [MODIFY] [AppFooter.vue](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/storefront/app/components/AppFooter.vue)
- Add `<script setup>` tag to call `useApi().getSettings()` using Nuxt's `useAsyncData`.
- Compute dynamic settings with fallback defaults for brand name, logo, address, phone, WhatsApp link, Instagram link, and operating hours.
- Bind settings to footer HTML template.
- Remove the Google Maps iframe element completely.

---

### 5. API Documentation

#### [NEW] [settings.md](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/docs/api/settings.md)
- Document the `GET /api/settings` endpoint, inputs, outputs, and sample response.

#### [NEW] [auth.md](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/docs/api/auth.md)
- Document `GET /api/profile`, `PUT /api/profile`, and `PUT /api/profile/password` endpoints under Sanctum.

---

## Verification Plan

### Automated Tests
Run tests inside `api` directory:
```bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH" && php artisan test
```

We will create and run the following test classes:
- **Settings API Test**: [SettingsApiTest.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/tests/Feature/Settings/SettingsApiTest.php)
  - Asserts `GET /api/settings` returns 200 and matches the expected public settings fields.
- **Admin Settings Page Test**: [AdminSettingsPanelTest.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/tests/Feature/Settings/AdminSettingsPanelTest.php)
  - Asserts settings page is visible to logged-in admin.
  - Asserts settings page redirects guest away.
- **Profile API Test**: [ProfileApiTest.php](file:///Users/adiprimanto/Developer/Rangkai%20Web/pt-doctor-plant-indonesia/api/tests/Feature/Auth/ProfileApiTest.php)
  - Asserts `GET /api/profile` returns details for authenticated users.
  - Asserts `PUT /api/profile` updates `name` and `phone` values.
  - Asserts `PUT /api/profile/password` validates current password and updates password.
  - Asserts guest gets redirected/blocked (401 Unauthorized) on profile endpoints.

### Manual Verification
- Log in to the Filament Admin panel (`/admin`) and navigate to **Pengaturan**.
- Update the store settings form (e.g. name, phone, hours) and click **Simpan Perubahan**. Verify success notification.
- Visit the storefront homepage (local port) and confirm that the footer displays the updated address, phone, and social links. Verify that the Google Maps iframe is no longer present.
