import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

export type UserRole = 'super_user' | 'gym_owner' | 'staff' | 'client'

export function usePermissions() {
  const authStore = useAuthStore()

  // Detectar rol del usuario
  const userRole = computed<UserRole>(() => {
    return authStore.userRole as UserRole
  })

  // ============ PERMISOS GENERALES ============
  
  const isSuperUser = computed(() => userRole.value === 'super_user')
  const isGymOwner = computed(() => userRole.value === 'gym_owner')
  const isStaff = computed(() => userRole.value === 'staff')
  const isClient = computed(() => userRole.value === 'client')

  // ============ DASHBOARD ============
  
  const canViewDashboard = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canViewFullDashboard = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ CLIENTES ============
  
  const canViewClients = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canCreateClient = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canEditClient = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  const canDeleteClient = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ MEMBRESÍAS ============
  
  const canViewMemberships = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canManageMemberships = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ STAFF ============
  
  const canViewStaff = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  const canManageStaff = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ PAGOS ============
  
  const canViewPayments = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canRegisterPayment = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canViewAllPayments = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ REPORTES ============
  
  const canViewReports = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ RESPALDOS ============
  
  const canCreateBackup = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  const canRestoreBackup = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ CONFIGURACIÓN ============
  
  const canViewSettings = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  const canEditSettings = computed(() => {
    return isSuperUser.value || isGymOwner.value
  })

  // ============ NOTIFICACIONES ============
  
  const canViewNotifications = computed(() => {
    return isSuperUser.value || isGymOwner.value || isStaff.value
  })

  // ============ GIMNASIOS (SOLO SUPER USER) ============
  
  const canManageGyms = computed(() => {
    return isSuperUser.value
  })

  return {
    // Roles
    userRole,
    isSuperUser,
    isGymOwner,
    isStaff,
    isClient,

    // Dashboard
    canViewDashboard,
    canViewFullDashboard,

    // Clientes
    canViewClients,
    canCreateClient,
    canEditClient,
    canDeleteClient,

    // Membresías
    canViewMemberships,
    canManageMemberships,

    // Staff
    canViewStaff,
    canManageStaff,

    // Pagos
    canViewPayments,
    canRegisterPayment,
    canViewAllPayments,

    // Reportes
    canViewReports,

    // Respaldos
    canCreateBackup,
    canRestoreBackup,

    // Configuración
    canViewSettings,
    canEditSettings,

    // Notificaciones
    canViewNotifications,

    // Gimnasios
    canManageGyms,
  }
}
