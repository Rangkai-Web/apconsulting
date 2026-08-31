import { describe, expect, it } from 'vitest'
import { useSeoText } from '../app/composables/useSeoText'

describe('useSeoText.toPlainText', () => {
  const { toPlainText } = useSeoText()

  it('strips HTML tags and collapses whitespace', () => {
    expect(toPlainText('<p>Hello   <strong>world</strong></p>')).toBe('Hello world')
  })

  it('decodes HTML entities left over after stripping tags', () => {
    expect(toPlainText("<p>Adm &amp; packing, jum&#039;at</p>")).toBe("Adm & packing, jum'at")
  })

  it('returns short text unchanged', () => {
    expect(toPlainText('Short description', 155)).toBe('Short description')
  })

  it('truncates long text with an ellipsis at the max length', () => {
    const long = 'a'.repeat(200)
    const result = toPlainText(long, 155)
    expect(result.length).toBe(155)
    expect(result.endsWith('…')).toBe(true)
  })
})

describe('useSeoText.toSafeJsonLd', () => {
  const { toSafeJsonLd } = useSeoText()

  it('escapes a </script> breakout attempt so the tag cannot be closed early', () => {
    const malicious = { name: '</script><script>alert(1)</script>' }
    const escaped = toSafeJsonLd(malicious)

    expect(escaped).not.toContain('</script>')
    expect(escaped).not.toContain('<script>')
  })

  it('round-trips through JSON.parse back to the original value', () => {
    const value = { name: '</script>', weird: `a${String.fromCharCode(0x2028)}b` }
    expect(JSON.parse(toSafeJsonLd(value))).toEqual(value)
  })
})
