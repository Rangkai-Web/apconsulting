import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Faq from '../../app/components/home/Faq.vue'

describe('Home Faq', () => {
  it('renders the headline and questions', () => {
    const wrapper = mount(Faq, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' } },
      },
    })
    const text = wrapper.text()
    expect(text).toContain('COMMON QUESTIONS')
    expect(text).toContain('What types of businesses does AP Consulting work with?')
  })

  it('expands the answer when the question is clicked', async () => {
    const wrapper = mount(Faq, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' } },
      },
    })
    const buttons = wrapper.findAll('button')
    expect(buttons.length).toBeGreaterThan(0)
    await buttons[0].trigger('click')
    expect(wrapper.text()).toContain('We serve a wide range of clients across Indonesia')
  })
})


