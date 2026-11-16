<div class="flex min-h-screen w-full flex-col-reverse lg:flex-row bg-[#F4F9FE] dark:bg-[#061F35] text-slate-800 antialiased">
    <!-- Sisi Kiri: Hero Background & Copywriting AP Consulting (Di mobile tampil di bawah) -->
    <div class="relative flex flex-col justify-between overflow-hidden bg-[#072642] px-6 py-10 sm:px-12 sm:py-16 lg:w-[55%] xl:w-[58%] lg:p-16 text-white min-h-[460px] lg:min-h-screen">
        <!-- Background Image & Crisp Blue-Navy Gradient Overlay -->
        <img
            src="{{ asset('images/redesign/hero-bg.png') }}"
            alt="AP Consulting Hero"
            aria-hidden="true"
            class="absolute inset-0 h-full w-full object-cover object-center pointer-events-none opacity-40 mix-blend-luminosity lg:opacity-100"
            loading="eager"
        />
        <div
            class="absolute inset-0 bg-gradient-to-br from-[#06233D]/98 via-[#083A63]/92 to-[#0869B2]/82 pointer-events-none"
        ></div>

        <!-- Top Identity Badge -->
        <div class="relative z-10">
            <div class="inline-flex items-center gap-3.5 rounded-[4px] bg-white/10 px-4 py-2 backdrop-blur-md border border-white/15 shadow-sm">
                <div class="bg-white px-2.5 py-1 rounded-[2px] shadow-xs">
                    <img
                        src="{{ asset('images/redesign/logo.png') }}"
                        alt="AP Consulting Logo"
                        class="h-7 w-auto object-contain"
                    />
                </div>
                <div class="text-left">
                    <span class="block text-xs font-bold tracking-wider text-white uppercase">AP Consulting</span>
                    <span class="block text-[11px] font-medium text-[#B9DBF4] leading-tight">Tax &amp; Consulting Services</span>
                </div>
            </div>
        </div>

        <!-- Center Editorial Copywriting -->
        <div class="relative z-10 my-8 lg:my-auto max-w-[580px] space-y-5">
            <p class="text-[11px] font-bold tracking-[0.16em] uppercase text-[#B9DBF4]">
                KANTOR KONSULTAN PAJAK BERBASIS HUKUM
            </p>

            <h1 class="text-3xl sm:text-4xl lg:text-[46px] font-bold leading-[1.16] tracking-[-0.03em] text-white">
                Clearer taxes.<br/>
                Move your business <span class="italic text-[#B9E2FF]">forward confidently.</span>
            </h1>

            <p class="text-[14.5px] sm:text-base text-[#E1EFFA] leading-relaxed">
                Kantor konsultan pajak berbasis hukum yang berpengalaman, profesional, dan berintegritas tinggi dalam membantu pemenuhan kewajiban perpajakan, payroll outsourcing, serta pembukuan akuntansi yang efektif di Indonesia.
            </p>

            <!-- Trust Badges & Official Credentials -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                <div class="flex items-start gap-3 rounded-[3px] bg-white/10 p-3.5 border border-white/15 backdrop-blur-sm">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[2px] bg-[#0869B2] text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-[#B9DBF4]">Izin Kemenkeu RI</div>
                        <div class="text-xs font-semibold text-white">KIP-6592/IP.B/PJ/2020</div>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-[3px] bg-white/10 p-3.5 border border-white/15 backdrop-blur-sm">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[2px] bg-[#0869B2] text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-[#B9DBF4]">Anggota Resmi</div>
                        <div class="text-xs font-semibold text-white">IKPI (Ikatan Konsultan Pajak)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Quote -->
        <div class="relative z-10 pt-6 border-t border-white/15 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-[#B9DBF4]">
            <span class="italic font-light">"Beyond numbers, it's about trust."</span>
            <span class="font-semibold uppercase tracking-wider text-[11px] text-white/90">Integrity · Excellence · Teamwork</span>
        </div>
    </div>

    <!-- Sisi Kanan: Form Login (Di mobile tampil di atas) -->
    <div class="flex flex-1 flex-col justify-between bg-white dark:bg-[#072642] px-6 py-10 sm:px-12 sm:py-16 lg:w-[45%] xl:w-[42%] lg:p-16 min-h-screen">
        <!-- Top Utility Header -->
        <div class="flex items-center justify-between">
            <span class="inline-flex items-center rounded-[2px] bg-[#EBF4FC] dark:bg-white/10 px-2.5 py-1 text-[11px] font-bold tracking-wider uppercase text-[#0869B2] dark:text-[#93C5FD]">
                Administrator Portal
            </span>
            <span class="text-xs text-[#8AA0B4]">
                v1.0
            </span>
        </div>

        <!-- Center Form Card -->
        <div class="my-auto mx-auto w-full max-w-[420px] py-8">
            <!-- Brand Logo & Heading -->
            <div class="mb-7">
                <div class="mb-5 inline-flex bg-[#061F35] p-2.5 rounded-[3px] shadow-sm">
                    <img
                        src="{{ asset('images/redesign/logo.png') }}"
                        alt="AP Consulting Logo"
                        class="h-9 w-auto object-contain"
                    />
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-[#092F50] dark:text-white sm:text-3xl">
                    Sign in
                </h2>
                <p class="mt-2 text-sm text-[#52687B] dark:text-[#A0B8CD] leading-relaxed">
                    Masukkan akun administrator Anda untuk mengelola konten dan sistem AP Consulting.
                </p>
            </div>

            <!-- Filament Form Component -->
            <div class="fi-login-form-wrapper">
                {{ $this->content }}
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-6 border-t border-[#E3EBF3] dark:border-white/10 text-center text-xs text-[#71879B] dark:text-[#8AA3B8]">
            Copyright &copy; {{ date('Y') }} AP Consulting. All Rights Reserved.
        </div>
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif
</div>
