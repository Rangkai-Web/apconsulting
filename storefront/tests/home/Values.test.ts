import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Values from '../../app/components/home/Values.vue'

describe('Home Values', () => {
  it('renders the core values and quote', () => {
    const wrapper = mount(Values)
    const text = wrapper.text()
    expect(text).toContain('THE VALUES WE STAND FOR')
    expect(text).toContain('Integrity')
    expect(text).toContain('Excellence')
    expect(text).toContain('Teamwork')
  })
})

