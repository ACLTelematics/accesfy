<template>
  <div class="min-h-screen bg-black text-white p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-2">⚙️ Configuración</h1>
      <p class="text-zinc-400">Personaliza la información de tu gimnasio</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-yellow-500"></div>
    </div>

    <!-- Main Content -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Logo -->
      <div class="lg:col-span-1">
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <span>📸</span> Logo del Gimnasio
          </h2>

          <!-- Logo Preview -->
          <div class="mb-4">
            <div
              v-if="logoPreview || settings.gym_logo"
              class="w-full aspect-square bg-zinc-800 rounded-lg flex items-center justify-center overflow-hidden"
            >
              <img
                :src="logoPreview || getLogoUrl(settings.gym_logo)"
                alt="Logo"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-full aspect-square bg-zinc-800 rounded-lg flex items-center justify-center"
            >
              <span class="text-6xl">🏋️</span>
            </div>
          </div>

          <!-- Upload Buttons -->
          <div class="space-y-2">
            <label class="block">
              <input
                type="file"
                ref="logoInput"
                @change="handleLogoChange"
                accept="image/jpeg,image/png,image/jpg,image/webp"
                class="hidden"
              />
              <button
                @click="$refs.logoInput.click()"
                :disabled="uploadingLogo"
                class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 disabled:bg-zinc-700 disabled:cursor-not-allowed text-black font-semibold rounded-lg transition-colors duration-300 flex items-center justify-center gap-2"
              >
                <span v-if="uploadingLogo">Subiendo...</span>
                <span v-else>📤 Cambiar Logo</span>
              </button>
            </label>

            <button
              v-if="settings.gym_logo"
              @click="deleteLogo"
              class="w-full px-4 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-400 font-semibold rounded-lg transition-colors duration-300"
            >
              🗑️ Eliminar Logo
            </button>
          </div>

          <p class="text-xs text-zinc-500 mt-3">Formatos: JPG, PNG, WEBP. Máximo 2MB</p>
        </div>

        <!-- Quick Stats -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6 mt-6">
          <h3 class="font-semibold mb-3">📊 Estado de Configuración</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-zinc-400">Logo</span>
              <span :class="settings.gym_logo ? 'text-green-400' : 'text-red-400'">
                {{ settings.gym_logo ? '✓ Configurado' : '✗ Pendiente' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-400">Información</span>
              <span :class="settings.gym_name ? 'text-green-400' : 'text-red-400'">
                {{ settings.gym_name ? '✓ Configurado' : '✗ Pendiente' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-400">Contacto</span>
              <span :class="settings.gym_phone ? 'text-green-400' : 'text-red-400'">
                {{ settings.gym_phone ? '✓ Configurado' : '✗ Pendiente' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-400">Redes Sociales</span>
              <span
                :class="
                  settings.social_instagram || settings.social_facebook
                    ? 'text-green-400'
                    : 'text-red-400'
                "
              >
                {{
                  settings.social_instagram || settings.social_facebook
                    ? '✓ Configurado'
                    : '✗ Pendiente'
                }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Forms -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Información Básica -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <span>ℹ️</span> Información Básica
          </h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2">
                Nombre del Gimnasio *
              </label>
              <input
                v-model="settings.gym_name"
                type="text"
                placeholder="Ej: Rochos Muscle Gym"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Eslogan </label>
              <input
                v-model="settings.gym_slogan"
                type="text"
                placeholder="Ej: Tu mejor versión"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Descripción </label>
              <textarea
                v-model="settings.gym_description"
                rows="4"
                placeholder="Describe tu gimnasio..."
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors resize-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Información de Contacto -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <span>📞</span> Información de Contacto
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Dirección </label>
              <input
                v-model="settings.gym_address"
                type="text"
                placeholder="Calle Principal 123, Colonia Centro"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Teléfono </label>
              <input
                v-model="settings.gym_phone"
                type="tel"
                placeholder="+52 123 456 7890"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Email </label>
              <input
                v-model="settings.gym_email"
                type="email"
                placeholder="info@gimnasio.com"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Sitio Web </label>
              <input
                v-model="settings.gym_website"
                type="url"
                placeholder="https://www.gimnasio.com"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>
          </div>
        </div>

        <!-- Horarios de Atención -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <span>🕐</span> Horarios de Atención
          </h2>

          <div class="space-y-3">
            <div
              v-for="day in daysOfWeek"
              :key="day.key"
              class="flex items-center gap-4 p-3 bg-zinc-800/50 rounded-lg"
            >
              <div class="w-24 font-medium">{{ day.label }}</div>

              <div class="flex items-center gap-2 flex-1">
                <input
                  v-model="openingHours[day.key].open"
                  type="time"
                  :disabled="openingHours[day.key].closed"
                  class="px-3 py-2 bg-zinc-700 border border-zinc-600 rounded focus:border-yellow-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                />

                <span class="text-zinc-500">a</span>

                <input
                  v-model="openingHours[day.key].close"
                  type="time"
                  :disabled="openingHours[day.key].closed"
                  class="px-3 py-2 bg-zinc-700 border border-zinc-600 rounded focus:border-yellow-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                />
              </div>

              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="openingHours[day.key].closed"
                  type="checkbox"
                  class="w-5 h-5 rounded border-zinc-600 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-0 bg-zinc-700"
                />
                <span class="text-sm text-zinc-400">Cerrado</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Redes Sociales -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <span>📱</span> Redes Sociales
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2 flex items-center gap-2">
                <span class="text-blue-500">📘</span> Facebook
              </label>
              <input
                v-model="settings.social_facebook"
                type="url"
                placeholder="https://facebook.com/gimnasio"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2 flex items-center gap-2">
                <span class="text-pink-500">📸</span> Instagram
              </label>
              <input
                v-model="settings.social_instagram"
                type="text"
                placeholder="@gimnasio"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2 flex items-center gap-2">
                <span class="text-sky-400">🐦</span> Twitter / X
              </label>
              <input
                v-model="settings.social_twitter"
                type="text"
                placeholder="@gimnasio"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2 flex items-center gap-2">
                <span class="text-green-500">💬</span> WhatsApp
              </label>
              <input
                v-model="settings.social_whatsapp"
                type="tel"
                placeholder="+52 123 456 7890"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              />
            </div>
          </div>
        </div>

        <!-- Configuración Adicional -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <span>🔧</span> Configuración Adicional
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Zona Horaria </label>
              <select
                v-model="settings.timezone"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              >
                <option value="America/Mexico_City">Ciudad de México (GMT-6)</option>
                <option value="America/Cancun">Cancún (GMT-5)</option>
                <option value="America/Tijuana">Tijuana (GMT-8)</option>
                <option value="America/Monterrey">Monterrey (GMT-6)</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Moneda </label>
              <select
                v-model="settings.currency"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              >
                <option value="MXN">MXN - Peso Mexicano</option>
                <option value="USD">USD - Dólar</option>
                <option value="EUR">EUR - Euro</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-zinc-300 mb-2"> Idioma </label>
              <select
                v-model="settings.language"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg focus:border-yellow-500 focus:outline-none transition-colors"
              >
                <option value="es">Español</option>
                <option value="en">English</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
          <button
            @click="saveSettings"
            :disabled="saving"
            class="flex-1 px-6 py-4 bg-yellow-500 hover:bg-yellow-600 disabled:bg-zinc-700 disabled:cursor-not-allowed text-black font-bold rounded-lg transition-colors duration-300 text-lg"
          >
            {{ saving ? '💾 Guardando...' : '💾 Guardar Configuración' }}
          </button>

          <button
            @click="resetForm"
            class="px-6 py-4 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors duration-300"
          >
            ↻ Restablecer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import api from '../services/api'

// State
const loading = ref(true)
const saving = ref(false)
const uploadingLogo = ref(false)
const logoPreview = ref('')
const logoInput = ref<HTMLInputElement>()

const settings = reactive({
  gym_name: '',
  gym_slogan: '',
  gym_description: '',
  gym_address: '',
  gym_phone: '',
  gym_email: '',
  gym_website: '',
  gym_logo: '',
  social_facebook: '',
  social_instagram: '',
  social_twitter: '',
  social_whatsapp: '',
  timezone: 'America/Mexico_City',
  currency: 'MXN',
  language: 'es',
})

const openingHours = reactive({
  monday: { open: '06:00', close: '22:00', closed: false },
  tuesday: { open: '06:00', close: '22:00', closed: false },
  wednesday: { open: '06:00', close: '22:00', closed: false },
  thursday: { open: '06:00', close: '22:00', closed: false },
  friday: { open: '06:00', close: '22:00', closed: false },
  saturday: { open: '08:00', close: '20:00', closed: false },
  sunday: { open: '08:00', close: '14:00', closed: false },
})

const daysOfWeek = [
  { key: 'monday', label: 'Lunes' },
  { key: 'tuesday', label: 'Martes' },
  { key: 'wednesday', label: 'Miércoles' },
  { key: 'thursday', label: 'Jueves' },
  { key: 'friday', label: 'Viernes' },
  { key: 'saturday', label: 'Sábado' },
  { key: 'sunday', label: 'Domingo' },
]

// Methods
const loadSettings = async () => {
  try {
    loading.value = true
    const response = await api.get('/settings')

    // Cargar configuraciones básicas
    Object.keys(settings).forEach((key) => {
      if (response.data[key]) {
        settings[key] = response.data[key]
      }
    })

    // Cargar horarios
    if (response.data.opening_hours) {
      const hours = JSON.parse(response.data.opening_hours)
      Object.assign(openingHours, hours)
    }
  } catch (error) {
    console.error('Error loading settings:', error)
    alert('Error al cargar configuración')
  } finally {
    loading.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true

    // Preparar datos
    const data = {
      ...settings,
      opening_hours: JSON.stringify(openingHours),
    }

    await api.post('/settings', data)

    alert('✅ Configuración guardada exitosamente')
  } catch (error) {
    console.error('Error saving settings:', error)
    alert('❌ Error al guardar configuración')
  } finally {
    saving.value = false
  }
}

const handleLogoChange = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (!file) return

  // Validar tamaño
  if (file.size > 2 * 1024 * 1024) {
    alert('❌ El logo debe ser menor a 2MB')
    return
  }

  // Validar tipo
  if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
    alert('❌ Solo se permiten imágenes JPG, PNG o WEBP')
    return
  }

  // Preview
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)

  // Upload
  try {
    uploadingLogo.value = true
    const formData = new FormData()
    formData.append('logo', file)

    const response = await api.post('/settings/logo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    settings.gym_logo = response.data.path
    alert('✅ Logo actualizado exitosamente')
  } catch (error) {
    console.error('Error uploading logo:', error)
    alert('❌ Error al subir el logo')
    logoPreview.value = ''
  } finally {
    uploadingLogo.value = false
  }
}

const deleteLogo = async () => {
  if (!confirm('¿Estás seguro de eliminar el logo?')) return

  try {
    // Aquí puedes agregar endpoint DELETE si lo implementas
    settings.gym_logo = ''
    logoPreview.value = ''
    await saveSettings()
  } catch (error) {
    console.error('Error deleting logo:', error)
    alert('❌ Error al eliminar el logo')
  }
}

const getLogoUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `http://localhost:8000/storage/${path}`
}

const resetForm = () => {
  if (!confirm('¿Restablecer los cambios no guardados?')) return
  loadSettings()
  logoPreview.value = ''
}

// Lifecycle
onMounted(() => {
  loadSettings()
})
</script>
