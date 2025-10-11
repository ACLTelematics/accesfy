# ╔══════════════════════════════════════════════════════════════════╗
# ║                         PROYECTO ACCESFY                         ║
# ║                Control de acceso y membresías (v1.0)             ║
# ╚══════════════════════════════════════════════════════════════════╝
**Autor:** AbigailCL 
**Repositorio:** https://github.com/abigailcl/accesfy  
**Versión:** 1.2  
**Fecha:** 2025-10-09  

---

## 🧩 1. ESQUEMA TÉCNICO GENERAL

╔══════════════════════════════════════════════════════════════════╗
║ ROLES ║
╠══════════════════════════════════════════════════════════════════╣
║ SUPER USER (2 cuentas) ║
║ ├─ Crea gimnasios ║
║ ├─ Crea/da de baja administradores ║
║ ├─ Accede a todo el sistema ║
║ └─ Tiene usuario y contraseña ║
║ ║
║ ADMINISTRADOR DEL GYM ║
║ ├─ 1 por gimnasio ║
║ ├─ Puede crear usuarios staff ║
║ ├─ Puede crear/modificar paquetes ║
║ ├─ Puede hacer y restaurar backups ║
║ ├─ Puede reactivar, eliminar o editar miembros ║
║ ├─ Puede enlazar/desenlazar parejas ║
║ └─ Accede con usuario y contraseña (no correo) ║
║ ║
║ STAFF DEL GYM (varios) ║
║ ├─ Creados por el administrador ║
║ ├─ Pueden editar, borrar o reactivar membresías ║
║ ├─ Pueden hacer backups ║
║ ├─ No pueden restaurar backups ║
║ ├─ Pueden enlazar/desenlazar parejas ║
║ └─ No pueden crear otros staff ║
║ ║
║ MIEMBROS DEL GYM ║
║ ├─ Registrados por admin o staff ║
║ ├─ Acceden con huella o PIN ║
║ ├─ PIN compartido si es pareja ║
║ ├─ Si se desenlazan, el PIN cambia ║
║ ├─ 4 intentos erróneos bloquean al miembro ║
║ └─ Recepción (staff) puede desbloquear ║
╚══════════════════════════════════════════════════════════════════╝


---

## 🗄️ 2. ESQUEMA DE BASE DE DATOS

╔══════════════════════════════════════════════════════════════════╗
║ TABLAS PRINCIPALES ║
╠══════════════════════════════════════════════════════════════════╣
║ gyms ║
║ ├─ id (PK) ║
║ ├─ name ║
║ ├─ address ║
║ ├─ created_by (FK → users.id) ║
║ └─ timestamps ║
║ ║
║ users ║
║ ├─ id (PK) ║
║ ├─ username (único) ║
║ ├─ password_hash ║
║ ├─ role (super, admin, staff) ║
║ ├─ gym_id (FK → gyms.id, nullable si superuser) ║
║ └─ timestamps ║
║ ║
║ members ║
║ ├─ id (PK) ║
║ ├─ gym_id (FK → gyms.id) ║
║ ├─ name ║
║ ├─ phone ║
║ ├─ pin (único por gym) ║
║ ├─ fingerprint_template (local, no nube) ║
║ ├─ package_id (FK → packages.id) ║
║ ├─ is_active (bool) ║
║ ├─ is_blocked (bool) ║
║ ├─ linked_member_id (FK → members.id, nullable) ║
║ └─ timestamps ║
║ ║
║ packages ║
║ ├─ id (PK) ║
║ ├─ gym_id (FK → gyms.id) ║
║ ├─ name (Individual, Pareja, Estudiante, etc.) ║
║ ├─ price ║
║ ├─ duration_days ║
║ └─ timestamps ║
║ ║
║ attendance ║
║ ├─ id (PK) ║
║ ├─ member_id (FK → members.id) ║
║ ├─ checkin_time ║
║ ├─ verified_by (staff_id FK → users.id) ║
║ └─ timestamps ║
║ ║
║ backups ║
║ ├─ id (PK) ║
║ ├─ gym_id (FK → gyms.id) ║
║ ├─ created_by (FK → users.id) ║
║ ├─ file_path (/backups/accesfy_<fecha>.sql) ║
║ ├─ can_restore (bool — solo admin) ║
║ └─ timestamps ║
╚══════════════════════════════════════════════════════════════════╝


---

## 🌐 3. ESQUEMA DE RUTAS API REST (Laravel 11)



╔══════════════════════════════════════════════════════════════════╗
║ AUTENTICACIÓN ║
╠══════════════════════════════════════════════════════════════════╣
POST /api/login → Login (usuario+contraseña)
POST /api/logout → Cerrar sesión
GET /api/profile → Obtener datos del usuario actual

╔══════════════════════════════════════════════════════════════════╗
║ GIMNASIOS ║
╠══════════════════════════════════════════════════════════════════╣
GET /api/gyms → [Super] Listar gimnasios
POST /api/gyms → [Super] Crear gimnasio
DELETE /api/gyms/{id} → [Super] Eliminar gimnasio

╔══════════════════════════════════════════════════════════════════╗
║ USUARIOS ║
╠══════════════════════════════════════════════════════════════════╣
GET /api/users → [Admin/Super] Listar usuarios
POST /api/users → [Admin/Super] Crear usuario (staff)
PATCH /api/users/{id} → [Admin/Super] Editar usuario
DELETE /api/users/{id} → [Super] Eliminar usuario

╔══════════════════════════════════════════════════════════════════╗
║ MIEMBROS ║
╠══════════════════════════════════════════════════════════════════╣
GET /api/members → [Admin/Staff] Listar miembros
POST /api/members → [Admin/Staff] Registrar miembro
PATCH /api/members/{id} → [Admin/Staff] Editar miembro
DELETE /api/members/{id} → [Admin/Staff] Eliminar miembro
PATCH /api/members/{id}/link → [Admin/Staff] Enlazar pareja
PATCH /api/members/{id}/unlink → [Admin/Staff] Desenlazar pareja
PATCH /api/members/{id}/reactivate → [Admin/Staff] Reactivar membresía
PATCH /api/members/{id}/unblock → [Admin/Staff] Desbloquear PIN

╔══════════════════════════════════════════════════════════════════╗
║ PAQUETES ║
╠══════════════════════════════════════════════════════════════════╣
GET /api/packages → [Admin/Staff] Ver paquetes
POST /api/packages → [Admin] Crear paquete
PATCH /api/packages/{id} → [Admin] Editar paquete
DELETE /api/packages/{id} → [Admin] Eliminar paquete

╔══════════════════════════════════════════════════════════════════╗
║ ASISTENCIA ║
╠══════════════════════════════════════════════════════════════════╣
POST /api/attendance → [Local SDK] Registrar asistencia
GET /api/attendance → [Admin/Staff] Consultar registros

╔══════════════════════════════════════════════════════════════════╗
║ BACKUPS ║
╠══════════════════════════════════════════════════════════════════╣
POST /api/backups → [Admin/Staff] Crear backup
GET /api/backups → [Admin/Staff] Listar backups
POST /api/backups/{id}/restore → [Admin] Restaurar backup
DELETE /api/backups/{id} → [Admin/Super] Eliminar backup



---

## ⚙️ 4. LÓGICA DE NEGOCIO Y VALIDACIONES

╔══════════════════════════════════════════════════════════════════╗
║ REGLAS CLAVE ║
╠══════════════════════════════════════════════════════════════════╣
║ - Todos los usuarios acceden por usuario y contraseña (no email). ║
║ - El super user crea gimnasios y sus administradores. ║
║ - Cada gimnasio tiene 1 administrador y varios staff. ║
║ - El staff no puede crear otros staff ni restaurar backups. ║
║ - El admin puede crear paquetes y gestionar staff. ║
║ - Paquetes iniciales: Individual, Pareja, Estudiante. ║
║ - En paquetes de pareja comparten PIN. ║
║ - Si se desenlazan, el PIN se regenera automáticamente. ║
║ - 4 intentos fallidos de PIN → bloqueo automático. ║
║ - El staff puede desbloquear manualmente al miembro. ║
║ - Las huellas no se envían a la nube. Solo se guarda el ID. ║
║ - Backups se generan en /backups/accesfy_<fecha>.sql ║
║ - Staff puede generar backups, admin puede restaurarlos. ║
╚══════════════════════════════════════════════════════════════════╝


---

## 📦 5. ESTRUCTURA DE CARPETAS LARAVEL (SUGERIDA)

accesfy/
├── app/
│ ├── Http/
│ │ ├── Controllers/
│ │ └── Middleware/
│ ├── Models/
│ └── Policies/
├── database/
│ ├── migrations/
│ └── seeders/
├── routes/
│ └── api.php
├── storage/
│ └── backups/
├── .env
└── composer.json


---

## ✅ FIN DEL DOCUMENTO

