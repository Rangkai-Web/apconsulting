import { describe, expect, it, vi } from 'vitest'
import { ref } from 'vue'
import { mount } from '@vue/test-utils'
import AboutHero from '../app/components/about/Hero.vue'
import AboutPeople from '../app/components/about/People.vue'
import * as apiModule from '../app/utils/api'

describe('AboutHero', () => {
  it('renders the headline, description, and CTAs', () => {
    const wrapper = mount(AboutHero)
    const text = wrapper.text()
    expect(text).toContain('ABOUT US')
    expect(text).toContain('AP CONSULTING')
    expect(text).toContain('Law-abiding tax experts.')
    expect(text).toContain('Trusted partners in your growth.')
    expect(text).toContain('AP Consulting')
    expect(text).toContain('Discover our profile')
    expect(text).toContain('Meet our leadership')
  })
})

describe('AboutPeople', () => {
  it('centers single team member with max-w-[360px] and grid-cols-1', () => {
    vi.spyOn(apiModule, 'safeUseFetch').mockReturnValue({
      data: ref({
        data: [{ id: 1, name: 'Yunita Salim', position: 'Administration' }],
      }),
      pending: ref(false),
      error: ref(null),
      refresh: vi.fn(),
    } as any)

    const wrapper = mount(AboutPeople, {
      global: {
        stubs: { UIcon: true },
      },
    })

    const text = wrapper.text()
    expect(text).toContain('Yunita Salim')
    expect(text).toContain('Administration')

    const grid = wrapper.find('.grid')
    expect(grid.exists()).toBe(true)
    expect(grid.classes()).toContain('max-w-[360px]')
    expect(grid.classes()).toContain('grid-cols-1')
    expect(grid.classes()).toContain('mx-auto')
  })

  it('centers two team members with max-w-[740px] and sm:grid-cols-2', () => {
    vi.spyOn(apiModule, 'safeUseFetch').mockReturnValue({
      data: ref({
        data: [
          { id: 1, name: 'Person 1', position: 'Position 1' },
          { id: 2, name: 'Person 2', position: 'Position 2' },
        ],
      }),
      pending: ref(false),
      error: ref(null),
      refresh: vi.fn(),
    } as any)

    const wrapper = mount(AboutPeople, {
      global: {
        stubs: { UIcon: true },
      },
    })

    const grid = wrapper.find('.grid')
    expect(grid.exists()).toBe(true)
    expect(grid.classes()).toContain('max-w-[740px]')
    expect(grid.classes()).toContain('sm:grid-cols-2')
    expect(grid.classes()).toContain('mx-auto')
  })

  it('uses full 4 columns when 4 or more team members exist', () => {
    vi.spyOn(apiModule, 'safeUseFetch').mockReturnValue({
      data: ref({
        data: [
          { id: 1, name: 'P1', position: 'Pos1' },
          { id: 2, name: 'P2', position: 'Pos2' },
          { id: 3, name: 'P3', position: 'Pos3' },
          { id: 4, name: 'P4', position: 'Pos4' },
        ],
      }),
      pending: ref(false),
      error: ref(null),
      refresh: vi.fn(),
    } as any)

    const wrapper = mount(AboutPeople, {
      global: {
        stubs: { UIcon: true },
      },
    })

    const grid = wrapper.find('.grid')
    expect(grid.exists()).toBe(true)
    expect(grid.classes()).toContain('max-w-[1240px]')
    expect(grid.classes()).toContain('lg:grid-cols-4')
  })
})


