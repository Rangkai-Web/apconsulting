import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Faq from '../../app/components/home/Faq.vue'

describe('Home Faq', () => {
  it('renders the headline and the one confirmed question', () => {
    const wrapper = mount(Faq)
    const text = wrapper.text()
    expect(text).toContain('FREQUENTLY ASKED QUESTIONS')
    expect(text).toContain('Clear answers for your next step.')
    expect(text).toContain('What services does PCG provide?')
  })

  it('expands the answer when the question is clicked', async () => {
    const wrapper = mount(Faq)
    expect(wrapper.text()).not.toContain('PCG provides professional consulting')
    await wrapper.find('[data-testid="faq-question"]').trigger('click')
    expect(wrapper.text()).toContain('PCG provides professional consulting and advisory support across taxation, accounting and bookkeeping, and other customized financial services.')
  })
})
