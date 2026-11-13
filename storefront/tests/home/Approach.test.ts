import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Approach from '../../app/components/home/Approach.vue'

describe('Home Approach', () => {
  it('renders all four steps in order', () => {
    const wrapper = mount(Approach)
    const text = wrapper.text()
    expect(text).toContain('OUR APPROACH')
    expect(text).toContain('How we work')
    expect(text).toContain('01')
    expect(text).toContain('Understand Your Business')
    expect(text).toContain('02')
    expect(text).toContain('Tailored Solutions')
    expect(text).toContain('03')
    expect(text).toContain('Clear Communication')
    expect(text).toContain('04')
    expect(text).toContain('Ongoing Support')
  })
})
