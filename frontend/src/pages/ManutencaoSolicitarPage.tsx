import { useEffect, useState } from 'react'
import { useParams } from 'react-router-dom'
import axios from 'axios'
import { Wrench, CheckCircle2, AlertTriangle, Loader2 } from 'lucide-react'
import { getMaquinaPublica, criarSolicitacao, type MaquinaPublica } from '@/api/manutencao'

interface FormState {
  solicitante: string
  motivo: string
}

const EMPTY: FormState = { solicitante: '', motivo: '' }

export function ManutencaoSolicitarPage() {
  const { maquinaId } = useParams<{ maquinaId: string }>()
  const id = Number(maquinaId)

  const [maquina, setMaquina]           = useState<MaquinaPublica | null>(null)
  const [loadingMaquina, setLoadingMaquina] = useState(true)
  const [erroMaquina, setErroMaquina]   = useState<string | null>(null)

  const [form, setForm]     = useState<FormState>(EMPTY)
  const [saving, setSaving] = useState(false)
  const [erro, setErro]     = useState<string | null>(null)
  const [enviado, setEnviado] = useState(false)

  useEffect(() => {
    if (!id) {
      setErroMaquina('ID de máquina inválido.')
      setLoadingMaquina(false)
      return
    }

    const controller = new AbortController()
    getMaquinaPublica(id, controller.signal)
      .then(setMaquina)
      .catch((err: unknown) => {
        if (!axios.isCancel(err)) setErroMaquina('Máquina não encontrada ou inativa.')
      })
      .finally(() => setLoadingMaquina(false))

    return () => controller.abort()
  }, [id])

  function handleField(e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) {
    const { name, value } = e.target
    setForm(prev => ({ ...prev, [name]: value }))
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    setErro(null)

    if (!form.solicitante.trim()) { setErro('Informe seu nome.'); return }
    if (!form.motivo.trim())      { setErro('Descreva o motivo da solicitação.'); return }
    if (!maquina)                 return

    setSaving(true)
    try {
      await criarSolicitacao({
        maquina_id:  maquina.id,
        solicitante: form.solicitante.trim(),
        motivo:      form.motivo.trim(),
      })
      setEnviado(true)
    } catch (err: unknown) {
      if (axios.isAxiosError(err) && err.response?.data?.message) {
        setErro(err.response.data.message as string)
      } else {
        setErro('Não foi possível enviar a solicitação. Tente novamente.')
      }
    } finally {
      setSaving(false)
    }
  }

  if (loadingMaquina) {
    return (
      <div className="min-h-screen bg-[#0a1118] flex items-center justify-center">
        <Loader2 className="w-8 h-8 text-[#00aa84] animate-spin" />
      </div>
    )
  }

  if (erroMaquina) {
    return (
      <div className="min-h-screen bg-[#0a1118] flex items-center justify-center p-6">
        <div className="text-center space-y-3">
          <AlertTriangle className="w-12 h-12 text-red-400 mx-auto" />
          <p className="text-white font-medium">{erroMaquina}</p>
          <p className="text-slate-400 text-sm">Verifique se o QR code está correto.</p>
        </div>
      </div>
    )
  }

  if (enviado) {
    return (
      <div className="min-h-screen bg-[#0a1118] flex items-center justify-center p-6">
        <div className="text-center space-y-4 max-w-sm">
          <div className="w-16 h-16 rounded-full bg-[#00aa84]/15 flex items-center justify-center mx-auto">
            <CheckCircle2 className="w-9 h-9 text-[#00aa84]" />
          </div>
          <div>
            <h2 className="text-xl font-semibold text-white">Solicitação enviada!</h2>
            <p className="text-slate-400 text-sm mt-1">
              Nossa equipe de manutenção foi notificada e entrará em contato em breve.
            </p>
          </div>
          <p className="text-xs text-slate-500">Máquina: {maquina?.nome}</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-[#0a1118] flex items-center justify-center p-6">
      <div className="w-full max-w-md space-y-6">

        <div className="text-center space-y-2">
          <div className="w-14 h-14 rounded-2xl bg-[#00aa84]/15 flex items-center justify-center mx-auto">
            <Wrench className="w-7 h-7 text-[#00aa84]" />
          </div>
          <h1 className="text-xl font-semibold text-white">Solicitação de Manutenção</h1>
          <div className="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 rounded-lg border border-white/10">
            <span className="text-sm text-slate-300 font-medium">{maquina?.nome}</span>
            {maquina?.codigo && (
              <span className="text-xs text-slate-500 font-mono">{maquina.codigo}</span>
            )}
          </div>
        </div>

        <form onSubmit={handleSubmit} className="bg-[#0f1923] border border-white/5 rounded-2xl p-6 space-y-4">

          <div>
            <label className="block text-xs font-medium text-slate-400 mb-1.5">
              Seu nome <span className="text-red-400">*</span>
            </label>
            <input
              name="solicitante"
              value={form.solicitante}
              onChange={handleField}
              placeholder="Ex: João Silva"
              autoComplete="name"
              className="w-full px-3 py-2.5 text-sm bg-white/5 border border-white/10 rounded-lg text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00aa84]/60 focus:bg-[#00aa84]/5 transition-colors"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-400 mb-1.5">
              Motivo da solicitação <span className="text-red-400">*</span>
            </label>
            <textarea
              name="motivo"
              value={form.motivo}
              onChange={handleField}
              placeholder="Descreva o problema ou motivo da manutenção…"
              rows={4}
              className="w-full px-3 py-2.5 text-sm bg-white/5 border border-white/10 rounded-lg text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00aa84]/60 focus:bg-[#00aa84]/5 transition-colors resize-none"
            />
          </div>

          {erro && (
            <p className="text-xs text-red-400 bg-red-400/10 border border-red-400/20 rounded-lg px-3 py-2">
              {erro}
            </p>
          )}

          <button
            type="submit"
            disabled={saving}
            className="w-full py-3 text-sm font-semibold text-white bg-[#00aa84] hover:bg-[#00aa84]/90 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl transition-colors flex items-center justify-center gap-2"
          >
            {saving
              ? <><Loader2 className="w-4 h-4 animate-spin" />Enviando…</>
              : 'Enviar solicitação'}
          </button>
        </form>

      </div>
    </div>
  )
}
