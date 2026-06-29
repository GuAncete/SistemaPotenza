import { Navigate } from 'react-router-dom'
import { useAuth } from '@/hooks/useAuth'

export function AdminHome() {
  const { user } = useAuth()

  if (user?.role !== 'funcionario') {
    return <Navigate to="dashboard" replace />
  }

  const primeira = user.rotinas?.find((r) => r.pagina != null)
  if (primeira?.pagina) {
    return <Navigate to={primeira.pagina} replace />
  }

  return <Navigate to="/admin/sem-acesso" replace />
}
