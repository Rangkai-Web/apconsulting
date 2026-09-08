import { ref, computed, type Ref } from 'vue'

export type Locale = 'en' | 'id'

export interface LanguageOption {
  code: Locale
  label: string
  shortLabel: string
  flag: string
}

export const LANGUAGES: LanguageOption[] = [
  {
    code: 'en',
    label: 'English',
    shortLabel: 'EN',
    flag: '/images/icon/UnitedFlagIcon.svg'
  },
  {
    code: 'id',
    label: 'Bahasa Indonesia',
    shortLabel: 'ID',
    flag: '/images/icon/IndonesiaFlagIcon.svg'
  }
]

const translations: Record<Locale, Record<string, string>> = {
  en: {
    'nav.home': 'Home',
    'nav.services': 'Services',
    'nav.insights': 'Insights',
    'nav.about': 'About',
    'nav.contact': 'Contact',
    'nav.discussNeeds': 'Discuss Your Needs',
    'cta.tag': 'START A CONVERSATION',
    'cta.titleNormal': 'Ready to bring more clarity to your business?',
    'cta.titleItalic': "Let's take the next step together.",
    'cta.description': 'Tell us about your tax, accounting, or financial needs. PCG will help you identify a practical starting point for your business.',
    'cta.buttonText': 'Discuss Your Needs',
    'footer.tagline': 'Professional support in tax, accounting, and financial services. We\'re helping businesses move forward with clarity and confidence.',
    'footer.rights': 'All Rights Reserved',
    'footer.quickLinks': 'Quick Links',
    'footer.services': 'Services',
    'footer.contactUs': 'Contact Us'
  },
  id: {
    'nav.home': 'Beranda',
    'nav.services': 'Layanan',
    'nav.insights': 'Wawasan',
    'nav.about': 'Tentang Kami',
    'nav.contact': 'Kontak',
    'nav.discussNeeds': 'Diskusikan Kebutuhan Anda',
    'cta.tag': 'MULAI PERCAKAPAN',
    'cta.titleNormal': 'Siap memberikan kejelasan lebih pada bisnis Anda?',
    'cta.titleItalic': 'Mari ambil langkah berikutnya bersama.',
    'cta.description': 'Beritahu kami tentang kebutuhan pajak, akuntansi, atau keuangan Anda. PCG akan membantu Anda mengidentifikasi titik awal yang praktis untuk bisnis Anda.',
    'cta.buttonText': 'Diskusikan Kebutuhan Anda',
    'footer.tagline': 'Dukungan profesional dalam layanan perpajakan, akuntansi, dan keuangan. Kami membantu bisnis melangkah maju dengan kejelasan dan rasa percaya diri.',
    'footer.rights': 'Hak Cipta Dilindungi',
    'footer.quickLinks': 'Tautan Cepat',
    'footer.services': 'Layanan',
    'footer.contactUs': 'Hubungi Kami'
  }
}

const fallbackState: Record<string, Ref<any>> = {}

function getLocaleState<T>(key: string, init: () => T): Ref<T> {
  try {
    if (typeof useState === 'function') {
      return useState<T>(key, init)
    }
  } catch {
    // ignore if useState is unavailable in unit test context
  }
  let state = fallbackState[key]
  if (!state) {
    state = ref(init())
    fallbackState[key] = state
  }
  return state as Ref<T>
}

export function useLanguage() {
  const currentLocale = getLocaleState<Locale>('currentLocale', () => 'en')

  const currentLanguage = computed(() => {
    return LANGUAGES.find((l) => l.code === currentLocale.value) || LANGUAGES[0]
  })

  function setLanguage(locale: Locale) {
    currentLocale.value = locale
  }

  function toggleLanguage() {
    currentLocale.value = currentLocale.value === 'en' ? 'id' : 'en'
  }

  function t(key: string, fallback?: string): string {
    const dict = translations[currentLocale.value] || translations.en
    return dict[key] ?? translations.en[key] ?? fallback ?? key
  }

  return {
    currentLocale,
    currentLanguage,
    setLanguage,
    toggleLanguage,
    t,
    languages: LANGUAGES
  }
}
