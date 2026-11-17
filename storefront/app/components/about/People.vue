<template>
  <section
    id="people"
    class="scroll-mt-20 bg-white py-12 sm:py-16 lg:py-20"
  >
    <div class="mx-auto max-w-[1240px] px-5 sm:px-6 lg:px-8">
      <!-- Centered Editorial Header -->
      <div class="mx-auto max-w-[680px] text-center mb-10 sm:mb-12" data-reveal="up">
        <p
          class="text-[11px] font-bold tracking-[0.14em] uppercase text-[#0869B2] mb-2"
        >
          {{ t("about.people.tag") }}
        </p>
        <h2
          class="text-2xl sm:text-4xl font-semibold leading-[1.18] tracking-[-0.03em] text-[#092F50] mb-3"
        >
          <span class="font-bold">{{ t("about.people.titleNormal") }}</span>
          {{ " " }}
          <span class="font-bold italic text-[#0869B2]">{{
            t("about.people.titleItalic")
          }}</span>
        </h2>
        <p class="text-sm sm:text-[15.5px] leading-[1.65] text-[#52687B]">
          {{ t("about.people.description") }}
        </p>
      </div>

      <!-- Skeleton Loading State -->
      <div
        v-if="pending"
        class="mx-auto grid max-w-[960px] gap-6 sm:grid-cols-2 lg:grid-cols-3"
      >
        <div
          v-for="n in 3"
          :key="n"
          class="h-[360px] w-full animate-pulse bg-[#F4F9FE] border border-[#DCE8F5] rounded-[2px]"
        />
      </div>

      <!-- Team Grid (API Data or AP Consulting Leadership Fallback) -->
      <div v-else :class="containerClasses">
        <article
          v-for="(person, pIdx) in displayedPeople"
          :key="person.id"
          class="group hover-lift flex flex-col bg-[#F8FBFF] rounded-[2px] border border-[#DCE8F5] hover:border-[#0869B2] transition-colors duration-200 overflow-hidden"
          data-reveal="up"
          :data-reveal-delay="pIdx * 120"
        >
          <!-- Photo -->
          <div class="relative w-full h-[260px] sm:h-[290px] bg-[#EBF3FA] overflow-hidden border-b border-[#DCE8F5]">
            <img
              :src="resolvePersonPhoto(person.photo, person.name)"
              :alt="person.name"
              loading="lazy"
              decoding="async"
              class="size-full object-cover object-top transition-transform duration-500 group-hover:scale-105"
            />
          </div>

          <!-- Profile Info -->
          <div class="p-6 text-center flex flex-col flex-grow justify-between items-center bg-white">
            <div>
              <h3 class="text-lg sm:text-[19px] font-bold text-[#092F50] leading-snug">
                {{ person.name }}
              </h3>
              <p class="text-xs sm:text-sm font-semibold text-[#0869B2] mt-1">
                {{ person.position }}
              </p>
              <p
                v-if="person.bio"
                class="mt-2.5 text-xs sm:text-[13.5px] text-[#52687B] leading-[1.6] line-clamp-3"
              >
                {{ person.bio }}
              </p>
            </div>

            <div
              v-if="hasSocials(person)"
              class="mt-5 pt-4 border-t border-[#F0F6FC] w-full flex flex-wrap items-center justify-center gap-2.5"
            >
              <a
                v-for="social in getSocials(person)"
                :key="social.name"
                :href="social.url"
                target="_blank"
                rel="noopener noreferrer"
                :aria-label="`${person.name}'s ${social.name}`"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[2px] bg-[#F0F7FF] text-[#0869B2] hover:bg-[#0869B2] hover:text-white text-xs font-semibold transition-colors"
              >
                <UIcon :name="social.icon" class="size-3.5" />
                <span>{{ social.name === "LinkedIn" ? t("about.people.seeProfile") : social.name }}</span>
              </a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useLanguage } from "~/composables/useLanguage";
import { getApiBaseUrl, resolveImageUrl, safeUseFetch } from "~/utils/api";

interface TeamMember {
  id: number | string;
  name: string;
  position: string;
  photo?: string;
  bio?: string;
  linkedin_url?: string;
  instagram_url?: string;
  tiktok_url?: string;
  threads_url?: string;
  facebook_url?: string;
  twitter_url?: string;
  youtube_url?: string;
}

const { t } = useLanguage();
const apiBase = getApiBaseUrl();

const { data: apiResponse, pending } = safeUseFetch<{ data: TeamMember[] }>(
  `${apiBase}/team`,
  {
    key: "team-list",
  }
);

const fallbackLeadership = computed<TeamMember[]>(() => [
  {
    id: "leadership-andry",
    name: "Andry Phangkawira, S.E., Ak., BKP.",
    position: "Partner",
    photo: "https://www.apconsulting.id/images/team/andry.jpeg",
    bio: t("about.people.licenseNote"),
    linkedin_url: "https://www.linkedin.com/in/andry-phangkawira/",
  },
]);

const displayedPeople = computed(() => {
  const items = apiResponse.value?.data || [];
  return items.length > 0 ? items : fallbackLeadership.value;
});

function resolvePersonPhoto(photo?: string, name?: string): string {
  if (photo && (photo.startsWith("http://") || photo.startsWith("https://") || photo.startsWith("/images/"))) {
    return photo;
  }
  if (photo) {
    return resolveImageUrl(photo, "https://www.apconsulting.id/images/team/andry.jpeg");
  }
  return "https://www.apconsulting.id/images/team/andry.jpeg";
}

const containerClasses = computed(() => {
  const count = displayedPeople.value.length;
  if (count === 1) {
    return "mx-auto grid max-w-[360px] gap-6 grid-cols-1";
  }
  if (count === 2) {
    return "mx-auto grid max-w-[740px] gap-6 grid-cols-1 sm:grid-cols-2";
  }
  if (count === 3) {
    return "mx-auto grid max-w-[1060px] gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3";
  }
  return "mx-auto grid max-w-[1240px] gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4";
});

function formatExternalUrl(url?: string): string {
  if (!url) return "";
  const trimmed = url.replace(/[\u00a0\s]+/g, "").trim();
  if (/^https?:\/\//i.test(trimmed)) {
    return trimmed;
  }
  return `https://${trimmed}`;
}

function getSocials(person: TeamMember) {
  const linkedinUrl = person.linkedin_url || (person.name?.toLowerCase().includes("andry")
    ? "https://www.linkedin.com/in/andry-phangkawira/"
    : undefined);

  const list = [
    {
      name: "LinkedIn",
      icon: "i-simple-icons-linkedin",
      url: linkedinUrl,
    },
    {
      name: "Instagram",
      icon: "i-simple-icons-instagram",
      url: person.instagram_url,
    },
    { name: "TikTok", icon: "i-simple-icons-tiktok", url: person.tiktok_url },
    { name: "Threads", icon: "i-simple-icons-threads", url: person.threads_url },
    { name: "X", icon: "i-simple-icons-x", url: person.twitter_url },
    { name: "Facebook", icon: "i-simple-icons-facebook", url: person.facebook_url },
    { name: "YouTube", icon: "i-simple-icons-youtube", url: person.youtube_url },
  ];
  return list
    .filter(
      (item) =>
        typeof item.url === "string" &&
        item.url.replace(/[\u00a0\s]+/g, "").trim() !== "" &&
        item.url !== "#"
    )
    .map((item) => ({
      ...item,
      url: formatExternalUrl(item.url),
    }));
}

function hasSocials(person: TeamMember) {
  return getSocials(person).length > 0;
}
</script>
