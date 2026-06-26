import type { PrioridadeMaquina } from '@/api/manutencao'

interface Props {
  prioridade: PrioridadeMaquina
}

const CONFIG: Record<PrioridadeMaquina, { label: string; className: string }> = {
  critica: { label: 'Crítica', className: 'bg-red-500/15 text-red-400 border-red-500/20' },
  alta:    { label: 'Alta',    className: 'bg-orange-500/15 text-orange-400 border-orange-500/20' },
  normal:  { label: 'Normal',  className: 'bg-blue-500/15 text-blue-400 border-blue-500/20' },
  baixa:   { label: 'Baixa',   className: 'bg-slate-500/15 text-slate-400 border-slate-500/20' },
}

export function PrioridadeBadge({ prioridade }: Props) {
  const { label, className } = CONFIG[prioridade] ?? CONFIG.normal
  return (
    <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ${className}`}>
      {label}
    </span>
  )
}
