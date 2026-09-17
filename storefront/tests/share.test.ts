import { describe, expect, it } from 'vitest'
import { buildShareUrl } from '../app/utils/share'

describe('buildShareUrl', () => {
  it('appends utm params to a plain article url', () => {
    const result = buildShareUrl('https://pcgmitra.co.id/insight/5')
    expect(result).toBe(
      'https://pcgmitra.co.id/insight/5?utm_source=share&utm_medium=social&utm_campaign=insight'
    )
  })

  it('overrides existing utm params instead of duplicating them', () => {
    const result = buildShareUrl('https://pcgmitra.co.id/insight/5?utm_source=old')
    expect(result).toBe(
      'https://pcgmitra.co.id/insight/5?utm_source=share&utm_medium=social&utm_campaign=insight'
    )
  })

  it('handles relative paths gracefully without throwing', () => {
    const result = buildShareUrl('/insight/5')
    expect(result).toContain('/insight/5?utm_source=share&utm_medium=social&utm_campaign=insight')
  })

  it('returns empty string for empty inputs', () => {
    const result = buildShareUrl('')
    expect(result).toBe('')
  })
})
