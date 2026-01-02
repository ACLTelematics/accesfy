<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Personal del Gimnasio</h1>
        <p class="text-gray-400">Gestiona los empleados con acceso al sistema</p>
      </div>
      <button
        v-if="canCreate"
        @click="showCreateModal = true"
        class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all flex items-center gap-2"
      >
        <UserPlus class="w-5 h-5" />
        Nuevo Empleado
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
            <Users class="w-5 h-5 text-blue-400" />
          </div>
        </div>
        <div class="text-2xl font-bold text-white mb-1">{{ staffList.length }}</div>
        <div class="text-sm text-gray-400">Total Empleados</div>
      </div>

      <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
            <CheckCircle class="w-5 h-5 text-green-400" />
          </div>
        </div>
        <div class="text-2xl font-bold text-white mb-1">
          {{ staffList.filter((s) => s.active).length }}
        </div>
        <div class="text-sm text-gray-400">Activos</div>
      </div>

      <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-2">
          <div class="w-10 h-10 bg-red-500/10 rounded-lg flex items-center justify-center">
            <XCircle class="w-5 h-5 text-red-400" />
          </div>
        </div>
        <div class="text-2xl font-bold text-white mb-1">
          {{ staffList.filter((s) => !s.active).length }}
        </div>
        <div class="text-sm text-gray-400">Inactivos</div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="flex gap-4">
      <div class="flex-1 relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre, email o usuario..."
          class="w-full pl-10 pr-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-500"
        />
      </div>
      <select
        v-model="statusFilter"
        class="px-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg text-white focus:outline-none focus:border-yellow-500"
      >
        <option value="all">Todos los estados</option>
        <option value="active">Activos</option>
        <option value="inactive">Inactivos</option>
      </select>
    </div>

    <!-- Staff Table -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
      <div v-if="loading" class="p-12 text-center">
        <Loader class="w-8 h-8 text-yellow-500 animate-spin mx-auto mb-4" />
        <p class="text-gray-400">Cargando empleados...</p>
      </div>

      <div v-else-if="filteredStaff.length === 0" class="p-12 text-center">
        <Users class="w-16 h-16 text-gray-600 mx-auto mb-4" />
        <p class="text-gray-400 mb-2">No hay empleados registrados</p>
        <p class="text-sm text-gray-500">
          {{ searchQuery ? 'No se encontraron resultados' : 'Comienza agregando empleados' }}
        </p>
      </div>

      <table v-else class="w-full">
        <thead class="bg-zinc-800 border-b border-zinc-700">
          <tr>
            <th class="text-left py-4 px-6 text-sm font-semibold text-gray-400">Empleado</th>
            <th class="text-left py-4 px-6 text-sm font-semibold text-gray-400">Usuario</th>
            <th class="text-left py-4 px-6 text-sm font-semibold text-gray-400">Email</th>
            <th class="text-left py-4 px-6 text-sm font-semibold text-gray-400">Teléfono</th>
            <th class="text-left py-4 px-6 text-sm font-semibold text-gray-400">Estado</th>
            <th class="text-left py-4 px-6 text-sm font-semibold text-gray-400">Fecha Alta</th>
            <th class="text-right py-4 px-6 text-sm font-semibold text-gray-400">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="staff in filteredStaff"
            :key="staff.id"
            class="border-b border-zinc-800 hover:bg-zinc-800/50 transition-all"
          >
            <td class="py-4 px-6">
              <div class="flex items-center gap-3">
                <div
                  class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center text-black font-bold text-sm"
                >
                  {{ getInitials(staff.name) }}
                </div>
                <div>
                  <p class="font-medium text-white">{{ staff.name }}</p>
                  <p class="text-xs text-gray-500">ID: {{ staff.id }}</p>
                </div>
              </div>
            </td>
            <td class="py-4 px-6">
              <span class="text-sm font-mono text-yellow-500">{{ staff.username }}</span>
            </td>
            <td class="py-4 px-6">
              <div class="flex items-center gap-2 text-gray-400">
                <Mail class="w-4 h-4" />
                <span class="text-sm">{{ staff.email || 'No registrado' }}</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <div class="flex items-center gap-2 text-gray-400">
                <Phone class="w-4 h-4" />
                <span class="text-sm">{{ staff.phone || 'No registrado' }}</span>
              </div>
            </td>
            <td class="py-4 px-6">
              <span
                :class="[
                  'inline-flex px-3 py-1 rounded-full text-xs font-medium',
                  staff.active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400',
                ]"
              >
                {{ staff.active ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="py-4 px-6">
              <span class="text-sm text-gray-400">{{ formatDate(staff.created_at) }}</span>
            </td>
            <td class="py-4 px-6">
              <div class="flex items-center justify-end gap-2">
                <button
                  v-if="canEdit"
                  @click="openEditModal(staff)"
                  class="p-2 hover:bg-zinc-700 rounded-lg transition-all"
                  title="Editar"
                >
                  <Edit2 class="w-4 h-4 text-gray-400" />
                </button>
                <button
                  @click="openResetPasswordModal(staff)"
                  class="p-2 hover:bg-zinc-700 rounded-lg transition-all"
                  title="Resetear Contraseña"
                >
                  <Key class="w-4 h-4 text-gray-400" />
                </button>
                <button
                  v-if="canDelete"
                  @click="confirmDelete(staff)"
                  class="p-2 hover:bg-red-900/20 rounded-lg transition-all"
                  title="Eliminar"
                >
                  <Trash2 class="w-4 h-4 text-red-400" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal: Crear/Editar Staff -->
    <Teleport to="body">
      <div
        v-if="showCreateModal || showEditModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @click.self="closeModals"
      >
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 max-w-2xl w-full">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">
              {{ showEditModal ? 'Editar Empleado' : 'Nuevo Empleado' }}
            </h3>
            <button @click="closeModals" class="text-gray-400 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Nombre Completo <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="formData.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="Juan Pérez"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Usuario (para login) <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="formData.username"
                  type="text"
                  :required="!showEditModal"
                  :disabled="showEditModal"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  placeholder="karina (sin espacios)"
                />
                <p v-if="!showEditModal" class="text-xs text-gray-500 mt-1">
                  Este será el nombre de usuario para hacer login
                </p>
                <p v-else class="text-xs text-gray-500 mt-1">El usuario no se puede modificar</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Email <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="formData.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="email@ejemplo.com"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Teléfono</label>
                <input
                  v-model="formData.phone"
                  type="tel"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="1234567890"
                />
              </div>

              <div v-if="!showEditModal" class="col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Contraseña <span class="text-red-400">*</span>
                </label>
                <input
                  v-model="formData.password"
                  type="password"
                  :required="!showEditModal"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                  placeholder="Mínimo 6 caracteres"
                />
              </div>

              <div class="col-span-2">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input
                    v-model="formData.active"
                    type="checkbox"
                    class="w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-green-500 focus:ring-green-500 focus:ring-offset-0"
                  />
                  <span class="text-gray-300">Cuenta activa</span>
                </label>
              </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-zinc-800">
              <button
                type="button"
                @click="closeModals"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="flex-1 px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <Loader v-if="submitting" class="w-5 h-5 animate-spin" />
                <Save v-else class="w-5 h-5" />
                {{ submitting ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal: Reset Password -->
    <Teleport to="body">
      <div
        v-if="showResetPasswordModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @click.self="showResetPasswordModal = false"
      >
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 max-w-md w-full">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Resetear Contraseña</h3>
            <button @click="showResetPasswordModal = false" class="text-gray-400 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

          <div v-if="!newPassword">
            <p class="text-gray-400 mb-4">
              Se generará una nueva contraseña para
              <strong class="text-white">{{ selectedStaff?.name }}</strong>
            </p>

            <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg mb-6">
              <div class="flex gap-3">
                <AlertCircle class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-yellow-200">
                  <p class="font-medium mb-1">Importante:</p>
                  <p>Deberás entregar la nueva contraseña al empleado.</p>
                </div>
              </div>
            </div>

            <div class="flex gap-3">
              <button
                @click="showResetPasswordModal = false"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all"
              >
                Cancelar
              </button>
              <button
                @click="resetPassword"
                :disabled="resettingPassword"
                class="flex-1 px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <Loader v-if="resettingPassword" class="w-5 h-5 animate-spin" />
                <Key v-else class="w-5 h-5" />
                {{ resettingPassword ? 'Generando...' : 'Generar Nueva Contraseña' }}
              </button>
            </div>
          </div>

          <div v-else>
            <div class="text-center mb-6">
              <div
                class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4"
              >
                <CheckCircle class="w-8 h-8 text-green-400" />
              </div>
              <p class="text-green-400 font-medium mb-4">¡Contraseña Actualizada!</p>

              <div class="p-6 bg-zinc-800 border border-zinc-700 rounded-lg mb-4">
                <p class="text-sm text-gray-400 mb-2">Nueva Contraseña</p>
                <p class="text-2xl font-bold text-yellow-500 tracking-wide mb-2">
                  {{ newPassword }}
                </p>
                <p class="text-xs text-gray-500">Entregar al empleado</p>
              </div>
            </div>

            <div class="flex gap-3 mb-4">
              <button
                @click="copyPassword"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all flex items-center justify-center gap-2"
              >
                <Copy class="w-4 h-4" />
                {{ copiedPassword ? '¡Copiado!' : 'Copiar' }}
              </button>
              <button
                @click="printPassword"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all flex items-center justify-center gap-2"
              >
                <Printer class="w-4 h-4" />
                Imprimir
              </button>
            </div>

            <button
              @click="closeResetPasswordModal"
              class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all"
            >
              Entendido
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  Users,
  UserPlus,
  Search,
  Edit2,
  Trash2,
  Key,
  Mail,
  Phone,
  CheckCircle,
  XCircle,
  AlertCircle,
  Loader,
  X,
  Save,
  Copy,
  Printer,
} from 'lucide-vue-next'

const authStore = useAuthStore()

const loading = ref(true)
const submitting = ref(false)
const resettingPassword = ref(false)
const staffList = ref<any[]>([])
const searchQuery = ref('')
const statusFilter = ref('all')

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showResetPasswordModal = ref(false)
const selectedStaff = ref<any>(null)
const newPassword = ref('')
const copiedPassword = ref(false)

const formData = ref({
  name: '',
  username: '',
  email: '',
  phone: '',
  password: '',
  active: true,
})

const canCreate = computed(() => {
  const role = authStore.userRole
  return role === 'gym_owner' || role === 'super_user'
})

const canEdit = computed(() => {
  const role = authStore.userRole
  return role === 'gym_owner' || role === 'super_user'
})

const canDelete = computed(() => {
  const role = authStore.userRole
  return role === 'gym_owner' || role === 'super_user'
})

const filteredStaff = computed(() => {
  let filtered = staffList.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (staff) =>
        staff.name.toLowerCase().includes(query) ||
        staff.username?.toLowerCase().includes(query) ||
        staff.email?.toLowerCase().includes(query) ||
        staff.phone?.includes(query),
    )
  }

  if (statusFilter.value !== 'all') {
    const isActive = statusFilter.value === 'active'
    filtered = filtered.filter((staff) => staff.active === isActive)
  }

  return filtered
})

const fetchStaff = async () => {
  try {
    loading.value = true
    const { data } = await api.get('/gym-owner/staff')
    staffList.value = data
  } catch (error: any) {
    console.error('Error cargando staff:', error)
    alert('Error al cargar la lista de empleados')
  } finally {
    loading.value = false
  }
}

const openEditModal = (staff: any) => {
  selectedStaff.value = staff
  formData.value = {
    name: staff.name,
    username: staff.username,
    email: staff.email || '',
    phone: staff.phone || '',
    password: '',
    active: staff.active,
  }
  showEditModal.value = true
}

const openResetPasswordModal = (staff: any) => {
  selectedStaff.value = staff
  newPassword.value = ''
  copiedPassword.value = false
  showResetPasswordModal.value = true
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  selectedStaff.value = null
  formData.value = {
    name: '',
    username: '',
    email: '',
    phone: '',
    password: '',
    active: true,
  }
}

const closeResetPasswordModal = () => {
  showResetPasswordModal.value = false
  selectedStaff.value = null
  newPassword.value = ''
  copiedPassword.value = false
}

const handleSubmit = async () => {
  try {
    submitting.value = true

    const user = authStore.user
    const userRole = authStore.userRole

    const payload: any = {
      name: formData.value.name,
      email: formData.value.email || null,
      phone: formData.value.phone || null,
      active: formData.value.active,
    }

    if (showEditModal.value) {
      // EDITAR - no enviar gym_owner_id, username ni password
      await api.put(`/staff/${selectedStaff.value.id}`, payload)
      alert('✅ Empleado actualizado exitosamente')
    } else {
      // CREAR - necesita gym_owner_id, username y password

      // Determinar gym_owner_id según el rol
      if (userRole === 'gym_owner') {
        payload.gym_owner_id = user.id
      } else if (userRole === 'super_user') {
        alert('SuperUser debe especificar el gym_owner_id')
        return
      } else {
        alert('No tienes permisos para crear staff')
        return
      }

      // Usar el username que el usuario escribió (sin números automáticos)
      payload.username = formData.value.username
      payload.password = formData.value.password

      const response = await api.post('/staff', payload)

      alert(
        `✅ Empleado creado exitosamente\n\nUsuario: ${payload.username}\nContraseña: ${payload.password}\n\n⚠️ Guarda estas credenciales`,
      )
    }

    closeModals()
    fetchStaff()
  } catch (error: any) {
    console.error('Error guardando staff:', error)
    const errors = error.response?.data?.errors
    if (errors) {
      const errorMessages = Object.values(errors).flat().join('\n')
      alert(`Errores de validación:\n${errorMessages}`)
    } else {
      alert(error.response?.data?.message || 'Error al guardar empleado')
    }
  } finally {
    submitting.value = false
  }
}

const resetPassword = async () => {
  try {
    resettingPassword.value = true

    const password = Math.random().toString(36).slice(-8)

    await api.post(`/staff/${selectedStaff.value.id}/reset-password`, {
      password: password,
      password_confirmation: password,
    })

    newPassword.value = password
  } catch (error: any) {
    console.error('Error reseteando contraseña:', error)
    const errors = error.response?.data?.errors
    if (errors) {
      const errorMessages = Object.values(errors).flat().join('\n')
      alert(`Errores:\n${errorMessages}`)
    } else {
      alert(error.response?.data?.message || 'Error al resetear contraseña')
    }
    showResetPasswordModal.value = false
  } finally {
    resettingPassword.value = false
  }
}

const confirmDelete = async (staff: any) => {
  if (!confirm(`¿Estás seguro de eliminar a ${staff.name}?\n\nUsuario: ${staff.username}`)) return

  try {
    await api.delete(`/staff/${staff.id}`)
    alert('✅ Empleado eliminado exitosamente')
    fetchStaff()
  } catch (error: any) {
    console.error('Error eliminando staff:', error)
    alert(error.response?.data?.message || 'Error al eliminar empleado')
  }
}

const copyPassword = () => {
  navigator.clipboard.writeText(newPassword.value)
  copiedPassword.value = true
  setTimeout(() => {
    copiedPassword.value = false
  }, 2000)
}

const printPassword = () => {
  const printWindow = window.open('', '', 'width=400,height=500')
  if (!printWindow) return

  printWindow.document.write(`
    <html>
      <head>
        <title>Contraseña - ${selectedStaff.value.name}</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            padding: 40px;
            text-align: center;
          }
          h1 { font-size: 24px; margin-bottom: 10px; }
          h2 { font-size: 20px; color: #666; margin-bottom: 30px; }
          .password {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #EAB308;
            margin: 30px 0;
            border: 3px solid #EAB308;
            padding: 20px;
            border-radius: 10px;
          }
          .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #999;
          }
        </style>
      </head>
      <body>
        <h1>${selectedStaff.value.name}</h1>
        <h2>Nueva Contraseña</h2>
        <div class="password">${newPassword.value}</div>
        <p>Usa esta contraseña para iniciar sesión en el sistema</p>
        <div class="footer">
          <p>Generado: ${new Date().toLocaleString('es-MX')}</p>
          <p>Mantén esta contraseña segura</p>
        </div>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}

const getInitials = (name: string) => {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const formatDate = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchStaff()
})
</script>
