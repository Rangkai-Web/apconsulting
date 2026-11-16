<div class="ap-login-page">
    <style>
        /* Scoped styles for AP Consulting Login Redesign */
        body.fi-body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #06192c !important;
        }

        .ap-login-page {
            display: flex;
            flex-direction: column-reverse;
            min-height: 100vh;
            width: 100%;
            background-color: #06192c;
            color: #ffffff;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        .ap-login-page *,
        .ap-login-page *::before,
        .ap-login-page *::after {
            box-sizing: border-box;
        }

        /* --- SISI KIRI: HERO & COPYWRITING (Desktop) / BAWAH (Mobile) --- */
        .ap-hero-section {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            background-color: #072642;
            padding: 2.5rem 1.5rem;
            min-height: 480px;
            width: 100%;
        }

        .ap-hero-bg-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0.38;
            mix-blend-mode: luminosity;
            pointer-events: none;
            z-index: 1;
        }

        .ap-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(6, 35, 61, 0.97) 0%, rgba(8, 58, 99, 0.92) 50%, rgba(8, 105, 178, 0.84) 100%);
            pointer-events: none;
            z-index: 2;
        }

        .ap-hero-content {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            gap: 2.5rem;
        }

        .ap-hero-top-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.6rem 1.1rem;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            width: fit-content;
        }

        .ap-hero-logo-box {
            background: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

        .ap-hero-logo-box img {
            height: 22px;
            width: auto;
            max-width: 90px;
            object-fit: contain;
            display: block;
        }

        .ap-hero-brand-title {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #ffffff;
            line-height: 1.2;
        }

        .ap-hero-brand-sub {
            font-size: 0.7rem;
            font-weight: 500;
            color: #b9dbf4;
            line-height: 1.2;
        }

        .ap-hero-editorial {
            max-width: 580px;
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            margin: auto 0;
        }

        .ap-hero-tag {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #9cd4ff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ap-hero-tag::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #0ea5e9;
            box-shadow: 0 0 8px #0ea5e9;
        }

        .ap-hero-heading {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: -0.025em;
            color: #ffffff;
            margin: 0;
        }

        .ap-hero-heading span {
            font-style: italic;
            color: #b9e2ff;
        }

        .ap-hero-desc {
            font-size: 0.92rem;
            line-height: 1.62;
            color: #d8e8f5;
            margin: 0;
        }

        .ap-hero-badges {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.85rem;
            margin-top: 0.5rem;
        }

        .ap-badge-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 0.75rem 1rem;
            border-radius: 6px;
        }

        .ap-badge-icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0869B2;
            color: #ffffff;
            border-radius: 4px;
        }

        .ap-badge-icon svg {
            width: 18px;
            height: 18px;
        }

        .ap-badge-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b9dbf4;
            line-height: 1.2;
        }

        .ap-badge-val {
            font-size: 0.78rem;
            font-weight: 600;
            color: #ffffff;
            line-height: 1.3;
        }

        .ap-hero-footer {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 1.25rem;
            font-size: 0.75rem;
            color: #b9dbf4;
        }

        .ap-hero-quote {
            font-style: italic;
            font-weight: 300;
        }

        .ap-hero-values {
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.7rem;
        }

        /* --- SISI KANAN: FORM LOGIN (Desktop) / ATAS (Mobile) --- */
        .ap-form-section {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #091726;
            padding: 2.25rem 1.5rem;
            width: 100%;
            min-height: auto;
            position: relative;
            z-index: 10;
        }

        .ap-form-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 440px;
            margin: 0 auto 1.5rem auto;
        }

        .ap-pill-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.7rem;
            background: rgba(8, 105, 178, 0.18);
            border: 1px solid rgba(8, 105, 178, 0.4);
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #58b2f7;
        }

        .ap-version-tag {
            font-size: 0.72rem;
            color: #7b94a8;
            font-weight: 500;
        }

        .ap-form-card {
            width: 100%;
            max-width: 440px;
            margin: auto;
            padding: 1.75rem;
            background: #0f243a;
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 12px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
        }

        .ap-form-header {
            margin-bottom: 1.6rem;
            text-align: left;
        }

        .ap-form-logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
            margin-bottom: 1.25rem;
        }

        .ap-form-logo {
            max-height: 38px;
            max-width: 180px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .ap-form-title {
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin: 0 0 0.35rem 0;
            line-height: 1.2;
        }

        .ap-form-sub {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #9cb8d1;
            margin: 0;
        }

        /* Override styling for Filament inner form components */
        .ap-form-content {
            width: 100%;
        }

        .ap-form-content form {
            width: 100% !important;
        }

        .ap-form-content .fi-fo-field-wrp {
            margin-bottom: 1.15rem !important;
        }

        .ap-form-content label,
        .ap-form-content .fi-fo-field-label-content {
            color: #d8e8f5 !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
        }

        .ap-form-content .fi-input-wrp {
            background-color: #0b1a2d !important;
            border: 1px solid rgba(255, 255, 255, 0.16) !important;
            border-radius: 6px !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .ap-form-content .fi-input-wrp:focus-within {
            border-color: #0869B2 !important;
            box-shadow: 0 0 0 3px rgba(8, 105, 178, 0.35) !important;
        }

        .ap-form-content input {
            color: #ffffff !important;
            font-size: 0.9rem !important;
        }

        .ap-form-content input::placeholder {
            color: #647d94 !important;
        }

        .ap-form-content .fi-btn-color-primary,
        .ap-form-content button[type="submit"] {
            background-color: #0869B2 !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            width: 100% !important;
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            font-size: 0.92rem !important;
            letter-spacing: 0.02em !important;
            box-shadow: 0 4px 14px rgba(8, 105, 178, 0.35) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }

        .ap-form-content .fi-btn-color-primary:hover,
        .ap-form-content button[type="submit"]:hover {
            background-color: #0a7bc9 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(8, 105, 178, 0.5) !important;
        }

        .ap-form-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.72rem;
            color: #6b8499;
        }

        /* --- RESPONSIVE MEDIA QUERIES (DESKTOP: min-width: 1024px) --- */
        @media (min-width: 1024px) {
            .ap-login-page {
                flex-direction: row;
                height: 100vh;
                overflow: hidden;
            }

            .ap-hero-section {
                flex: 1 1 54%;
                width: 54%;
                min-height: 100vh;
                padding: 3.5rem 4rem;
            }

            .ap-hero-heading {
                font-size: 2.5rem;
                line-height: 1.15;
            }

            .ap-hero-badges {
                grid-template-columns: repeat(2, 1fr);
            }

            .ap-hero-footer {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .ap-form-section {
                flex: 1 1 46%;
                width: 46%;
                min-height: 100vh;
                overflow-y: auto;
                padding: 3.5rem 3rem;
            }

            .ap-form-card {
                padding: 2.25rem;
            }
        }
    </style>

    <!-- SISI KIRI (DESKTOP) / BAWAH (MOBILE): Hero Copywriting & Trust Badges -->
    <div class="ap-hero-section">
        <!-- Background Image & Gradient Overlay -->
        <img
            src="{{ asset('images/redesign/hero-bg.png') }}"
            alt="AP Consulting Hero"
            aria-hidden="true"
            class="ap-hero-bg-img"
            loading="eager"
        />
        <div class="ap-hero-overlay"></div>

        <div class="ap-hero-content">
            <!-- Top Identity Badge -->
            <div class="ap-hero-top-badge">
                <div class="ap-hero-logo-box">
                    <img
                        src="{{ asset('images/redesign/logo.png') }}"
                        alt="AP Consulting"
                    />
                </div>
                <div>
                    <div class="ap-hero-brand-title">AP CONSULTING</div>
                    <div class="ap-hero-brand-sub">Tax &amp; Business Advisory</div>
                </div>
            </div>

            <!-- Center Editorial Copywriting -->
            <div class="ap-hero-editorial">
                <!-- <div class="ap-hero-tag">
                    Kantor Konsultan Pajak Berbasis Hukum
                </div> -->

                <h1 class="ap-hero-heading">
                    Clearer taxes.<br />
                    Move your business <span>forward confidently.</span>
                </h1>

                <p class="ap-hero-desc">
                    Kantor konsultan pajak berbasis hukum yang berpengalaman, profesional, dan berintegritas tinggi dalam membantu pemenuhan kewajiban perpajakan, payroll outsourcing, serta pembukuan akuntansi yang efektif di Indonesia.
                </p>

                <!-- Trust Badges & Official Credentials -->
                <div class="ap-hero-badges">
                    <div class="ap-badge-card">
                        <div class="ap-badge-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <div class="ap-badge-label">Izin Kemenkeu RI</div>
                            <div class="ap-badge-val">KIP-6592/IP.B/PJ/2020</div>
                        </div>
                    </div>

                    <div class="ap-badge-card">
                        <div class="ap-badge-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <div class="ap-badge-label">Anggota Resmi</div>
                            <div class="ap-badge-val">IKPI (Ikatan Konsultan Pajak)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Quote & Values -->
            <div class="ap-hero-footer">
                <span class="ap-hero-quote">"Beyond numbers, it's about trust."</span>
                <span class="ap-hero-values">Integrity · Excellence · Teamwork</span>
            </div>
        </div>
    </div>

    <!-- SISI KANAN (DESKTOP) / ATAS (MOBILE): Form Login -->
    <div class="ap-form-section">
        <!-- Top Utility Header -->
        <div class="ap-form-top-bar">
            <span class="ap-pill-badge">
                Administrator Portal
            </span>
            <span class="ap-version-tag">
                v1.0
            </span>
        </div>

        <!-- Center Form Card -->
        <div class="ap-form-card">
            <!-- Brand Logo & Heading -->
            <div class="ap-form-header">
                <div class="ap-form-logo-wrapper">
                    <img
                        src="{{ asset('images/redesign/logo.png') }}"
                        alt="AP Consulting Logo"
                        class="ap-form-logo"
                    />
                </div>
                <h2 class="ap-form-title">
                    Sign in
                </h2>
                <p class="ap-form-sub">
                    Masukkan akun administrator Anda untuk mengelola konten dan sistem AP Consulting.
                </p>
            </div>

            <!-- Filament Form Content -->
            <div class="ap-form-content">
                {{ $this->content }}
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="ap-form-footer">
            Copyright &copy; {{ date('Y') }} AP Consulting. All Rights Reserved.
        </div>
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif
</div>
