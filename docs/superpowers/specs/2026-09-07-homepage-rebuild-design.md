# Homepage Rebuild — Company Profile (PCG) — Design

Date: 2026-09-07
Status: Approved for planning

## Context

This repo is mid-pivot: the storefront/admin were originally built for **BbystarGO**, an ecommerce store ("Toko Online"). It is being repurposed into a **company profile site for PCG (Phangkawira Consulting Group)**, a tax/accounting/financial consulting firm. Evidence:

- `api/`: most ecommerce Filament resources (Banner, Category, Customer, Order, PaymentMethod) and their controllers/models are already deleted in the working tree (`git status`). Only `ProductResource` and `SettingsPage` remain.
- `storefront/app/`: still has ecommerce pages/components (`index.vue` — old ecommerce home, `tentang-kami.vue`, `ProductCard.vue`, `VariantSelector.vue`, `QtyStepper.vue`, `RatingStars.vue`, `EmptyState.vue`, `AppCuteDecor.vue`, `store/wishlist.ts`).
- `storefront/public/images/` has a Figma "DSD" (Design System Documentation) export at `design-system-page-design-interchange/` (component library: Button, Checkbox, Option, Navbar, Frame 37 — 75 color/text styles, all Poppins) plus per-page raw image/icon assets in `homepage/`, `about/`, `services/`, `contact/`, `insight/`, `icon/`.
- Confirmed live against the source Figma file (`https://www.figma.com/design/2oCbkmAnxCENuBJ1mmMCyz/PCG`, node `10:28973` = "Homepage" frame, 1440×8379): the actual company name is **PCG**, business = tax/accounting/bookkeeping/financial consulting.
- `CLAUDE.md` at the repo root still describes the old "BbystarGO — Toko Online" stack. It is stale and will need a follow-up update once this pivot is underway (not part of this spec).

## Scope

**Phase 1 (this spec): rebuild `/` (homepage) only**, using the Figma design system + the real homepage frame content. Other pages (`/tentang-kami` → `/about`, `/layanan` (services), `/kontak` (contact), `/insight`) get their own spec later, once their Figma sections are confirmed the same way this one was (today only their Hero section was exported to `public/images/`; their full frames haven't been walked in Figma yet).

Admin/Filament CMS for editing homepage content is **out of scope** — content is hardcoded in Vue components this phase (decision below).

### Out of scope (explicitly deferred, not forgotten)
- `/about`, `/services`, `/contact`, `/insight` pages (phase 2+, separate specs)
- Filament CMS resources for homepage content
- Non-homepage cleanup beyond what's listed below

## Decisions made during brainstorming

1. **Legacy ecommerce code**: delete entirely, don't keep dead code around.
2. **Page scope**: homepage only for phase 1.
3. **Content source**: hardcoded in Vue components/constants, no backend fetch.
4. **Design tokens**: extracted once into the Tailwind/Nuxt UI theme before building sections (not defined ad hoc per section).
5. **Components**: theme Nuxt UI v4 (`UButton`, etc.) via `app.config.ts` / `main.css` to match Figma variants, rather than building a fully custom component kit. Navbar and Footer are custom components (Nuxt UI has no equivalents) built from the real Figma content.

## Design tokens

Source: `storefront/public/images/design-system-page-design-interchange/design-system.manifest.json` (`styles`), cross-checked against the live Homepage frame screenshots.

### Font
Single family: **Poppins**, weights Regular/Medium/SemiBold/Bold/ExtraBold. Not currently loaded in the project — needs adding (e.g. `@nuxt/fonts` or self-hosted, check what `nuxt-fonts-global.css` in `.nuxt/` currently pulls).

### Colors (hex, from manifest)

| Token | Hex |
|---|---|
| yellow-50 | `#FEF9E8` |
| yellow-100 | `#FBEEB7` |
| yellow-500 | `#FFBB00` |
| yellow-600 | `#DBB514` |
| yellow-700 | `#AB8D10` |
| yellow-900 | `#655409` |
| blue-50 | `#E6EBEE` |
| blue-100 | `#B2C1CA` |
| blue-500 | `#083653` |
| blue-600 | `#07314C` |
| blue-700 | `#06263B` |
| neutral-01 | `#FFFFFF` |
| neutral-02 | `#D4D4D4` |
| neutral-03 | `#999999` |
| neutral-04 | `#666666` |
| neutral-05 | `#444444` |
| neutral-06 | `#222222` |
| neutral-07 | `#111111` |

Mapping to Nuxt UI: `primary` = yellow scale (buttons, accents), a new `dark`/`navy` custom color = blue scale (text headings, dark section backgrounds, footer CTA bg), `neutral` = the neutral scale (replaces current `slate`).

**Cleanup**: `storefront/app/assets/css/main.css` currently defines `--color-primary-*` (coral/red) and `--color-secondary-*` (green) — these are the old ecommerce palette and must be replaced with the table above.

### Typography scale
Desktop and Mobile variants exist for each size/weight (`Desktop/Heading 1/Bold` = Poppins 42px Bold, `Mobile/H1/Bold` = 36px, etc — full scale: Display 64px, Heading 1 42px, Heading 2 36px, Heading 3 28px, Subheading 1 24px, Subheading 2 20px, Text XL 18px, Text L 16px, Text M 14px, Text S 12px, each in Regular/Medium/SemiBold/Bold). Map to Tailwind `text-*` utilities via `app.config.ts` theme or a small set of custom classes (`.text-display`, `.text-h1`, …) — implementer's call, not prescribed here.

## Components

- **Button**: theme `UButton` — `type` (Outline/Primary/Secondary) → Nuxt UI `variant`/`color`, `size` (Small/Normal/Large) → Nuxt UI `size`. Default per Figma: Primary + Large. Reference: `design-system-page-design-interchange/llm/components/button-e0eg41.context.json`.
- **Navbar**: new `AppHeader.vue` (replacing the ecommerce one). Content confirmed from Figma (`node 1:852`): logo (PCG, from `icon/` or exported logo asset — check `homepage`/root image folders for a logo file, else export from Figma), nav links `Home / Services / Insights / About / Contact`, right-aligned CTA button "Discuss Your Needs" (Primary). Needs a mobile hamburger state — `icon/Hamburger_Icon.svg` exists for this.
- **Footer**: new `AppFooter.vue` (replacing the ecommerce one), content confirmed from Figma (`node 3:14329`) — see section 11 below. Not a placeholder; this is the real design.
- **Checkbox / Option**: not used on the homepage. Tokens still available for phase 2.
- Social icons (Instagram, X, Facebook, LinkedIn) in the footer: not present as files in `public/images/icon/` — use the project's existing icon system (Nuxt Icon / iconify, already installed per `.nuxt/nuxt-icon-client-bundle.mjs`) rather than exporting new SVGs.

## Homepage sections

Source of truth: live Figma frame `10:28973` ("Homepage"), file key `2oCbkmAnxCENuBJ1mmMCyz`. Order and content below were read directly off that frame (not guessed from filenames).

| # | Figma node | Section name | Component (suggested) |
|---|---|---|---|
| 0 | `1:852` | Navbar | `AppHeader.vue` |
| 1 | `76:176` | Hero | `components/home/Hero.vue` |
| 2 | `4:14413` | About PCG | `components/home/AboutPcg.vue` |
| 3 | `32:7086` | Stats ("PCG At a Glance") | `components/home/Stats.vue` |
| 4 | `13:154` | How We Help (3 cards) | `components/home/HowWeHelp.vue` |
| 5 | `39:404` | Why Businesses Choose Us | `components/home/WhyChooseUs.vue` |
| 6 | `5:14772` | Our Approach (4 steps) | `components/home/OurApproach.vue` |
| 7 | `3:14290` | Insights (3 article cards) | `components/home/Insights.vue` |
| 8 | `32:7136` | FAQ (accordion) | `components/home/Faq.vue` |
| 9 | `16:456` | CTA (dark) | `components/home/Cta.vue` |
| 10 | `3:14329` | Footer | `AppFooter.vue` |

### 1. Hero (`76:176`)
- Headline (3 lines, mixed style): "**CLEARER FINANCES.**" (navy, bold) / "*STRONGER BUSINESS DECISIONS*" (yellow, bold italic) / "**DECISIONS.**" (navy, bold) — Display/Heading scale, all-caps.
- Body: "PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals"
- Buttons: "EXPLORE OUR SERVICES" (Outline) + "DISCUSS YOUR NEEDS" (Primary/yellow)
- Visual: 3 building photos (`public/images/homepage/HeroContent_Desktop.png` / `_Mobile.png`) over a dotted globe graphic (`Globe_Desktop.png`) and a light grey background band (`Hero_Bg_Desktop.png`)

### 2. About PCG (`4:14413`)
- Eyebrow: "ABOUT PCG"
- Headline: "More than accountants. *Partners in your progress.*"
- Body (right column): "We helps businesses, families, and ambitious individuals make confident financial decisions. We turn complex information into clear, practical next steps, helping you stay compliant, improve performance, and plan for what comes next."
- Button: "DISCOVER HOW WE HELP" (Primary)
- Image: flat-lay desk photo — `public/images/homepage/About_Desktop.png` / `_Mobile.png`

### 3. Stats (`32:7086`) — dark navy background (`blue-700`)
- Eyebrow: "PCG AT A GLANCE" (yellow)
- Headline: "A structured foundation for better business decisions."
- Sub: "Key facts about our service model and approach—presented without unsupported client or performance claims."
- 4 stat pairs: `4` Core service areas / `4` Steps in our approach / `20` Integrated support partner / `200+` Customers

### 4. How We Help (`13:154`)
- Eyebrow: "HOW WE HELP"
- Headline: "*Your Business Deserves* More Than Routine Bookkeeping."
- Body: "We help businesses manage tax, accounting, bookkeeping, and financial reporting with clarity and structure, so you can make confident decisions and focus on growth."
- Image: team photo — `public/images/homepage/HowWeHelp_Desktop.png` / `_Mobile.png` (rendered as duotone/grayscale in Figma — apply via CSS filter, source file is full color)
- 3 cards (icon + title + body), icons from `public/images/icon/`:
  1. `Compliance_Icon.svg` — "Compliance with confidence." — "Tax compliance, filings, reviews, tax planning, and audit support—handled with care and clarity"
  2. `Financial_Icon.svg` — "Build a Stronger Financial Foundation" — "Accurate bookkeeping, financial reporting, bank reconciliation, and journal adjustments to keep your business organized."
  3. `Analysis_Icon.svg` — "Insight for better decisions." — "Financial analysis, cash-flow forecasts, budgeting, and performance reviews to help you plan ahead."

### 5. Why Businesses Choose Us (`39:404`)
- Eyebrow: "WHY BUSINESSES CHOOSE US"
- Headline: "*The right support* can change the way your business moves forward."
- Body: "Managing a business requires more than keeping records up to date. It requires clear information, practical guidance, and a reliable partner who understands the bigger picture."
- Two overlapping photo cards:
  - `public/images/homepage/TaxAdvisory_Desktop.png` / `_Mobile.png`, overlay label `TaxAdvisory_Icon.svg` + "Tax Advisory" — "Manage obligations, identify risks, and make more informed tax decisions."
  - `public/images/homepage/Finance&Consulting_Desktop.png` / `_Mobile.png`, overlay label `FinanceConsult_Icon.svg` + "Finance & Consulting" — "Use financial visibility to plan ahead and build sustainable growth."

### 6. Our Approach (`5:14772`)
- Eyebrow: "OUR APPROACH"
- Headline: "From *Financial Clarity* to Confident Action."
- Sub: "A structured approach to help your business stay compliant, organized, and ready for what comes next."
- 4 cards (icon + title + body), 2×2 grid, icons from `public/images/icon/`:
  1. `Understand_Icon.svg` — "Understand Your Business" — "We assess your financial position, records, and administrative processes to understand where your business stands today."
  2. `Identify_Icon.svg` — "Identify What Matters" — "We identify risks, gaps, and priorities, then focus on the areas that need attention first."
  3. `Solutions_Icon.svg` — "Put Solutions in Place" — "We implement the agreed tax, accounting, and financial solutions based on your business needs."
  4. `Review_Icon.svg` — "Review and Move Forward" — "We review progress regularly and provide ongoing support as your business evolves."

### 7. Insights (`3:14290`)
- Eyebrow: "INSIGHTS"
- Headline: "Practical insight for better *business decisions.*"
- Sub: "Clear perspectives on tax, accounting, and financial management, made for businesses that want to move forward with confidence."
- 3 article cards (photo, category tag, title, excerpt, date, "View Details" button): "Financial Management / What your financial reports are really telling you", "Accounting / Why monthly financial reporting matters", "Finance / A practical way to improve cash-flow visibility" — all dated "Aug 31,2026".
- **Gap**: the 3 article thumbnail photos are not exported to `public/images/`. Pull them from Figma node `3:14290` during implementation (`get_design_context` / `download_assets`) or use placeholders until real article images exist.

### 8. FAQ (`32:7136`)
- Eyebrow: "FREQUENTLY ASKED QUESTIONS"
- Headline: "*Clear answers for your next step.*"
- Sub: "Before starting a conversation, here are answers to the questions businesses commonly ask about PCG's services, process, and engagement."
- Accordion, 5 items (first expanded by default): "What services does PCG provide?" (answer: "PCG provides professional consulting and advisory support across taxation, accounting and bookkeeping, and other customized financial services. The available scope depends on your business needs and will be discussed before an engagement begins.") / "Who does PCG work with?" / "What information should I prepare before speaking with PCG?" / "Can PCG support ongoing monthly accounting or bookkeeping?" / "How do I get started with PCG?" (last 4 answers not yet expanded in the screenshot — pull via Figma when implementing this section).

### 9. CTA (`16:456`) — dark background
- Background: diagonal line pattern, `public/images/homepage/CTA_Desktop.png` / `_Mobile.png`
- Eyebrow: "START A CONVERSATION" (yellow)
- Headline: "Ready to bring more clarity to your business? *Let's take the next step together.*"
- Body: "Tell us about your tax, accounting, or financial needs. PCG will help you identify a practical starting point for your business."
- Button: "Discuss Your Needs" (white/inverted)

### 10. Footer (`3:14329`)
- Logo (PCG) + tagline: "Professional support in tax, accounting, and financial services. We're helping businesses move forward with clarity and confidence."
- Social icons: Instagram, X, Facebook, LinkedIn (use icon library, see Components section)
- Quick Links: Home, About, Services, Insights, Contact
- Services: Taxation, Accounting & Bookkeeping, Financial Services, Business Support
- Contact Us: address ("Lt 3 Sayur Sentul, Blk. D, Rukan Cordoba, Jl. Green Lake City Boulevard No.23, Kota Tangerang, Banten 15146"), email ("pcgserver9@gmail.com")
- Bottom bar: "Copyright © 2026 Phangkawira Consulting Group. All Rights Reserved" + "Privacy Policy" / "Terms & Conditions" links (no target pages yet — link to `#` or omit `href` until those pages exist)

## Mobile / responsive

Checked against the live Figma mobile frame (`node 74:8038`, "Homepage_Mobile", 375×13176). Same 10 sections + navbar, same order, same copy — this is a **responsive reflow**, not a separate design:

- **Navbar** (`105:1879`): collapses to logo + hamburger icon (`icon/Hamburger_Icon.svg`) — nav links and the "Discuss Your Needs" button move into a menu, not shown expanded in the static frame. Implementer should treat this as a standard mobile nav drawer/sheet; exact open-state content = same links as desktop nav.
- **Hero** (`76:12448`): headline/body/both buttons stack full-width above the image; the 3 building photos stack in a row below (still using `HeroContent_Mobile.png`, `Hero_Bg_Mobile.png` if present — desktop-only names in current export, confirm mobile variants exist before build).
- **Footer** (`76:13013`): "Quick Links" and "Services" columns sit side by side (2-col), "Contact Us" full-width below, social icons row above the columns. Same content as desktop footer, just re-flowed.
- No new copy, no removed sections, no mobile-only content was found. Build one component per section with Tailwind responsive classes (mobile-first stack → desktop row/grid), not separate mobile/desktop components.

Mobile section node IDs (for implementer reference during build, same order as the desktop table): Navbar `105:1879`, Hero `76:12448`, About PCG `76:12462`, Stats `76:12501`, How We Help `76:12639`, Why Businesses `76:12775`, Our Approach `76:12801`, Insights `76:12913`, FAQ `76:12952`, CTA `76:12999`, Footer `76:13013`.

## Cleanup (delete as part of this work)

- `storefront/app/pages/index.vue` (rewritten, not literally deleted — becomes the new homepage)
- `storefront/app/pages/tentang-kami.vue`
- `storefront/app/components/ProductCard.vue`
- `storefront/app/components/ProductGrid.vue`
- `storefront/app/components/VariantSelector.vue`
- `storefront/app/components/QtyStepper.vue`
- `storefront/app/components/RatingStars.vue`
- `storefront/app/components/EmptyState.vue`
- `storefront/app/components/AppCuteDecor.vue`
- `storefront/app/store/wishlist.ts`
- Rewrite (not delete): `storefront/app/components/AppHeader.vue`, `storefront/app/components/AppFooter.vue`
- Replace: `--color-primary-*` / `--color-secondary-*` in `storefront/app/assets/css/main.css`
- Remove now-irrelevant tests: `storefront/tests/cart.store.test.ts`, check `orderService.test.ts` / `useSeoText.test.ts` for ecommerce-only assumptions

`api/` is untouched this phase (`ProductResource` stays until the admin CMS spec addresses it).

## Data & content

All copy above is hardcoded into each section component (props or local constants) — no API calls, no new Filament resources this phase.

## Testing

- Existing Vitest suite: drop ecommerce-only tests (`cart.store.test.ts`), keep/adjust `health.test.ts`, `generate-sitemap.test.ts`.
- Add a smoke test that the homepage route renders without error and contains the expected section landmarks (pattern to match whatever `tests/health.test.ts` already does).
- Manual pass: view homepage in browser at desktop + mobile widths, compare against Figma frame `10:28973` section by section.

## Known follow-ups (not this spec)

- `/about`, `/services`, `/contact`, `/insight` pages — need the same Figma frame-walk this spec did for homepage before they can be planned.
- Insight article thumbnail images — not yet exported.
- FAQ answers 2–5 — not yet read from Figma (only Q1's answer was expanded in the screenshot).
- Root `CLAUDE.md` still describes the old ecommerce stack — needs updating once this pivot is far enough along.
- Admin CMS for homepage content editing, if ever wanted.
