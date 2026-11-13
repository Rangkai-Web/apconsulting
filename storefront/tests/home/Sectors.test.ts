import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Sectors from '../../app/components/home/Sectors.vue'

describe('Home Sectors', () => {
  it('renders the industry sectors and confidentiality statement', () => {
    const wrapper = mount(Sectors)
    const text = wrapper.text()
    expect(text).toContain('INDUSTRIES WE SERVE')
    expect(text).toContain('Experience across your industry')
    expect(text).toContain('Your data stays confidential')
  })
})

