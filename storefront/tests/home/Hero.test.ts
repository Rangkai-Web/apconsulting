import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Hero from '../../app/components/home/Hero.vue'

describe('Home Hero', () => {
  it('renders the headline, body copy, and both CTAs', () => {
    const wrapper = mount(Hero)
    const text = wrapper.text()
    expect(text).toContain('CLEARER FINANCES.')
    expect(text).toContain('STRONGER BUSINESS DECISIONS')
    expect(text).toContain('DECISIONS.')
    expect(text).toContain('PCG helps businesses manage tax, accounting, bookkeeping, and other financial needs with a structured, professional approach tailored to their goals')
    expect(text).toContain('EXPLORE OUR SERVICES')
    expect(text).toContain('DISCUSS YOUR NEEDS')
  })
})
