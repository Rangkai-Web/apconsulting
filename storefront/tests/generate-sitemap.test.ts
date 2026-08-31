import { describe, expect, it } from 'vitest'
import { buildSitemapXml } from '../scripts/generate-sitemap.mjs'

describe('buildSitemapXml', () => {
  it('wraps each URL in a <url><loc> entry', () => {
    const xml = buildSitemapXml(['https://bbystargo.com/', 'https://bbystargo.com/katalog'])

    expect(xml).toContain('<url><loc>https://bbystargo.com/</loc></url>')
    expect(xml).toContain('<url><loc>https://bbystargo.com/katalog</loc></url>')
    expect(xml).toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
  })

  it('escapes XML special characters in URLs', () => {
    const xml = buildSitemapXml(['https://bbystargo.com/produk/a&b'])

    expect(xml).toContain('a&amp;b')
    expect(xml).not.toContain('a&b')
  })
})
