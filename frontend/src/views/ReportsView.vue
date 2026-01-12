<template>
  <div class="min-h-screen bg-black text-white p-8">
    <!-- Header con estilo editorial -->
    <div class="mb-12">
      <div class="flex items-center gap-4 mb-4">
        <div class="w-1 h-12 bg-gradient-to-b from-yellow-500 to-yellow-700"></div>
        <div>
          <h1
            class="text-5xl font-bold tracking-tight bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 bg-clip-text text-transparent"
          >
            Reportes e Informes
          </h1>
          <p class="text-zinc-400 text-lg mt-1">Análisis detallado de tu gimnasio</p>
        </div>
      </div>

      <!-- Fecha y exportación -->
      <div class="flex items-center justify-between mt-6">
        <div class="flex items-center gap-4">
          <Calendar class="w-5 h-5 text-yellow-500" />
          <span class="text-zinc-300">{{ currentDate }}</span>
        </div>
        <div class="flex gap-3">
          <button
            @click="exportToPDF"
            :disabled="loading"
            class="px-5 py-2.5 bg-red-600/20 hover:bg-red-600/30 border border-red-600/30 rounded-lg transition-all duration-300 flex items-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <FileText class="w-4 h-4 text-red-400 group-hover:scale-110 transition-transform" />
            <span class="text-red-400">Exportar PDF</span>
          </button>
          <button
            @click="exportToExcel"
            :disabled="loading"
            class="px-5 py-2.5 bg-green-600/20 hover:bg-green-600/30 border border-green-600/30 rounded-lg transition-all duration-300 flex items-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Download class="w-4 h-4 text-green-400 group-hover:scale-110 transition-transform" />
            <span class="text-green-400">Exportar Excel</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Filtros de período -->
    <div class="mb-8 bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
      <h3
        class="text-sm font-semibold text-zinc-400 uppercase tracking-wider mb-4 flex items-center gap-2"
      >
        <Filter class="w-4 h-4" />
        Período de análisis
      </h3>
      <div class="flex flex-wrap gap-3">
        <button
          v-for="period in periods"
          :key="period.value"
          @click="handlePeriodChange(period.value)"
          :class="[
            'px-6 py-3 rounded-lg font-medium transition-all duration-300',
            selectedPeriod === period.value
              ? 'bg-yellow-500 text-black shadow-lg shadow-yellow-500/50'
              : 'bg-zinc-800/50 text-zinc-300 hover:bg-zinc-700/50 border border-zinc-700',
          ]"
        >
          {{ period.label }}
        </button>
      </div>

      <!-- Rango personalizado -->
      <div v-if="selectedPeriod === 'custom'" class="mt-4 flex gap-4">
        <div class="flex-1">
          <label class="text-sm text-zinc-400 mb-2 block">Desde</label>
          <input
            type="date"
            v-model="customRange.from"
            class="w-full px-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 outline-none transition-all"
          />
        </div>
        <div class="flex-1">
          <label class="text-sm text-zinc-400 mb-2 block">Hasta</label>
          <input
            type="date"
            v-model="customRange.to"
            class="w-full px-4 py-2.5 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 outline-none transition-all"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="loadReports"
            class="px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-black font-medium rounded-lg transition-all duration-300"
          >
            Aplicar
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="flex flex-col items-center gap-4">
        <div
          class="w-12 h-12 border-4 border-yellow-500/30 border-t-yellow-500 rounded-full animate-spin"
        ></div>
        <p class="text-zinc-400">Cargando reportes...</p>
      </div>
    </div>

    <!-- Contenido de reportes -->
    <div v-else>
      <!-- KPIs principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
          v-for="(kpi, index) in kpis"
          :key="index"
          class="bg-gradient-to-br from-zinc-900 to-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6 hover:border-yellow-500/30 transition-all duration-300 group"
        >
          <div class="flex items-start justify-between mb-4">
            <div :class="['w-12 h-12 rounded-lg flex items-center justify-center', kpi.bgColor]">
              <component :is="kpi.icon" :class="['w-6 h-6', kpi.iconColor]" />
            </div>
            <div
              :class="[
                'px-3 py-1 rounded-full text-xs font-semibold',
                kpi.trend === 'up'
                  ? 'bg-green-500/20 text-green-400'
                  : 'bg-red-500/20 text-red-400',
              ]"
            >
              <span>{{ kpi.trend === 'up' ? '↑' : '↓' }} {{ kpi.change }}</span>
            </div>
          </div>
          <h3 class="text-zinc-400 text-sm mb-2">{{ kpi.label }}</h3>
          <p class="text-3xl font-bold text-white">{{ kpi.value }}</p>
        </div>
      </div>

      <!-- Gráficas principales -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Ingresos por mes -->
        <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Ingresos Mensuales</h3>
            <DollarSign class="w-5 h-5 text-green-400" />
          </div>
          <div class="h-80">
            <canvas ref="incomeChart"></canvas>
          </div>
        </div>

        <!-- Asistencias por día -->
        <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Asistencias Diarias</h3>
            <TrendingUp class="w-5 h-5 text-blue-400" />
          </div>
          <div class="h-80">
            <canvas ref="attendanceChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Análisis detallado -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Top 5 clientes más activos -->
        <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
          <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <Award class="w-5 h-5 text-yellow-500" />
            Top 5 Clientes Más Activos
          </h3>

          <div v-if="topClients.length > 0" class="space-y-4">
            <div
              v-for="(client, index) in topClients"
              :key="index"
              class="flex items-center gap-4 p-4 bg-zinc-800/50 rounded-lg hover:bg-zinc-800 transition-all"
            >
              <div
                class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-black font-bold text-lg"
              >
                {{ index + 1 }}
              </div>
              <div class="flex-1">
                <p class="font-medium text-white">{{ client.name }}</p>
                <p class="text-sm text-zinc-400">{{ client.visits }} visitas</p>
              </div>
              <div class="w-16 h-2 bg-zinc-700 rounded-full overflow-hidden">
                <div
                  class="h-full bg-gradient-to-r from-yellow-400 to-yellow-600"
                  :style="{ width: `${(client.visits / topClients[0].visits) * 100}%` }"
                ></div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-zinc-500">
            <Award class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p>No hay datos de clientes activos</p>
          </div>
        </div>

        <!-- Distribución por método de pago -->
        <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
          <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <CreditCard class="w-5 h-5 text-blue-400" />
            Métodos de Pago
          </h3>

          <div v-if="paymentMethods.length > 0" class="space-y-4">
            <div
              v-for="method in paymentMethods"
              :key="method.type"
              class="p-4 bg-zinc-800/50 rounded-lg"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="text-white font-medium">{{ method.label }}</span>
                <span class="text-yellow-400 font-bold">${{ method.amount.toLocaleString() }}</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="flex-1 h-2 bg-zinc-700 rounded-full overflow-hidden">
                  <div
                    :class="['h-full', method.color]"
                    :style="{ width: `${method.percentage}%` }"
                  ></div>
                </div>
                <span class="text-sm text-zinc-400 w-12 text-right">{{ method.percentage }}%</span>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-zinc-500">
            <CreditCard class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p>No hay pagos en este período</p>
          </div>
        </div>

        <!-- Horas pico detalladas -->
        <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
          <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <Clock class="w-5 h-5 text-orange-400" />
            Horas Pico
          </h3>

          <div v-if="peakHours.length > 0" class="space-y-3">
            <div v-for="hour in peakHours" :key="hour.time" class="flex items-center gap-3">
              <span class="text-zinc-400 text-sm w-20">{{ hour.time }}</span>
              <div class="flex-1 h-8 bg-zinc-800/50 rounded-lg overflow-hidden relative">
                <div
                  class="h-full bg-gradient-to-r from-orange-500 to-red-500 transition-all duration-1000 ease-out"
                  :style="{ width: `${hour.percentage}%` }"
                ></div>
                <span
                  class="absolute inset-0 flex items-center justify-center text-white text-sm font-medium"
                >
                  {{ hour.visits }} visitas
                </span>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-zinc-500">
            <Clock class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p>No hay datos de horas pico</p>
          </div>
        </div>
      </div>

      <!-- Tabla de transacciones recientes -->
      <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <Receipt class="w-5 h-5 text-green-400" />
            Últimas Transacciones
          </h3>
          <button
            v-if="recentTransactions.length > 10"
            @click="showAllTransactions = !showAllTransactions"
            class="text-yellow-500 hover:text-yellow-400 text-sm font-medium transition-colors"
          >
            {{ showAllTransactions ? 'Ver menos' : 'Ver todas' }}
          </button>
        </div>

        <div v-if="recentTransactions.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-zinc-800">
                <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Fecha</th>
                <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Cliente</th>
                <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Método</th>
                <th class="text-right py-3 px-4 text-zinc-400 font-semibold text-sm">Monto</th>
                <th class="text-left py-3 px-4 text-zinc-400 font-semibold text-sm">Tipo</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(transaction, index) in displayedTransactions"
                :key="index"
                class="border-b border-zinc-800/50 hover:bg-zinc-800/30 transition-colors"
              >
                <td class="py-4 px-4 text-zinc-300 text-sm">{{ formatDate(transaction.date) }}</td>
                <td class="py-4 px-4 text-white font-medium">{{ transaction.client }}</td>
                <td class="py-4 px-4">
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-semibold',
                      getMethodColor(transaction.method),
                    ]"
                  >
                    {{ translateMethod(transaction.method) }}
                  </span>
                </td>
                <td class="py-4 px-4 text-right text-green-400 font-bold">
                  ${{ transaction.amount.toLocaleString() }}
                </td>
                <td class="py-4 px-4 text-zinc-400 text-sm">{{ transaction.frequency }}</td>
              </tr>
            </tbody>
          </table>

          <div
            v-if="!showAllTransactions && recentTransactions.length > 10"
            class="mt-4 text-center"
          >
            <p class="text-zinc-500 text-sm">
              Mostrando 10 de {{ recentTransactions.length }} transacciones
            </p>
          </div>
        </div>

        <div v-else class="text-center py-12 text-zinc-500">
          <Receipt class="w-16 h-16 mx-auto mb-4 opacity-30" />
          <p class="text-lg">No hay transacciones en este período</p>
        </div>
      </div>

      <!-- Resumen estadístico -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="bg-gradient-to-br from-green-900/20 to-green-900/5 border border-green-800/30 rounded-xl p-6"
        >
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
              <TrendingUp class="w-5 h-5 text-green-400" />
            </div>
            <h4 class="text-lg font-semibold text-white">Tasa de Retención</h4>
          </div>
          <p class="text-4xl font-bold text-green-400 mb-2">{{ stats.retention }}%</p>
          <p class="text-sm text-zinc-400">Clientes que renovaron este mes</p>
        </div>

        <div
          class="bg-gradient-to-br from-blue-900/20 to-blue-900/5 border border-blue-800/30 rounded-xl p-6"
        >
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
              <Users class="w-5 h-5 text-blue-400" />
            </div>
            <h4 class="text-lg font-semibold text-white">Promedio Diario</h4>
          </div>
          <p class="text-4xl font-bold text-blue-400 mb-2">{{ stats.dailyAverage }}</p>
          <p class="text-sm text-zinc-400">Asistencias por día</p>
        </div>

        <div
          class="bg-gradient-to-br from-yellow-900/20 to-yellow-900/5 border border-yellow-800/30 rounded-xl p-6"
        >
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-yellow-300/20 rounded-lg flex items-center justify-center">
              <DollarSign class="w-5 h-5 text-yellow-300" />
            </div>
            <h4 class="text-lg font-semibold text-white">Ingreso Promedio</h4>
          </div>
          <p class="text-4xl font-bold text-yellow-300 mb-2">${{ stats.averageIncome }}</p>
          <p class="text-sm text-zinc-400">Por cliente activo</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  Calendar,
  FileText,
  Download,
  Filter,
  DollarSign,
  TrendingUp,
  Award,
  CreditCard,
  Clock,
  Receipt,
  Users,
  Activity,
  UserPlus,
  Dumbbell,
} from 'lucide-vue-next'
import Chart from 'chart.js/auto'

const authStore = useAuthStore()

// State
const loading = ref(false)
const selectedPeriod = ref('month')
const customRange = ref({
  from: '',
  to: '',
})
const showAllTransactions = ref(false)

// Refs para charts
const incomeChart = ref<HTMLCanvasElement | null>(null)
const attendanceChart = ref<HTMLCanvasElement | null>(null)
let incomeChartInstance: Chart | null = null
let attendanceChartInstance: Chart | null = null

// Data
const kpis = ref([
  {
    label: 'Ingresos del Período',
    value: '$0',
    change: '0%',
    trend: 'up' as 'up' | 'down',
    icon: DollarSign,
    bgColor: 'bg-green-500/20',
    iconColor: 'text-green-400',
  },
  {
    label: 'Total de Asistencias',
    value: '0',
    change: '0%',
    trend: 'up' as 'up' | 'down',
    icon: Activity,
    bgColor: 'bg-blue-500/20',
    iconColor: 'text-blue-400',
  },
  {
    label: 'Nuevos Miembros',
    value: '0',
    change: '0%',
    trend: 'up' as 'up' | 'down',
    icon: UserPlus,
    bgColor: 'bg-yellow-300/20',
    iconColor: 'text-yellow-300',
  },
  {
    label: 'Ocupación Promedio',
    value: '0%',
    change: '0%',
    trend: 'up' as 'up' | 'down',
    icon: Dumbbell,
    bgColor: 'bg-orange-500/20',
    iconColor: 'text-orange-400',
  },
])

const topClients = ref<any[]>([])
const paymentMethods = ref<any[]>([])
const peakHours = ref<any[]>([])
const recentTransactions = ref<any[]>([])
const stats = ref({
  retention: 0,
  dailyAverage: 0,
  averageIncome: 0,
})

const periods = [
  { label: 'Hoy', value: 'today' },
  { label: 'Esta Semana', value: 'week' },
  { label: 'Este Mes', value: 'month' },
  { label: 'Este Trimestre', value: 'quarter' },
  { label: 'Este Año', value: 'year' },
  { label: 'Personalizado', value: 'custom' },
]

// Computed
const currentDate = computed(() => {
  return new Date().toLocaleDateString('es-MX', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
})

const displayedTransactions = computed(() => {
  return showAllTransactions.value
    ? recentTransactions.value
    : recentTransactions.value.slice(0, 10)
})

const loadReports = async () => {
  loading.value = true
  try {
    const gymOwnerId = authStore.user?.gym_owner_id || authStore.user?.id

    console.log('📊 Cargando reportes para gym:', gymOwnerId)
    console.log('📅 Período seleccionado:', selectedPeriod.value)

    // Cargar todos los datos necesarios
    const [paymentsRes, attendancesRes, clientsRes, peakHoursRes] = await Promise.all([
      api.get(`/payments/gym-owner/${gymOwnerId}`),
      api.get('/dashboard/attendances-for-reports'),
      api.get('/clients'),
      api.get('/dashboard/peak-hours'),
    ])

    // Procesar pagos
    const payments = paymentsRes.data || []
    console.log('💰 Payments cargados:', payments.length, 'total')
    console.log('💰 Primer pago:', payments[0])
    processPayments(payments)

    // Procesar asistencias
    const attendances = attendancesRes.data || []
    console.log('🚪 Attendances cargadas:', attendances.length, 'total')
    processAttendances(attendances)

    // Procesar clientes más activos
    const clients = clientsRes.data || []
    console.log('👥 Clients cargados:', clients.length, 'total')
    processTopClients(clients, attendances)

    // ✅ Procesar nuevos miembros del período
    processNewMembers(clients)

    // ✅ Calcular ocupación promedio
    calculateOccupancy(attendances)

    // Procesar horas pico
    processPeakHours(peakHoursRes.data || {})

    // Calcular estadísticas
    calculateStats(payments, attendances, clients)
  } catch (error) {
    console.error('❌ Error cargando reportes:', error)
  } finally {
    loading.value = false

    // ✅ Esperar a que loading=false actualice el DOM
    await nextTick()

    // ✅ Esperar otro tick para asegurar que los canvas existan
    await nextTick()

    // Ahora sí generar las gráficas con los datos ya cargados
    try {
      const gymOwnerId = authStore.user?.gym_owner_id || authStore.user?.id
      const [paymentsRes, attendancesRes] = await Promise.all([
        api.get(`/payments/gym-owner/${gymOwnerId}`),
        api.get('/dashboard/attendances-for-reports'),
      ])

      generateCharts(paymentsRes.data || [], attendancesRes.data || [])
    } catch (chartError) {
      console.error('❌ Error generando gráficas:', chartError)
    }
  }
}

const processNewMembers = (clients: any[]) => {
  if (!clients || clients.length === 0) {
    kpis.value[2].value = '0'
    return
  }

  const filtered = filterByPeriod(clients, 'created_at')
  kpis.value[2].value = filtered.length.toString()

  console.log('👤 Nuevos miembros en período:', filtered.length)
}

const calculateOccupancy = (attendances: any[]) => {
  if (!attendances || attendances.length === 0) {
    kpis.value[3].value = '0%'
    return
  }

  const filtered = filterByPeriod(attendances, 'check_in')
  const days = getDaysInPeriod()
  const avgDailyAttendance = days > 0 ? filtered.length / days : 0

  // Capacidad estimada del gimnasio (ajusta según tu gimnasio)
  const gymCapacity = 50

  const occupancy = gymCapacity > 0 ? Math.round((avgDailyAttendance / gymCapacity) * 100) : 0
  kpis.value[3].value = `${Math.min(occupancy, 100)}%`

  console.log('🏋️ Ocupación calculada:', occupancy, '%')
}

const processPayments = (payments: any[]) => {
  if (!payments || payments.length === 0) {
    console.log('⚠️ No hay pagos para procesar')
    kpis.value[0].value = '$0'
    paymentMethods.value = []
    recentTransactions.value = []
    return
  }

  // Filtrar por período
  const filtered = filterByPeriod(payments, 'payment_date')

  console.log('💰 Payments después de filtro:', filtered.length, 'de', payments.length)
  console.log('💰 Período actual:', selectedPeriod.value)
  if (filtered.length > 0) {
    console.log('💰 Primer pago filtrado:', filtered[0])
  } else {
    console.log('⚠️ No hay pagos en el período seleccionado')
    console.log('💰 Fecha actual:', new Date().toISOString())
    if (payments.length > 0) {
      console.log('💰 Fecha del primer pago:', payments[0].payment_date)
    }
  }

  if (filtered.length === 0) {
    kpis.value[0].value = '$0'
    paymentMethods.value = []
    recentTransactions.value = []
    return
  }

  // Total de ingresos
  const totalIncome = filtered.reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0)
  kpis.value[0].value = `$${Math.round(totalIncome).toLocaleString()}`

  // Métodos de pago
  const methodStats: Record<string, { amount: number; count: number }> = {}
  filtered.forEach((payment) => {
    const method = payment.method || 'other'
    if (!methodStats[method]) {
      methodStats[method] = { amount: 0, count: 0 }
    }
    methodStats[method].amount += parseFloat(payment.amount) || 0
    methodStats[method].count++
  })

  const total = Object.values(methodStats).reduce((sum, m) => sum + m.amount, 0)
  paymentMethods.value = Object.entries(methodStats)
    .map(([method, data]) => ({
      type: method,
      label: translateMethod(method),
      amount: Math.round(data.amount),
      percentage: total > 0 ? Math.round((data.amount / total) * 100) : 0,
      color: getMethodGradient(method),
    }))
    .sort((a, b) => b.amount - a.amount)

  console.log('💳 Métodos de pago procesados:', paymentMethods.value)

  // Transacciones recientes
  recentTransactions.value = filtered
    .sort((a, b) => new Date(b.payment_date).getTime() - new Date(a.payment_date).getTime())
    .slice(0, 50)
    .map((p) => ({
      date: p.payment_date,
      client: p.client?.name || 'N/A',
      method: p.method,
      amount: parseFloat(p.amount) || 0,
      frequency:
        p.frequency === 'monthly' ? 'Mensual' : p.frequency === 'daily' ? 'Diario' : 'Visita',
    }))
}

const processAttendances = (attendances: any[]) => {
  if (!attendances || attendances.length === 0) {
    kpis.value[1].value = '0'
    return
  }

  const possibleDateFields = [
    'check_in',
    'checked_in',
    'checkin',
    'check_in_time',
    'created_at',
    'attendance_date',
  ]
  const dateField = possibleDateFields.find((field) => attendances[0][field] !== undefined)

  if (!dateField) {
    console.error('❌ No se encontró ningún campo de fecha válido en las asistencias')
    kpis.value[1].value = '0'
    return
  }

  const filtered = filterByPeriod(attendances, dateField)
  kpis.value[1].value = filtered.length.toString()
}

const processTopClients = (clients: any[], attendances: any[]) => {
  if (!clients || clients.length === 0 || !attendances || attendances.length === 0) {
    topClients.value = []
    return
  }

  const filtered = filterByPeriod(attendances, 'check_in')

  // Contar visitas por cliente
  const visitCount: Record<number, number> = {}
  filtered.forEach((att) => {
    const clientId = att.client_id
    if (clientId) {
      visitCount[clientId] = (visitCount[clientId] || 0) + 1
    }
  })

  // Top 5
  topClients.value = Object.entries(visitCount)
    .sort((a: any, b: any) => b[1] - a[1])
    .slice(0, 5)
    .map(([clientId, visits]) => {
      const client = clients.find((c) => c.id === parseInt(clientId))
      return {
        name: client?.name || 'N/A',
        visits: visits,
      }
    })
}

const processPeakHours = (data: any) => {
  if (!data) {
    peakHours.value = []
    return
  }

  let hoursArray: any[] = []

  if (Array.isArray(data)) {
    hoursArray = data
  } else if (typeof data === 'object') {
    hoursArray = Object.entries(data).map(([hour, count]) => ({
      hour: parseInt(hour),
      count: typeof count === 'number' ? count : 0,
    }))
  }

  if (hoursArray.length === 0) {
    peakHours.value = []
    return
  }

  const maxVisits = Math.max(...hoursArray.map((h) => h.count || 0), 1)

  peakHours.value = hoursArray
    .filter((h) => h.count > 0)
    .map((hour) => ({
      time: `${hour.hour}:00`,
      visits: hour.count,
      percentage: Math.round((hour.count / maxVisits) * 100),
    }))
    .sort((a, b) => b.visits - a.visits)
    .slice(0, 10)
}

const calculateStats = (payments: any[], attendances: any[], clients: any[]) => {
  if (!clients || clients.length === 0) {
    stats.value = { retention: 0, dailyAverage: 0, averageIncome: 0 }
    return
  }

  // ✅ OPCIÓN 2: Retención = (Clientes que pagaron en el período / Clientes activos) × 100
  const activeClients = clients.filter((c) => c.active).length

  // Obtener IDs únicos de clientes que pagaron en el período
  const filteredPayments = filterByPeriod(payments, 'payment_date')
  const clientsWhoPaid = new Set(filteredPayments.map((p) => p.client_id).filter((id) => id))
  const renewedClients = clientsWhoPaid.size

  stats.value.retention = activeClients > 0 ? Math.round((renewedClients / activeClients) * 100) : 0

  console.log('📊 Retención calculada:', {
    activeClients,
    renewedClients,
    retention: stats.value.retention + '%',
  })

  // ✅ Detectar campo de fecha correcto para asistencias
  let filteredAttendances: any[] = []
  if (attendances && attendances.length > 0) {
    const possibleDateFields = [
      'check_in',
      'checked_in',
      'checkin',
      'check_in_time',
      'created_at',
      'attendance_date',
    ]
    const dateField = possibleDateFields.find((field) => attendances[0][field] !== undefined)

    if (dateField) {
      filteredAttendances = filterByPeriod(attendances, dateField)
    }
  }

  // Promedio diario de asistencias
  const days = getDaysInPeriod()
  stats.value.dailyAverage = days > 0 ? Math.round(filteredAttendances.length / days) : 0

  // Ingreso promedio por cliente
  const totalIncome = filteredPayments.reduce((sum, p) => sum + (parseFloat(p.amount) || 0), 0)
  stats.value.averageIncome = activeClients > 0 ? Math.round(totalIncome / activeClients) : 0
}

const generateCharts = (payments: any[], attendances: any[]) => {
  // Destruir charts anteriores
  if (incomeChartInstance) {
    incomeChartInstance.destroy()
    incomeChartInstance = null
  }
  if (attendanceChartInstance) {
    attendanceChartInstance.destroy()
    attendanceChartInstance = null
  }

  // Chart de ingresos
  if (incomeChart.value) {
    const ctx = incomeChart.value.getContext('2d')
    if (ctx) {
      const filteredPayments = filterByPeriod(payments, 'payment_date')
      const incomeData = aggregateByMonth(filteredPayments, 'payment_date', 'amount')

      incomeChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: incomeData.labels,
          datasets: [
            {
              label: 'Ingresos',
              data: incomeData.values,
              backgroundColor: 'rgba(34, 197, 94, 0.5)',
              borderColor: 'rgb(34, 197, 94)',
              borderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: 'rgba(255, 255, 255, 0.1)' },
              ticks: { color: '#a1a1aa' },
            },
            x: {
              grid: { display: false },
              ticks: { color: '#a1a1aa' },
            },
          },
        },
      })
    }
  }

  // Chart de asistencias
  if (attendanceChart.value) {
    const ctx = attendanceChart.value.getContext('2d')
    if (ctx) {
      const filteredAttendances = filterByPeriod(attendances, 'check_in')
      const attendanceData = aggregateByDay(filteredAttendances, 'check_in')

      attendanceChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: attendanceData.labels,
          datasets: [
            {
              label: 'Asistencias',
              data: attendanceData.values,
              backgroundColor: 'rgba(59, 130, 246, 0.2)',
              borderColor: 'rgb(59, 130, 246)',
              borderWidth: 3,
              fill: true,
              tension: 0.4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: 'rgba(255, 255, 255, 0.1)' },
              ticks: { color: '#a1a1aa' },
            },
            x: {
              grid: { display: false },
              ticks: { color: '#a1a1aa' },
            },
          },
        },
      })
    }
  }
}

const filterByPeriod = (data: any[], dateField: string) => {
  if (!data || data.length === 0) return []

  const now = new Date()

  const filtered = data.filter((item) => {
    if (!item || !item[dateField]) return false

    const itemDate = new Date(item[dateField])
    if (isNaN(itemDate.getTime())) return false

    switch (selectedPeriod.value) {
      case 'today': {
        const todayStartUTC = new Date(
          Date.UTC(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0),
        )
        const todayEndUTC = new Date(
          Date.UTC(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999),
        )
        return itemDate >= todayStartUTC && itemDate <= todayEndUTC
      }

      case 'week': {
        const weekAgoUTC = new Date(
          Date.UTC(now.getFullYear(), now.getMonth(), now.getDate() - 7, 0, 0, 0, 0),
        )
        const todayEndUTC = new Date(
          Date.UTC(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999),
        )
        return itemDate >= weekAgoUTC && itemDate <= todayEndUTC
      }

      case 'month': {
        const itemYear = itemDate.getUTCFullYear()
        const itemMonth = itemDate.getUTCMonth()
        const nowYear = now.getUTCFullYear()
        const nowMonth = now.getUTCMonth()
        return itemYear === nowYear && itemMonth === nowMonth
      }

      case 'quarter': {
        const itemYear = itemDate.getUTCFullYear()
        const itemMonth = itemDate.getUTCMonth()
        const nowYear = now.getUTCFullYear()
        const nowMonth = now.getUTCMonth()
        const itemQuarter = Math.floor(itemMonth / 3)
        const nowQuarter = Math.floor(nowMonth / 3)
        return itemYear === nowYear && itemQuarter === nowQuarter
      }

      case 'year': {
        const itemYear = itemDate.getUTCFullYear()
        const nowYear = now.getUTCFullYear()
        return itemYear === nowYear
      }

      case 'custom': {
        if (customRange.value.from && customRange.value.to) {
          const fromParts = customRange.value.from.split('-')
          const toParts = customRange.value.to.split('-')
          const fromUTC = new Date(
            Date.UTC(
              parseInt(fromParts[0]),
              parseInt(fromParts[1]) - 1,
              parseInt(fromParts[2]),
              0,
              0,
              0,
              0,
            ),
          )
          const toUTC = new Date(
            Date.UTC(
              parseInt(toParts[0]),
              parseInt(toParts[1]) - 1,
              parseInt(toParts[2]),
              23,
              59,
              59,
              999,
            ),
          )
          return itemDate >= fromUTC && itemDate <= toUTC
        }
        return true
      }

      default:
        return true
    }
  })

  return filtered
}

const getDaysInPeriod = () => {
  const now = new Date()

  switch (selectedPeriod.value) {
    case 'today':
      return 1
    case 'week':
      return 7
    case 'month':
      return now.getDate()
    case 'quarter': {
      const quarterStart = new Date(now.getFullYear(), Math.floor(now.getMonth() / 3) * 3, 1)
      const diff = now.getTime() - quarterStart.getTime()
      return Math.ceil(diff / (1000 * 60 * 60 * 24)) || 1
    }
    case 'year': {
      const yearStart = new Date(now.getFullYear(), 0, 1)
      const diff = now.getTime() - yearStart.getTime()
      return Math.ceil(diff / (1000 * 60 * 60 * 24)) || 1
    }
    case 'custom':
      if (customRange.value.from && customRange.value.to) {
        const from = new Date(customRange.value.from)
        const to = new Date(customRange.value.to)
        const diff = to.getTime() - from.getTime()
        return Math.ceil(diff / (1000 * 60 * 60 * 24)) || 1
      }
      return 30
    default:
      return 30
  }
}

const aggregateByMonth = (data: any[], dateField: string, valueField: string) => {
  if (!data || data.length === 0) {
    return { labels: [], values: [] }
  }

  const months: Record<string, number> = {}
  data.forEach((item) => {
    if (!item || !item[dateField]) return
    const date = new Date(item[dateField])
    if (isNaN(date.getTime())) return

    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
    months[key] = (months[key] || 0) + (parseFloat(item[valueField]) || 0)
  })

  return {
    labels: Object.keys(months).map((k) => {
      const [year, month] = k.split('-')
      return new Date(parseInt(year), parseInt(month) - 1).toLocaleDateString('es-MX', {
        month: 'short',
        year: 'numeric',
      })
    }),
    values: Object.values(months),
  }
}

const aggregateByDay = (data: any[], dateField: string) => {
  if (!data || data.length === 0) {
    return { labels: [], values: [] }
  }

  const days: Record<string, number> = {}
  data.forEach((item) => {
    if (!item || !item[dateField]) return
    const date = new Date(item[dateField])
    if (isNaN(date.getTime())) return

    const key = date.toISOString().split('T')[0]
    days[key] = (days[key] || 0) + 1
  })

  return {
    labels: Object.keys(days).map((k) => {
      const date = new Date(k)
      return date.toLocaleDateString('es-MX', { day: 'numeric', month: 'short' })
    }),
    values: Object.values(days),
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'N/A'
  return date.toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
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

const getMethodColor = (method: string) => {
  const colors: Record<string, string> = {
    cash: 'bg-green-500/20 text-green-400',
    card: 'bg-blue-500/20 text-blue-400',
    transfer: 'bg-purple-500/20 text-purple-400',
    other: 'bg-zinc-500/20 text-zinc-400',
  }
  return colors[method] || 'bg-zinc-500/20 text-zinc-400'
}

const getMethodGradient = (method: string) => {
  const gradients: Record<string, string> = {
    cash: 'bg-gradient-to-r from-green-500 to-emerald-500',
    card: 'bg-gradient-to-r from-blue-500 to-cyan-500',
    transfer: 'bg-gradient-to-r from-yellow-300 to-yellow-400',
    other: 'bg-gradient-to-r from-zinc-500 to-gray-500',
  }
  return gradients[method] || 'bg-gradient-to-r from-zinc-500 to-gray-500'
}

const handlePeriodChange = (value: string) => {
  selectedPeriod.value = value
  if (value !== 'custom') {
    loadReports()
  }
}

const exportToPDF = () => {
  alert('Exportar a PDF - Funcionalidad en desarrollo')
}

const exportToExcel = () => {
  alert('Exportar a Excel - Funcionalidad en desarrollo')
}

// Lifecycle
onMounted(() => {
  loadReports()
})
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}
</style>
