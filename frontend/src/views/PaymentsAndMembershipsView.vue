<template>
  <div class="p-6 space-y-6">
    <!-- Tabs -->
    <div class="flex gap-2 border-b border-zinc-800">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'px-6 py-3 font-medium text-sm transition-all relative',
          activeTab === tab.id
            ? 'text-yellow-500 border-b-2 border-yellow-500'
            : 'text-gray-400 hover:text-white',
        ]"
      >
        <component :is="tab.icon" class="w-4 h-4 inline mr-2" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Tab Content -->
    <div>
      <!-- TAB 1: REGISTRAR PAGO -->
      <div v-if="activeTab === 'payments'" class="space-y-6">
        <div class="grid grid-cols-3 gap-6">
          <!-- Botón Principal o Formulario -->
          <div class="col-span-2">
            <!-- Botón para abrir formulario -->
            <div v-if="!showPaymentForm" class="bg-zinc-900 border border-zinc-800 rounded-xl p-8">
              <div class="text-center">
                <div
                  class="w-20 h-20 bg-yellow-500/10 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                  <DollarSign class="w-10 h-10 text-yellow-500" />
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Registrar Nuevo Pago</h3>
                <p class="text-gray-400 mb-6">Busca un cliente y registra su pago</p>
                <button
                  @click="showPaymentForm = true"
                  class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-black font-semibold rounded-lg transition-all"
                >
                  Comenzar
                </button>
              </div>
            </div>

            <!-- Formulario de Pago -->
            <div v-else class="space-y-6">
              <!-- Buscar Cliente -->
              <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-white">Buscar Cliente</h3>
                  <button
                    @click="closePaymentForm"
                    class="text-gray-400 hover:text-red-400 transition-all"
                  >
                    <X class="w-5 h-5" />
                  </button>
                </div>

                <div class="relative">
                  <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    v-model="searchQuery"
                    @input="handleSearch"
                    type="text"
                    placeholder="Buscar por nombre, teléfono o ID..."
                    class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-yellow-500"
                    autofocus
                  />
                </div>

                <!-- Resultados de búsqueda -->
                <div
                  v-if="searchResults.length > 0"
                  class="mt-4 space-y-2 max-h-60 overflow-y-auto"
                >
                  <button
                    v-for="client in searchResults"
                    :key="client.id"
                    @click="selectClient(client)"
                    class="w-full p-4 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg text-left transition-all"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium text-white">{{ client.name }}</p>
                        <p class="text-sm text-gray-400">{{ client.phone || 'Sin teléfono' }}</p>
                      </div>
                      <div class="text-right">
                        <p class="text-sm text-gray-400">
                          {{ translateMembershipType(client.membership?.type) }}
                        </p>
                        <p
                          class="text-xs"
                          :class="
                            isExpired(client.membership_expires) ? 'text-red-400' : 'text-green-400'
                          "
                        >
                          Vence: {{ formatDate(client.membership_expires) }}
                        </p>
                      </div>
                    </div>
                  </button>
                </div>

                <div
                  v-else-if="searchQuery.length > 0 && searchResults.length === 0"
                  class="mt-4 text-center py-8 text-gray-500"
                >
                  No se encontraron clientes
                </div>
              </div>

              <!-- Cliente Seleccionado -->
              <div v-if="selectedClient" class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                      <User class="w-5 h-5 text-yellow-500" />
                      {{ selectedClient.name }}
                    </h3>
                    <div class="flex gap-4 text-sm text-gray-400 mt-1">
                      <span v-if="selectedClient.email">📧 {{ selectedClient.email }}</span>
                      <span v-if="selectedClient.phone">📱 {{ selectedClient.phone }}</span>
                    </div>
                  </div>
                  <button @click="selectedClient = null" class="text-gray-400 hover:text-red-400">
                    <X class="w-5 h-5" />
                  </button>
                </div>

                <!-- Info de Membresía -->
                <div
                  class="p-4 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border border-yellow-500/20 rounded-lg mb-6"
                >
                  <div class="grid grid-cols-4 gap-4">
                    <div>
                      <p class="text-xs text-gray-400 mb-1">Membresía Actual</p>
                      <p class="font-bold text-white text-lg">
                        {{ translateMembershipType(selectedClient.membership?.type) }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 mb-1">Precio</p>
                      <p class="font-bold text-yellow-500 text-lg">
                        ${{ selectedClient.membership?.price }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 mb-1">Vencimiento</p>
                      <p
                        :class="[
                          'font-bold text-lg',
                          isExpired(selectedClient.membership_expires)
                            ? 'text-red-400'
                            : 'text-green-400',
                        ]"
                      >
                        {{ formatDate(selectedClient.membership_expires) }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 mb-1">Estado</p>
                      <span
                        :class="[
                          'inline-flex px-3 py-1 rounded-full text-sm font-medium',
                          selectedClient.active
                            ? 'bg-green-500/20 text-green-400'
                            : 'bg-red-500/20 text-red-400',
                        ]"
                      >
                        {{ selectedClient.active ? 'Activo' : 'Inactivo' }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Formulario de Pago -->
                <div class="space-y-6">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-300 mb-2">
                        Monto a Pagar <span class="text-red-400">*</span>
                      </label>
                      <div class="relative">
                        <DollarSign
                          class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                        />
                        <input
                          v-model="paymentForm.amount"
                          type="number"
                          step="0.01"
                          placeholder="0.00"
                          class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-lg font-semibold focus:outline-none focus:border-yellow-500"
                        />
                      </div>
                      <p class="text-xs text-gray-500 mt-1">
                        💡 Precio sugerido: ${{ selectedClient.membership?.price }}
                      </p>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-300 mb-2">
                        Método de Pago <span class="text-red-400">*</span>
                      </label>
                      <select
                        v-model="paymentForm.method"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                      >
                        <option value="cash">💵 Efectivo</option>
                        <option value="card">💳 Tarjeta</option>
                        <option value="transfer">🏦 Transferencia</option>
                        <option value="other">📝 Otro</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2"
                      >Fecha de Pago</label
                    >
                    <input
                      v-model="paymentForm.payment_date"
                      type="date"
                      class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                      Nota (Opcional)
                    </label>
                    <textarea
                      v-model="paymentForm.note"
                      rows="2"
                      placeholder="Agregar nota sobre el pago..."
                      class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500 resize-none"
                    ></textarea>
                  </div>
                  <!-- Opciones de Acción -->
                  <div class="p-4 bg-zinc-800/50 rounded-lg space-y-3">
                    <h4 class="text-sm font-medium text-gray-300 flex items-center gap-2">
                      <Settings class="w-4 h-4" />
                      Opciones al Registrar
                    </h4>

                    <!-- ✅ CHECKBOX 1: EXTENDER MEMBRESÍA -->
                    <label
                      class="flex items-start gap-3 cursor-pointer group p-3 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-all"
                    >
                      <input
                        v-model="paymentForm.extend_membership"
                        @change="handleExtendToggle"
                        :disabled="paymentForm.deactivate_account || paymentForm.change_membership"
                        type="checkbox"
                        :class="[
                          'mt-1 w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-green-500 focus:ring-green-500 focus:ring-offset-0',
                          paymentForm.deactivate_account || paymentForm.change_membership
                            ? 'opacity-50 cursor-not-allowed'
                            : '',
                        ]"
                      />
                      <div class="flex-1">
                        <p class="text-white font-medium">✅ Extender membresía +30 días</p>
                        <p class="text-sm text-gray-400">
                          Nueva fecha: {{ getNewExpirationDate() }}
                        </p>
                      </div>
                    </label>

                    <!-- ✅ CHECKBOX 2: CAMBIAR MEMBRESÍA -->
                    <label
                      class="flex items-start gap-3 cursor-pointer group p-3 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-all"
                    >
                      <input
                        v-model="paymentForm.change_membership"
                        @change="handleChangeMembershipToggle"
                        :disabled="paymentForm.deactivate_account || paymentForm.extend_membership"
                        type="checkbox"
                        :class="[
                          'mt-1 w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-0',
                          paymentForm.deactivate_account || paymentForm.extend_membership
                            ? 'opacity-50 cursor-not-allowed'
                            : '',
                        ]"
                      />
                      <div class="flex-1">
                        <p class="text-white font-medium">🔄 Cambiar tipo de membresía</p>
                        <p class="text-sm text-gray-400">Cambiar a otra membresía disponible</p>
                      </div>
                    </label>

                    <!-- Dropdown de nueva membresía -->
                    <div v-if="paymentForm.change_membership" class="ml-8 mt-2 space-y-2">
                      <select
                        v-model="paymentForm.new_membership_id"
                        @change="updateAmountFromNewMembership"
                        :disabled="paymentForm.deactivate_account"
                        :class="[
                          'w-full px-4 py-2 bg-zinc-900 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-yellow-500',
                          paymentForm.deactivate_account ? 'opacity-50 cursor-not-allowed' : '',
                        ]"
                      >
                        <option value="">Seleccionar nueva membresía...</option>
                        <option
                          v-for="membership in availableMemberships"
                          :key="membership.id"
                          :value="membership.id"
                        >
                          {{ translateMembershipType(membership.type) }} - ${{ membership.price }}
                        </option>
                      </select>

                      <p v-if="paymentForm.new_membership_id" class="text-xs text-yellow-500">
                        💡 El monto se actualizó automáticamente al precio de la nueva membresía
                      </p>
                    </div>

                    <!-- ✅ CHECKBOX 3: DESACTIVAR CUENTA -->
                    <label
                      class="flex items-start gap-3 cursor-pointer group p-3 bg-zinc-800 rounded-lg hover:bg-red-900/20 transition-all border border-transparent hover:border-red-500/30"
                    >
                      <input
                        v-model="paymentForm.deactivate_account"
                        @change="handleDeactivateToggle"
                        :disabled="paymentForm.extend_membership || paymentForm.change_membership"
                        type="checkbox"
                        :class="[
                          'mt-1 w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-red-500 focus:ring-red-500 focus:ring-offset-0',
                          paymentForm.extend_membership || paymentForm.change_membership
                            ? 'opacity-50 cursor-not-allowed'
                            : '',
                        ]"
                      />
                      <div class="flex-1">
                        <p class="text-white font-medium">⚠️ Desactivar cuenta</p>
                        <p class="text-sm text-red-400">
                          El cliente no podrá hacer check-in después de esta acción
                        </p>
                      </div>
                    </label>
                  </div>
                </div>
                <div class="flex gap-3 pt-4 border-t border-zinc-800">
                  <button
                    @click="closePaymentForm"
                    type="button"
                    class="flex-1 px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg font-medium transition-all"
                  >
                    Cancelar
                  </button>
                  <button
                    @click="registerPayment"
                    :disabled="loading || !paymentForm.amount"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-black rounded-lg font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                  >
                    <Loader v-if="loading" class="w-5 h-5 animate-spin" />
                    <Check v-else class="w-5 h-5" />
                    {{ loading ? 'Registrando...' : 'Registrar Pago' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar: Últimos Pagos -->
        <div class="col-span-1">
          <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 sticky top-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
              <Clock class="w-5 h-5" />
              Últimos Pagos
            </h3>

            <div v-if="recentPayments.length === 0" class="text-center py-8 text-gray-500">
              No hay pagos recientes
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="payment in recentPayments"
                :key="payment.id"
                class="p-3 bg-zinc-800 rounded-lg"
              >
                <p class="font-medium text-white text-sm">
                  {{ payment.client?.name || 'Cliente eliminado' }}
                </p>
                <div class="flex justify-between items-center mt-1">
                  <span class="text-xs text-gray-400">{{ translateMethod(payment.method) }}</span>
                  <span class="text-sm font-semibold text-green-400">${{ payment.amount }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ formatDateTime(payment.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: GESTIONAR MEMBRESÍAS -->
    <div v-if="activeTab === 'memberships'" class="space-y-6">
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-white">Tipos de Membresía</h3>
          <p class="text-sm text-gray-400">
            {{ memberships.filter((m) => m.active).length }} activas
          </p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div
            v-for="membership in memberships"
            :key="membership.id"
            class="p-6 bg-zinc-800 border border-zinc-700 rounded-lg"
          >
            <div class="flex items-start justify-between mb-4">
              <div>
                <h4 class="text-lg font-semibold text-white">
                  {{ translateMembershipType(membership.type) }}
                </h4>
                <p class="text-sm text-gray-400">{{ membership.duration_days }} días</p>
              </div>
              <button
                @click="toggleMembershipActive(membership)"
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium transition-all',
                  membership.active
                    ? 'bg-green-500/20 text-green-400'
                    : 'bg-gray-500/20 text-gray-400',
                ]"
              >
                {{ membership.active ? 'Activa' : 'Inactiva' }}
              </button>
            </div>

            <div class="space-y-3">
              <div>
                <label class="block text-xs text-gray-500 mb-1">Precio</label>
                <div class="flex gap-2">
                  <input
                    v-model="membership.price"
                    type="number"
                    step="0.01"
                    :disabled="!canEditMembershipPrice"
                    :class="[
                      'flex-1 px-3 py-2 bg-zinc-900 border border-zinc-700 rounded text-white focus:outline-none',
                      canEditMembershipPrice
                        ? 'focus:border-yellow-500'
                        : 'opacity-50 cursor-not-allowed',
                    ]"
                  />
                  <button
                    v-if="canEditMembershipPrice"
                    @click="updateMembershipPrice(membership)"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black rounded font-medium transition-all"
                  >
                    <Save class="w-4 h-4" />
                  </button>
                  <div
                    v-else
                    class="px-4 py-2 bg-zinc-700 text-gray-400 rounded flex items-center"
                    title="Solo el propietario puede modificar precios"
                  >
                    <Lock class="w-4 h-4" />
                  </div>
                </div>
                <p v-if="!canEditMembershipPrice" class="text-xs text-amber-500 mt-1">
                  🔒 Solo el propietario puede modificar precios
                </p>
              </div>

              <div>
                <label class="block text-xs text-gray-500 mb-1">Descripción</label>
                <p class="text-sm text-gray-400">
                  {{ membership.description || 'Sin descripción' }}
                </p>
              </div>

              <div class="flex items-center justify-between pt-3 border-t border-zinc-700">
                <span class="text-xs text-gray-500">Clientes usando</span>
                <span class="text-sm font-semibold text-yellow-500">{{
                  membership.clients_count || 0
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: HISTORIAL -->
    <div v-if="activeTab === 'history'" class="space-y-6">
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-white">Historial de Pagos</h3>

          <div class="flex gap-3">
            <select
              v-model="historyFilters.method"
              class="px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-sm focus:outline-none focus:border-yellow-500"
            >
              <option value="">Todos los métodos</option>
              <option value="cash">Efectivo</option>
              <option value="card">Tarjeta</option>
              <option value="transfer">Transferencia</option>
              <option value="other">Otro</option>
            </select>

            <input
              v-model="historyFilters.dateFrom"
              type="date"
              class="px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-sm focus:outline-none focus:border-yellow-500"
            />
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-zinc-800">
                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Cliente</th>
                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Monto</th>
                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Método</th>
                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Fecha</th>
                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-400">Nota</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="payment in filteredPayments"
                :key="payment.id"
                class="border-b border-zinc-800 hover:bg-zinc-800/50 transition-all"
              >
                <td class="py-3 px-4 text-sm text-white">{{ payment.client?.name || 'N/A' }}</td>
                <td class="py-3 px-4 text-sm font-semibold text-green-400">
                  ${{ payment.amount }}
                </td>
                <td class="py-3 px-4 text-sm text-gray-400">
                  {{ translateMethod(payment.method) }}
                </td>
                <td class="py-3 px-4 text-sm text-gray-400">
                  {{ formatDate(payment.payment_date) }}
                </td>
                <td class="py-3 px-4 text-sm text-gray-400">{{ payment.note || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredPayments.length === 0" class="text-center py-12 text-gray-500">
          No hay pagos registrados
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
  CreditCard,
  FileText,
  Search,
  X,
  Check,
  Clock,
  Save,
  User,
  Settings,
  Loader,
  Lock,
} from 'lucide-vue-next'

const authStore = useAuthStore()

const tabs = [
  { id: 'payments', label: 'Registrar Pago', icon: DollarSign },
  { id: 'memberships', label: 'Membresías', icon: CreditCard },
  { id: 'history', label: 'Historial', icon: FileText },
]

const activeTab = ref('payments')
const loading = ref(false)
const showPaymentForm = ref(false)
let searchTimeout: any = null

const searchQuery = ref('')
const searchResults = ref<any[]>([])
const selectedClient = ref<any>(null)
const recentPayments = ref<any[]>([])
const availableMemberships = ref<any[]>([])

const paymentForm = ref({
  amount: '',
  method: 'cash',
  payment_date: new Date().toISOString().split('T')[0],
  note: '',
  extend_membership: true,
  change_membership: false,
  new_membership_id: '',
  deactivate_account: false,
})

const memberships = ref<any[]>([])
const allPayments = ref<any[]>([])
const historyFilters = ref({
  method: '',
  dateFrom: '',
})

const canEditMembershipPrice = computed(() => {
  const userRole = authStore.userRole
  return userRole === 'gym_owner' || userRole === 'super_user'
})

const filteredPayments = computed(() => {
  let filtered = allPayments.value

  if (historyFilters.value.method) {
    filtered = filtered.filter((p) => p.method === historyFilters.value.method)
  }

  if (historyFilters.value.dateFrom) {
    filtered = filtered.filter((p) => p.payment_date >= historyFilters.value.dateFrom)
  }

  return filtered
})

const handleSearch = async () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  if (searchQuery.value.length < 2) {
    searchResults.value = []
    return
  }

  searchTimeout = setTimeout(async () => {
    try {
      const { data } = await api.get(`/clients/search?query=${searchQuery.value}`)
      searchResults.value = data
    } catch (error: any) {
      console.error('Error buscando clientes:', error)
      alert('Error al buscar clientes')
    }
  }, 300)
}

const selectClient = (client: any) => {
  selectedClient.value = client
  searchResults.value = []
  searchQuery.value = ''
  paymentForm.value.amount = client.membership?.price || ''
}

const closePaymentForm = () => {
  showPaymentForm.value = false
  selectedClient.value = null
  searchQuery.value = ''
  searchResults.value = []
  paymentForm.value = {
    amount: '',
    method: 'cash',
    payment_date: new Date().toISOString().split('T')[0],
    note: '',
    extend_membership: true,
    change_membership: false,
    new_membership_id: '',
    deactivate_account: false,
  }
}
const handleExtendToggle = () => {
  if (paymentForm.value.extend_membership) {
    // Si activas "Extender", desactiva las otras dos opciones
    paymentForm.value.change_membership = false
    paymentForm.value.deactivate_account = false
    paymentForm.value.new_membership_id = ''
  }
}

const handleChangeMembershipToggle = () => {
  if (paymentForm.value.change_membership) {
    // Si activas "Cambiar membresía", desactiva las otras dos opciones
    paymentForm.value.extend_membership = false
    paymentForm.value.deactivate_account = false
  } else {
    // Si desactivas "Cambiar membresía", limpia la selección
    paymentForm.value.new_membership_id = ''
  }
}

const handleDeactivateToggle = () => {
  if (paymentForm.value.deactivate_account) {
    // Si activas "Desactivar", desactiva las otras dos opciones
    paymentForm.value.extend_membership = false
    paymentForm.value.change_membership = false
    paymentForm.value.new_membership_id = ''
  }
}

const updateAmountFromNewMembership = () => {
  const newMembership = availableMemberships.value.find(
    (m) => m.id === parseInt(paymentForm.value.new_membership_id),
  )
  if (newMembership) {
    paymentForm.value.amount = newMembership.price
  }
}

const getNewExpirationDate = () => {
  if (!selectedClient.value?.membership_expires) return 'N/A'

  const currentExpiration = new Date(selectedClient.value.membership_expires)
  const newDate = new Date(currentExpiration)
  newDate.setDate(newDate.getDate() + 30)

  return formatDate(newDate.toISOString())
}

const registerPayment = async () => {
  if (!selectedClient.value || !paymentForm.value.amount) {
    alert('Por favor completa todos los campos')
    return
  }

  if (
    paymentForm.value.deactivate_account &&
    !confirm('¿Estás seguro de desactivar este cliente?')
  ) {
    return
  }

  loading.value = true

  try {
    const userRole = authStore.userRole
    const user = authStore.user

    if (!user) {
      alert('Usuario no autenticado')
      return
    }

    let gymOwnerId: number
    let staffId: number | null = null

    if (userRole === 'gym_owner') {
      gymOwnerId = user.id
    } else if (userRole === 'staff') {
      gymOwnerId = (user as any).gym_owner_id
      staffId = user.id
    } else {
      alert('No tienes permisos')
      return
    }

    const payload: any = {
      gym_owner_id: gymOwnerId,
      client_id: selectedClient.value.id,
      staff_id: staffId,
      amount: parseFloat(paymentForm.value.amount),
      method: paymentForm.value.method,
      frequency: 'monthly',
      payment_date: paymentForm.value.payment_date,
      note: paymentForm.value.note || null,
      extend_membership: paymentForm.value.extend_membership,
      deactivate_client: paymentForm.value.deactivate_account,
    }

    if (paymentForm.value.change_membership && paymentForm.value.new_membership_id) {
      await api.put(`/clients/${selectedClient.value.id}`, {
        membership_id: parseInt(paymentForm.value.new_membership_id),
      })
    }

    await api.post('/payments', payload)

    alert('✅ Pago registrado exitosamente')
    closePaymentForm()
    fetchRecentPayments()
    fetchAllPayments()
  } catch (error: any) {
    console.error('Error registrando pago:', error)
    alert(error.response?.data?.message || 'Error al registrar pago')
  } finally {
    loading.value = false
  }
}
const fetchRecentPayments = async () => {
  try {
    const userRole = authStore.userRole
    const user = authStore.user

    if (!user) return

    let gymOwnerId: number

    if (userRole === 'gym_owner') {
      gymOwnerId = user.id
    } else if (userRole === 'staff') {
      gymOwnerId = (user as any).gym_owner_id
    } else {
      console.error('Rol no válido para cargar pagos')
      return
    }

    const { data } = await api.get(`/payments/gym-owner/${gymOwnerId}`)
    recentPayments.value = data.slice(0, 5)
  } catch (error: any) {
    console.error('Error cargando pagos recientes:', error)
    console.error('Response:', error.response?.data)
  }
}

const toggleMembershipActive = async (membership: any) => {
  try {
    await api.post(`/memberships/${membership.id}/toggle-active`)
    membership.active = !membership.active
    alert('✅ Estado actualizado correctamente')
  } catch (error: any) {
    console.error('Error actualizando membresía:', error)
    alert(error.response?.data?.message || 'Error al actualizar estado')
  }
}

const updateMembershipPrice = async (membership: any) => {
  try {
    loading.value = true

    const price = parseFloat(membership.price)

    if (isNaN(price) || price < 0) {
      alert('❌ Por favor ingresa un precio válido')
      return
    }

    console.log('Actualizando membresía ID:', membership.id, 'Precio:', price)

    await api.put(`/memberships/${membership.id}`, {
      price: price,
    })

    alert('✅ Precio actualizado correctamente')
    await fetchMemberships()
  } catch (error: any) {
    console.error('Error actualizando precio:', error)
    console.error('Response data:', error.response?.data)

    // Mostrar error más detallado
    const errorMsg =
      error.response?.data?.message ||
      error.response?.data?.error ||
      'Error al actualizar el precio'
    alert('❌ ' + errorMsg)
  } finally {
    loading.value = false
  }
}

const fetchMemberships = async () => {
  try {
    const { data } = await api.get('/gym-owner/memberships')
    console.log('Membresías cargadas:', data.length)
    memberships.value = data
    availableMemberships.value = data.filter((m: any) => m.active)
  } catch (error: any) {
    console.error('Error cargando membresías:', error)
    console.error('Response:', error.response?.data)
  }
}

const fetchAllPayments = async () => {
  try {
    const userRole = authStore.userRole
    const user = authStore.user

    if (!user) return

    let gymOwnerId: number

    if (userRole === 'gym_owner') {
      gymOwnerId = user.id
    } else if (userRole === 'staff') {
      gymOwnerId = (user as any).gym_owner_id
    } else {
      console.error('Rol no válido para cargar historial')
      return
    }

    const { data } = await api.get(`/payments/gym-owner/${gymOwnerId}`)
    allPayments.value = data
  } catch (error: any) {
    console.error('Error cargando historial:', error)
    console.error('Response:', error.response?.data)
  }
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
    hour: '2-digit',
    minute: '2-digit',
  })
}

const isExpired = (date: string) => {
  if (!date) return true
  return new Date(date) < new Date()
}

const getClientStatus = (client: any) => {
  if (!client.active) {
    return {
      label: 'Inactivo',
      class: 'bg-red-500/20 text-red-400',
    }
  }

  if (isExpired(client.membership_expires)) {
    return {
      label: 'Vencido',
      class: 'bg-orange-500/20 text-orange-400',
    }
  }

  return {
    label: 'Activo',
    class: 'bg-green-500/20 text-green-400',
  }
}

onMounted(() => {
  fetchRecentPayments()
  fetchMemberships()
  fetchAllPayments()
})
</script>
