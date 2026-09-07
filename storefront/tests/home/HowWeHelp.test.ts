import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import HowWeHelp from '../../app/components/home/HowWeHelp.vue'

describe('Home HowWeHelp', () => {
  it('renders the headline and all three cards', () => {
    const wrapper = mount(HowWeHelp)
    const text = wrapper.text()
    expect(text).toContain('HOW WE HELP')
    expect(text).toContain('Your Business Deserves')
    expect(text).toContain('More Than Routine Bookkeeping.')
    expect(text).toContain('Compliance with confidence.')
    expect(text).toContain('Build a Stronger Financial Foundation')
    expect(text).toContain('Insight for better decisions.')
  })
})
