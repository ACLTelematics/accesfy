<template>
  <div class="p-6 space-y-6">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="text-center">
        <div
          class="w-16 h-16 border-4 border-yellow-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"
        ></div>
        <p class="text-gray-400">Cargando información del cliente...</p>
      </div>
    </div>

    <!-- Content -->
    <div v-else-if="client">
      <!-- Header Section -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-start justify-between">
          <!-- Client Info -->
          <div class="flex items-start gap-6">
            <!-- Avatar -->
            <div
              class="w-24 h-24 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center text-black font-bold text-3xl"
            >
              {{ getInitials(client.name) }}
            </div>

            <!-- Basic Info -->
            <div>
              <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-white">{{ client.name }}</h1>
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-sm font-medium',
                    getStatusBadge(client).class,
                  ]"
                >
                  {{ getStatusBadge(client).label }}
                </span>
              </div>

              <div class="space-y-1 text-gray-400">
                <div v-if="client.email" class="flex items-center gap-2">
                  <Mail class="w-4 h-4" />
                  <span>{{ client.email }}</span>
                </div>
                <div v-if="client.phone" class="flex items-center gap-2">
                  <Phone class="w-4 h-4" />
                  <span>{{ client.phone }}</span>
                </div>
                <div v-if="client.gender" class="flex items-center gap-2">
                  <User class="w-4 h-4" />
                  <span>{{ translateGender(client.gender) }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <Calendar class="w-4 h-4" />
                  <span>Registrado: {{ formatDate(client.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <button
              v-if="canEdit"
              @click="goToEdit"
              class="p-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg transition-all"
              title="Editar Cliente"
            >
              <Edit2 class="w-5 h-5 text-gray-400" />
            </button>
            <button
              @click="showResetPinModal = true"
              class="p-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg transition-all"
              title="Resetear PIN"
            >
              <Key class="w-5 h-5 text-gray-400" />
            </button>
            <button
              v-if="canDelete"
              @click="confirmDelete"
              class="p-2 bg-red-900/20 hover:bg-red-900/40 border border-red-500/30 rounded-lg transition-all"
              title="Eliminar Cliente"
            >
              <Trash2 class="w-5 h-5 text-red-400" />
            </button>
            <button
              @click="$router.push('/dashboard/clients')"
              class="p-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg transition-all"
              title="Volver"
            >
              <ArrowLeft class="w-5 h-5 text-gray-400" />
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-4 gap-4">
        <!-- Días Restantes -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
              <Calendar class="w-5 h-5 text-blue-400" />
            </div>
            <span class="text-xs text-gray-500">Membresía</span>
          </div>
          <div class="text-2xl font-bold text-white mb-1">
            {{ getDaysRemaining() }}
          </div>
          <div class="text-sm text-gray-400">días restantes</div>
        </div>

        <!-- Total Pagado -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
              <DollarSign class="w-5 h-5 text-green-400" />
            </div>
            <span class="text-xs text-gray-500">Total Pagado</span>
          </div>
          <div class="text-2xl font-bold text-white mb-1">${{ getTotalPaid() }}</div>
          <div class="text-sm text-gray-400">{{ client.payments?.length || 0 }} pagos</div>
        </div>

        <!-- Asistencias -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center">
              <Activity class="w-5 h-5 text-purple-400" />
            </div>
            <span class="text-xs text-gray-500">Asistencias</span>
          </div>
          <div class="text-2xl font-bold text-white mb-1">
            {{ client.attendances?.length || 0 }}
          </div>
          <div class="text-sm text-gray-400">check-ins totales</div>
        </div>

        <!-- Último Acceso -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center">
              <Clock class="w-5 h-5 text-yellow-400" />
            </div>
            <span class="text-xs text-gray-500">Último Acceso</span>
          </div>
          <div class="text-lg font-bold text-white mb-1">
            {{ getLastAttendance() }}
          </div>
          <div class="text-sm text-gray-400">{{ getLastAttendanceDate() }}</div>
        </div>
      </div>

      <!-- Membership Card -->
      <div
        class="bg-gradient-to-br from-yellow-500/10 via-orange-500/10 to-red-500/10 border border-yellow-500/20 rounded-xl p-6"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-white mb-2">Membresía Actual</h3>
            <div class="flex items-center gap-6">
              <div>
                <p class="text-sm text-gray-400 mb-1">Tipo</p>
                <p class="text-lg font-semibold text-yellow-500">
                  {{ translateMembershipType(client.membership?.type) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-400 mb-1">Precio</p>
                <p class="text-lg font-semibold text-white">${{ client.membership?.price || 0 }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-400 mb-1">Duración</p>
                <p class="text-lg font-semibold text-white">
                  {{ client.membership?.duration_days || 0 }} días
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-400 mb-1">Vence</p>
                <p
                  :class="[
                    'text-lg font-semibold',
                    isExpired(client.membership_expires) ? 'text-red-400' : 'text-green-400',
                  ]"
                >
                  {{ formatDate(client.membership_expires) }}
                </p>
              </div>
            </div>
          </div>

          <button
            @click="$router.push(`/dashboard/payments-memberships?client=${client.id}`)"
            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all flex items-center gap-2"
          >
            <CreditCard class="w-5 h-5" />
            Registrar Pago
          </button>
        </div>
      </div>

      <!-- Tabs Section -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <!-- Tab Headers -->
        <div class="flex border-b border-zinc-800">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'flex-1 px-6 py-4 font-medium text-sm transition-all relative',
              activeTab === tab.id
                ? 'bg-zinc-800 text-yellow-500 border-b-2 border-yellow-500'
                : 'text-gray-400 hover:text-white hover:bg-zinc-800/50',
            ]"
          >
            <component :is="tab.icon" class="w-4 h-4 inline mr-2" />
            {{ tab.label }}
            <span
              v-if="tab.count"
              class="ml-2 px-2 py-0.5 bg-yellow-500/20 text-yellow-500 rounded-full text-xs"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <!-- TAB 1: Historial de Asistencias -->
          <div v-if="activeTab === 'attendances'">
            <div v-if="client.attendances && client.attendances.length > 0" class="space-y-3">
              <div
                v-for="attendance in client.attendances.slice(0, 20)"
                :key="attendance.id"
                class="flex items-center justify-between p-4 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-all"
              >
                <div class="flex items-center gap-4">
                  <div
                    class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center"
                  >
                    <CheckCircle class="w-5 h-5 text-green-400" />
                  </div>
                  <div>
                    <p class="font-medium text-white">Check-in exitoso</p>
                    <p class="text-sm text-gray-400">{{ formatDateTime(attendance.check_in) }}</p>
                  </div>
                </div>
                <div>
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium',
                      attendance.status === 'active'
                        ? 'bg-green-500/20 text-green-400'
                        : 'bg-gray-500/20 text-gray-400',
                    ]"
                  >
                    {{ attendance.status === 'active' ? 'Activo' : 'Completado' }}
                  </span>
                </div>
              </div>

              <div v-if="client.attendances.length > 20" class="text-center py-4">
                <p class="text-sm text-gray-500">
                  Mostrando 20 de {{ client.attendances.length }} asistencias
                </p>
              </div>
            </div>

            <div v-else class="text-center py-12">
              <div
                class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4"
              >
                <Activity class="w-8 h-8 text-gray-600" />
              </div>
              <p class="text-gray-400 mb-2">Sin asistencias registradas</p>
              <p class="text-sm text-gray-500">Este cliente aún no ha realizado ningún check-in</p>
            </div>
          </div>

          <!-- TAB 2: Historial de Pagos -->
          <div v-if="activeTab === 'payments'">
            <div v-if="client.payments && client.payments.length > 0" class="space-y-3">
              <div
                v-for="payment in client.payments"
                :key="payment.id"
                class="flex items-center justify-between p-4 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-all"
              >
                <div class="flex items-center gap-4">
                  <div
                    class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center"
                  >
                    <DollarSign class="w-5 h-5 text-green-400" />
                  </div>
                  <div>
                    <p class="font-medium text-white text-lg">${{ payment.amount }}</p>
                    <p class="text-sm text-gray-400">
                      {{ translateMethod(payment.method) }} • {{ formatDate(payment.payment_date) }}
                    </p>
                    <p v-if="payment.note" class="text-xs text-gray-500 mt-1">{{ payment.note }}</p>
                  </div>
                </div>
                <div>
                  <span
                    class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-medium"
                  >
                    Pagado
                  </span>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-12">
              <div
                class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4"
              >
                <DollarSign class="w-8 h-8 text-gray-600" />
              </div>
              <p class="text-gray-400 mb-2">Sin pagos registrados</p>
              <p class="text-sm text-gray-500">Este cliente aún no ha realizado ningún pago</p>
              <button
                @click="$router.push(`/dashboard/payments-memberships?client=${client.id}`)"
                class="mt-4 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-medium transition-all"
              >
                Registrar Primer Pago
              </button>
            </div>
          </div>

          <!-- TAB 3: Gráfica de Uso -->
          <div v-if="activeTab === 'usage'">
            <div class="space-y-6">
              <div>
                <h4 class="text-lg font-semibold text-white mb-4">Asistencias por Mes</h4>
                <div class="space-y-3">
                  <div v-for="month in getMonthlyStats()" :key="month.label" class="space-y-1">
                    <div class="flex items-center justify-between text-sm">
                      <span class="text-gray-400">{{ month.label }}</span>
                      <span class="text-white font-medium">{{ month.count }} visitas</span>
                    </div>
                    <div class="w-full bg-zinc-800 rounded-full h-2 overflow-hidden">
                      <div
                        class="bg-gradient-to-r from-yellow-500 to-orange-500 h-full rounded-full transition-all"
                        :style="{ width: `${month.percentage}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="client.attendances && client.attendances.length > 0">
                <h4 class="text-lg font-semibold text-white mb-4">Estadísticas Generales</h4>
                <div class="grid grid-cols-3 gap-4">
                  <div class="p-4 bg-zinc-800 rounded-lg">
                    <p class="text-sm text-gray-400 mb-1">Promedio Mensual</p>
                    <p class="text-2xl font-bold text-white">{{ getMonthlyAverage() }}</p>
                    <p class="text-xs text-gray-500">visitas/mes</p>
                  </div>
                  <div class="p-4 bg-zinc-800 rounded-lg">
                    <p class="text-sm text-gray-400 mb-1">Racha Actual</p>
                    <p class="text-2xl font-bold text-yellow-500">{{ getCurrentStreak() }}</p>
                    <p class="text-xs text-gray-500">días consecutivos</p>
                  </div>
                  <div class="p-4 bg-zinc-800 rounded-lg">
                    <p class="text-sm text-gray-400 mb-1">Mejor Mes</p>
                    <p class="text-2xl font-bold text-green-400">{{ getBestMonth() }}</p>
                    <p class="text-xs text-gray-500">visitas</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-20">
      <div
        class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4"
      >
        <AlertCircle class="w-8 h-8 text-red-400" />
      </div>
      <h3 class="text-xl font-semibold text-white mb-2">Cliente no encontrado</h3>
      <p class="text-gray-400 mb-6">No se pudo cargar la información del cliente</p>
      <button
        @click="$router.push('/dashboard/clients')"
        class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all"
      >
        Volver a la lista
      </button>
    </div>

    <!-- Modal: Reset PIN -->
    <Teleport to="body">
      <div
        v-if="showResetPinModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @click.self="showResetPinModal = false"
      >
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 max-w-md w-full">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Resetear PIN de Acceso</h3>
            <button @click="showResetPinModal = false" class="text-gray-400 hover:text-white">
              <X class="w-5 h-5" />
            </button>
          </div>

          <div v-if="!newPin" class="space-y-4">
            <p class="text-gray-400">
              Se generará un nuevo PIN de 4 dígitos para este cliente. El PIN actual quedará
              inválido.
            </p>

            <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
              <div class="flex gap-3">
                <AlertCircle class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-yellow-200">
                  <p class="font-medium mb-1">Importante:</p>
                  <p>Asegúrate de copiar e imprimir el nuevo PIN para entregarlo al cliente.</p>
                </div>
              </div>
            </div>

            <div class="flex gap-3">
              <button
                @click="showResetPinModal = false"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all"
              >
                Cancelar
              </button>
              <button
                @click="generateNewPin"
                :disabled="generatingPin"
                class="flex-1 px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-semibold transition-all disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <Loader v-if="generatingPin" class="w-5 h-5 animate-spin" />
                <Key v-else class="w-5 h-5" />
                {{ generatingPin ? 'Generando...' : 'Generar Nuevo PIN' }}
              </button>
            </div>
          </div>

          <div v-else class="space-y-6">
            <div class="text-center">
              <div
                class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4"
              >
                <CheckCircle class="w-8 h-8 text-green-400" />
              </div>
              <p class="text-green-400 font-medium mb-6">¡PIN Actualizado Exitosamente!</p>

              <div class="p-6 bg-zinc-800 border border-zinc-700 rounded-lg mb-6">
                <p class="text-sm text-gray-400 mb-2">Nuevo PIN de Acceso</p>
                <p class="text-4xl font-bold text-yellow-500 tracking-widest mb-4">{{ newPin }}</p>
                <p class="text-xs text-gray-500">Entregar al cliente para realizar check-in</p>
              </div>
            </div>

            <div class="flex gap-3">
              <button
                @click="copyNewPin"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all flex items-center justify-center gap-2"
              >
                <Copy class="w-4 h-4" />
                {{ copiedNewPin ? '¡Copiado!' : 'Copiar' }}
              </button>
              <button
                @click="printNewPin"
                class="flex-1 px-4 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all flex items-center justify-center gap-2"
              >
                <Printer class="w-4 h-4" />
                Imprimir
              </button>
            </div>

            <button
              @click="closeResetPinModal"
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
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { usePermissions } from '@/composables/usePermissions'
import api from '@/services/api'
import {
  User,
  Mail,
  Phone,
  Calendar,
  Edit2,
  Trash2,
  ArrowLeft,
  Key,
  DollarSign,
  Activity,
  Clock,
  CreditCard,
  CheckCircle,
  AlertCircle,
  X,
  Copy,
  Printer,
  Loader,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const permissions = usePermissions()

const loading = ref(true)
const client = ref<any>(null)
const activeTab = ref('attendances')
const showResetPinModal = ref(false)
const generatingPin = ref(false)
const newPin = ref('')
const copiedNewPin = ref(false)

const tabs = computed(() => [
  {
    id: 'attendances',
    label: 'Asistencias',
    icon: Activity,
    count: client.value?.attendances?.length || 0,
  },
  {
    id: 'payments',
    label: 'Pagos',
    icon: DollarSign,
    count: client.value?.payments?.length || 0,
  },
  {
    id: 'usage',
    label: 'Estadísticas',
    icon: Clock,
    count: null,
  },
])

const canEdit = computed(() => permissions.canEditClient.value)
const canDelete = computed(() => permissions.canDeleteClient.value)

const fetchClient = async () => {
  try {
    loading.value = true
    const clientId = route.params.id
    const { data } = await api.get(`/clients/${clientId}`)
    client.value = data
  } catch (error: any) {
    console.error('Error cargando cliente:', error)
    client.value = null
  } finally {
    loading.value = false
  }
}

const getInitials = (name: string) => {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2 && parts[0] && parts[1]) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const getStatusBadge = (client: any) => {
  if (!client.active) {
    return {
      label: 'Inactivo',
      class: 'bg-red-500/20 text-red-400 border border-red-500/30',
    }
  }
  if (isExpired(client.membership_expires)) {
    return {
      label: 'Vencido',
      class: 'bg-orange-500/20 text-orange-400 border border-orange-500/30',
    }
  }
  return {
    label: 'Activo',
    class: 'bg-green-500/20 text-green-400 border border-green-500/30',
  }
}

const getDaysRemaining = () => {
  if (!client.value?.membership_expires) return 0
  const expiration = new Date(client.value.membership_expires)
  const today = new Date()
  const diff = expiration.getTime() - today.getTime()
  const days = Math.ceil(diff / (1000 * 60 * 60 * 24))
  return days > 0 ? days : 0
}

const getTotalPaid = () => {
  if (!client.value?.payments) return 0
  return client.value.payments
    .reduce((sum: number, payment: any) => sum + parseFloat(payment.amount), 0)
    .toFixed(2)
}

const getLastAttendance = () => {
  if (!client.value?.attendances || client.value.attendances.length === 0) {
    return 'Nunca'
  }
  const sortedAttendances = [...client.value.attendances].sort(
    (a, b) => new Date(b.check_in).getTime() - new Date(a.check_in).getTime(),
  )
  const lastDate = new Date(sortedAttendances[0].check_in)
  const today = new Date()
  const diffTime = today.getTime() - lastDate.getTime()
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Hoy'
  if (diffDays === 1) return 'Ayer'
  if (diffDays < 7) return `Hace ${diffDays} días`
  if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`
  return `Hace ${Math.floor(diffDays / 30)} meses`
}

const getLastAttendanceDate = () => {
  if (!client.value?.attendances || client.value.attendances.length === 0) {
    return '-'
  }
  const sortedAttendances = [...client.value.attendances].sort(
    (a, b) => new Date(b.check_in).getTime() - new Date(a.check_in).getTime(),
  )
  return formatDate(sortedAttendances[0].check_in)
}

const getMonthlyStats = () => {
  if (!client.value?.attendances) return []

  const monthCounts: Record<string, number> = {}
  const now = new Date()

  // Obtener últimos 6 meses
  for (let i = 5; i >= 0; i--) {
    const date = new Date(now.getFullYear(), now.getMonth() - i, 1)
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
    monthCounts[key] = 0
  }

  // Contar asistencias por mes
  client.value.attendances.forEach((attendance: any) => {
    const date = new Date(attendance.check_in)
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
    if (monthCounts.hasOwnProperty(key)) {
      monthCounts[key]++
    }
  })

  const maxCount = Math.max(...Object.values(monthCounts), 1)

  return Object.entries(monthCounts).map(([key, count]) => {
    const [year, month] = key.split('-')
    const date = new Date(parseInt(year), parseInt(month) - 1)
    return {
      label: date.toLocaleDateString('es-MX', { month: 'long', year: 'numeric' }),
      count,
      percentage: (count / maxCount) * 100,
    }
  })
}

const getMonthlyAverage = () => {
  if (!client.value?.attendances || client.value.attendances.length === 0) return 0

  const firstAttendance = new Date(
    Math.min(...client.value.attendances.map((a: any) => new Date(a.check_in).getTime())),
  )
  const today = new Date()
  const monthsDiff =
    (today.getFullYear() - firstAttendance.getFullYear()) * 12 +
    (today.getMonth() - firstAttendance.getMonth()) +
    1

  return Math.round(client.value.attendances.length / monthsDiff)
}

const getCurrentStreak = () => {
  if (!client.value?.attendances || client.value.attendances.length === 0) return 0

  const sortedAttendances = [...client.value.attendances].sort(
    (a, b) => new Date(b.check_in).getTime() - new Date(a.check_in).getTime(),
  )

  let streak = 0
  let currentDate = new Date()
  currentDate.setHours(0, 0, 0, 0)

  for (const attendance of sortedAttendances) {
    const attendanceDate = new Date(attendance.check_in)
    attendanceDate.setHours(0, 0, 0, 0)

    const diffDays = Math.floor(
      (currentDate.getTime() - attendanceDate.getTime()) / (1000 * 60 * 60 * 24),
    )

    if (diffDays === streak) {
      streak++
    } else if (diffDays === streak + 1) {
      currentDate = attendanceDate
      streak++
    } else {
      break
    }
  }

  return streak
}

const getBestMonth = () => {
  const stats = getMonthlyStats()
  if (stats.length === 0) return 0
  return Math.max(...stats.map((s) => s.count))
}

const translateMembershipType = (type: string) => {
  const translations: Record<string, string> = {
    visit: 'Visita',
    weekly: 'Semanal',
    biweekly: 'Quincenal',
    individual: 'Mensual Individual',
    couple: 'Mensual Pareja',
    student: 'Mensual Estudiante',
    quarterly: 'Trimestral',
    semiannual: 'Semestral',
    annual: 'Anual',
    custom: 'Personalizada',
  }
  return translations[type] || type
}

const translateGender = (gender: string) => {
  const translations: Record<string, string> = {
    male: 'Masculino',
    female: 'Femenino',
    other: 'Otro',
  }
  return translations[gender] || gender
}

const translateMethod = (method: string) => {
  const translations: Record<string, string> = {
    cash: 'Efectivo',
    card: 'Tarjeta',
    transfer: 'Transferencia',
    other: 'Otro',
  }
  return translations[method] || method
}

const formatDate = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}

const formatDateTime = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const isExpired = (date: string) => {
  if (!date) return true
  return new Date(date) < new Date()
}

const goToEdit = () => {
  router.push(`/dashboard/clients/${client.value.id}/edit`)
}

const confirmDelete = async () => {
  if (
    !confirm(`¿Estás seguro de eliminar a ${client.value.name}? Esta acción no se puede deshacer.`)
  ) {
    return
  }

  try {
    await api.delete(`/clients/${client.value.id}`)
    alert('Cliente eliminado exitosamente')
    router.push('/dashboard/clients')
  } catch (error: any) {
    console.error('Error eliminando cliente:', error)
    alert(error.response?.data?.message || 'Error al eliminar cliente')
  }
}

const generateNewPin = async () => {
  try {
    generatingPin.value = true

    // Generar PIN de 4 dígitos
    const pin = Math.floor(1000 + Math.random() * 9000).toString()

    // Actualizar en el backend
    await api.put(`/clients/${client.value.id}`, {
      pin: pin,
    })

    newPin.value = pin

    // Recargar cliente
    await fetchClient()
  } catch (error: any) {
    console.error('Error generando PIN:', error)
    alert(error.response?.data?.message || 'Error al generar nuevo PIN')
    showResetPinModal.value = false
  } finally {
    generatingPin.value = false
  }
}

const copyNewPin = () => {
  navigator.clipboard.writeText(newPin.value)
  copiedNewPin.value = true
  setTimeout(() => {
    copiedNewPin.value = false
  }, 2000)
}

const printNewPin = () => {
  const printWindow = window.open('', '', 'width=400,height=500')
  if (!printWindow) return

  printWindow.document.write(`
    <html>
      <head>
        <title>PIN de Acceso - ${client.value.name}</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            padding: 40px;
            text-align: center;
          }
          h1 { font-size: 24px; margin-bottom: 10px; }
          h2 { font-size: 20px; color: #666; margin-bottom: 30px; }
          .pin {
            font-size: 72px;
            font-weight: bold;
            letter-spacing: 10px;
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
        <h1>${client.value.name}</h1>
        <h2>PIN de Acceso al Gimnasio</h2>
        <div class="pin">${newPin.value}</div>
        <p>Ingresa este PIN en la pantalla de check-in</p>
        <div class="footer">
          <p>Generado: ${new Date().toLocaleString('es-MX')}</p>
          <p>Mantén este PIN seguro</p>
        </div>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}
const goToClientDetail = (id: number) => {
  router.push(`/dashboard/clients/${id}`)
}
const closeResetPinModal = () => {
  showResetPinModal.value = false
  newPin.value = ''
  copiedNewPin.value = false
}

onMounted(() => {
  fetchClient()
})
</script>
