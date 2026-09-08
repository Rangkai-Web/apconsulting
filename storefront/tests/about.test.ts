import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AboutHero from '../app/components/about/Hero.vue'

describe('AboutHero', () => {
  it('renders the headline, description, and CTAs', () => {
    const wrapper = mount(AboutHero)
    const text = wrapper.text()
    expect(text).toContain('More than accountants.')
    expect(text).toContain('Partners in your progress.')
    expect(text).toContain('PCG helps businesses make confident financial decisions.')
    expect(text).toContain('DISCOVER OUR APPROACH')
    expect(text).toContain('MEET THE TEAM')
  })
})

