import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Cta from '../../app/components/home/Cta.vue'

describe('Home Cta', () => {
  it('renders the headline, body, and button', () => {
    const wrapper = mount(Cta, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' } },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('LET’S TALK')
    expect(text).toContain('Ready to bring')
    expect(text).toContain('clarity')
    expect(text).toContain('to your tax and accounting?')
    expect(text).toContain('Schedule a consultation')
  })
})


