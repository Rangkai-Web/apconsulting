import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Stats from '../../app/components/home/Stats.vue'

describe('Home Stats', () => {
  it('renders all four stats with their labels', () => {
    const wrapper = mount(Stats)
    const text = wrapper.text()
    expect(text).toContain('4')
    expect(text).toContain('Core service areas')
    expect(text).toContain('Steps in our approach')
    expect(text).toContain('20')
    expect(text).toContain('Integrated support partner')
    expect(text).toContain('200+')
    expect(text).toContain('Customers')
  })
})
