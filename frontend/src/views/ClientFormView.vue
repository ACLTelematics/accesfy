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
        <!-- Información Personal -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
            <UserPlus :size="20" class="text-[#f7c948]" />
            Información Personal
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
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

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2">
                Correo Electrónico
              </label>
              <div class="relative">
                <Mail :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                <input
                  v-model="formData.email"
                  type="email"
                  class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="ejemplo@correo.com"
                />
              </div>
              <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
            </div>

            <!-- Teléfono -->
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Teléfono </label>
              <div class="relative">
                <Phone :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                <input
                  v-model="formData.phone"
                  type="tel"
                  class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:border-[#f7c948] transition-colors"
                  placeholder="1234567890"
                />
              </div>
              <p v-if="errors.phone" class="text-red-500 text-sm mt-1">{{ errors.phone }}</p>
            </div>

            <!-- Género -->
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Género </label>
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

          <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <!-- Tipo de Membresía -->
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
                  v-for="membership in memberships"
                  :key="membership.id"
                  :value="membership.id"
                >
                  {{ translateMembershipType(membership.type) }} - ${{ membership.price }} ({{
                    membership.duration_days
                  }}
                  días)
                </option>
              </select>
              <p v-if="errors.membership_id" class="text-red-500 text-sm mt-1">
                {{ errors.membership_id }}
              </p>
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

          <!-- PIN Info (solo crear) -->
          <div v-if="!isEditMode" class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4 mb-4">
            <div class="flex items-start gap-3">
              <Key :size="20" class="text-[#f7c948] mt-0.5" />
              <div>
                <h3 class="text-white font-medium mb-1">PIN de Acceso</h3>
                <p class="text-zinc-400 text-sm">
                  Se generará automáticamente un PIN de 4 dígitos para el check-in del miembro.
                  Asegúrate de entregárselo después de guardar.
                </p>
              </div>
            </div>
          </div>

          <!-- PIN Display (editar) -->
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

          <!-- Biometric Section (Preparada, deshabilitada) -->
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
            {{ loading ? 'Guardando...' : isEditMode ? 'Guardar Cambios' : 'Crear Miembro' }}
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
            <!-- Success Icon -->
            <div
              class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4"
            >
              <Check :size="32" class="text-green-500" />
            </div>

            <h2 class="text-2xl font-bold text-white mb-2">¡Miembro Creado!</h2>
            <p class="text-zinc-400 mb-6">
              El miembro ha sido registrado exitosamente. Entrega este PIN para el check-in:
            </p>

            <!-- PIN Display -->
            <div class="bg-zinc-800 border-2 border-[#f7c948] rounded-xl p-6 mb-6">
              <p class="text-zinc-400 text-sm mb-2">PIN de Acceso</p>
              <p class="text-5xl font-bold text-[#f7c948] tracking-wider">
                {{ generatedPin }}
              </p>
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

// Form Data
const formData = ref({
  name: '',
  email: '',
  phone: '',
  gender: '',
  membership_id: '',
})

const errors = ref<Record<string, string>>({})

// Computed
const isEditMode = computed(() => !!route.params.id)

// Traducir tipos de membresía al español
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
    memberships.value = response.data
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
  let isValid = true

  if (!formData.value.name.trim()) {
    errors.value.name = 'El nombre es requerido'
    isValid = false
  }

  if (formData.value.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'Email inválido'
    isValid = false
  }

  if (!formData.value.membership_id) {
    errors.value.membership_id = 'Selecciona una membresía'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) return

  try {
    loading.value = true

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
      alert('Cliente actualizado exitosamente')

      if (generatedPin.value) {
        showPinModal.value = true
      } else {
        goBack()
      }
    } else {
      generatedPin.value = generateRandomPin()
      payload.pin = generatedPin.value

      await api.post('/clients', payload)
      showPinModal.value = true
    }
  } catch (error: any) {
    console.error('Error al guardar cliente:', error)
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      alert('Error al guardar el cliente. Por favor intenta de nuevo.')
    }
  } finally {
    loading.value = false
  }
}

const copyPin = async () => {
  try {
    await navigator.clipboard.writeText(generatedPin.value)
    copied.value = true
    setTimeout(() => (copied.value = false), 2000)
  } catch (error) {
    console.error('Error al copiar:', error)
  }
}

const printPin = () => {
  const printWindow = window.open('', '', 'width=400,height=300')
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>PIN de Acceso</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              text-align: center;
              padding: 40px;
            }
            h1 { font-size: 24px; margin-bottom: 20px; }
            .pin {
              font-size: 48px;
              font-weight: bold;
              letter-spacing: 8px;
              margin: 20px 0;
            }
            .info { font-size: 14px; color: #666; }
          </style>
        </head>
        <body>
          <h1>PIN de Acceso</h1>
          <div class="pin">${generatedPin.value}</div>
          <p class="info">Cliente: ${formData.value.name}</p>
          <p class="info">Usa este PIN para hacer check-in en el gimnasio</p>
        </body>
      </html>
    `)
    printWindow.document.close()
    printWindow.print()
  }
}

const closePinModal = () => {
  showPinModal.value = false
  goBack()
}

const goBack = () => {
  router.push('/dashboard/clients')
}

// Lifecycle
onMounted(() => {
  fetchMemberships()
  if (isEditMode.value) {
    fetchClient()
  }
})
</script>
