import { useAuth } from '@/hooks/useAuth'

export function DashboardPage() {
  const { user, signOut } = useAuth()

  return (
    <div className="min-h-screen bg-slate-900 text-white flex flex-col items-center justify-center gap-6">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold text-orange-500">POTENZA</h1>
        <p className="text-slate-400">Sistema de Apontamento</p>
      </div>
      <div className="bg-white/5 border border-white/10 rounded-xl px-8 py-6 text-center space-y-2">
        <p className="text-slate-300">
          Bem-vindo, <span className="font-semibold text-white">{user?.name}</span>!
        </p>
        <p className="text-slate-500 text-sm capitalize">Perfil: {user?.role}</p>
      </div>
      <button
        onClick={signOut}
        className="text-slate-500 hover:text-slate-300 text-sm underline transition-colors"
      >
        Sair
      </button>
    </div>
  )
}
