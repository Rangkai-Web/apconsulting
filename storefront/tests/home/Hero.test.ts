import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Hero from '../../app/components/home/Hero.vue'

describe('Home Hero', () => {
  it('renders the headline, body copy, and CTAs', () => {
    const wrapper = mount(Hero, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' } },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('AP CONSULTING')
    expect(text).toContain('Clearer taxes')
    expect(text).toContain('Professional tax and accounting support')
    expect(text).toContain('Discuss your needs')
  })
})

