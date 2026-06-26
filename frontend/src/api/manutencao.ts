import axios from 'axios'
import { apiClient } from './client'
import type { ApiEnvelope } from './auth'

const BASE_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api'

const publicClient = axios.create({
  baseURL: BASE_URL,
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
})

export type PrioridadeMaquina = 'baixa' | 'normal' | 'alta' | 'critica'
export type StatusOrdem = 'aberta' | 'em_atendimento' | 'pausada' | 'concluida' | 'cancelada'

export interface MaquinaPublica {
  id: number
  nome: string
  codigo: string | null
  prioridade: PrioridadeMaquina
}

export interface OrdemManutencao {
  id: number
  maquina_id: number
  solicitante: string
  motivo: string
  status: StatusOrdem
  solicitado_em: string
  atendido_em: string | null
  concluido_em: string | null
  observacoes: string | null
  maquina: {
    id: number
    nome: string
    codigo: string | null
    prioridade: PrioridadeMaquina
    etapa_fluxo: { id: number; nome: string } | null
  }
}

export async function getMaquinaPublica(id: number, signal?: AbortSignal): Promise<MaquinaPublica> {
  const res = await publicClient.get<ApiEnvelope<MaquinaPublica>>(`/public/manutencao/maquina/${id}`, { signal })
  return res.data.data
}

export async function criarSolicitacao(data: {
  maquina_id: number
  solicitante: string
  motivo: string
}): Promise<OrdemManutencao> {
  const res = await publicClient.post<ApiEnvelope<OrdemManutencao>>('/public/manutencao/solicitar', data)
  return res.data.data
}

export async function getOrdensManutencao(
  filtros?: { status?: StatusOrdem; maquina_id?: number; etapa_fluxo_id?: number; data?: string },
  signal?: AbortSignal
): Promise<OrdemManutencao[]> {
  const res = await apiClient.get<ApiEnvelope<OrdemManutencao[]>>('/admin/manutencao/ordens', {
    params: filtros,
    signal,
  })
  return res.data.data
}

export async function atualizarOrdem(
  id: number,
  data: { status?: StatusOrdem; observacoes?: string | null }
): Promise<OrdemManutencao> {
  const res = await apiClient.patch<ApiEnvelope<OrdemManutencao>>(`/admin/manutencao/ordens/${id}`, data)
  return res.data.data
}
