import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { LoginPage }        from '@/pages/LoginPage'
import { MaquinasPage }     from '@/pages/MaquinasPage'
import { OperariosPage }    from '@/pages/OperariosPage'
import { ApontamentosPage } from '@/pages/ApontamentosPage'
import { AdminLayout }      from '@/layouts/AdminLayout'
import { ProtectedRoute }   from '@/components/ProtectedRoute'

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />

        {/* Área admin — protegida por role */}
        <Route
          path="/admin"
          element={
            <ProtectedRoute requiredRole={['admin', 'gestor']}>
              <AdminLayout />
            </ProtectedRoute>
          }
        >
          <Route index element={<Navigate to="maquinas" replace />} />
          <Route path="maquinas"     element={<MaquinasPage />} />
          <Route path="operarios"    element={<OperariosPage />} />
          <Route path="apontamentos" element={<ApontamentosPage />} />
        </Route>

        {/* Raiz e fallback */}
        <Route path="/"  element={<Navigate to="/admin/maquinas" replace />} />
        <Route path="*"  element={<Navigate to="/login" replace />} />
      </Routes>
    </BrowserRouter>
  )
}

export default App
