# 📘 ACCESFY - Documentación API para Frontend

## 🌐 Información General

**Base URL:** `http://localhost:8000/api`  
**Autenticación:** Bearer Token (Laravel Sanctum)  
**Formato:** JSON  
**Charset:** UTF-8

---

## 🔐 Autenticación

### Login

Todos los roles usan el mismo flujo pero con endpoints diferentes.

#### SuperUser Login
```http
POST /api/auth/super-user/login
Content-Type: application/json

{
  "email": "admin@accesfy.com",
  "password": "password123"
}
```

**Response 200:**
```json
{
  "user": {
    "id": 1,
    "name": "Admin Master",
    "email": "admin@accesfy.com",
    "active": true
  },
  "token": "1|randomTokenString",
  "token_type": "Bearer"
}
```

#### GymOwner Login
```http
POST /api/auth/gym-owner/login
```

#### Staff Login
```http
POST /api/auth/staff/login
```

### Logout
```http
POST /api/auth/{role}/logout
Authorization: Bearer {token}
```

**Response 200:**
```json
{
  "message": "Sesión cerrada exitosamente"
}
```

### Usuario Actual
```http
GET /api/auth/{role}/me
Authorization: Bearer {token}
```

---

## 👥 Gestión de Clientes

### Listar Clientes
```http
GET /api/clients
Authorization: Bearer {token}
```

**Response 200:**
```json
[
  {
    "id": 1,
    "gym_owner_id": 1,
    "name": "Juan Martínez",
    "email": "juan@email.com",
    "phone": "555-0101",
    "active": true,
    "gender": "male",
    "membership_expires": "2025-02-15 00:00:00",
    "membership": {
      "id": 1,
      "type": "individual",
      "price": "49.99"
    },
    "is_couple": false,
    "biometric_enabled": false
  }
]
```

### Crear Cliente
```http
POST /api/clients
Authorization: Bearer {token}
Content-Type: application/json

{
  "gym_owner_id": 1,
  "name": "Carlos López",
  "email": "carlos@email.com",
  "phone": "555-1234",
  "gender": "male",
  "membership_id": 1,
  "membership_expires": "2025-06-30",
  "pin_hash": "hashed_pin_value",
  "biometric_enabled": true
}
```

### Buscar Clientes
```http
GET /api/clients/search?query=Juan
Authorization: Bearer {token}
```

---

## 💳 Membresías

### Listar Membresías del Gym
```http
GET /api/gym-owner/memberships
Authorization: Bearer {token}
```

**Response 200:**
```json
[
  {
    "id": 1,
    "gym_owner_id": 1,
    "type": "individual",
    "price": "49.99",
    "description": "Acceso ilimitado 1 mes",
    "daily_payment": false,
    "active": true,
    "clients_count": 45
  }
]
```

### Crear Membresía
```http
POST /api/memberships
Authorization: Bearer {token}

{
  "gym_owner_id": 1,
  "type": "custom",
  "price": 99.99,
  "description": "Plan CrossFit Premium",
  "daily_payment": false,
  "active": true
}
```

**Tipos disponibles:** `individual`, `couple`, `student`, `custom`, `visit`

### Activar/Desactivar Membresía
```http
POST /api/memberships/{id}/toggle-active
Authorization: Bearer {token}
```

---

## 📍 Check-in Biométrico

### Registro de Entrada
```http
POST /api/attendance/check-in-biometric
Content-Type: application/json

{
  "pin_hash": "hashed_pin_or_fingerprint"
}
```

**Response 200 (Éxito):**
```json
{
  "success": true,
  "message": "Acceso concedido",
  "client": {
    "name": "Juan Pérez",
    "membership_type": "individual",
    "membership_status": "Activa"
  },
  "attendance": {
    "check_in": "14:30"
  }
}
```

**Response 404 (Cliente no encontrado):**
```json
{
  "success": false,
  "message": "Cliente no encontrado o inactivo"
}
```

**Response 403 (Membresía vencida):**
```json
{
  "success": false,
  "message": "Membresía vencida",
  "client": {
    "name": "Juan Pérez",
    "membership_expires": "2024-12-01"
  }
}
```

**Response 400 (Ya registró entrada hoy):**
```json
{
  "success": false,
  "message": "Ya tiene un acceso activo hoy",
  "client": {
    "name": "Juan Pérez",
    "check_in": "08:30"
  }
}
```

---

## 📊 Dashboard

### Estadísticas Generales
```http
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Response 200:**
```json
{
  "active_members": 247,
  "accesses_today": 89,
  "current_occupancy": 61,
  "expiring_soon": 23
}
```

### Actividad Reciente
```http
GET /api/dashboard/recent-activity
Authorization: Bearer {token}
```

**Response 200:**
```json
[
  {
    "client_name": "Juan Pérez",
    "check_in": "hace 5 minutos",
    "status": "active"
  }
]
```

### Miembros Próximos a Vencer
```http
GET /api/dashboard/expiring-members
Authorization: Bearer {token}
```

**Response 200:**
```json
[
  {
    "id": 1,
    "name": "Pedro Sánchez",
    "membership_expires": "2025-12-10",
    "days_left": 1
  }
]
```

### Distribución de Membresías
```http
GET /api/dashboard/membership-distribution
Authorization: Bearer {token}
```

**Response 200:**
```json
[
  { "type": "individual", "count": 98 },
  { "type": "couple", "count": 65 },
  { "type": "student", "count": 52 }
]
```

### Distribución de Género
```http
GET /api/dashboard/gender-distribution
Authorization: Bearer {token}
```

**Response 200:**
```json
{
  "Hombres": 142,
  "Mujeres": 98,
  "Otro": 7
}
```

### Horas Pico
```http
GET /api/dashboard/peak-hours
Authorization: Bearer {token}
```

**Response 200:**
```json
{
  "6-10am": 45,
  "10am-2pm": 28,
  "2-6pm": 52,
  "6-10pm": 122
}
```

---

## 🔔 Notificaciones

### Listar Notificaciones No Leídas
```http
GET /api/notifications
Authorization: Bearer {token}
```

**Response 200:**
```json
[
  {
    "id": 1,
    "client_id": 5,
    "type": "membership_expiring",
    "title": "Membresía próxima a vencer",
    "message": "Tu membresía vence en 3 día(s). Renueva pronto para seguir disfrutando.",
    "is_read": false,
    "created_at": "2025-12-03 10:00:00"
  }
]
```

### Marcar Como Leída
```http
POST /api/notifications/{id}/mark-read
Authorization: Bearer {token}
```

### Marcar Todas Como Leídas
```http
POST /api/notifications/mark-all-read
Authorization: Bearer {token}
```

---

## ⚙️ Configuración del Gimnasio

### Obtener Toda la Configuración
```http
GET /api/settings
Authorization: Bearer {token}
```

**Response 200:**
```json
{
  "gym_name": "Gimnasio Fit Plus",
  "gym_address": "Calle Principal #123",
  "gym_phone": "555-9876",
  "opening_time": "06:00",
  "closing_time": "22:00",
  "currency": "MXN",
  "gym_logo": "logos/abc123.jpg"
}
```

### Actualizar Configuración
```http
POST /api/settings
Authorization: Bearer {token}
Content-Type: application/json

{
  "gym_name": "Nuevo Nombre",
  "gym_address": "Nueva Dirección",
  "gym_phone": "555-1234",
  "opening_time": "05:00",
  "closing_time": "23:00"
}
```

### Subir Logo
```http
POST /api/settings/logo
Authorization: Bearer {token}
Content-Type: multipart/form-data

logo: [archivo de imagen]
```

**Restricciones:**
- Formatos: JPEG, PNG, JPG, WEBP
- Tamaño máximo: 2MB

**Response 200:**
```json
{
  "success": true,
  "message": "Logo actualizado",
  "path": "http://localhost:8000/storage/logos/abc123.jpg"
}
```

---

## 👤 Gestión de Staff

### Listar Staff del Gym
```http
GET /api/gym-owner/staff
Authorization: Bearer {token}
```

### Crear Staff
```http
POST /api/staff
Authorization: Bearer {token}

{
  "gym_owner_id": 1,
  "name": "María González",
  "email": "maria@gym.com",
  "password": "securepass123",
  "active": true
}
```

### Resetear Contraseña de Staff
```http
POST /api/staff/{id}/reset-password
Authorization: Bearer {token}

{
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## 💾 Backups

### Crear Backup
```http
POST /api/backups
Authorization: Bearer {token}

{
  "gym_owner_id": 1,
  "file_path": "backups/gym_1_2025-12-06.sql"
}
```

**Permisos:**
- SuperUser: ✅ Crear, ✅ Aplicar
- GymOwner: ✅ Crear, ❌ Aplicar
- Staff: ✅ Crear, ❌ Aplicar

### Aplicar Backup (Solo SuperUser)
```http
POST /api/backups/{id}/apply
Authorization: Bearer {token}
```

---

## 🚨 Manejo de Errores

### Error de Autenticación (401)
```json
{
  "message": "No autenticado",
  "error": "Unauthenticated"
}
```

### Error de Validación (422)
```json
{
  "message": "Errores de validación",
  "errors": {
    "email": ["El campo email es obligatorio"],
    "password": ["La contraseña debe tener al menos 6 caracteres"]
  }
}
```

### Error 403 (Sin permisos)
```json
{
  "message": "No tienes permiso para realizar esta acción"
}
```

### Error 404 (No encontrado)
```json
{
  "message": "Recurso no encontrado",
  "error": "Not Found"
}
```

---

## 🎨 Paleta de Colores del Sistema

```css
--primary-bg: #000000;
--primary-accent: #f7c948;
--glass-bg: rgba(255, 255, 255, 0.05);
--glass-border: rgba(255, 255, 255, 0.1);
--text-primary: #ffffff;
--text-secondary: rgba(255, 255, 255, 0.6);
```

---

## 📝 Notas Importantes

1. **Todas las fechas** están en formato ISO 8601: `YYYY-MM-DD HH:MM:SS`
2. **Tokens** deben enviarse en el header: `Authorization: Bearer {token}`
3. **Membresías custom** usan el campo `description` para el nombre personalizado
4. **Notificaciones** se generan automáticamente 4, 3, 2 y 1 días antes de expirar
5. **Check-in biométrico** NO requiere autenticación (es público)
6. **Gender** puede ser: `male`, `female`, `other`

---

## 🧪 Credenciales de Prueba

### SuperUser
```
Email: admin@accesfy.com
Password: password123
```

### GymOwner
```
Email: carlos@gimnasio1.com
Password: $4dm1n
```

---

## 📞 Soporte

Para dudas sobre la API, contactar al equipo de backend.

**Versión:** 1.0  
**Última actualización:** Diciembre 2025