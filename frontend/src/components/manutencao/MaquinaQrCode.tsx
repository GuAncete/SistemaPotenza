import { useRef } from 'react'
import { QRCodeSVG } from 'qrcode.react'
import { Download, X } from 'lucide-react'

interface Props {
  maquinaId: number
  maquinaNome: string
  onClose: () => void
}

const APP_URL = (import.meta.env.VITE_APP_URL as string | undefined) ?? window.location.origin

export function MaquinaQrCode({ maquinaId, maquinaNome, onClose }: Props) {
  const svgRef = useRef<SVGSVGElement>(null)
  const url    = `${APP_URL}/manutencao/${maquinaId}`

  function handleDownload() {
    const svg = svgRef.current
    if (!svg) return

    const serializer = new XMLSerializer()
    const svgStr     = serializer.serializeToString(svg)
    const blob       = new Blob([svgStr], { type: 'image/svg+xml' })
    const link       = document.createElement('a')
    link.href        = URL.createObjectURL(blob)
    link.download    = `qrcode-${maquinaNome.replace(/\s+/g, '-').toLowerCase()}.svg`
    link.click()
    URL.revokeObjectURL(link.href)
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />

      <div className="relative z-10 w-full max-w-xs bg-[#0f1923] border border-white/10 rounded-2xl shadow-2xl p-6 space-y-5">

        <div className="flex items-center justify-between">
          <h2 className="text-sm font-semibold text-white">QR Code — Manutenção</h2>
          <button
            type="button"
            onClick={onClose}
            className="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        <div className="flex flex-col items-center gap-3">
          <div className="p-3 bg-white rounded-xl">
            <QRCodeSVG
              ref={svgRef}
              value={url}
              size={180}
              level="M"
              includeMargin={false}
            />
          </div>
          <p className="text-xs text-slate-400 text-center">{maquinaNome}</p>
          <p className="text-xs text-slate-600 font-mono text-center break-all">{url}</p>
        </div>

        <button
          type="button"
          onClick={handleDownload}
          className="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium text-white bg-[#00aa84] hover:bg-[#00aa84]/90 rounded-lg transition-colors"
        >
          <Download className="w-4 h-4" />
          Baixar SVG
        </button>
      </div>
    </div>
  )
}
