import { describe, expect, it } from 'vitest'
import { useLanguage } from '../app/composables/useLanguage'

describe('useLanguage composable', () => {
  it('defaults to English locale', () => {
    const { currentLocale, currentLanguage, t } = useLanguage()
    expect(currentLocale.value).toBe('en')
    expect(currentLanguage.value.shortLabel).toBe('EN')
    expect(t('nav.home')).toBe('Home')
  })

  it('switches language to Indonesian', () => {
    const { currentLocale, setLanguage, t } = useLanguage()
    setLanguage('id')
    expect(currentLocale.value).toBe('id')
    expect(t('nav.home')).toBe('Beranda')
    expect(t('nav.services')).toBe('Layanan')
    expect(t('nav.contact')).toBe('Kontak')
  })

  it('toggles language back and forth', () => {
    const { currentLocale, toggleLanguage, setLanguage } = useLanguage()
    setLanguage('en')
    expect(currentLocale.value).toBe('en')

    toggleLanguage()
    expect(currentLocale.value).toBe('id')

    toggleLanguage()
    expect(currentLocale.value).toBe('en')
  })
})
