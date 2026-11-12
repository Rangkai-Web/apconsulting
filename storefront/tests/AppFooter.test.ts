import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AppFooter from '../app/components/AppFooter.vue'

describe('AppFooter', () => {
  it('renders contact details and the copyright line', () => {
    const wrapper = mount(AppFooter)
    const text = wrapper.text()
    expect(text).toContain('info@apconsulting.id')
    expect(text).toContain('Kota Tangerang, Banten 15146')
    expect(text).toContain('Copyright © 2026 AP Consulting. All Rights Reserved')
  })

  it('lists all four services', () => {
    const wrapper = mount(AppFooter)
    const text = wrapper.text()
    expect(text).toContain('Taxation')
    expect(text).toContain('Accounting & Bookkeeping')
    expect(text).toContain('Financial Services')
    expect(text).toContain('Business')
  })
})
