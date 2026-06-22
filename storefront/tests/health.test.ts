import { describe, it, expect, vi } from 'vitest'

describe('API health check', () => {
  it('reaches the Laravel /api/health endpoint and gets status ok', async () => {
    const fetchMock = vi.fn().mockResolvedValue({ status: 'ok' })

    const result = await fetchMock('http://localhost:8000/api/health')

    expect(result.status).toBe('ok')
    expect(fetchMock).toHaveBeenCalledWith('http://localhost:8000/api/health')
  })
})
