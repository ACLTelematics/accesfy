<template>
  <div class="min-h-screen flex items-center justify-center bg-black">
    <div class="login-box">
      <img src="/logo.png" alt="ACCESFY" class="logo" />

      <h1 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h1>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <input
            v-model="username"
            type="text"
            placeholder="Usuario"
            required
            class="input-field"
          />
        </div>
        <div>
          <input
            v-model="password"
            type="password"
            placeholder="Contraseña"
            required
            class="input-field"
          />
        </div>

        <button type="submit" :disabled="loading" class="btn-primary w-full">
          {{ loading ? 'Ingresando...' : 'Entrar' }}
        </button>

        <div v-if="error" class="error-message">
          {{ error }}
        </div>
      </form>

      <div class="text-center mt-6 text-sm text-white/50">
        <p>¿Olvidaste tu contraseña? Llama al administrador</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    console.log('Login:', username.value, password.value)

    // Simular login exitoso (temporal)
    setTimeout(() => {
      router.push('/dashboard')
    }, 1000)
  } catch (err) {
    error.value = 'Credenciales incorrectas'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-box {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  padding: 3rem;
  border-radius: 20px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 0 20px rgba(182, 182, 182, 0.08);
}

.logo {
  width: 250x;
  height: 100px;
  margin: 0 auto 2rem;
  display: block;
}

.input-field {
  width: 100%;
  padding: 0.875rem;
  font-size: 1rem;
  border: none;
  border-radius: 10px;
  outline: none;
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
  transition: all 0.2s;
}

.input-field::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.input-field:focus {
  background-color: rgba(255, 255, 255, 0.15);
}

.btn-primary {
  padding: 0.875rem;
  font-size: 1rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  background-color: #f7c948;
  color: #000;
}

.btn-primary:hover:not(:disabled) {
  background-color: #ffdb5c;
  transform: translateY(-2px);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  padding: 0.75rem;
  background-color: rgba(255, 82, 82, 0.2);
  border-radius: 8px;
  color: #ff5252;
  text-align: center;
  font-size: 0.9rem;
}
</style>
