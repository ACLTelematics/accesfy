<template>
  <div class="min-h-screen bg-black text-white flex flex-col">
    <!-- Header Fullscreen -->
    <header class="bg-zinc-950 border-b border-zinc-800 px-6 py-4">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo y Gym Name -->
        <div class="flex items-center gap-4">
          <img src="/logo.png" alt="Logo" class="h-12 w-auto" />
          <div>
            <h1 class="text-xl font-bold text-[#f7c948]">{{ gymName }}</h1>
            <p class="text-sm text-zinc-500">Sistema de Check-in</p>
          </div>
        </div>

        <!-- Botón Salir -->
        <button
          @click="handleExit"
          class="flex items-center gap-2 px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-all"
        >
          <LogOut :size="20" />
          Salir
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center p-8">
      <div class="w-full max-w-6xl">
        <!-- Estado: Normal - Ingreso de PIN -->
        <div
          v-if="state === 'idle' || state === 'validating'"
          class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center"
        >
          <!-- Columna Izquierda: Info y PIN Display -->
          <div class="lg:col-span-1 text-center lg:text-left">
            <!-- Título -->
            <div class="mb-8">
              <h1 class="text-5xl font-bold text-white mb-4">Check-In</h1>
              <p class="text-xl text-zinc-400">Ingresa tu PIN de 4 dígitos</p>
            </div>

            <!-- PIN Display -->
            <div class="bg-zinc-900 border-2 border-zinc-800 rounded-2xl p-8 mb-6">
              <div class="flex justify-center lg:justify-start gap-3 mb-4">
                <div
                  v-for="i in 4"
                  :key="i"
                  :class="[
                    'w-14 h-14 rounded-xl border-2 flex items-center justify-center text-2xl font-bold transition-all duration-200',
                    pin.length >= i
                      ? 'bg-[#f7c948] border-[#f7c948] text-black'
                      : 'bg-zinc-800 border-zinc-700 text-zinc-600',
                  ]"
                >
                  {{ pin.length >= i ? '●' : '' }}
                </div>
              </div>

              <!-- Hidden Input (teclado físico) -->
              <input
                ref="pinInput"
                v-model="pin"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="4"
                class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-center text-xl tracking-widest focus:outline-none focus:border-[#f7c948] transition-colors"
                placeholder="0000"
                @input="handleInput"
                @keyup.enter="handleSubmit"
                autofocus
              />
            </div>

            <!-- Info adicional -->
            <p class="text-zinc-500 text-sm">Usa el teclado en pantalla o físico</p>
          </div>

          <!-- Columna Central: Teclado Numérico -->
          <div class="lg:col-span-1">
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8">
              <div class="grid grid-cols-3 gap-4">
                <!-- Números 1-9 -->
                <button
                  v-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]"
                  :key="num"
                  @click="addDigit(num)"
                  :disabled="state === 'validating'"
                  class="aspect-square bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 text-white text-4xl font-bold rounded-2xl transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation shadow-lg hover:shadow-xl"
                >
                  {{ num }}
                </button>

                <!-- Botón Borrar -->
                <button
                  @click="backspace"
                  :disabled="state === 'validating'"
                  class="aspect-square bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 text-white rounded-2xl transition-all duration-150 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation shadow-lg hover:shadow-xl"
                >
                  <Delete :size="36" />
                </button>

                <!-- Número 0 -->
                <button
                  @click="addDigit(0)"
                  :disabled="state === 'validating'"
                  class="aspect-square bg-zinc-800 hover:bg-zinc-700 active:bg-zinc-600 text-white text-4xl font-bold rounded-2xl transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation shadow-lg hover:shadow-xl"
                >
                  0
                </button>

                <!-- Botón Enviar -->
                <button
                  @click="handleSubmit"
                  :disabled="pin.length !== 4 || state === 'validating'"
                  class="aspect-square bg-[#f7c948] hover:bg-[#FFDB5C] active:bg-[#e6b83a] text-black rounded-2xl transition-all duration-150 flex items-center justify-center font-bold disabled:opacity-50 disabled:cursor-not-allowed touch-manipulation shadow-lg hover:shadow-2xl"
                >
                  <Check v-if="state !== 'validating'" :size="36" />
                  <Loader v-else :size="36" class="animate-spin" />
                </button>
              </div>
            </div>
          </div>

          <!-- Columna Derecha: Opción de Huella -->
          <div class="lg:col-span-1 flex items-center justify-center">
            <div
              class="bg-zinc-900/50 border-2 border-zinc-800 rounded-3xl p-12 text-center opacity-50 cursor-not-allowed max-w-sm"
            >
              <div
                class="inline-flex items-center justify-center w-32 h-32 bg-zinc-800 rounded-full mb-6"
              >
                <Fingerprint :size="64" class="text-zinc-600" />
              </div>
              <h3 class="text-2xl font-bold text-zinc-500 mb-3">Huella Digital</h3>
              <p class="text-zinc-600 mb-4">Sensor biométrico</p>
              <div
                class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-full"
              >
                <Clock :size="16" class="text-zinc-500" />
                <span class="text-zinc-500 text-sm font-medium">Próximamente</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Estado: Éxito -->
        <div v-else-if="state === 'success'" class="max-w-3xl mx-auto text-center animate-fade-in">
          <div
            class="bg-gradient-to-br from-green-500/20 to-green-600/20 border-2 border-green-500 rounded-3xl p-20"
          >
            <div
              class="inline-flex items-center justify-center w-48 h-48 bg-green-500 rounded-full mb-8 animate-scale-in"
            >
              <CheckCircle :size="120" class="text-white" />
            </div>
            <h1 class="text-7xl font-bold text-green-400 mb-6">¡BIENVENIDO!</h1>
            <p class="text-5xl font-semibold text-white mb-6">
              {{ clientName }}
            </p>
            <p class="text-3xl text-zinc-300 mb-8">Check-in: {{ currentTime }}</p>
            <p class="text-xl text-zinc-400">Disfruta tu entrenamiento 💪</p>
          </div>
        </div>

        <!-- Estado: Error -->
        <div v-else-if="state === 'error'" class="max-w-3xl mx-auto text-center animate-fade-in">
          <div
            class="bg-gradient-to-br from-red-500/20 to-red-600/20 border-2 border-red-500 rounded-3xl p-20"
          >
            <div
              class="inline-flex items-center justify-center w-48 h-48 bg-red-500 rounded-full mb-8 animate-shake"
            >
              <XCircle :size="120" class="text-white" />
            </div>
            <h1 class="text-7xl font-bold text-red-400 mb-6">ACCESO DENEGADO</h1>
            <p class="text-3xl text-white mb-6">
              {{ errorMessage }}
            </p>
            <p class="text-2xl text-zinc-400">Verifica tu PIN o contacta al personal</p>
          </div>
        </div>

        <!-- Estado: Membresía Vencida -->
        <div v-else-if="state === 'expired'" class="max-w-3xl mx-auto text-center animate-fade-in">
          <div
            class="bg-gradient-to-br from-yellow-500/20 to-orange-600/20 border-2 border-yellow-500 rounded-3xl p-20"
          >
            <div
              class="inline-flex items-center justify-center w-48 h-48 bg-yellow-500 rounded-full mb-8 animate-pulse"
            >
              <AlertCircle :size="120" class="text-black" />
            </div>
            <h1 class="text-7xl font-bold text-yellow-400 mb-6">MEMBRESÍA VENCIDA</h1>
            <p class="text-4xl font-semibold text-white mb-6">
              {{ clientName }}
            </p>
            <p class="text-3xl text-zinc-300 mb-8">Venció: {{ expirationDate }}</p>
            <p class="text-2xl text-zinc-400">Dirígete a recepción para renovar</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-zinc-950 border-t border-zinc-800 px-6 py-4">
      <div class="max-w-7xl mx-auto text-center text-zinc-500 text-sm">
        <p>ACCESFY © 2024 - Sistema de Gestión de Gimnasios</p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import {
  Fingerprint,
  Clock,
  LogOut,
  Check,
  Delete,
  CheckCircle,
  XCircle,
  AlertCircle,
  Loader,
} from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()

// Estados
type State = 'idle' | 'validating' | 'success' | 'error' | 'expired'

const state = ref<State>('idle')
const pin = ref('')
const pinInput = ref<HTMLInputElement | null>(null)

// Datos
const gymName = computed(() => authStore.gymName || 'ACCESFY')
const clientName = ref('')
const errorMessage = ref('')
const expirationDate = ref('')
const currentTime = ref('')

// Timer
let resetTimer: number | null = null

// Agregar dígito
const addDigit = (digit: number) => {
  if (pin.value.length < 4) {
    pin.value += digit.toString()

    if (pin.value.length === 4) {
      setTimeout(() => handleSubmit(), 300)
    }
  }
}

// Borrar
const backspace = () => {
  if (pin.value.length > 0) {
    pin.value = pin.value.slice(0, -1)
  }
}

// Input handler
const handleInput = (e: Event) => {
  const input = e.target as HTMLInputElement
  pin.value = input.value.replace(/[^0-9]/g, '').slice(0, 4)

  if (pin.value.length === 4) {
    setTimeout(() => handleSubmit(), 300)
  }
}

// Submit
const handleSubmit = async () => {
  if (pin.value.length !== 4 || state.value === 'validating') return

  try {
    state.value = 'validating'

    const response = await api.post('/attendances/check-in-biometric', {
      pin: pin.value,
    })

    if (response.data.success) {
      state.value = 'success'
      clientName.value = response.data.client_name || 'Miembro'
      currentTime.value = new Date().toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
      })

      resetTimer = window.setTimeout(() => {
        resetForm()
      }, 3000)
    } else {
      if (response.data.message?.includes('vencida') || response.data.message?.includes('expiró')) {
        state.value = 'expired'
        clientName.value = response.data.client_name || 'Miembro'
        expirationDate.value = response.data.expiration_date || 'hace algunos días'
      } else {
        state.value = 'error'
        errorMessage.value = response.data.message || 'PIN incorrecto'
      }

      resetTimer = window.setTimeout(() => {
        resetForm()
      }, 4000)
    }
  } catch (error: any) {
    console.error('Error en check-in:', error)
    state.value = 'error'
    errorMessage.value = error.response?.data?.message || 'Error al validar PIN'

    resetTimer = window.setTimeout(() => {
      resetForm()
    }, 4000)
  }
}

// Reset
const resetForm = () => {
  state.value = 'idle'
  pin.value = ''
  clientName.value = ''
  errorMessage.value = ''
  expirationDate.value = ''
  currentTime.value = ''

  setTimeout(() => {
    pinInput.value?.focus()
  }, 100)
}

// Salir
const handleExit = () => {
  if (confirm('¿Salir del sistema de check-in?')) {
    router.push('/dashboard')
  }
}

// Keyboard
const handleKeydown = (e: KeyboardEvent) => {
  if (state.value !== 'idle' && state.value !== 'validating') return

  if (e.key >= '0' && e.key <= '9') {
    return
  }

  if (e.key === 'Backspace') {
    backspace()
  }

  if (e.key === 'Enter') {
    handleSubmit()
  }
}

// Lifecycle
onMounted(() => {
  pinInput.value?.focus()
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  if (resetTimer) {
    clearTimeout(resetTimer)
  }
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes scale-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  10%,
  30%,
  50%,
  70%,
  90% {
    transform: translateX(-10px);
  }
  20%,
  40%,
  60%,
  80% {
    transform: translateX(10px);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

.animate-scale-in {
  animation: scale-in 0.5s ease-out;
}

.animate-shake {
  animation: shake 0.5s ease-out;
}

.touch-manipulation {
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}

button:focus-visible {
  outline: 2px solid #f7c948;
  outline-offset: 2px;
}
</style>
