import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Services from '../../app/components/home/Services.vue'

describe('Home Services', () => {
  it('renders the services section and cards', () => {
    const wrapper = mount(Services, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' } },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('OUR EXPERTISE')
    expect(text).toContain('Tax Services')
    expect(text).toContain('Payroll Outsourcing')
    expect(text).toContain('Accounting Services')
  })
})

