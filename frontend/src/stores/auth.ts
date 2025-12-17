import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'
import { useRouter } from 'vue-router'

interface User {
  id: number
  name: string
  email: string
  username: string
  gym_name: string
  activated_until?: string
  active: boolean
}

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
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

  // Login
  const login = async (username: string, password: string) => {
    loading.value = true
    error.value = ''

    try {
      const response = await api.post('/auth/gym-owner/login', {
        username,
        password,
      })

      token.value = response.data.token
      user.value = response.data.user

      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))

      router.push('/dashboard')
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Credenciales incorrectas'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Logout
  const logout = async () => {
    try {
      if (token.value) {
        await api.post('/auth/gym-owner/logout')
      }
    } catch (err) {
      console.error('Error al cerrar sesión:', err)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      router.push('/login')
    }
  }

  // Obtener usuario actual
  const fetchUser = async () => {
    try {
      const response = await api.get('/auth/gym-owner/me')
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
