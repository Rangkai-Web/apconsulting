import { describe, expect, it } from 'vitest'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'

describe('Homepage composition', () => {
  it('references every section component, in spec order, in app/pages/index.vue', () => {
    const source = readFileSync(resolve(__dirname, '../../app/pages/index.vue'), 'utf-8')
    const expectedOrder = [
      'HomeHero',
      'HomeServices',
      'HomeSectors',
      'HomeAbout',
      'HomeValues',
      'HomeApproach',
      'HomeInsights',
      'HomeFaq',
      'HomeCta',
    ]
    const positions = expectedOrder.map((tag) => source.indexOf(`<${tag}`))
    expect(positions.every((pos) => pos !== -1)).toBe(true)
    expect(positions).toEqual([...positions].sort((a, b) => a - b))
  })
})

