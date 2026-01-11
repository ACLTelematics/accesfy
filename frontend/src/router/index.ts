import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginView from '../views/LoginView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { requiresGuest: true },
    },

    // ✅ RUTA INDEPENDIENTE DE CHECK-IN (fuera del dashboard)
    {
      path: '/checkin',
      name: 'checkin-standalone',
      component: () => import('../views/CheckInView.vue'),
      meta: { requiresAuth: true },
    },

    {
      path: '/dashboard',
      component: () => import('../layouts/DashboardLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('../views/DashboardView.vue'),
        },
        // ❌ COMENTAR ESTA (la del dashboard con sidebar)
        // {
        //   path: 'checkin',
        //   name: 'checkin',
        //   component: () => import('../views/CheckInView.vue'),
        // },
        {
          path: 'clients',
          name: 'clients',
          component: () => import('../views/ClientsListView.vue'),
        },
        {
          path: 'clients/new',
          name: 'clients-new',
          component: () => import('../views/ClientFormView.vue'),
        },

        {
          path: 'staff',
          name: 'staff',
          component: () => import('../views/StaffListView.vue'),
        },
        {
          path: 'payments-memberships',
          name: 'payments-memberships',
          component: () => import('../views/PaymentsAndMembershipsView.vue'),
        },
        {
          path: 'settings',
          name: 'settings',
          component: () => import('../views/SettingsView.vue'),
        },
        {
          path: 'profile',
          name: 'profile',
          component: () => import('../views/ProfileView.vue'),
        },
        {
          path: 'clients/:id',
          name: 'client-detail',
          component: () => import('@/views/ClientDetailView.vue'),
          meta: { requiresAuth: true },
        },
        {
          path: 'clients/:id/edit',
          name: 'client-edit',
          component: () => import('@/views/ClientFormView.vue'),
          meta: { requiresAuth: true },
        },
        {
          path: 'reports',
          name: 'Reports',
          component: () => import('@/views/ReportsView.vue'),
          meta: { requiresAuth: true },
        },
      ],
    },
    {
      path: '/',
      redirect: '/login', // ← Cambié a /login en lugar de /dashboard
    },
  ],
})

// Navigation Guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Inicializar auth si no está iniciado
  if (!authStore.user && authStore.token) {
    authStore.initAuth()
  }

  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      next('/login')
    } else {
      next()
    }
  } else if (to.meta.requiresGuest) {
    if (authStore.isAuthenticated) {
      next('/dashboard')
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
