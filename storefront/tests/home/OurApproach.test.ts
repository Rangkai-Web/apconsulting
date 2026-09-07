import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import OurApproach from '../../app/components/home/OurApproach.vue'

describe('Home OurApproach', () => {
  it('renders the headline and all four steps in order', () => {
    const wrapper = mount(OurApproach)
    const text = wrapper.text()
    expect(text).toContain('OUR APPROACH')
    expect(text).toContain('From')
    expect(text).toContain('Financial Clarity')
    expect(text).toContain('to')
    expect(text).toContain('Confident Action.')

    const stepTitles = ['Understand Your Business', 'Identify What Matters', 'Put Solutions in Place', 'Review and Move Forward']
    const positions = stepTitles.map((title) => text.indexOf(title))
    expect(positions.every((pos) => pos !== -1)).toBe(true)
    expect(positions).toEqual([...positions].sort((a, b) => a - b))
  })
})
