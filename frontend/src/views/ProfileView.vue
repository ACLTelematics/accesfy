<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Mi Perfil</h1>
        <p class="text-gray-400">Gestiona tu información personal y credenciales</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="text-center">
        <div
          class="w-16 h-16 border-4 border-yellow-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-400">Cargando perfil...</p>
      </div>
    </div>

    <!-- Content -->
    <div v-else class="grid grid-cols-3 gap-6">
      <!-- Left Column: Profile Card -->
      <div class="col-span-1 space-y-6">
        <!-- Profile Card -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 text-center">
          <div
            class="w-24 h-24 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center text-black font-bold text-3xl mx-auto mb-4"
          >
            {{ getInitials(profile.name) }}
          </div>
          <h3 class="text-xl font-bold text-white mb-1">{{ profile.name }}</h3>
          <p class="text-sm text-gray-400 mb-4">{{ getRoleLabel(userRole) }}</p>

          <div class="space-y-2 text-left">
            <div class="flex items-center gap-2 text-gray-400 text-sm">
              <Mail class="w-4 h-4" />
              <span>{{ profile.email || 'No registrado' }}</span>
            </div>
            <div v-if="profile.phone" class="flex items-center gap-2 text-gray-400 text-sm">
              <Phone class="w-4 h-4" />
              <span>{{ profile.phone }}</span>
            </div>
            <div v-if="userRole === 'staff'" class="flex items-center gap-2 text-gray-400 text-sm">
              <User class="w-4 h-4" />
              <span>{{ profile.username }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-400 text-sm">
              <Calendar class="w-4 h-4" />
              <span>Miembro desde {{ formatDate(profile.created_at) }}</span>
            </div>
          </div>

          <!-- Gym Info (for GymOwner and Staff) -->
          <div
            v-if="userRole === 'gym_owner' || userRole === 'staff'"
            class="mt-4 pt-4 border-t border-zinc-800"
          >
            <div class="flex items-center gap-2 text-yellow-500 text-sm">
              <Building class="w-4 h-4" />
              <span class="font-medium">{{ gymName }}</span>
            </div>
          </div>
        </div>

        <!-- Account Status -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <h4 class="font-semibold text-white mb-3 flex items-center gap-2">
            <Shield class="w-5 h-5 text-green-400" />
            Estado de la Cuenta
          </h4>
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-400">Estado</span>
              <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-medium">
                {{ profile.active ? 'Activa' : 'Inactiva' }}
              </span>
            </div>
            <div
              v-if="userRole === 'gym_owner' && profile.subscription_expires"
              class="flex items-center justify-between"
            >
              <span class="text-sm text-gray-400">Suscripción</span>
              <span class="text-xs text-gray-500">
                Vence {{ formatDate(profile.subscription_expires) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Forms -->
      <div class="col-span-2 space-y-6">
        <!-- Personal Information -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <User class="w-5 h-5 text-yellow-500" />
            Información Personal
          </h3>

          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Nombre Completo <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="profileForm.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="Tu nombre completo"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Email <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="profileForm.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="email@ejemplo.com"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2"> Teléfono </label>
                <input
                  v-model="profileForm.phone"
                  type="tel"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="1234567890"
                />
              </div>

              <div v-if="userRole === 'staff'">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Usuario (login)
                </label>
                <input
                  :value="profile.username"
                  type="text"
                  disabled
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-gray-500 cursor-not-allowed"
                  title="El usuario no se puede modificar"
                />
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="updatingProfile"
                class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all disabled:opacity-50 flex items-center gap-2"
              >
                <Loader v-if="updatingProfile" class="w-5 h-5 animate-spin" />
                <Save v-else class="w-5 h-5" />
                {{ updatingProfile ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <Key class="w-5 h-5 text-yellow-500" />
            Cambiar Contraseña
          </h3>

          <form @submit.prevent="changePassword" class="space-y-4">
            <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
              <div class="flex gap-3">
                <Info class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-blue-200">
                  <p class="font-medium mb-1">Requisitos de seguridad:</p>
                  <ul class="list-disc list-inside space-y-1">
                    <li>Mínimo 6 caracteres</li>
                    <li>La nueva contraseña debe ser diferente a la actual</li>
                  </ul>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">
                Contraseña Actual <span class="text-red-400">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.current_password"
                  :type="showCurrentPassword ? 'text' : 'password'"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500 pr-12"
                  placeholder="Tu contraseña actual"
                />
                <button
                  type="button"
                  @click="showCurrentPassword = !showCurrentPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                >
                  <EyeOff v-if="showCurrentPassword" class="w-5 h-5" />
                  <Eye v-else class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">
                Nueva Contraseña <span class="text-red-400">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.new_password"
                  :type="showNewPassword ? 'text' : 'password'"
                  required
                  minlength="6"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500 pr-12"
                  placeholder="Mínimo 6 caracteres"
                />
                <button
                  type="button"
                  @click="showNewPassword = !showNewPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                >
                  <EyeOff v-if="showNewPassword" class="w-5 h-5" />
                  <Eye v-else class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">
                Confirmar Nueva Contraseña <span class="text-red-400">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.new_password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  minlength="6"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500 pr-12"
                  placeholder="Repite la nueva contraseña"
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                >
                  <EyeOff v-if="showConfirmPassword" class="w-5 h-5" />
                  <Eye v-else class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div class="flex justify-end gap-3">
              <button
                type="button"
                @click="resetPasswordForm"
                class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="changingPassword"
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all disabled:opacity-50 flex items-center gap-2"
              >
                <Loader v-if="changingPassword" class="w-5 h-5 animate-spin" />
                <Key v-else class="w-5 h-5" />
                {{ changingPassword ? 'Cambiando...' : 'Cambiar Contraseña' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Danger Zone (Only for testing - optional) -->
        <div
          v-if="userRole === 'super_user'"
          class="bg-red-900/10 border border-red-500/30 rounded-xl p-6"
        >
          <h3 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
            <AlertTriangle class="w-5 h-5" />
            Zona Peligrosa
          </h3>
          <p class="text-sm text-gray-400 mb-4">
            Acciones administrativas que requieren precaución.
          </p>
          <button
            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all"
          >
            Acciones Avanzadas
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  User,
  Mail,
  Phone,
  Calendar,
  Key,
  Save,
  Loader,
  Eye,
  EyeOff,
  Building,
  Shield,
  Info,
  AlertTriangle,
} from 'lucide-vue-next'

const authStore = useAuthStore()

const loading = ref(true)
const updatingProfile = ref(false)
const changingPassword = ref(false)

const profile = ref<any>({})
const gymName = ref('')

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const profileForm = ref({
  name: '',
  email: '',
  phone: '',
})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const userRole = computed(() => authStore.userRole)

const fetchProfile = async () => {
  try {
    loading.value = true

    // Obtener endpoint según el rol
    let endpoint = ''
    if (userRole.value === 'gym_owner') {
      endpoint = '/auth/gym-owner/me'
    } else if (userRole.value === 'staff') {
      endpoint = '/auth/staff/me'
    } else if (userRole.value === 'super_user') {
      endpoint = '/auth/super-user/me'
    }

    const { data } = await api.get(endpoint)
    profile.value = data

    // Llenar formulario
    profileForm.value = {
      name: data.name || '',
      email: data.email || '',
      phone: data.phone || '',
    }

    // Obtener nombre del gym si aplica
    if (userRole.value === 'gym_owner') {
      gymName.value = data.gym_name || 'Mi Gimnasio'
    } else if (userRole.value === 'staff' && data.gym_owner) {
      gymName.value = data.gym_owner.gym_name || 'Gimnasio'
    }
  } catch (error: any) {
    console.error('Error cargando perfil:', error)
    alert('Error al cargar tu perfil')
  } finally {
    loading.value = false
  }
}

const updateProfile = async () => {
  try {
    updatingProfile.value = true

    let endpoint = ''
    if (userRole.value === 'gym_owner') {
      endpoint = `/gym-owners/${profile.value.id}`
    } else if (userRole.value === 'staff') {
      endpoint = `/staff/${profile.value.id}`
    } else if (userRole.value === 'super_user') {
      endpoint = `/super-users/${profile.value.id}`
    }

    await api.put(endpoint, {
      name: profileForm.value.name,
      email: profileForm.value.email,
      phone: profileForm.value.phone || null,
    })

    // Actualizar el store
    authStore.user = {
      ...authStore.user,
      name: profileForm.value.name,
      email: profileForm.value.email,
    }

    alert('✅ Perfil actualizado exitosamente')
    fetchProfile()
  } catch (error: any) {
    console.error('Error actualizando perfil:', error)
    const errors = error.response?.data?.errors
    if (errors) {
      const errorMessages = Object.values(errors).flat().join('\n')
      alert(`Errores de validación:\n${errorMessages}`)
    } else {
      alert(error.response?.data?.message || 'Error al actualizar perfil')
    }
  } finally {
    updatingProfile.value = false
  }
}

const changePassword = async () => {
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    alert('❌ Las contraseñas no coinciden')
    return
  }

  if (passwordForm.value.new_password.length < 6) {
    alert('❌ La nueva contraseña debe tener al menos 6 caracteres')
    return
  }

  try {
    changingPassword.value = true

    let endpoint = ''
    if (userRole.value === 'gym_owner') {
      endpoint = '/gym-owners/change-password'
    } else if (userRole.value === 'staff') {
      endpoint = '/staff/change-password'
    } else if (userRole.value === 'super_user') {
      endpoint = '/super-users/change-password'
    }

    await api.post(endpoint, {
      current_password: passwordForm.value.current_password,
      new_password: passwordForm.value.new_password,
      new_password_confirmation: passwordForm.value.new_password_confirmation,
    })

    alert('✅ Contraseña cambiada exitosamente')
    resetPasswordForm()
  } catch (error: any) {
    console.error('Error cambiando contraseña:', error)
    const errors = error.response?.data?.errors
    if (errors) {
      const errorMessages = Object.values(errors).flat().join('\n')
      alert(`Errores:\n${errorMessages}`)
    } else {
      alert(error.response?.data?.message || 'Error al cambiar contraseña')
    }
  } finally {
    changingPassword.value = false
  }
}

const resetPasswordForm = () => {
  passwordForm.value = {
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  }
  showCurrentPassword.value = false
  showNewPassword.value = false
  showConfirmPassword.value = false
}

const getInitials = (name: string) => {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2 && parts[0] && parts[1]) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const getRoleLabel = (role: string) => {
  const labels: Record<string, string> = {
    gym_owner: 'Administrador',
    staff: 'Empleado',
    super_user: 'Super Administrador',
  }
  return labels[role] || role
}

const formatDate = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchProfile()
})
</script>
