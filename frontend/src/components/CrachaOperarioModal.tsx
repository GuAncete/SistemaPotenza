import { useEffect, useRef } from 'react'
import JsBarcode from 'jsbarcode'
import { X, Printer } from 'lucide-react'
import type { Operario } from '@/api/operarios'

interface Props {
  operario: Operario | null
  onClose: () => void
}

export function CrachaOperarioModal({ operario, onClose }: Props) {
  const svgRef = useRef<SVGSVGElement>(null)

  useEffect(() => {
    if (!operario || !svgRef.current) return
    JsBarcode(svgRef.current, operario.matricula, {
      format: 'CODE128',
      displayValue: true,
      width: 2,
      height: 60,
      fontSize: 14,
      margin: 8,
    })
  }, [operario])

  if (!operario) return null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 print:static print:block print:p-0">
      <div className="absolute inset-0 bg-black/60 backdrop-blur-sm print:hidden" onClick={onClose} />

      <div className="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden print:static print:shadow-none print:rounded-none print:w-auto print:max-w-none">

        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-200 print:hidden">
          <h2 className="text-base font-semibold text-slate-900">Crachá do operário</h2>
          <button
            type="button"
            onClick={onClose}
            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <div className="px-6 py-8 flex flex-col items-center gap-1 text-center">
          <p className="text-sm font-semibold text-slate-900">{operario.user.name}</p>
          {operario.etapa_fluxo && (
            <p className="text-xs text-slate-500">{operario.etapa_fluxo.nome}</p>
          )}
          <svg ref={svgRef} className="mt-4" />
        </div>

        <div className="px-6 pb-6 print:hidden">
          <button
            type="button"
            onClick={() => window.print()}
            className="w-full flex items-center justify-center gap-2 py-2.5 text-sm font-semibold text-white bg-[#00aa84] hover:bg-[#00aa84]/90 rounded-lg transition-colors"
          >
            <Printer className="w-4 h-4" />
            Imprimir crachá
          </button>
        </div>
      </div>
    </div>
  )
}
