import { useState, useCallback } from 'react'
import { login as apiLogin, logout as apiLogout, type User, type LoginPayload } from '@/api/auth'

const TOKEN_KEY = 'potenza_token'
const USER_KEY = 'potenza_user'

function getStoredUser(): User | null {
  try {
    const raw = localStorage.getItem(USER_KEY)
    return raw ? (JSON.parse(raw) as User) : null
  } catch {
    return null
  }
}

function getStoredToken(): string | null {
  return localStorage.getItem(TOKEN_KEY)
}

export function useAuth() {
  const [user, setUser] = useState<User | null>(getStoredUser)
  const [token, setToken] = useState<string | null>(getStoredToken)
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const isAuthenticated = Boolean(token)

  const signIn = useCallback(async (payload: LoginPayload) => {
    setIsLoading(true)
    setError(null)
    try {
      const result = await apiLogin(payload)
      localStorage.setItem(TOKEN_KEY, result.token)
      localStorage.setItem(USER_KEY, JSON.stringify(result.user))
      setToken(result.token)
      setUser(result.user)
      return result
    } catch (err: unknown) {
      const message =
        (err as { response?: { data?: { message?: string } } })?.response?.data?.message ??
        'Credenciais inválidas. Verifique e-mail e senha.'
      setError(message)
      throw err
    } finally {
      setIsLoading(false)
    }
  }, [])

  const signOut = useCallback(async () => {
    try {
      await apiLogout()
    } catch {
      // ignora erros no logout — limpa o estado de qualquer forma
    } finally {
      localStorage.removeItem(TOKEN_KEY)
      localStorage.removeItem(USER_KEY)
      setToken(null)
      setUser(null)
    }
  }, [])

  return { user, token, isAuthenticated, isLoading, error, signIn, signOut }
}
