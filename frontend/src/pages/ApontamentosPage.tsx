import { useEffect, useState } from 'react'
import axios from 'axios'
import { ClipboardList, Loader2 } from 'lucide-react'
import { getKanban, type EtapaKanban } from '@/api/kanban'

const STATUS_LABEL: Record<string, { label: string; color: string }> = {
  aguardando:  { label: 'Aguardando',  color: 'text-yellow-400' },
  em_setup:    { label: 'Em Setup',    color: 'text-blue-400'   },
  em_producao: { label: 'Em Produção', color: 'text-[#00aa84]'  },
  finalizado:  { label: 'Finalizado',  color: 'text-slate-400'  },
}

export function ApontamentosPage() {
  const [etapas, setEtapas]   = useState<EtapaKanban[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError]     = useState<string | null>(null)

  useEffect(() => {
    const controller = new AbortController()

    setLoading(true)
    setError(null)

    getKanban(controller.signal)
      .then(setEtapas)
      .catch((err: unknown) => {
        if (!axios.isCancel(err)) {
          setError('Não foi possível carregar os apontamentos.')
        }
      })
      .finally(() => {
        if (!controller.signal.aborted) setLoading(false)
      })

    return () => controller.abort()
  }, [])

  const totalLotes = etapas.reduce((acc, e) => acc + e.lotes.length, 0)

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="p-2 rounded-lg bg-[#00aa84]/10">
            <ClipboardList className="w-5 h-5 text-[#00aa84]" />
          </div>
          <div>
            <h1 className="text-xl font-semibold text-white">Apontamentos</h1>
            <p className="text-sm text-slate-400">Fluxo de produção por etapa</p>
          </div>
        </div>
        {!loading && !error && (
          <span className="text-xs text-slate-500 bg-white/5 px-3 py-1 rounded-full">
            {totalLotes} {totalLotes === 1 ? 'lote' : 'lotes'} em andamento
          </span>
        )}
      </div>

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
      {!loading && !error && etapas.length === 0 && (
        <div className="flex items-center justify-center py-16">
          <p className="text-sm text-slate-500">Nenhum apontamento em andamento.</p>
        </div>
      )}

      {!loading && !error && etapas.length > 0 && (
        <div className="space-y-4">
          {etapas.map((etapa) => (
            <div key={etapa.id} className="bg-[#0f1923] border border-white/5 rounded-xl overflow-hidden">
              <div className="flex items-center justify-between px-6 py-3 border-b border-white/5">
                <h2 className="text-sm font-semibold text-white">{etapa.nome}</h2>
                <span className="text-xs text-slate-500 bg-white/5 px-2.5 py-0.5 rounded-full">
                  {etapa.lotes.length} {etapa.lotes.length === 1 ? 'lote' : 'lotes'}
                </span>
              </div>
              {etapa.lotes.length === 0 ? (
                <p className="px-6 py-4 text-sm text-slate-600">Nenhum lote nesta etapa.</p>
              ) : (
                <table className="w-full text-sm">
                  <thead>
                    <tr className="text-left">
                      <th className="px-6 py-3 text-xs font-medium text-slate-400 uppercase tracking-wider">Ordem / Lote</th>
                      <th className="px-6 py-3 text-xs font-medium text-slate-400 uppercase tracking-wider">Produto</th>
                      <th className="px-6 py-3 text-xs font-medium text-slate-400 uppercase tracking-wider">Qtd</th>
                      <th className="px-6 py-3 text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-white/5">
                    {etapa.lotes.map((lote, idx) => {
                      const s = STATUS_LABEL[lote.status] ?? { label: lote.status, color: 'text-slate-400' }
                      return (
                        <tr key={`${etapa.id}-${lote.ordem_lote ?? idx}`} className="hover:bg-white/[0.02] transition-colors">
                          <td className="px-6 py-3 font-mono text-white">{lote.ordem_lote}</td>
                          <td className="px-6 py-3 text-slate-300">{lote.produto}</td>
                          <td className="px-6 py-3 text-slate-300">{lote.quantidade}</td>
                          <td className={`px-6 py-3 font-medium ${s.color}`}>{s.label}</td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
