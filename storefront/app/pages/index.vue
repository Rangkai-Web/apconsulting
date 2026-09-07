<template>
  <div />
</template>

<script setup lang="ts"></script>
        <div
          v-for="testi in testimonials"
          :key="testi.name"
          class="bg-white/70 border border-primary-300/40 rounded-2xl p-6 shadow-xs space-y-4"
        >
          <div class="flex items-center gap-1">
            <UIcon
              v-for="star in testi.rating"
              :key="star"
              name="i-heroicons-star-solid"
              class="w-4 h-4 text-primary-400"
            />
          </div>
          <p class="text-xs text-gray-600 font-bold leading-relaxed mb-0">
            "{{ testi.text }}"
          </p>
          <div
            class="flex items-center gap-2.5 pt-2 border-t border-primary-50/40"
          >
            <div
              class="w-7.5 h-7.5 rounded-full bg-linear-to-tr from-primary-500 to-primary-400 border-2 border-white flex items-center justify-center text-white text-[11px] font-black uppercase shadow-xs shrink-0"
            >
              {{ testi.name.charAt(0) }}
            </div>
            <span class="text-xs font-extrabold text-gray-800">{{
              testi.name
            }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Accordion FAQ Section -->
    <section
      id="faq"
      class="max-w-4xl mx-auto px-4 py-12 md:py-16 relative z-20"
    >
      <div class="text-center mb-8">
        <h2 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
          Pertanyaan Populer
        </h2>
        <p class="text-xs text-gray-400 font-semibold mt-1">
          Temukan jawaban cepat atas pertanyaan Anda
        </p>
      </div>

      <div
        class="bg-white/80 border border-primary-100/50 rounded-3xl p-6 shadow-xs"
      >
        <UAccordion
          :items="faqs"
          :ui="{
            root: 'w-full space-y-2',
            item: 'border border-primary-50 rounded-xl overflow-hidden shadow-xs bg-primary-50/5/20',
            trigger:
              'py-4 px-5 text-left text-xs font-bold text-gray-800 hover:text-primary-500 focus-visible:outline-none flex justify-between items-center transition-colors',
            content:
              'py-4 px-5 text-xs text-gray-500 leading-relaxed font-semibold bg-white border-t border-primary-50/30',
          }"
        >
          <template #content="{ item }">
            <div class="space-y-2">
              <p>{{ item.content }}</p>
              <div v-if="item.link" class="pt-1">
                <a
                  :href="item.link.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1.5 font-bold text-primary-600 hover:text-primary-700 underline break-all"
                >
                  <span>{{ item.link.label || item.link.url }}</span>
                  <UIcon
                    name="i-heroicons-arrow-top-right-on-square"
                    class="w-3.5 h-3.5 shrink-0"
                  />
                </a>
              </div>
            </div>
          </template>
        </UAccordion>

        <div class="mt-6 text-center">
          <UButton
            to="/faq"
            color="primary"
            variant="soft"
            class="font-extrabold text-xs sm:text-sm px-6 py-2.5 rounded-full hover:scale-105 transition-all duration-300 cursor-pointer inline-flex items-center gap-1.5"
          >
            Lihat Semua Pertanyaan & Panduan (FAQ)
            <UIcon name="i-heroicons-arrow-right" class="w-4 h-4" />
          </UButton>
        </div>
      </div>
    </section>

    <!-- Bottom CTA Banner Section -->
    <section class="max-w-5xl mx-auto px-4 pb-16 relative z-20">
      <div
        class="bg-primary-50/80 border border-primary-100/80 rounded-3xl p-8 md:p-12 text-center space-y-6 shadow-xs relative overflow-hidden"
      >
        <div
          class="absolute -top-10 -left-10 w-40 h-40 bg-primary-200/10 rounded-full blur-2xl"
        ></div>
        <div
          class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary-100/10 rounded-full blur-2xl"
        ></div>
        <h3 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
          Siap Memulai Belanja?
        </h3>
        <p
          class="text-xs text-gray-500 font-semibold max-w-md mx-auto leading-relaxed"
        >
          Temukan gantungan kunci, stiker, kaos, tas, dan aksesoris imut lainnya
          langsung di katalog kami.
        </p>
        <div class="flex justify-center pt-2">
          <UButton
            to="/katalog"
            color="primary"
            variant="solid"
            class="font-extrabold text-xs sm:text-sm px-8 py-3 rounded-full shadow-lg shadow-primary-500/15 hover:scale-105 transition-all duration-300 cursor-pointer"
          >
            Belanja Sekarang
          </UButton>
        </div>
      </div>
    </section>
  </div>
</template>
