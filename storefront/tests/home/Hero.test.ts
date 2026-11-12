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
    expect(text).toContain('AP Consulting is a legal-based tax consulting firm with deep experience, professionalism, and high integrity in assisting clients (both corporate entities and individuals) to fulfill their tax obligations effectively.')
    expect(text).toContain('EXPLORE OUR SERVICES')
    expect(text).toContain('DISCUSS YOUR NEEDS')
  })
})
