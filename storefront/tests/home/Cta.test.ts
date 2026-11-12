import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Cta from '../../app/components/home/Cta.vue'

describe('Home Cta', () => {
  it('renders the headline, body, and button', () => {
    const wrapper = mount(Cta)
    const text = wrapper.text()
    expect(text).toContain('START A CONVERSATION')
    expect(text).toContain('Ready to bring more clarity to your business?')
    expect(text).toContain("Let's take the next step together.")
    expect(text).toContain('Tell us about your tax, accounting, or financial needs. AP Consulting will help you identify a practical starting point for your business.')
    expect(text).toContain('Discuss Your Needs')
  })
})
