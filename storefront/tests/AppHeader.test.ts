import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AppHeader from '../app/components/AppHeader.vue'

describe('AppHeader', () => {
  it('renders all primary nav links and the CTA', () => {
    const wrapper = mount(AppHeader)
    const text = wrapper.text()
    expect(text).toContain('Home')
    expect(text).toContain('Services')
    expect(text).toContain('Insights')
    expect(text).toContain('About')
    expect(text).toContain('Contact')
    expect(text).toContain('Discuss Your Needs')
  })

  it('toggles the mobile menu open state when the hamburger is clicked', async () => {
    const wrapper = mount(AppHeader)
    expect(wrapper.find('[data-testid="mobile-menu"]').exists()).toBe(false)
    await wrapper.find('[data-testid="hamburger-button"]').trigger('click')
    expect(wrapper.find('[data-testid="mobile-menu"]').exists()).toBe(true)
  })
})
