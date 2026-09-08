import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import LanguageSwitcher from '../app/components/LanguageSwitcher.vue'

describe('LanguageSwitcher', () => {
  it('renders active language button and flag', () => {
    const wrapper = mount(LanguageSwitcher)
    const button = wrapper.find('[data-testid="language-switcher-button"]')
    expect(button.exists()).toBe(true)
    expect(button.text()).toContain('EN')
    expect(wrapper.find('img').attributes('src')).toBe('/images/icon/UnitedFlagIcon.svg')
  })

  it('opens dropdown when clicked and updates language', async () => {
    const wrapper = mount(LanguageSwitcher)
    expect(wrapper.find('[data-testid="language-dropdown"]').exists()).toBe(false)

    const button = wrapper.find('[data-testid="language-switcher-button"]')
    await button.trigger('click')

    expect(wrapper.find('[data-testid="language-dropdown"]').exists()).toBe(true)
    expect(wrapper.text()).toContain('English')
    expect(wrapper.text()).toContain('Bahasa Indonesia')

    const idOption = wrapper.find('[data-testid="lang-option-id"]')
    await idOption.trigger('click')

    expect(button.text()).toContain('ID')
    expect(wrapper.find('img').attributes('src')).toBe('/images/icon/IndonesiaFlagIcon.svg')
  })
})
