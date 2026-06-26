import { useState } from 'react'
import axios from 'axios'
import { X, Loader2, Play, Pause, CheckCircle, XCircle } from 'lucide-react'
import { atualizarOrdem, type OrdemManutencao, type StatusOrdem } from '@/api/manutencao'
import { PrioridadeBadge } from './PrioridadeBadge'

interface Props {
  ordem: OrdemManutencao
  onClose: () => void
  onSuccess: (updated: OrdemManutencao) => void
}

const STATUS_LABELS: Record<StatusOrdem, string> = {
  aberta:         'Aberta',
  em_atendimento: 'Em atendimento',
  pausada:        'Pausada',
  concluida:      'Concluída',
  cancelada:      'Cancelada',
}

const STATUS_BADGE: Record<StatusOrdem, string> = {
  aberta:         'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
  em_atendimento: 'text-blue-400 bg-blue-400/10 border-blue-400/20',
  pausada:        'text-orange-400 bg-orange-400/10 border-orange-400/20',
  concluida:      'text-[#00aa84] bg-[#00aa84]/10 border-[#00aa84]/20',
  cancelada:      'text-slate-400 bg-slate-400/10 border-slate-400/20',
}

export function OrdemManutencaoModal({ ordem, onClose, onSuccess }: Props) {
  const [observacoes, setObservacoes] = useState(ordem.observacoes ?? '')
  const [saving, setSaving]           = useState<StatusOrdem | 'obs' | null>(null)
  const [erro, setErro]               = useState<string | null>(null)

  const isTerminada = ordem.status === 'concluida' || ordem.status === 'cancelada'

  async function changeStatus(novoStatus: StatusOrdem) {
    setErro(null)
    setSaving(novoStatus)
    try {
      const updated = await atualizarOrdem(ordem.id, { status: novoStatus })
      onSuccess(updated)
    } catch (err: unknown) {
      if (axios.isAxiosError(err) && err.response?.data?.message) {
        setErro(err.response.data.message as string)
      } else {
        setErro('Não foi possível atualizar o status.')
      }
    } finally {
      setSaving(null)
    }
  }

  async function salvarObservacoes() {
    setErro(null)
    setSaving('obs')
    try {
      const updated = await atualizarOrdem(ordem.id, { observacoes: observacoes.trim() || null })
      onSuccess(updated)
    } catch (err: unknown) {
      if (axios.isAxiosError(err) && err.response?.data?.message) {
        setErro(err.response.data.message as string)
      } else {
        setErro('Não foi possível salvar as observações.')
      }
    } finally {
      setSaving(null)
    }
  }

  function formatDate(iso: string | null) {
    if (!iso) return '—'
    return new Date(iso).toLocaleString('pt-BR')
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

      <div className="relative z-10 w-full max-w-lg bg-[#0f1923] border border-white/10 rounded-2xl shadow-2xl">

        {/* Header */}
        <div className="flex items-center justify-between px-6 py-4 border-b border-white/5">
          <div>
            <h2 className="text-base font-semibold text-white">OS #{ordem.id}</h2>
            <p className="text-xs text-slate-400">
              {ordem.maquina.nome}
              {ordem.maquina.etapa_fluxo && (
                <span className="text-slate-600"> · {ordem.maquina.etapa_fluxo.nome}</span>
              )}
            </p>
          </div>
          <div className="flex items-center gap-3">
            <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ${STATUS_BADGE[ordem.status]}`}>
              {STATUS_LABELS[ordem.status]}
            </span>
            <PrioridadeBadge prioridade={ordem.maquina.prioridade} />
            <button
              type="button"
              onClick={onClose}
              className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Detalhes */}
        <div className="px-6 py-4 space-y-3 border-b border-white/5">
          <InfoRow label="Solicitante"   value={ordem.solicitante} />
          <InfoRow label="Motivo"        value={ordem.motivo} />
          <InfoRow label="Solicitado em" value={formatDate(ordem.solicitado_em)} />
          <InfoRow label="Atendido em"   value={formatDate(ordem.atendido_em)} />
          <InfoRow label="Concluído em"  value={formatDate(ordem.concluido_em)} />
        </div>

        <div className="px-6 py-5 space-y-4">

          {/* Botões de ação */}
          {!isTerminada && (
            <div className="grid grid-cols-2 gap-2">
              {(ordem.status === 'aberta' || ordem.status === 'pausada') && (
                <ActionButton
                  icon={<Play className="w-4 h-4" />}
                  label={ordem.status === 'pausada' ? 'Retomar' : 'Iniciar'}
                  className="bg-blue-600 hover:bg-blue-500 text-white"
                  loading={saving === 'em_atendimento'}
                  disabled={saving !== null}
                  onClick={() => changeStatus('em_atendimento')}
                />
              )}

              {ordem.status === 'em_atendimento' && (
                <ActionButton
                  icon={<Pause className="w-4 h-4" />}
                  label="Pausar"
                  className="bg-orange-600 hover:bg-orange-500 text-white"
                  loading={saving === 'pausada'}
                  disabled={saving !== null}
                  onClick={() => changeStatus('pausada')}
                />
              )}

              {(ordem.status === 'em_atendimento' || ordem.status === 'pausada') && (
                <ActionButton
                  icon={<CheckCircle className="w-4 h-4" />}
                  label="Concluir"
                  className="bg-[#00aa84] hover:bg-[#00aa84]/90 text-white"
                  loading={saving === 'concluida'}
                  disabled={saving !== null}
                  onClick={() => changeStatus('concluida')}
                />
              )}

              <ActionButton
                icon={<XCircle className="w-4 h-4" />}
                label="Cancelar OS"
                className="bg-white/5 hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-white/10"
                loading={saving === 'cancelada'}
                disabled={saving !== null}
                onClick={() => changeStatus('cancelada')}
              />
            </div>
          )}

          {/* Observações */}
          <div>
            <label className="block text-xs font-medium text-slate-400 mb-1.5">Observações</label>
            <textarea
              value={observacoes}
              onChange={e => setObservacoes(e.target.value)}
              placeholder="Anotações da equipe de manutenção…"
              rows={3}
              disabled={isTerminada}
              className="w-full px-3 py-2 text-sm bg-white/5 border border-white/10 rounded-lg text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00aa84]/60 transition-colors resize-none disabled:opacity-50"
            />
          </div>

          {erro && (
            <p className="text-xs text-red-400 bg-red-400/10 border border-red-400/20 rounded-lg px-3 py-2">
              {erro}
            </p>
          )}

          <div className="flex gap-3">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 py-2 text-sm font-medium text-slate-400 bg-white/5 hover:bg-white/10 rounded-lg transition-colors"
            >
              Fechar
            </button>
            {!isTerminada && (
              <button
                type="button"
                onClick={salvarObservacoes}
                disabled={saving !== null}
                className="flex-1 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 disabled:opacity-50 rounded-lg transition-colors flex items-center justify-center gap-2"
              >
                {saving === 'obs' ? <><Loader2 className="w-3.5 h-3.5 animate-spin" />Salvando…</> : 'Salvar observações'}
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}

function InfoRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex gap-4">
      <span className="text-xs text-slate-500 w-28 shrink-0">{label}</span>
      <span className="text-xs text-slate-300 break-words">{value}</span>
    </div>
  )
}

interface ActionButtonProps {
  icon: React.ReactNode
  label: string
  className: string
  loading: boolean
  disabled: boolean
  onClick: () => void
}

function ActionButton({ icon, label, className, loading, disabled, onClick }: ActionButtonProps) {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      className={`flex items-center justify-center gap-2 py-3 px-4 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 ${className}`}
    >
      {loading ? <Loader2 className="w-4 h-4 animate-spin" /> : icon}
      {label}
    </button>
  )
}
