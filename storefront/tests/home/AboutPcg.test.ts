import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AboutPcg from '../../app/components/home/AboutPcg.vue'

describe('Home AboutPcg', () => {
  it('renders the headline and CTA', () => {
    const wrapper = mount(AboutPcg)
    const text = wrapper.text()
    expect(text).toContain('ABOUT AP CONSULTING')
    expect(text).toContain('More than accountants.')
    expect(text).toContain('Partners in your progress.')
    expect(text).toContain('DISCOVER HOW WE HELP')
  })
})
