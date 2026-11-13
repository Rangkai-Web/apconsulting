import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AppFooter from '../app/components/AppFooter.vue'

describe('AppFooter', () => {
  it('renders contact details and the copyright line', () => {
    const wrapper = mount(AppFooter, {
      global: {
        stubs: {
          NuxtLink: { template: '<a><slot /></a>' },
          UIcon: true,
          LanguageSwitcher: true,
        },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('contact@apconsulting.id')
    expect(text).toContain('SUN Plaza')
    expect(text).toContain('Gading Serpong')
    expect(text).toContain('Copyright © 2026 AP Consulting. All Rights Reserved')
  })

  it('lists the core services', () => {
    const wrapper = mount(AppFooter, {
      global: {
        stubs: {
          NuxtLink: { template: '<a><slot /></a>' },
          UIcon: true,
          LanguageSwitcher: true,
        },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('Tax Services')
    expect(text).toContain('Payroll Outsourcing Services')
    expect(text).toContain('Tax Accounting Services')
  })
})


