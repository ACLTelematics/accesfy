<template>
  <div class="flex h-screen bg-black">
    <!-- Sidebar -->
    <div class="w-72 bg-zinc-950 border-r border-zinc-800 flex flex-col">
      <!-- Logo -->
      <div class="p-6 border-b border-zinc-800">
        <div class="flex flex-col gap-2">
          <img src="/logo.png" alt="Accesfy" class="h-10 w-auto object-contain" />
          <div class="border-t border-zinc-700 pt-2">
            <h2 class="text-lg font-bold text-yellow-500">{{ authStore.gymName }}</h2>
            <p class="text-xs text-gray-500">Sistema de Gestión</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <div class="flex-1 overflow-y-auto p-4">
        <div class="mb-6">
          <div class="text-xs font-semibold text-gray-500 uppercase mb-3 px-3">Menú Principal</div>
          <nav class="space-y-1">
            <router-link
              v-for="item in navItems"
              :key="item.name"
              :to="item.path"
              :class="[
                'flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-base font-medium',
                $route.name === item.name
                  ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20'
                  : 'text-gray-400 hover:bg-zinc-800 hover:text-white',
              ]"
            >
              <component :is="item.icon" class="w-6 h-6" />
              <span>{{ item.label }}</span>
            </router-link>
          </nav>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="p-4 border-t border-zinc-800">
        <router-link
          to="/dashboard/settings"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-lg transition-all mb-4 text-base font-medium',
            $route.name === 'settings'
              ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20'
              : 'text-gray-400 hover:bg-zinc-800 hover:text-white',
          ]"
        >
          <Settings class="w-6 h-6" />
          <span>Configuración</span>
        </router-link>

        <div
          class="flex items-center gap-3 px-3 py-3 bg-zinc-900 rounded-lg border border-zinc-800"
        >
          <div
            class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-black font-bold text-sm"
          >
            {{ userInitials }}
          </div>
          <div class="flex-1">
            <div class="text-sm font-medium text-white">{{ authStore.userName }}</div>
            <div class="text-xs text-gray-500">Administrador</div>
          </div>
        </div>

        <button
          @click="handleLogout"
          class="w-full mt-3 px-3 py-2 text-sm text-gray-400 hover:text-red-400 transition-all text-left"
        >
          Cerrar Sesión
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto bg-black">
      <!-- Top Bar -->
      <div class="bg-zinc-950 border-b border-zinc-800 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-semibold text-white">{{ pageTitle }}</h2>

          <div class="flex items-center gap-3">
            <!-- Notificaciones -->
            <div class="relative">
              <button
                class="p-2 bg-zinc-900 border border-zinc-800 rounded-lg hover:bg-zinc-800 transition-all relative"
              >
                <Bell class="w-5 h-5 text-gray-400" />
                <span
                  v-if="notificationCount > 0"
                  class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
                >
                  {{ notificationCount }}
                </span>
              </button>
            </div>

            <!-- Botón Nuevo Cliente -->
            <router-link
              to="/dashboard/clients/new"
              class="flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-medium transition-all"
            >
              <Plus class="w-4 h-4" />
              Nuevo Miembro
            </router-link>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <router-view />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  LayoutDashboard,
  Users,
  CreditCard,
  UsersRound,
  DollarSign,
  Settings,
  Bell,
  Plus,
  FileText,
  Database,
} from 'lucide-vue-next'

const route = useRoute()
const authStore = useAuthStore()

const navItems = [
  { name: 'dashboard', path: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { name: 'clients', path: '/dashboard/clients', label: 'Miembros', icon: Users },
  { name: 'memberships', path: '/dashboard/memberships', label: 'Membresías', icon: CreditCard },
  { name: 'staff', path: '/dashboard/staff', label: 'Staff', icon: UsersRound },
  { name: 'payments', path: '/dashboard/payments', label: 'Pagos', icon: DollarSign },
  {
    name: 'notifications',
    path: '/dashboard/notifications',
    label: 'Notificaciones',
    icon: Bell,
  },
  { name: 'reports', path: '/dashboard/reports', label: 'Reportes', icon: FileText },
  { name: 'backups', path: '/dashboard/backups', label: 'Respaldos', icon: Database },
]

const pageTitle = computed(() => {
  const item = navItems.find((i) => i.name === route.name)
  if (item) return item.label
  if (route.name === 'settings') return 'Configuración'
  return 'Dashboard'
})

const userInitials = computed(() => {
  const name = authStore.userName
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
})

const notificationCount = ref(0)

const handleLogout = () => {
  if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
    authStore.logout()
  }
}
</script>
