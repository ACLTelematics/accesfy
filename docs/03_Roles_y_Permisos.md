# Roles y Permisos - Proyecto Accesfy

## 🔑 Roles del sistema

### 🧍‍♂️ Super Admin
- Tiene control total del sistema.
- Puede crear o eliminar cuentas de administradores de gimnasio (Gym Owners).
- Puede recuperar contraseñas de administradores.
- Puede crear otro Super Admin como respaldo.
- Puede ver métricas globales de todos los gimnasios.

---

### 🏋️‍♂️ Gym Owner (Administrador de gimnasio)
- Tiene control completo sobre su gimnasio.
- Puede crear, editar o eliminar:
  - Staff (empleados)
  - Clientes
- Puede **activar o desactivar clientes**.
- Puede **restaurar y crear backups** del gimnasio (manuales).
- Puede **editar tarifas** de membresías:
  - Estudiante
  - Pareja
  - Individual
  - Personalizada
- Puede **ver métricas financieras** (ingresos, pagos, membresías activas, etc.).
- Puede **reactivar membresías** de clientes.
- Cada membresía activa dura **30 días**.

---

### 👨‍💼 Staff (Empleado)
- Puede **crear backups**, pero **no restaurarlos**.
- Puede **ver métricas de clientes**, como:
  - Asistencias
  - Membresías activas o inactivas
- No puede ver métricas de ganancias o pagos.
- Puede **activar o desactivar membresías** de clientes, ya que recibe el pago en efectivo.
- No puede eliminar usuarios ni clientes.

---

### 💪 Cliente (Miembro del gimnasio)
- Puede acceder a su **estado de membresía** (activa / vencida).
- Puede ver su **historial de asistencias**.
- No tiene acceso a datos financieros ni a otros usuarios.

---

## 🧱 Estructura jerárquica

