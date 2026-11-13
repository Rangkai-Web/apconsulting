import { describe, expect, it, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import { defineComponent, h, Suspense, ref } from 'vue'
import Insights from '../../app/components/home/Insights.vue'

describe('Home Insights', () => {
  beforeEach(() => {
    vi.stubGlobal('useFetch', () => ({
      data: ref({ data: [] }),
      pending: ref(false),
      error: ref(null),
    }))
  })

  it('renders the headline and article cards', async () => {
    const TestComponent = defineComponent({
      render() {
        return h(Suspense, null, {
          default: () => h(Insights),
        })
      },
    })
    const wrapper = mount(TestComponent, {
      global: {
        stubs: { NuxtLink: { template: '<a><slot /></a>' }, UIcon: true },
      },
    })
    await flushPromises()
    const text = wrapper.text()
    expect(text).toContain('LATEST INSIGHTS')
    expect(text).toContain('Fresh perspectives for better decisions.')
    expect(text).toContain('TAXATION')
  })
})

