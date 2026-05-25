import { apiClient } from './client'
import type { ApiEnvelope } from './auth'

export interface EtapaFluxo {
  id: number
  nome: string
}

export interface Maquina {
  id: number
  nome: string
  codigo: string | null
  ano: number | null
  descricao: string | null
  ativa: boolean
  foto_url: string | null
  etapa_fluxo_id: number
  etapa_fluxo: EtapaFluxo | null
}

export async function getMaquinas(signal?: AbortSignal): Promise<Maquina[]> {
  const res = await apiClient.get<ApiEnvelope<Maquina[]>>('/maquinas', { signal })
  return res.data.data
}

export async function createMaquina(data: FormData): Promise<Maquina> {
  const res = await apiClient.post<ApiEnvelope<Maquina>>('/maquinas', data)
  return res.data.data
}

// PHP só popula $_FILES em requisições POST, então usamos method spoofing (_method=PATCH)
export async function updateMaquina(id: number, data: FormData): Promise<Maquina> {
  data.append('_method', 'PATCH')
  const res = await apiClient.post<ApiEnvelope<Maquina>>(`/maquinas/${id}`, data)
  return res.data.data
}
