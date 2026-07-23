<script setup lang="ts">
import { useApi } from "~/services/api";

const api = useApi();
const { data: settingsResponse } = useAsyncData("store-settings-about", () =>
  api.getSettings(),
);

const settings = computed(() => settingsResponse.value?.data || {});

const defaultContent = `
  <h2>Doctor Plant &mdash; Merek Skincare Asal Tiongkok, Berdiri Sejak 1994</h2>
  <p>Doctor Plant (植物医生) adalah merek perawatan kulit asal Tiongkok yang berdiri sejak 1994, berfokus pada kecantikan kulit sehat alami berbahan dasar tumbuhan alpine. Selama lebih dari 30 tahun, kami bertransformasi dari toko ritel produk skincare berbahan tumbuhan menjadi jaringan retail franchise yang diakui sebagai toko single-brand skincare terkemuka di dunia.</p>

  <h3>Alpine Plants, Pure Beauty</h3>
  <p>Bahan aktif andalan kami adalah ekstrak Dendrobium (anggrek batang bulan), salah satu dari sembilan tanaman herbal keabadian dalam pengobatan tradisional Tiongkok, yang secara turun-temurun dikenal sebagai "tanaman dokter". Melalui riset ilmiah modern, kandungan aktif Dendrobium ini diverifikasi dan diolah menjadi Dendrobium Active Extract &mdash; bahan utama di balik produk perawatan kulit kami.</p>
  <p>Diakui Euromonitor International sebagai toko single-brand skincare terkemuka di dunia, sekaligus peringkat pertama secara global dalam penjualan ritel produk skincare berbahan Dendrobium.</p>

  <h3>Filosofi Brand Kami</h3>
  <ol>
    <li><strong>Tumbuhan Alpine</strong> &mdash; representasi kulit yang murni dan cantik, salah satu dari sembilan tanaman herbal keabadian Tiongkok.</li>
    <li><strong>Penggunaan Tradisional</strong> &mdash; Dendrobium dikenal sebagai "tanaman dokter" dalam budaya Tiongkok.</li>
    <li><strong>Riset & Verifikasi Ilmiah Modern</strong> &mdash; menghasilkan Dendrobium Active Extract.</li>
    <li><strong>Budidaya & Ekstraksi Organik</strong> &mdash; dilakukan di Kota Pu'er, Yunnan, Tiongkok.</li>
    <li><strong>Hasil yang Terbukti</strong> &mdash; menjadi pemimpin pasar di kategorinya.</li>
    <li><strong>Kembali ke Masyarakat</strong> &mdash; mendukung penanaman komunitas dan pelestarian tumbuhan asli.</li>
  </ol>

  <h3>Riset &amp; Pengembangan</h3>
  <p>Doctor Plant menjalin kerja sama riset bahan aktif dengan Chinese Academy of Sciences (Kunming Institute of Botany) serta berbagai institusi riset di Tiongkok, menjadikan kami merek nomor satu dalam riset dan pengembangan bahan aktif skincare di Tiongkok.</p>
`;

const aboutHtml = computed(
  () => settings.value.about_content || defaultContent,
);

useSeoMeta({
  title: "Tentang Kami - Doctor Plant Indonesia",
  description:
    "Doctor Plant, merek skincare asal Tiongkok berbahan dasar tumbuhan alpine, berdiri sejak 1994.",
});
</script>

<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-8">
      Tentang Kami
    </h1>
    <div class="about-content text-gray-700 leading-relaxed" v-html="aboutHtml" />
  </div>
</template>

<style scoped>
.about-content :deep(h2) {
  font-size: 1.5rem;
  font-weight: 800;
  color: #111827;
  margin-top: 2rem;
  margin-bottom: 0.75rem;
}
.about-content :deep(h2:first-child) {
  margin-top: 0;
}
.about-content :deep(h3) {
  font-size: 1.125rem;
  font-weight: 700;
  color: #111827;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
}
.about-content :deep(p) {
  margin-bottom: 1rem;
}
.about-content :deep(ul),
.about-content :deep(ol) {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
}
.about-content :deep(ul) {
  list-style-type: disc;
}
.about-content :deep(ol) {
  list-style-type: decimal;
}
.about-content :deep(li) {
  margin-bottom: 0.5rem;
}
</style>
