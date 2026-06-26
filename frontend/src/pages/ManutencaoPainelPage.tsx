import { useCallback, useEffect, useMemo, useState } from 'react'
import axios from 'axios'
import { Wrench, Loader2, QrCode, Clock, User } from 'lucide-react'
import {
  getOrdensManutencao,
  type OrdemManutencao,
  type PrioridadeMaquina,
  type StatusOrdem,
} from '@/api/manutencao'
import { OrdemManutencaoModal } from '@/components/manutencao/OrdemManutencaoModal'
import { PrioridadeBadge } from '@/components/manutencao/PrioridadeBadge'

const STATUS_FILTROS: { value: StatusOrdem | 'todas'; label: string }[] = [
  { value: 'todas',          label: 'Todas'          },
  { value: 'aberta',         label: 'Abertas'        },
  { value: 'em_atendimento', label: 'Em atendimento' },
  { value: 'pausada',        label: 'Pausadas'       },
  { value: 'concluida',      label: 'Concluídas'     },
  { value: 'cancelada',      label: 'Canceladas'     },
]

const STATUS_BADGE: Record<StatusOrdem, string> = {
  aberta:         'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
  em_atendimento: 'text-blue-400 bg-blue-400/10 border-blue-400/20',
  pausada:        'text-orange-400 bg-orange-400/10 border-orange-400/20',
  concluida:      'text-[#00aa84] bg-[#00aa84]/10 border-[#00aa84]/20',
  cancelada:      'text-slate-400 bg-slate-400/10 border-slate-400/20',
}

const STATUS_LABELS: Record<StatusOrdem, string> = {
  aberta:         'Aberta',
  em_atendimento: 'Em atendimento',
  pausada:        'Pausada',
  concluida:      'Concluída',
  cancelada:      'Cancelada',
}

const PRIORIDADE_ORDER: PrioridadeMaquina[] = ['critica', 'alta', 'normal', 'baixa']

const PRIORIDADE_CONFIG: Record<PrioridadeMaquina, { headerClass: string; borderClass: string }> = {
  critica: { headerClass: 'text-red-400',    borderClass: 'border-red-500/30'    },
  alta:    { headerClass: 'text-orange-400', borderClass: 'border-orange-500/30' },
  normal:  { headerClass: 'text-blue-400',   borderClass: 'border-blue-500/30'   },
  baixa:   { headerClass: 'text-slate-400',  borderClass: 'border-slate-500/30'  },
}

export function ManutencaoPainelPage() {
  const [ordens, setOrdens]             = useState<OrdemManutencao[]>([])
  const [loading, setLoading]           = useState(true)
  const [error, setError]               = useState<string | null>(null)
  const [filtroStatus, setFiltroStatus] = useState<StatusOrdem | 'todas'>('todas')
  const [filtroSetor, setFiltroSetor]   = useState<number | ''>('')
  const [filtroData, setFiltroData]     = useState('')
  const [ordemSelecionada, setOrdemSelecionada] = useState<OrdemManutencao | null>(null)

  const load = useCallback((signal?: AbortSignal) => {
    setLoading(true)
    setError(null)
    const params: Parameters<typeof getOrdensManutencao>[0] = {}
    if (filtroStatus !== 'todas') params.status = filtroStatus
    if (filtroSetor !== '')       params.etapa_fluxo_id = filtroSetor
    if (filtroData)               params.data = filtroData

    getOrdensManutencao(params, signal)
      .then(setOrdens)
      .catch((err: unknown) => {
        if (!axios.isCancel(err)) setError('Não foi possível carregar as ordens.')
      })
      .finally(() => { if (!signal?.aborted) setLoading(false) })
  }, [filtroStatus, filtroSetor, filtroData])

  useEffect(() => {
    const controller = new AbortController()
    load(controller.signal)
    return () => controller.abort()
  }, [load])

  function handleOrdemAtualizada(updated: OrdemManutencao) {
    setOrdens(prev => prev.map(o => o.id === updated.id ? updated : o))
    setOrdemSelecionada(updated)
  }

  function formatDate(iso: string) {
    return new Date(iso).toLocaleString('pt-BR', {
      day: '2-digit', month: '2-digit',
      hour: '2-digit', minute: '2-digit',
    })
  }

  const setores = useMemo(() => {
    const map = new Map<number, string>()
    for (const o of ordens) {
      if (o.maquina.etapa_fluxo) {
        map.set(o.maquina.etapa_fluxo.id, o.maquina.etapa_fluxo.nome)
      }
    }
    return Array.from(map.entries()).sort((a, b) => a[1].localeCompare(b[1]))
  }, [ordens])

  const grouped = useMemo(() => {
    return PRIORIDADE_ORDER.map(prioridade => {
      const byPrio = ordens.filter(o => o.maquina.prioridade === prioridade)
      const setorMap = new Map<string, OrdemManutencao[]>()
      for (const o of byPrio) {
        const key = o.maquina.etapa_fluxo?.nome ?? 'Sem setor'
        if (!setorMap.has(key)) setorMap.set(key, [])
        setorMap.get(key)!.push(o)
      }
      const setorGroups = Array.from(setorMap.entries()).sort((a, b) => a[0].localeCompare(b[0]))
      return { prioridade, setorGroups, total: byPrio.length }
    }).filter(g => g.total > 0)
  }, [ordens])

  return (
    <div className="space-y-6">

      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="p-2 rounded-lg bg-[#00aa84]/10">
            <Wrench className="w-5 h-5 text-[#00aa84]" />
          </div>
          <div>
            <h1 className="text-xl font-semibold text-white">Manutenção</h1>
            <p className="text-sm text-slate-400">Ordens de serviço abertas e histórico</p>
          </div>
        </div>
        <span className="text-xs text-slate-500">{ordens.length} ordem{ordens.length !== 1 ? 's' : ''}</span>
      </div>

      {/* Filtros */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="flex items-center gap-1 p-1 bg-white/5 rounded-lg flex-wrap">
          {STATUS_FILTROS.map(f => (
            <button
              key={f.value}
              onClick={() => setFiltroStatus(f.value)}
              className={`px-3 py-1.5 text-sm font-medium rounded-md transition-colors ${
                filtroStatus === f.value
                  ? 'bg-[#00aa84] text-white'
                  : 'text-slate-400 hover:text-white'
              }`}
            >
              {f.label}
            </button>
          ))}
        </div>

        <select
          value={filtroSetor}
          onChange={e => setFiltroSetor(e.target.value === '' ? '' : Number(e.target.value))}
          className="px-3 py-2 text-sm bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#00aa84]/60 transition-colors min-w-[160px]"
        >
          <option value="">Todos os setores</option>
          {setores.map(([id, nome]) => (
            <option key={id} value={id}>{nome}</option>
          ))}
        </select>

        <input
          type="date"
          value={filtroData}
          onChange={e => setFiltroData(e.target.value)}
          className="px-3 py-2 text-sm bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#00aa84]/60 transition-colors [color-scheme:dark]"
        />
        {filtroData && (
          <button
            onClick={() => setFiltroData('')}
            className="text-xs text-slate-400 hover:text-white transition-colors"
          >
            Limpar data
          </button>
        )}
      </div>

      {/* Loading / error / empty */}
      {loading && (
        <div className="flex items-center justify-center gap-2 py-16 text-slate-400">
          <Loader2 className="w-5 h-5 animate-spin" />
          <span className="text-sm">Carregando…</span>
        </div>
      )}
      {error && (
        <div className="flex items-center justify-center py-16">
          <p className="text-sm text-red-400">{error}</p>
        </div>
      )}
      {!loading && !error && ordens.length === 0 && (
        <div className="flex flex-col items-center justify-center py-16 gap-3 text-slate-500">
          <QrCode className="w-8 h-8" />
          <p className="text-sm">Nenhuma ordem de manutenção encontrada.</p>
        </div>
      )}

      {/* Cards agrupados por prioridade → setor */}
      {!loading && !error && grouped.map(({ prioridade, setorGroups }) => {
        const cfg = PRIORIDADE_CONFIG[prioridade]
        return (
          <div key={prioridade} className="space-y-4">
            <div className="flex items-center gap-3">
              <PrioridadeBadge prioridade={prioridade} />
              <div className="flex-1 h-px bg-white/5" />
            </div>

            {setorGroups.map(([setor, itens]) => (
              <div key={setor} className="space-y-2">
                {setorGroups.length > 1 && (
                  <p className="text-xs font-medium text-slate-500 uppercase tracking-wider pl-1">{setor}</p>
                )}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                  {itens.map(o => (
                    <button
                      key={o.id}
                      onClick={() => setOrdemSelecionada(o)}
                      className={`text-left p-4 rounded-xl border bg-[#0f1923] hover:bg-white/[0.04] transition-colors ${cfg.borderClass}`}
                    >
                      <div className="flex items-start justify-between gap-2 mb-3">
                        <span className="text-xs font-mono text-slate-500">OS #{o.id}</span>
                        <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ${STATUS_BADGE[o.status]}`}>
                          {STATUS_LABELS[o.status]}
                        </span>
                      </div>

                      <p className="text-base font-semibold text-white leading-tight mb-1">{o.maquina.nome}</p>
                      {o.maquina.etapa_fluxo && (
                        <p className={`text-xs font-medium mb-2 ${cfg.headerClass}`}>{o.maquina.etapa_fluxo.nome}</p>
                      )}

                      <p className="text-sm text-slate-400 line-clamp-2 mb-3">{o.motivo}</p>

                      <div className="flex items-center gap-2 text-xs text-slate-500">
                        <User className="w-3 h-3 shrink-0" />
                        <span className="truncate">{o.solicitante}</span>
                        <span className="flex items-center gap-1 ml-auto shrink-0">
                          <Clock className="w-3 h-3" />
                          {formatDate(o.solicitado_em)}
                        </span>
                      </div>
                    </button>
                  ))}
                </div>
              </div>
            ))}
          </div>
        )
      })}

      {ordemSelecionada && (
        <OrdemManutencaoModal
          ordem={ordemSelecionada}
          onClose={() => setOrdemSelecionada(null)}
          onSuccess={handleOrdemAtualizada}
        />
      )}
    </div>
  )
}
