import { apiClient } from './client'
import type { ApiEnvelope } from './auth'

export interface LoteKanban {
  ordem_lote: string
  produto: string
  quantidade: number
  status: string
}

export interface EtapaKanban {
  id: number
  nome: string
  ordem: number
  lotes: LoteKanban[]
}

export async function getKanban(signal?: AbortSignal): Promise<EtapaKanban[]> {
  const res = await apiClient.get<ApiEnvelope<EtapaKanban[]>>('/kanban', { signal })
  return res.data.data
}
