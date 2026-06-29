import { apiClient } from './client'
import type { ApiEnvelope } from './auth'

export interface ChamadaSuporte {
  id: number
  origem: 'operario' | 'manutencao'
  criado_em: string
  maquina: { id: number; nome: string } | null
  operario: { id: number; nome: string } | null
}

export async function chamarSuporte(): Promise<void> {
  await apiClient.post('/apontamento/chamar-suporte')
}

export async function chamarSuporteManutencao(): Promise<void> {
  await apiClient.post('/manutencao/admin/chamar-suporte')
}

export async function getChamadasSuporte(): Promise<ChamadaSuporte[]> {
  try {
    const res = await apiClient.get<ApiEnvelope<ChamadaSuporte[]>>('/admin/chamadas-suporte')
    return res.data.data ?? []
  } catch {
    return []
  }
}

export async function visualizarChamada(id: number): Promise<void> {
  await apiClient.put(`/admin/chamadas-suporte/${id}/visualizar`)
}
