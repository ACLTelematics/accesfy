<template>
  <div class="p-6">
    <!-- Header with time -->
    <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Bienvenido de vuelta 👋</h1>
        <p class="text-gray-400">Aquí está lo que sucede en tu gimnasio hoy</p>
      </div>
      <div class="text-right">
        <div class="text-3xl font-bold text-yellow-500">{{ currentTime }}</div>
        <div class="text-sm text-gray-500">{{ currentDate }}</div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-yellow-500"
      ></div>
    </div>

    <template v-if="!loading">
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
          v-for="(stat, idx) in stats"
          :key="idx"
          class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6 hover:border-yellow-500/50 transition-all duration-300"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="p-3 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600">
              <component :is="stat.icon" class="w-8 h-8 text-black" />
            </div>
          </div>
          <div class="text-gray-400 text-sm mb-2">{{ stat.title }}</div>
          <div class="text-5xl font-bold text-yellow-500 mb-2">{{ stat.value }}</div>
          <div class="text-gray-600 text-sm">{{ stat.subtitle }}</div>
        </div>
      </div>

      <!-- Gráficas Circulares -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Distribución de Planes -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-2">Distribución de Planes</h2>
          <p class="text-sm text-gray-500 mb-6">Membresías activas</p>
          <DonutChart v-if="membershipChartData.length > 0" :data="membershipChartData" />
          <div v-else class="text-center text-gray-500 py-8">Sin datos</div>
        </div>

        <!-- Género de Miembros -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-2">Género de Miembros</h2>
          <p class="text-sm text-gray-500 mb-6">Distribución actual</p>
          <DonutChart v-if="genderChartData.length > 0" :data="genderChartData" />
          <div v-else class="text-center text-gray-500 py-8">Sin datos</div>
        </div>

        <!-- Horas Pico -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-2">Horas Pico</h2>
          <p class="text-sm text-gray-500 mb-6">Afluencia por horario</p>
          <DonutChart v-if="peakHoursChartData.length > 0" :data="peakHoursChartData" />
          <div v-else class="text-center text-gray-500 py-8">Sin datos</div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Hourly Access Chart -->
        <div class="lg:col-span-2 bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <Clock class="w-5 h-5 text-orange-400" />
              Accesos por Hora
            </h2>
            <div class="flex items-center gap-2 text-sm text-gray-400">
              <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
              <span>Últimos 7 días</span>
            </div>
          </div>

          <div v-if="peakHours.length > 0" class="space-y-3">
            <div v-for="hour in peakHours" :key="hour.range" class="flex items-center gap-3">
              <span class="text-zinc-400 text-sm w-32">{{ formatHourRange(hour.range) }}</span>
              <div class="flex-1 h-10 bg-zinc-800/50 rounded-lg overflow-hidden relative">
                <div
                  class="h-full bg-gradient-to-r from-orange-500 to-yellow-500 transition-all duration-1000 ease-out hover:from-orange-400 hover:to-yellow-400"
                  :style="{ width: `${(hour.count / maxPeakCount) * 100}%` }"
                ></div>
                <span
                  class="absolute inset-0 flex items-center justify-center text-white text-sm font-medium"
                >
                  {{ hour.count }} {{ hour.count === 1 ? 'visita' : 'visitas' }}
                </span>
              </div>
            </div>
          </div>

          <div v-else class="h-64 flex flex-col items-center justify-center text-gray-500">
            <Clock class="w-12 h-12 mb-3 opacity-30" />
            <p>No hay datos de accesos disponibles</p>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-6">Actividad Reciente</h2>
          <div v-if="recentActivity.length > 0" class="space-y-4">
            <div
              v-for="(activity, idx) in recentActivity"
              :key="idx"
              class="flex items-center gap-3 p-3 bg-zinc-900 border border-zinc-800 rounded-lg hover:border-zinc-700 transition-colors"
            >
              <div
                class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-black font-bold"
              >
                {{ activity.client_name.charAt(0) }}
              </div>
              <div class="flex-1">
                <div class="text-white text-sm font-medium">{{ activity.client_name }}</div>
                <div class="text-gray-500 text-xs">{{ activity.check_in }}</div>
              </div>
              <div
                :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  activity.status === 'active'
                    ? 'bg-green-500/20 text-green-400 border border-green-500/30'
                    : 'bg-gray-500/20 text-gray-400 border border-gray-500/30',
                ]"
              >
                {{ activity.status === 'active' ? 'Activo' : 'Completado' }}
              </div>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">Sin actividad reciente</div>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expiring Memberships -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Membresías por Vencer</h2>
            <div
              v-if="expiringMembers.length > 0"
              class="px-3 py-1 bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded-full text-sm font-medium"
            >
              {{ expiringMembers.length }} alertas
            </div>
          </div>
          <div v-if="expiringMembers.length > 0" class="space-y-3">
            <div
              v-for="(member, idx) in expiringMembers"
              :key="idx"
              class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-lg hover:border-zinc-700 transition-colors"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold"
                >
                  {{ member.name.charAt(0) }}
                </div>
                <div>
                  <div class="text-white text-sm font-medium">{{ member.name }}</div>
                  <div class="text-gray-500 text-xs">
                    {{ formatDate(member.membership_expires) }}
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-orange-400 text-sm font-semibold">{{ member.days_left }} días</div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">No hay membresías por vencer</div>
        </div>

        <!-- Membership Distribution List -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6">
          <h2 class="text-xl font-bold text-white mb-6">Estadísticas por Tipo</h2>
          <div v-if="membershipDistribution.length > 0" class="space-y-4">
            <div
              v-for="(item, idx) in membershipDistribution"
              :key="idx"
              class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg">
                  <CreditCard class="w-5 h-5 text-black" />
                </div>
                <span class="text-gray-300 capitalize">{{ item.type }}</span>
              </div>
              <span class="text-2xl font-bold text-yellow-500">{{ item.count }}</span>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">Sin datos de membresías</div>
        </div>
      </div>
    </template>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import { Users, Activity, Dumbbell, Calendar, CreditCard, Clock } from 'lucide-vue-next'
import DonutChart from '@/components/DonutChart.vue'

// ...
interface Stats {
  active_members: number
  accesses_today: number
  current_occupancy: number
  expiring_soon: number
}

interface RecentActivity {
  client_name: string
  check_in: string
  status: 'active' | 'completed'
}

interface ExpiringMember {
  id: number
  name: string
  membership_expires: string
  days_left: number
}

interface PeakHour {
  range: string
  count: number
}

interface MembershipDist {
  type: string
  count: number
}

const loading = ref(true)
const stats = ref<any[]>([])
const recentActivity = ref<RecentActivity[]>([])
const expiringMembers = ref<ExpiringMember[]>([])
const peakHours = ref<PeakHour[]>([])
const membershipDistribution = ref<MembershipDist[]>([])
const genderDistribution = ref<Record<string, number>>({})

const currentTime = ref('')
const currentDate = ref('')

const maxPeakCount = computed(() => {
  if (peakHours.value.length === 0) return 1
  return Math.max(...peakHours.value.map((h) => h.count))
})

// ✅ Funciones de traducción
const translateMembershipType = (type: string): string => {
  const translations: Record<string, string> = {
    daily: 'Diario',
    weekly: 'Semanal',
    biweekly: 'Quincenal',
    monthly: 'Mensual',
    quarterly: 'Trimestral',
    annual: 'Anual',
    visit: 'Visita',
    student: 'Estudiante',
    couple: 'Pareja',
    individual: 'Individual',
  }
  return translations[type.toLowerCase()] || type
}

const translateGender = (gender: string): string => {
  const translations: Record<string, string> = {
    male: 'Masculino',
    female: 'Femenino',
    other: 'Otro',
    prefer_not_to_say: 'Prefiero no decir',
  }
  return translations[gender.toLowerCase()] || gender
}

const formatHourRange = (range: string): string => {
  // Convertir "6-10pm" → "6:00 - 10:00" o "10am-2pm" → "10:00 - 14:00"
  const match = range.match(/(\d+)(am|pm)?-(\d+)(am|pm)/)
  if (match) {
    const [, start, startPeriod, end, endPeriod] = match
    let startHour = parseInt(start)
    let endHour = parseInt(end)

    // Ajustar AM/PM
    if (startPeriod === 'pm' && startHour !== 12) startHour += 12
    if (startPeriod === 'am' && startHour === 12) startHour = 0

    if (endPeriod === 'pm' && endHour !== 12) endHour += 12
    if (endPeriod === 'am' && endHour === 12) endHour = 0

    return `${startHour.toString().padStart(2, '0')}:00 - ${endHour.toString().padStart(2, '0')}:00`
  }
  return range
}

// Datos para gráficas circulares
const membershipChartData = computed(() => {
  return membershipDistribution.value.map((item) => ({
    label: translateMembershipType(item.type),
    value: item.count,
  }))
})

const genderChartData = computed(() => {
  return Object.entries(genderDistribution.value).map(([label, value]) => ({
    label: translateGender(label),
    value,
  }))
})

const peakHoursChartData = computed(() => {
  return peakHours.value.map((item) => ({
    label: formatHourRange(item.range),
    value: item.count,
  }))
})

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
  currentDate.value = now.toLocaleDateString('es-ES', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  })
}

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

const loadDashboardData = async () => {
  loading.value = true
  try {
    const [statsRes, activityRes, expiringRes, peakHoursRes, distRes, genderRes] =
      await Promise.all([
        api.get<Stats>('/dashboard/stats'),
        api.get<RecentActivity[]>('/dashboard/recent-activity'),
        api.get<ExpiringMember[]>('/dashboard/expiring-members'),
        api.get<Record<string, number>>('/dashboard/peak-hours'),
        api.get<MembershipDist[]>('/dashboard/membership-distribution'),
        api.get<Record<string, number>>('/dashboard/gender-distribution'),
      ])

    const statsData = statsRes.data
    stats.value = [
      {
        title: 'Miembros Activos',
        value: statsData.active_members,
        subtitle: 'Con membresía vigente',
        icon: Users,
      },
      {
        title: 'Accesos Hoy',
        value: statsData.accesses_today,
        subtitle: 'Check-ins registrados',
        icon: Activity,
      },
      {
        title: 'Ocupación Actual',
        value: statsData.current_occupancy,
        subtitle: 'Personas dentro',
        icon: Dumbbell,
      },
      {
        title: 'Por Vencer',
        value: statsData.expiring_soon,
        subtitle: 'Próximos 7 días',
        icon: Calendar,
      },
    ]

    recentActivity.value = activityRes.data
    expiringMembers.value = expiringRes.data

    const peakData = peakHoursRes.data
    peakHours.value = Object.entries(peakData).map(([range, count]) => ({
      range,
      count: count as number,
    }))

    membershipDistribution.value = distRes.data
    genderDistribution.value = genderRes.data
  } catch (error) {
    console.error('Error cargando datos del dashboard:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  updateTime()
  setInterval(updateTime, 1000)
  loadDashboardData()
  setInterval(loadDashboardData, 30000)
})
</script>
