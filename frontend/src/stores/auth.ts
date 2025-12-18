import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'
import { useRouter } from 'vue-router'

type UserRole = 'super_user' | 'gym_owner' | 'staff'

interface User {
  id: number
  name: string
  email: string
  username: string
  gym_name?: string
  activated_until?: string
  active: boolean
}

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const userRole = ref<UserRole | null>(localStorage.getItem('userRole') as UserRole)
  const loading = ref(false)
  const error = ref('')

  // Computed
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userName = computed(() => user.value?.name || '')
  const gymName = computed(() => user.value?.gym_name || '')

  // Cargar usuario desde localStorage al iniciar
  const initAuth = () => {
    const storedUser = localStorage.getItem('user')
    if (storedUser && token.value) {
      try {
        user.value = JSON.parse(storedUser)
      } catch (e) {
        console.error('Error parsing user:', e)
        logout()
      }
    }
  }

  // Login automático - Detecta el rol desde el backend
  const login = async (username: string, password: string) => {
    loading.value = true
    error.value = ''

    // Intentar en orden: staff → gym_owner → super_user
    const loginAttempts = [
      { endpoint: '/auth/gym-owner/login', role: 'gym_owner' as UserRole }, // ← PRIMERO
      { endpoint: '/auth/staff/login', role: 'staff' as UserRole },
      { endpoint: '/auth/super-user/login', role: 'super_user' as UserRole },
    ]

    for (const attempt of loginAttempts) {
      try {
        const response = await api.post(attempt.endpoint, {
          username,
          password,
        })

        // Si llegamos aquí, el login fue exitoso
        token.value = response.data.token
        user.value = response.data.user
        userRole.value = response.data.role || attempt.role

        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        localStorage.setItem('userRole', userRole.value)

        loading.value = false
        router.push('/dashboard')
        return
      } catch (err) {
        // Si falla, continuar con el siguiente intento
        continue
      }
    }

    // Si llegamos aquí, todos los intentos fallaron
    loading.value = false
    error.value = 'Credenciales incorrectas'
    throw new Error('Credenciales incorrectas')
  }

  // Logout
  const logout = async () => {
    try {
      if (token.value && userRole.value) {
        const endpoints: Record<UserRole, string> = {
          super_user: '/auth/super-user/logout',
          gym_owner: '/auth/gym-owner/logout',
          staff: '/auth/staff/logout',
        }
        await api.post(endpoints[userRole.value])
      }
    } catch (err) {
      console.error('Error al cerrar sesión:', err)
    } finally {
      token.value = null
      user.value = null
      userRole.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('userRole')
      router.push('/login')
    }
  }

  // Obtener usuario actual
  const fetchUser = async () => {
    if (!userRole.value) return

    try {
      const endpoints: Record<UserRole, string> = {
        super_user: '/auth/super-user/me',
        gym_owner: '/auth/gym-owner/me',
        staff: '/auth/staff/me',
      }
      const response = await api.get(endpoints[userRole.value])
      user.value = response.data
      localStorage.setItem('user', JSON.stringify(response.data))
    } catch (err) {
      console.error('Error al obtener usuario:', err)
      logout()
    }
  }

  // Verificar si el token es válido
  const checkAuth = async () => {
    if (!token.value) {
      return false
    }

    try {
      await fetchUser()
      return true
    } catch {
      logout()
      return false
    }
  }

  return {
    user,
    token,
    userRole,
    loading,
    error,
    isAuthenticated,
    userName,
    gymName,
    initAuth,
    login,
    logout,
    fetchUser,
    checkAuth,
  }
})
