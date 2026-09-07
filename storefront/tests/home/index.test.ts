import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Hero from '../../app/components/home/Hero.vue'
import AboutPcg from '../../app/components/home/AboutPcg.vue'
import Stats from '../../app/components/home/Stats.vue'
import HowWeHelp from '../../app/components/home/HowWeHelp.vue'
import WhyChooseUs from '../../app/components/home/WhyChooseUs.vue'
import OurApproach from '../../app/components/home/OurApproach.vue'
import Insights from '../../app/components/home/Insights.vue'
import Faq from '../../app/components/home/Faq.vue'
import Cta from '../../app/components/home/Cta.vue'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'

describe('Homepage composition', () => {
  it('references every section component, in spec order, in app/pages/index.vue', () => {
    const source = readFileSync(resolve(__dirname, '../../app/pages/index.vue'), 'utf-8')
    const expectedOrder = [
      'HomeHero',
      'HomeAboutPcg',
      'HomeStats',
      'HomeHowWeHelp',
      'HomeWhyChooseUs',
      'HomeOurApproach',
      'HomeInsights',
      'HomeFaq',
      'HomeCta',
    ]
    const positions = expectedOrder.map((tag) => source.indexOf(`<${tag}`))
    expect(positions.every((pos) => pos !== -1)).toBe(true)
    expect(positions).toEqual([...positions].sort((a, b) => a - b))
  })

  it('every referenced section component mounts without throwing', () => {
    for (const Component of [Hero, AboutPcg, Stats, HowWeHelp, WhyChooseUs, OurApproach, Insights, Faq, Cta]) {
      expect(() => mount(Component)).not.toThrow()
    }
  })
})
