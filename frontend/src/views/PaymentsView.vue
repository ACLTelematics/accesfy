<template>
  <div class="p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-white mb-2">Registro de Pagos</h1>
      <p class="text-zinc-400">Registra los pagos de tus miembros y gestiona sus membresías</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Formulario de Pago -->
      <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-6">
        <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
          <DollarSign :size="24" class="text-[#f7c948]" />
          Nuevo Pago
        </h2>

        <form @submit.prevent="handleSubmit">
          <!-- Búsqueda de Cliente -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-zinc-300 mb-2">
              Buscar Cliente <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <Search
                :size="18"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 z-10"
              />
              <input
                v-model="searchQuery"
                @input="searchClients"
                type="text"
                placeholder="Buscar por nombre, teléfono o email..."
                class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
              />

              <!-- Dropdown de resultados -->
              <div
                v-if="searchResults.length > 0 && searchQuery"
                class="absolute w-full mt-2 bg-zinc-800 border border-zinc-700 rounded-lg shadow-xl z-20 max-h-64 overflow-y-auto"
              >
                <button
                  v-for="client in searchResults"
                  :key="client.id"
                  type="button"
                  @click="selectClient(client)"
                  class="w-full px-4 py-3 text-left hover:bg-zinc-700 transition-colors border-b border-zinc-700 last:border-b-0"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-white font-medium">{{ client.name }}</p>
                      <p class="text-zinc-400 text-sm">{{ client.phone || client.email }}</p>
                    </div>
                    <div :class="['px-2 py-1 rounded text-xs', getMembershipStatus(client).class]">
                      {{ getMembershipStatus(client).text }}
                    </div>
                  </div>
                </button>
              </div>

              <!-- No hay resultados -->
              <div
                v-if="searchQuery && searchResults.length === 0 && !searchLoading"
                class="absolute w-full mt-2 bg-zinc-800 border border-zinc-700 rounded-lg shadow-xl z-20 p-4 text-center text-zinc-400"
              >
                No se encontraron clientes
              </div>
            </div>

            <!-- Cliente Seleccionado -->
            <div
              v-if="selectedClient"
              class="mt-3 p-4 bg-zinc-800/50 border border-zinc-700 rounded-lg"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <User :size="18" class="text-[#f7c948]" />
                    <p class="text-white font-medium">{{ selectedClient.name }}</p>
                  </div>
                  <div class="space-y-1 text-sm">
                    <p class="text-zinc-400">
                      <span class="text-zinc-500">Teléfono:</span>
                      {{ selectedClient.phone || 'No registrado' }}
                    </p>
                    <p class="text-zinc-400">
                      <span class="text-zinc-500">Membresía:</span>
                      {{ selectedClient.membership?.type || 'Sin membresía' }}
                    </p>
                    <p class="text-zinc-400">
                      <span class="text-zinc-500">Vence:</span>
                      {{
                        selectedClient.membership_expires
                          ? formatDate(selectedClient.membership_expires)
                          : 'No definida'
                      }}
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="clearClient"
                  class="text-zinc-500 hover:text-red-500 transition-colors"
                >
                  <X :size="20" />
                </button>
              </div>
            </div>
          </div>

          <!-- Monto -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-zinc-300 mb-2">
              Monto <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <DollarSign
                :size="18"
                class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500"
              />
              <input
                v-model.number="formData.amount"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                placeholder="0.00"
              />
            </div>
          </div>

          <!-- Método de Pago -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-zinc-300 mb-2">
              Método de Pago <span class="text-red-500">*</span>
            </label>
            <select
              v-model="formData.method"
              required
              class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
            >
              <option value="">Seleccionar...</option>
              <option value="cash">Efectivo</option>
              <option value="card">Tarjeta</option>
              <option value="transfer">Transferencia</option>
              <option value="other">Otro</option>
            </select>
          </div>

          <!-- Frecuencia -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-zinc-300 mb-2">
              Frecuencia <span class="text-red-500">*</span>
            </label>
            <select
              v-model="formData.frequency"
              required
              class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
            >
              <option value="">Seleccionar...</option>
              <option value="daily">Diario</option>
              <option value="monthly">Mensual</option>
              <option value="visit">Por Visita</option>
            </select>
          </div>

          <!-- Fecha de Pago -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-zinc-300 mb-2"> Fecha de Pago </label>
            <input
              v-model="formData.payment_date"
              type="date"
              class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
            />
          </div>

          <!-- Nota -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-zinc-300 mb-2"> Nota (Opcional) </label>
            <textarea
              v-model="formData.note"
              rows="3"
              class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors resize-none"
              placeholder="Agregar nota sobre este pago..."
            ></textarea>
          </div>

          <!-- 3 OPCIONES CRÍTICAS -->
          <div class="mb-6 p-4 bg-zinc-800/50 border border-zinc-700 rounded-lg">
            <h3 class="text-white font-medium mb-3 flex items-center gap-2">
              <Settings :size="18" class="text-[#f7c948]" />
              Opciones Adicionales
            </h3>

            <div class="space-y-3">
              <!-- Opción 1: Extender Membresía -->
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  v-model="formData.extend_membership"
                  type="checkbox"
                  class="w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-[#f7c948] focus:ring-[#f7c948] focus:ring-offset-0"
                />
                <div>
                  <p class="text-white group-hover:text-[#f7c948] transition-colors">
                    Extender membresía +30 días
                  </p>
                  <p class="text-zinc-500 text-sm">La membresía se extenderá automáticamente</p>
                </div>
              </label>

              <!-- Opción 2: Desactivar Cuenta -->
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  v-model="formData.deactivate_account"
                  type="checkbox"
                  class="w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-red-500 focus:ring-red-500 focus:ring-offset-0"
                />
                <div>
                  <p class="text-white group-hover:text-red-500 transition-colors">
                    Desactivar cuenta del cliente
                  </p>
                  <p class="text-zinc-500 text-sm">⚠️ El cliente no podrá hacer check-in</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Botón Submit -->
          <button
            type="submit"
            :disabled="loading || !selectedClient"
            class="w-full px-6 py-3 bg-[#f7c948] text-black rounded-lg hover:bg-[#FFDB5C] transition-colors font-medium flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Loader v-if="loading" :size="18" class="animate-spin" />
            <Save v-else :size="18" />
            {{ loading ? 'Registrando...' : 'Registrar Pago' }}
          </button>
        </form>
      </div>

      <!-- Historial de Pagos -->
      <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-white flex items-center gap-2">
            <FileText :size="24" class="text-[#f7c948]" />
            Historial de Pagos
          </h2>
          <button
            @click="fetchPayments"
            class="p-2 hover:bg-zinc-800 rounded-lg transition-colors"
            title="Actualizar"
          >
            <RefreshCw :size="18" class="text-zinc-400" />
          </button>
        </div>

        <!-- Filtro por Cliente -->
        <div v-if="selectedClient" class="mb-4">
          <button
            @click="filterBySelectedClient"
            class="text-sm text-[#f7c948] hover:text-[#FFDB5C] transition-colors flex items-center gap-2"
          >
            <Filter :size="16" />
            Ver pagos de {{ selectedClient.name }}
          </button>
          <button
            v-if="filteredByClient"
            @click="clearFilter"
            class="text-sm text-zinc-500 hover:text-zinc-400 transition-colors flex items-center gap-2 mt-2"
          >
            <X :size="16" />
            Mostrar todos los pagos
          </button>
        </div>

        <!-- Loading -->
        <div v-if="paymentsLoading" class="flex items-center justify-center py-12">
          <Loader :size="32" class="animate-spin text-[#f7c948]" />
        </div>

        <!-- Lista de Pagos -->
        <div
          v-else-if="displayedPayments.length > 0"
          class="space-y-3 max-h-[600px] overflow-y-auto"
        >
          <div
            v-for="payment in displayedPayments"
            :key="payment.id"
            class="p-4 bg-zinc-800/50 border border-zinc-700 rounded-lg hover:border-zinc-600 transition-colors"
          >
            <div class="flex items-start justify-between mb-2">
              <div class="flex-1">
                <p class="text-white font-medium">
                  {{ payment.client?.name || 'Cliente eliminado' }}
                </p>
                <p class="text-zinc-400 text-sm">{{ formatDate(payment.payment_date) }}</p>
              </div>
              <div class="text-right">
                <p class="text-[#f7c948] font-bold text-lg">${{ payment.amount }}</p>
                <p :class="['text-xs px-2 py-1 rounded', getMethodClass(payment.method)]">
                  {{ getMethodLabel(payment.method) }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-4 text-sm text-zinc-500">
              <span class="flex items-center gap-1">
                <Calendar :size="14" />
                {{ getFrequencyLabel(payment.frequency) }}
              </span>
              <span v-if="payment.staff" class="flex items-center gap-1">
                <User :size="14" />
                {{ payment.staff.name }}
              </span>
            </div>

            <p v-if="payment.note" class="mt-2 text-sm text-zinc-400 italic">
              "{{ payment.note }}"
            </p>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-12 text-center">
          <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mb-4">
            <FileText :size="32" class="text-zinc-600" />
          </div>
          <p class="text-zinc-400 mb-2">No hay pagos registrados</p>
          <p class="text-zinc-500 text-sm">Los pagos aparecerán aquí cuando se registren</p>
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
  DollarSign,
  Search,
  User,
  X,
  Save,
  Loader,
  Settings,
  FileText,
  RefreshCw,
  Filter,
  Calendar,
} from 'lucide-vue-next'

const authStore = useAuthStore()

// Estados
const loading = ref(false)
const paymentsLoading = ref(false)
const searchLoading = ref(false)
const searchQuery = ref('')
const searchResults = ref<any[]>([])
const selectedClient = ref<any>(null)
const payments = ref<any[]>([])
const filteredByClient = ref(false)

// Form Data
const formData = ref({
  amount: 0,
  method: '',
  frequency: '',
  payment_date: new Date().toISOString().split('T')[0],
  note: '',
  extend_membership: false,
  deactivate_account: false,
})

// Computed
const displayedPayments = computed(() => {
  if (filteredByClient.value && selectedClient.value) {
    return payments.value.filter((p) => p.client_id === selectedClient.value.id)
  }
  return payments.value
})

// Funciones
let searchTimeout: any = null

const searchClients = () => {
  if (!searchQuery.value || searchQuery.value.length < 2) {
    searchResults.value = []
    return
  }

  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(async () => {
    try {
      searchLoading.value = true
      const response = await api.get(`/clients/search`, {
        params: { query: searchQuery.value },
      })
      searchResults.value = response.data
    } catch (error) {
      console.error('Error al buscar clientes:', error)
    } finally {
      searchLoading.value = false
    }
  }, 300)
}

const selectClient = (client: any) => {
  selectedClient.value = client
  searchQuery.value = ''
  searchResults.value = []
}

const clearClient = () => {
  selectedClient.value = null
  searchQuery.value = ''
  searchResults.value = []
  filteredByClient.value = false
}

const getMembershipStatus = (client: any) => {
  if (!client.membership_expires) {
    return { text: 'Sin membresía', class: 'bg-zinc-700 text-zinc-400' }
  }

  const expiresAt = new Date(client.membership_expires)
  const now = new Date()
  const daysLeft = Math.ceil((expiresAt.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))

  if (daysLeft < 0) {
    return { text: 'Vencida', class: 'bg-red-500/20 text-red-400' }
  } else if (daysLeft <= 7) {
    return { text: `${daysLeft} días`, class: 'bg-yellow-500/20 text-yellow-400' }
  } else {
    return { text: 'Activa', class: 'bg-green-500/20 text-green-400' }
  }
}

const handleSubmit = async () => {
  if (!selectedClient.value) {
    alert('Selecciona un cliente')
    return
  }

  if (!formData.value.amount || formData.value.amount <= 0) {
    alert('Ingresa un monto válido')
    return
  }

  try {
    loading.value = true

    const user = authStore.user
    let gymOwnerId: number
    let staffId: number | null = null

    if (user.role === 'gym_owner') {
      gymOwnerId = user.id
    } else if (user.role === 'staff') {
      gymOwnerId = user.gym_owner_id
      staffId = user.id
    } else {
      alert('No tienes permisos para registrar pagos')
      return
    }

    const payload = {
      gym_owner_id: gymOwnerId,
      client_id: selectedClient.value.id,
      staff_id: staffId,
      amount: formData.value.amount,
      method: formData.value.method,
      frequency: formData.value.frequency,
      payment_date: formData.value.payment_date,
      note: formData.value.note || null,
      extend_membership: formData.value.extend_membership,
      deactivate_account: formData.value.deactivate_account,
    }

    await api.post('/payments', payload)

    alert('Pago registrado exitosamente')

    // Reset form
    formData.value = {
      amount: 0,
      method: '',
      frequency: '',
      payment_date: new Date().toISOString().split('T')[0],
      note: '',
      extend_membership: false,
      deactivate_account: false,
    }

    // Refresh payments
    await fetchPayments()

    // Si extendió membresía o desactivó, actualizar cliente
    if (payload.extend_membership || payload.deactivate_account) {
      const clientResponse = await api.get(`/clients/${selectedClient.value.id}`)
      selectedClient.value = clientResponse.data
    }
  } catch (error: any) {
    console.error('Error al registrar pago:', error)
    alert('Error al registrar el pago. Por favor intenta de nuevo.')
  } finally {
    loading.value = false
  }
}

const fetchPayments = async () => {
  try {
    paymentsLoading.value = true

    const user = authStore.user
    const gymOwnerId = user.role === 'gym_owner' ? user.id : user.gym_owner_id

    const response = await api.get(`/payments/gym-owner/${gymOwnerId}`)
    payments.value = response.data
  } catch (error) {
    console.error('Error al cargar pagos:', error)
  } finally {
    paymentsLoading.value = false
  }
}

const filterBySelectedClient = () => {
  filteredByClient.value = true
}

const clearFilter = () => {
  filteredByClient.value = false
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('es-MX', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const getMethodLabel = (method: string) => {
  const labels: Record<string, string> = {
    cash: 'Efectivo',
    card: 'Tarjeta',
    transfer: 'Transferencia',
    other: 'Otro',
  }
  return labels[method] || method
}

const getMethodClass = (method: string) => {
  const classes: Record<string, string> = {
    cash: 'bg-green-500/20 text-green-400',
    card: 'bg-blue-500/20 text-blue-400',
    transfer: 'bg-purple-500/20 text-purple-400',
    other: 'bg-zinc-700 text-zinc-400',
  }
  return classes[method] || 'bg-zinc-700 text-zinc-400'
}

const getFrequencyLabel = (frequency: string) => {
  const labels: Record<string, string> = {
    daily: 'Diario',
    monthly: 'Mensual',
    visit: 'Por Visita',
  }
  return labels[frequency] || frequency
}

// Lifecycle
onMounted(() => {
  fetchPayments()
})
</script>
