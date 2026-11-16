<template>
  <div class="w-full bg-white font-sans text-[#092F50] antialiased">
    <HomeHero />
    <HomeServices />
    <HomeSectors />
    <HomeAbout />
    <HomeValues />
    <HomeApproach />
    <HomeInsights />
    <HomeFaq />
    <HomeCta />
  </div>
</template>

<script setup lang="ts">
import { useSeoText } from "~/composables/useSeoText";

const config = useRuntimeConfig();
const { toSafeJsonLd } = useSeoText();
const canonicalUrl = `${config.public.siteUrl}/`;

// Comprehensive SEO Metadata
const title = "AP Consulting — Tax & Consulting Services";
const description =
  "Kantor konsultan pajak berbasis hukum, akuntansi, dan payroll outsourcing profesional di Indonesia. Dikelola oleh konsultan berizin resmi Kemenkeu & anggota IKPI.";

useSeoMeta({
  title,
  ogTitle: title,
  description,
  ogDescription: description,
  ogImage: `${config.public.siteUrl}/images/redesign/logo.png`,
  ogUrl: canonicalUrl,
  twitterCard: "summary_large_image",
  twitterTitle: title,
  twitterDescription: description,
  twitterImage: `${config.public.siteUrl}/images/redesign/logo.png`,
});

useHead({
  link: [
    { rel: "canonical", href: canonicalUrl },
    { rel: "alternate", hreflang: "id", href: `${config.public.siteUrl}/` },
    { rel: "alternate", hreflang: "en", href: `${config.public.siteUrl}/` },
    { rel: "alternate", hreflang: "x-default", href: `${config.public.siteUrl}/` },
  ],
  script: [
    {
      type: "application/ld+json",
      innerHTML: toSafeJsonLd({
        "@context": "https://schema.org",
        "@graph": [
          {
            "@type": ["AccountingService", "FinancialService"],
            "@id": `${config.public.siteUrl}/#organization`,
            name: "AP Consulting",
            alternateName: [
              "AP Tax Consulting Services",
              "Kantor Konsultan Pajak AP Consulting",
              "Konsultan Pajak Andry Phangkawira",
            ],
            url: config.public.siteUrl,
            logo: `${config.public.siteUrl}/images/redesign/logo.png`,
            image: `${config.public.siteUrl}/images/redesign/office.png`,
            description,
            email: "contact@apconsulting.id",
            telephone: "+6285754172217",
            priceRange: "$$",
            currenciesAccepted: "IDR",
            paymentAccepted: "Bank Transfer",
            address: [
              {
                "@type": "PostalAddress",
                name: "Head Office Jakarta",
                streetAddress: "SUN Plaza, Jl. Meruya Ilir Raya No. 20",
                addressLocality: "Jakarta Barat",
                addressRegion: "DKI Jakarta",
                postalCode: "11620",
                addressCountry: "ID",
              },
              {
                "@type": "PostalAddress",
                name: "Branch Office Tangerang",
                streetAddress: "Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong",
                addressLocality: "Tangerang",
                addressRegion: "Banten",
                postalCode: "15810",
                addressCountry: "ID",
              },
            ],
            founder: {
              "@type": "Person",
              name: "Andry Phangkawira, S.E., Ak., BKP.",
              jobTitle: "Partner & Licensed Tax Consultant",
              identifier: "KIP-6592/IP.B/PJ/2020",
              sameAs: "https://www.linkedin.com/in/andry-phangkawira/",
            },
            memberOf: {
              "@type": "Organization",
              name: "Ikatan Konsultan Pajak Indonesia (IKPI)",
            },
            hasOfferCatalog: {
              "@type": "OfferCatalog",
              name: "Layanan AP Consulting",
              itemListElement: [
                {
                  "@type": "Offer",
                  itemOffered: {
                    "@type": "Service",
                    name: "Jasa Perpajakan (Tax Services)",
                    description: "Tax Compliance, Penelaahan Pajak (Tax Review), dan Pendampingan Pemeriksaan Pajak (Tax Audit).",
                  },
                },
                {
                  "@type": "Offer",
                  itemOffered: {
                    "@type": "Service",
                    name: "Jasa Payroll Outsourcing",
                    description: "Administrasi HR, pendaftaran BPJS, penghitungan PPh 21, dan pembuatan e-Payslip.",
                  },
                },
                {
                  "@type": "Offer",
                  itemOffered: {
                    "@type": "Service",
                    name: "Jasa Akuntansi (Accounting Services)",
                    description: "Penyusunan Laporan Keuangan PT/CV/UMKM, Accounting Review, Internal Audit, dan Konsultasi Software.",
                  },
                },
              ],
            },
          },
          {
            "@type": "WebSite",
            "@id": `${config.public.siteUrl}/#website`,
            url: config.public.siteUrl,
            name: "AP Consulting",
            publisher: {
              "@id": `${config.public.siteUrl}/#organization`,
            },
            inLanguage: ["id-ID", "en-US"],
          },
          {
            "@type": "FAQPage",
            "@id": `${config.public.siteUrl}/#faq`,
            mainEntity: [
              {
                "@type": "Question",
                name: "Jenis bisnis apa saja yang didampingi oleh AP Consulting?",
                acceptedAnswer: {
                  "@type": "Answer",
                  text: "Kami melayani beragam klien di seluruh Indonesia — mulai dari UMKM yang sedang berkembang dan startup hingga perusahaan nasional serta perusahaan penanaman modal asing (PT PMA) yang membutuhkan dukungan perpajakan, payroll, dan akuntansi yang komprehensif.",
                },
              },
              {
                "@type": "Question",
                name: "Layanan apa saja yang disediakan oleh AP Consulting?",
                acceptedAnswer: {
                  "@type": "Answer",
                  text: "Kami menyediakan tiga pilar layanan utama: Jasa Perpajakan (kepatuhan SPT Masa/Tahunan, penelaahan pajak, dan pendampingan audit), Jasa Payroll Outsourcing (penggajian, BPJS, PPh 21, dan e-payslip), serta Jasa Akuntansi (laporan keuangan, review akuntansi, dan audit internal).",
                },
              },
              {
                "@type": "Question",
                name: "Bagaimana AP Consulting membantu penanganan sengketa atau pemeriksaan pajak?",
                acceptedAnswer: {
                  "@type": "Answer",
                  text: "Kami mendampingi dan mewakili klien sepanjang proses pemeriksaan — mulai dari penyusunan penjelasan atas surat permintaan penjelasan data (SP2DK), penyiapan rekonsiliasi dan pembuktian dokumen, hingga proses keberatan dan banding di Pengadilan Pajak.",
                },
              },
              {
                "@type": "Question",
                name: "Bagaimana cara memulai konsultasi dengan AP Consulting?",
                acceptedAnswer: {
                  "@type": "Answer",
                  text: "Cukup hubungi tim kami melalui formulir kontak di website, WhatsApp (+62 857 5417 2217), atau email (contact@apconsulting.id) untuk menjadwalkan konsultasi awal.",
                },
              },
            ],
          },
        ],
      }),
    },
  ],
});
</script>
