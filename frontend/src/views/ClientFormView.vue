<template>
  <div class="min-h-screen bg-black p-8">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white mb-2">
            {{ isEditMode ? 'Editar Miembro' : 'Nuevo Miembro' }}
          </h1>
          <p class="text-zinc-400">
            {{
              isEditMode
                ? 'Actualiza la información del miembro'
                : 'Registra un nuevo miembro en tu gimnasio'
            }}
          </p>
        </div>
        <button
          @click="goBack"
          class="px-4 py-2 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors"
        >
          Cancelar
        </button>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-8">
      <form @submit.prevent="handleSubmit">
        <!-- Toggle: Membresía Individual o Pareja -->
        <div v-if="!isEditMode" class="mb-8">
          <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-6">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                v-model="isCouple"
                class="w-5 h-5 rounded border-zinc-600 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-0 bg-zinc-700"
              />
              <div>
                <p class="text-white font-medium">¿Es membresía pareja?</p>
                <p class="text-zinc-400 text-sm">
                  Activa esta opción para registrar 2 personas con 1 solo pago y PIN compartido
                </p>
              </div>
            </label>
          </div>
        </div>

        <!-- MODO INDIVIDUAL -->
        <div v-if="!isCouple">
          <!-- Información Personal -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <UserPlus :size="20" class="text-[#f7c948]" />
              Información Personal
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="formData.name"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="Ej: Juan Pérez"
                />
                <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2"
                  >Correo Electrónico</label
                >
                <div class="relative">
                  <Mail :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                  <input
                    v-model="formData.email"
                    type="email"
                    class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                    placeholder="ejemplo@correo.com"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Teléfono</label>
                <div class="relative">
                  <Phone
                    :size="18"
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500"
                  />
                  <input
                    v-model="formData.phone"
                    type="tel"
                    class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                    placeholder="1234567890"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Género</label>
                <select
                  v-model="formData.gender"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                >
                  <option value="">Seleccionar...</option>
                  <option value="male">Masculino</option>
                  <option value="female">Femenino</option>
                  <option value="other">Otro</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Membresía -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <CreditCard :size="20" class="text-[#f7c948]" />
              Membresía
            </h2>

            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Tipo de Membresía <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="formData.membership_id"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                >
                  <option value="">Seleccionar membresía...</option>
                  <option
                    v-for="membership in individualMemberships"
                    :key="membership.id"
                    :value="membership.id"
                  >
                    {{ translateMembershipType(membership.type) }} - ${{ membership.price }} ({{
                      membership.duration_days
                    }}
                    días)
                  </option>
                </select>
                <p class="text-zinc-500 text-sm mt-2">
                  ✨ La fecha de vencimiento se calculará automáticamente
                </p>
              </div>
            </div>
          </div>

          <!-- Sistema de Acceso -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <Lock :size="20" class="text-[#f7c948]" />
              Sistema de Acceso
            </h2>

            <div
              v-if="!isEditMode"
              class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4 mb-4"
            >
              <div class="flex items-start gap-3">
                <Key :size="20" class="text-[#f7c948] mt-0.5" />
                <div>
                  <h3 class="text-white font-medium mb-1">PIN de Acceso</h3>
                  <p class="text-zinc-400 text-sm">
                    Se generará automáticamente un PIN de 4 dígitos para el check-in del miembro.
                  </p>
                </div>
              </div>
            </div>

            <div
              v-if="isEditMode && clientData?.pin_hash"
              class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4 mb-4"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <Key :size="20" class="text-[#f7c948]" />
                  <div>
                    <h3 class="text-white font-medium">PIN Actual</h3>
                    <p class="text-zinc-400 text-sm">El cliente ya tiene un PIN configurado</p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="generateNewPin"
                  class="px-4 py-2 bg-[#f7c948] text-black rounded-lg hover:bg-[#FFDB5C] transition-colors text-sm font-medium"
                >
                  Generar Nuevo PIN
                </button>
              </div>
            </div>

            <!-- Biometric (Preparada, deshabilitada) -->
            <div class="bg-zinc-800/30 border border-zinc-700 rounded-lg p-4 opacity-50">
              <div class="flex items-start gap-3 mb-3">
                <Fingerprint :size="20" class="text-zinc-500 mt-0.5" />
                <div>
                  <h3 class="text-white font-medium mb-1">Huella Digital</h3>
                  <p class="text-zinc-400 text-sm">Sistema biométrico no disponible actualmente</p>
                </div>
              </div>
              <button
                type="button"
                disabled
                class="w-full px-4 py-2 bg-zinc-700 text-zinc-500 rounded-lg cursor-not-allowed"
              >
                Registrar Huella (No disponible)
              </button>
            </div>
          </div>
        </div>

        <!-- MODO PAREJA -->
        <div v-else>
          <!-- Persona 1 -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <UserPlus :size="20" class="text-[#f7c948]" />
              Persona 1
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="coupleData.person1_name"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="Ej: Juan Pérez"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2"
                  >Correo Electrónico</label
                >
                <input
                  v-model="coupleData.person1_email"
                  type="email"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="ejemplo@correo.com"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Teléfono</label>
                <input
                  v-model="coupleData.person1_phone"
                  type="tel"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="1234567890"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Género <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="coupleData.person1_gender"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                >
                  <option value="">Seleccionar...</option>
                  <option value="male">Masculino</option>
                  <option value="female">Femenino</option>
                  <option value="other">Otro</option>
                </select>
              </div>
            </div>

            <!-- Huella Persona 1 (preparada) -->
            <div class="mt-4 bg-zinc-800/30 border border-zinc-700 rounded-lg p-4 opacity-50">
              <button
                type="button"
                disabled
                class="w-full px-4 py-2 bg-zinc-700 text-zinc-500 rounded-lg cursor-not-allowed flex items-center justify-center gap-2"
              >
                <Fingerprint :size="18" />
                Capturar Huella Persona 1 (No disponible)
              </button>
            </div>
          </div>

          <!-- Persona 2 -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <UserPlus :size="20" class="text-[#f7c948]" />
              Persona 2
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="coupleData.person2_name"
                  type="text"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="Ej: María López"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2"
                  >Correo Electrónico</label
                >
                <input
                  v-model="coupleData.person2_email"
                  type="email"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="ejemplo@correo.com"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Teléfono</label>
                <input
                  v-model="coupleData.person2_phone"
                  type="tel"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="0987654321"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Género <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="coupleData.person2_gender"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                >
                  <option value="">Seleccionar...</option>
                  <option value="male">Masculino</option>
                  <option value="female">Femenino</option>
                  <option value="other">Otro</option>
                </select>
              </div>
            </div>

            <!-- Huella Persona 2 (preparada) -->
            <div class="mt-4 bg-zinc-800/30 border border-zinc-700 rounded-lg p-4 opacity-50">
              <button
                type="button"
                disabled
                class="w-full px-4 py-2 bg-zinc-700 text-zinc-500 rounded-lg cursor-not-allowed flex items-center justify-center gap-2"
              >
                <Fingerprint :size="18" />
                Capturar Huella Persona 2 (No disponible)
              </button>
            </div>
          </div>

          <!-- Membresía Compartida -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <CreditCard :size="20" class="text-[#f7c948]" />
              Membresía Compartida
            </h2>

            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">
                  Tipo de Membresía <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="coupleData.membership_id"
                  required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                >
                  <option value="">Seleccionar membresía...</option>
                  <option
                    v-for="membership in coupleMemberships"
                    :key="membership.id"
                    :value="membership.id"
                  >
                    {{ translateMembershipType(membership.type) }} - ${{ membership.price }} ({{
                      membership.duration_days
                    }}
                    días)
                  </option>
                </select>
                <p class="text-zinc-500 text-sm mt-2">
                  💑 Ambas personas compartirán esta membresía y el mismo PIN
                </p>
              </div>
            </div>
          </div>

          <!-- PIN Compartido Info -->
          <div class="mb-8">
            <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
              <Lock :size="20" class="text-[#f7c948]" />
              Sistema de Acceso Compartido
            </h2>

            <div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <Key :size="20" class="text-[#f7c948] mt-0.5" />
                <div>
                  <h3 class="text-white font-medium mb-1">PIN Compartido</h3>
                  <p class="text-zinc-400 text-sm">
                    Se generará automáticamente un PIN de 4 dígitos que compartirán ambas personas
                    para hacer check-in.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-800">
          <button
            type="button"
            @click="goBack"
            class="px-6 py-3 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-6 py-3 bg-[#f7c948] text-black rounded-lg hover:bg-[#FFDB5C] transition-colors font-medium flex items-center gap-2"
            :disabled="loading"
          >
            <Loader v-if="loading" :size="18" class="animate-spin" />
            <Save v-else :size="18" />
            {{
              loading
                ? 'Guardando...'
                : isCouple
                  ? 'Crear Pareja'
                  : isEditMode
                    ? 'Guardar Cambios'
                    : 'Crear Miembro'
            }}
          </button>
        </div>
      </form>
    </div>

    <!-- Modal de PIN Generado -->
    <Teleport to="body">
      <div
        v-if="showPinModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @click.self="closePinModal"
      >
        <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-8 max-w-md w-full">
          <div class="text-center">
            <div
              class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
            >
              <Check :size="32" class="text-green-500" />
            </div>

            <h2 class="text-2xl font-bold text-white mb-2">
              {{
                isCouple ? '¡Pareja Creada!' : isEditMode ? '¡PIN Actualizado!' : '¡Miembro Creado!'
              }}
            </h2>
            <p class="text-zinc-400 mb-6">
              {{
                isCouple
                  ? 'PIN compartido generado. Entrégalo a ambas personas:'
                  : 'Entrega este PIN para el check-in:'
              }}
            </p>

            <!-- PIN Display -->
            <div class="bg-zinc-800 border-2 border-[#f7c948] rounded-xl p-6 mb-6">
              <p class="text-zinc-400 text-sm mb-2">
                PIN de Acceso {{ isCouple ? '(Compartido)' : '' }}
              </p>
              <p class="text-5xl font-bold text-[#f7c948] tracking-wider">
                {{ generatedPin }}
              </p>
            </div>

            <!-- Nombres si es pareja -->
            <div
              v-if="isCouple && createdCouple"
              class="bg-zinc-800/50 rounded-lg p-4 mb-6 text-left"
            >
              <p class="text-zinc-400 text-sm mb-2">Miembros registrados:</p>
              <div class="space-y-2">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-[#f7c948] rounded-full"></div>
                  <span class="text-white">{{ createdCouple.client1.name }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-[#f7c948] rounded-full"></div>
                  <span class="text-white">{{ createdCouple.client2.name }}</span>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
              <button
                @click="copyPin"
                class="flex-1 px-4 py-3 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors flex items-center justify-center gap-2"
              >
                <Copy :size="18" />
                {{ copied ? '¡Copiado!' : 'Copiar' }}
              </button>
              <button
                @click="printPin"
                class="flex-1 px-4 py-3 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors flex items-center justify-center gap-2"
              >
                <Printer :size="18" />
                Imprimir
              </button>
            </div>

            <button
              @click="closePinModal"
              class="w-full mt-4 px-4 py-3 bg-[#f7c948] text-black rounded-lg hover:bg-[#FFDB5C] transition-colors font-medium"
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
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  UserPlus,
  Mail,
  Phone,
  CreditCard,
  Lock,
  Key,
  Fingerprint,
  Save,
  Loader,
  Check,
  Copy,
  Printer,
} from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// Estado
const loading = ref(false)
const memberships = ref<any[]>([])
const clientData = ref<any>(null)
const showPinModal = ref(false)
const generatedPin = ref('')
const copied = ref(false)
const isCouple = ref(false)
const createdCouple = ref<any>(null)

// Form Data Individual
const formData = ref({
  name: '',
  email: '',
  phone: '',
  gender: '',
  membership_id: '',
})

// Form Data Pareja
const coupleData = ref({
  person1_name: '',
  person1_email: '',
  person1_phone: '',
  person1_gender: '',
  person1_fingerprint: 'dummy_fingerprint_1',
  person2_name: '',
  person2_email: '',
  person2_phone: '',
  person2_gender: '',
  person2_fingerprint: 'dummy_fingerprint_2',
  membership_id: '',
})

const errors = ref<Record<string, string>>({})

// Computed
const isEditMode = computed(() => !!route.params.id)

const coupleMemberships = computed(() => {
  return memberships.value.filter((m: any) => m.type === 'couple')
})
const individualMemberships = computed(() => {
  return memberships.value.filter((m: any) => m.type !== 'couple')
})
// Traducir tipos
const translateMembershipType = (type: string): string => {
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

// Funciones
const fetchMemberships = async () => {
  try {
    const response = await api.get('/gym-owner/memberships')
    memberships.value = response.data.filter((m: any) => m.active)
  } catch (error) {
    console.error('Error al cargar membresías:', error)
  }
}

const fetchClient = async () => {
  if (!isEditMode.value) return

  try {
    loading.value = true
    const response = await api.get(`/clients/${route.params.id}`)
    clientData.value = response.data

    formData.value = {
      name: response.data.name || '',
      email: response.data.email || '',
      phone: response.data.phone || '',
      gender: response.data.gender || '',
      membership_id: response.data.membership_id || '',
    }
  } catch (error) {
    console.error('Error al cargar cliente:', error)
    alert('Error al cargar los datos del cliente')
    goBack()
  } finally {
    loading.value = false
  }
}

const generateRandomPin = (): string => {
  return Math.floor(1000 + Math.random() * 9000).toString()
}

const generateNewPin = () => {
  if (confirm('¿Estás seguro de generar un nuevo PIN? El PIN anterior dejará de funcionar.')) {
    generatedPin.value = generateRandomPin()
    showPinModal.value = true
  }
}

const validateForm = (): boolean => {
  errors.value = {}

  if (!isCouple.value) {
    if (!formData.value.name.trim()) {
      errors.value.name = 'El nombre es requerido'
      return false
    }
    if (!formData.value.membership_id) {
      errors.value.membership_id = 'Selecciona una membresía'
      return false
    }
  } else {
    if (!coupleData.value.person1_name.trim() || !coupleData.value.person2_name.trim()) {
      alert('Los nombres de ambas personas son requeridos')
      return false
    }
    if (!coupleData.value.person1_gender || !coupleData.value.person2_gender) {
      alert('El género de ambas personas es requerido')
      return false
    }
    if (!coupleData.value.membership_id) {
      alert('Selecciona una membresía')
      return false
    }
  }

  return true
}

const handleSubmit = async () => {
  if (!validateForm()) return

  try {
    loading.value = true

    if (isCouple.value) {
      // CREAR PAREJA
      const response = await api.post('/clients/couple', coupleData.value)
      generatedPin.value = response.data.shared_pin
      createdCouple.value = {
        client1: response.data.clients.person1,
        client2: response.data.clients.person2,
      }
      showPinModal.value = true
    } else {
      // CREAR/EDITAR INDIVIDUAL
      const payload: any = {
        name: formData.value.name,
        email: formData.value.email || null,
        phone: formData.value.phone || null,
        gender: formData.value.gender || null,
        membership_id: formData.value.membership_id,
      }

      if (isEditMode.value) {
        if (generatedPin.value) {
          payload.pin = generatedPin.value
        }
        await api.put(`/clients/${route.params.id}`, payload)
        if (!showPinModal.value) {
          alert('✅ Cliente actualizado exitosamente')
          goBack()
        }
      } else {
        const response = await api.post('/clients', payload)
        generatedPin.value = response.data.pin
        showPinModal.value = true
      }
    }
  } catch (error: any) {
    console.error('Error al guardar cliente:', error)
    alert(error.response?.data?.message || 'Error al guardar el cliente')
  } finally {
    loading.value = false
  }
}

const copyPin = async () => {
  try {
    await navigator.clipboard.writeText(generatedPin.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (error) {
    console.error('Error al copiar:', error)
  }
}

const printPin = () => {
  const printWindow = window.open('', '_blank')
  if (!printWindow) return

  const content =
    isCouple.value && createdCouple.value
      ? `
    <html>
      <head>
        <title>PIN de Acceso - Pareja</title>
        <style>
          body { font-family: Arial, sans-serif; text-align: center; padding: 40px; }
          h1 { color: #f7c948; }
          .pin { font-size: 48px; font-weight: bold; letter-spacing: 8px; margin: 20px 0; }
          .names { margin: 20px 0; font-size: 18px; }
        </style>
      </head>
      <body>
        <h1>PIN de Acceso Compartido</h1>
        <div class="pin">${generatedPin.value}</div>
        <div class="names">
          <p><strong>Persona 1:</strong> ${createdCouple.value.client1.name}</p>
          <p><strong>Persona 2:</strong> ${createdCouple.value.client2.name}</p>
        </div>
        <p>Guarda este PIN en un lugar seguro</p>
      </body>
    </html>
  `
      : `
    <html>
      <head>
        <title>PIN de Acceso</title>
        <style>
          body { font-family: Arial, sans-serif; text-align: center; padding: 40px; }
          h1 { color: #f7c948; }
          .pin { font-size: 48px; font-weight: bold; letter-spacing: 8px; margin: 20px 0; }
        </style>
      </head>
      <body>
        <h1>PIN de Acceso</h1>
        <div class="pin">${generatedPin.value}</div>
        <p><strong>${formData.value.name}</strong></p>
        <p>Guarda este PIN en un lugar seguro</p>
      </body>
    </html>
  `

  printWindow.document.write(content)
  printWindow.document.close()
  printWindow.print()
}

const closePinModal = () => {
  showPinModal.value = false
  goBack()
}

const goBack = () => {
  router.push('/dashboard/clients')
}

onMounted(() => {
  fetchMemberships()
  fetchClient()
})
</script>
