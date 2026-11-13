import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import About from '../../app/components/home/About.vue'

describe('Home About', () => {
  it('renders the about preview narrative and CTA', () => {
    const wrapper = mount(About, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' } },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('ABOUT AP CONSULTING')
    expect(text).toContain('Tax expertise. A partnership you can rely on.')
    expect(text).toContain('Get to know AP Consulting')
  })
})
