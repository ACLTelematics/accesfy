<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { usePermissions } from '@/composables/usePermissions'
import api from '@/services/api'
import { Search, UserPlus, Edit2, Trash2, Eye, Calendar, Mail, Phone } from 'lucide-vue-next'

const router = useRouter()
const { canEditClient, canDeleteClient, canCreateClient } = usePermissions()

// Estados
const clients = ref<any[]>([])
const loading = ref(true)
const searchQuery = ref('')
const filterStatus = ref<'all' | 'active' | 'inactive' | 'expiring'>('all')

// Función para obtener clientes
const fetchClients = async () => {
  try {
    loading.value = true
    const response = await api.get('/clients')
    clients.value = response.data
  } catch (error) {
    console.error('Error al cargar clientes:', error)
  } finally {
    loading.value = false
  }
}

// Clientes filtrados
const filteredClients = computed(() => {
  let filtered = clients.value

  // Filtro de búsqueda
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (client) =>
        client.name.toLowerCase().includes(query) ||
        client.email?.toLowerCase().includes(query) ||
        client.phone?.toLowerCase().includes(query),
    )
  }

  // Filtro de estado
  if (filterStatus.value !== 'all') {
    const now = new Date()
    const sevenDaysFromNow = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000)

    filtered = filtered.filter((client) => {
      if (filterStatus.value === 'active') {
        return client.active && new Date(client.membership_expires) > now
      } else if (filterStatus.value === 'inactive') {
        return !client.active
      } else if (filterStatus.value === 'expiring') {
        const expiresAt = new Date(client.membership_expires)
        return client.active && expiresAt <= sevenDaysFromNow && expiresAt > now
      }
      return true
    })
  }

  return filtered
})

// Estado de membresía
const getMembershipStatus = (client: any) => {
  if (!client.active) {
    return { text: 'Inactivo', color: 'bg-zinc-700 text-zinc-400' }
  }

  const now = new Date()
  const expiresAt = new Date(client.membership_expires)
  const daysLeft = Math.ceil((expiresAt.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))

  if (expiresAt < now) {
    return { text: 'Vencido', color: 'bg-red-500/20 text-red-400 border border-red-500/30' }
  } else if (daysLeft <= 7) {
    return {
      text: `Vence en ${daysLeft}d`,
      color: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    }
  } else {
    return { text: 'Activo', color: 'bg-green-500/20 text-green-400 border border-green-500/30' }
  }
}

// Formatear fecha
const formatDate = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('es-MX', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

// Eliminar cliente
const deleteClient = async (id: number) => {
  if (!confirm('¿Estás seguro de eliminar este cliente?')) return

  try {
    await api.delete(`/clients/${id}`)
    await fetchClients()
  } catch (error) {
    console.error('Error al eliminar cliente:', error)
    alert('Error al eliminar cliente')
  }
}

// Navegar a formulario
const goToNewClient = () => {
  router.push('/dashboard/clients/new')
}

const goToEditClient = (id: number) => {
  router.push(`/dashboard/clients/${id}/edit`)
}

const goToClientDetail = (id: number) => {
  router.push(`/dashboard/clients/${id}`)
}

// Cargar clientes al montar
onMounted(() => {
  fetchClients()
})
</script>

<template>
  <div class="min-h-screen bg-black text-white p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold">Miembros</h1>
        <p class="text-zinc-400 mt-1">Gestiona todos los miembros del gimnasio</p>
      </div>

      <button
        v-if="canCreateClient"
        @click="goToNewClient"
        class="flex items-center gap-2 px-6 py-3 bg-[#f7c948] text-black rounded-lg font-semibold hover:bg-[#FFDB5C] transition-all duration-200 shadow-lg hover:shadow-xl"
      >
        <UserPlus :size="20" />
        Nuevo Miembro
      </button>
    </div>

    <!-- Búsqueda y Filtros -->
    <div class="mb-6 space-y-4">
      <!-- Búsqueda -->
      <div class="relative">
        <Search
          class="absolute left-4 top-1/2 transform -translate-y-1/2 text-zinc-500"
          :size="20"
        />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre, email o teléfono..."
          class="w-full pl-12 pr-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:border-[#f7c948] transition-colors"
        />
      </div>

      <!-- Filtros -->
      <div class="flex gap-3">
        <button
          @click="filterStatus = 'all'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            filterStatus === 'all'
              ? 'bg-[#f7c948] text-black'
              : 'bg-zinc-900 text-zinc-400 hover:bg-zinc-800',
          ]"
        >
          Todos ({{ clients.length }})
        </button>
        <button
          @click="filterStatus = 'active'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            filterStatus === 'active'
              ? 'bg-[#f7c948] text-black'
              : 'bg-zinc-900 text-zinc-400 hover:bg-zinc-800',
          ]"
        >
          Activos
        </button>
        <button
          @click="filterStatus = 'inactive'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            filterStatus === 'inactive'
              ? 'bg-[#f7c948] text-black'
              : 'bg-zinc-900 text-zinc-400 hover:bg-zinc-800',
          ]"
        >
          Inactivos
        </button>
        <button
          @click="filterStatus = 'expiring'"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            filterStatus === 'expiring'
              ? 'bg-[#f7c948] text-black'
              : 'bg-zinc-900 text-zinc-400 hover:bg-zinc-800',
          ]"
        >
          Por Vencer
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div
        class="animate-spin rounded-full h-12 w-12 border-4 border-zinc-800 border-t-[#f7c948]"
      ></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredClients.length === 0" class="text-center py-20">
      <div class="bg-zinc-900 rounded-2xl p-12 max-w-md mx-auto border border-zinc-800">
        <div
          class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4"
        >
          <UserPlus :size="32" class="text-zinc-600" />
        </div>
        <h3 class="text-xl font-semibold mb-2">No hay miembros</h3>
        <p class="text-zinc-400 mb-6">
          {{
            searchQuery ? 'No se encontraron resultados' : 'Comienza agregando tu primer miembro'
          }}
        </p>
        <button
          v-if="canCreateClient && !searchQuery"
          @click="goToNewClient"
          class="px-6 py-3 bg-[#f7c948] text-black rounded-lg font-semibold hover:bg-[#FFDB5C] transition-all"
        >
          Agregar Miembro
        </button>
      </div>
    </div>

    <!-- Tabla de Clientes -->
    <div v-else class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-zinc-800">
              <th class="text-left px-6 py-4 text-sm font-semibold text-zinc-400">Nombre</th>
              <th class="text-left px-6 py-4 text-sm font-semibold text-zinc-400">Contacto</th>
              <th class="text-left px-6 py-4 text-sm font-semibold text-zinc-400">Membresía</th>
              <th class="text-left px-6 py-4 text-sm font-semibold text-zinc-400">Vencimiento</th>
              <th class="text-left px-6 py-4 text-sm font-semibold text-zinc-400">Estado</th>
              <th class="text-right px-6 py-4 text-sm font-semibold text-zinc-400">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="client in filteredClients"
              :key="client.id"
              class="border-b border-zinc-800 hover:bg-zinc-800/50 transition-colors"
            >
              <!-- Nombre -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-10 h-10 rounded-full bg-gradient-to-br from-[#f7c948] to-[#c6a657] flex items-center justify-center text-black font-bold"
                  >
                    {{ client.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-semibold">{{ client.name }}</div>
                    <div class="text-sm text-zinc-400">ID: {{ client.id }}</div>
                  </div>
                </div>
              </td>

              <!-- Contacto -->
              <td class="px-6 py-4">
                <div class="space-y-1">
                  <div v-if="client.email" class="flex items-center gap-2 text-sm text-zinc-400">
                    <Mail :size="14" />
                    <span>{{ client.email }}</span>
                  </div>
                  <div v-if="client.phone" class="flex items-center gap-2 text-sm text-zinc-400">
                    <Phone :size="14" />
                    <span>{{ client.phone }}</span>
                  </div>
                </div>
              </td>

              <!-- Membresía -->
              <td class="px-6 py-4">
                <span class="text-sm text-zinc-300">
                  {{ client.membership?.type || 'N/A' }}
                </span>
              </td>

              <!-- Vencimiento -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2 text-sm text-zinc-400">
                  <Calendar :size="14" />
                  <span>{{ formatDate(client.membership_expires) }}</span>
                </div>
              </td>

              <!-- Estado -->
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    getMembershipStatus(client).color,
                  ]"
                >
                  {{ getMembershipStatus(client).text }}
                </span>
              </td>

              <!-- Acciones -->
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="goToClientDetail(client.id)"
                    class="p-2 text-zinc-400 hover:text-[#f7c948] hover:bg-zinc-800 rounded-lg transition-all"
                    title="Ver detalle"
                  >
                    <Eye :size="18" />
                  </button>
                  <button
                    v-if="canEditClient"
                    @click="goToEditClient(client.id)"
                    class="p-2 text-zinc-400 hover:text-blue-400 hover:bg-zinc-800 rounded-lg transition-all"
                    title="Editar"
                  >
                    <Edit2 :size="18" />
                  </button>
                  <button
                    v-if="canDeleteClient"
                    @click="deleteClient(client.id)"
                    class="p-2 text-zinc-400 hover:text-red-400 hover:bg-zinc-800 rounded-lg transition-all"
                    title="Eliminar"
                  >
                    <Trash2 :size="18" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer con contador -->
      <div class="px-6 py-4 border-t border-zinc-800 bg-zinc-900/50">
        <p class="text-sm text-zinc-400">
          Mostrando <span class="text-white font-semibold">{{ filteredClients.length }}</span> de
          <span class="text-white font-semibold">{{ clients.length }}</span> miembros
        </p>
      </div>
    </div>
  </div>
</template>
