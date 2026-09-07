import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Insights from '../../app/components/home/Insights.vue'

describe('Home Insights', () => {
  it('renders the headline and all three article cards', () => {
    const wrapper = mount(Insights)
    const text = wrapper.text()
    expect(text).toContain('INSIGHTS')
    expect(text).toContain('Practical insight for better')
    expect(text).toContain('business decisions.')
    expect(text).toContain('What your financial reports are really telling you')
    expect(text).toContain('Why monthly financial reporting matters')
    expect(text).toContain('A practical way to improve cash-flow visibility')
    expect(text).toContain('Financial Management')
    expect(text).toContain('Accounting')
    expect(text).toContain('Finance')
    const viewDetailsCount = wrapper.findAll('[data-testid="view-details"]').length
    expect(viewDetailsCount).toBe(3)
  })
})
