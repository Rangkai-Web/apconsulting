import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import WhyChooseUs from '../../app/components/home/WhyChooseUs.vue'

describe('Home WhyChooseUs', () => {
  it('renders the headline and both service overlay cards', () => {
    const wrapper = mount(WhyChooseUs)
    const text = wrapper.text()
    expect(text).toContain('WHY BUSINESSES CHOOSE US')
    expect(text).toContain('The right support')
    expect(text).toContain('Tax Advisory')
    expect(text).toContain('Manage obligations, identify risks, and make more informed tax decisions.')
    expect(text).toContain('Finance & Consulting')
    expect(text).toContain('Use financial visibility to plan ahead and build sustainable growth.')
  })
})
