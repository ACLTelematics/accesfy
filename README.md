## 🧩 Estructura de Base de Datos (Tablas Principales)

Esta sección describe las tablas esenciales del sistema **ACCESFY** para soportar usuarios, gimnasios, membresías, asistencia, backups y personalización por gimnasio.  
Diseñada con enfoque **multi-tenant**, donde cada gimnasio gestiona su propio entorno de datos.

---

### 🔹 Tabla `users`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador único |
| name | VARCHAR(100) | Nombre completo |
| email | VARCHAR(150) | Correo (único) |
| password | VARCHAR(255) | Contraseña cifrada |
| role | ENUM('admin','owner','staff','member') | Rol del usuario |
| gym_id | BIGINT (FK, nullable) | Vinculado al gimnasio (solo si no es admin global) |
| active | BOOLEAN | Estado del usuario |
| created_at / updated_at | TIMESTAMP | Auditoría |

> 🔸 El campo `gym_id` es `NULL` para el **Admin SaaS**, y apunta al gimnasio correspondiente para dueños, staff o miembros.

---

### 🔹 Tabla `gyms`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| name | VARCHAR(100) | Nombre del gimnasio |
| slug | VARCHAR(100) | Identificador único para URLs |
| owner_id | BIGINT (FK users.id) | Dueño del gimnasio |
| plan_id | BIGINT (FK plans.id) | Plan activo |
| status | ENUM('active','suspended') | Estado |
| created_at / updated_at | TIMESTAMP | Auditoría |

---

### 🔹 Tabla `plans`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| name | VARCHAR(50) | Nombre del plan |
| max_gyms | INT | Límite de gimnasios |
| max_members | INT | Límite de miembros |
| price | DECIMAL(10,2) | Precio mensual |
| created_at / updated_at | TIMESTAMP | Auditoría |

---

### 🔹 Tabla `memberships`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| member_id | BIGINT (FK users.id) | Usuario con rol "member" |
| gym_id | BIGINT (FK gyms.id) | Gimnasio |
| type | VARCHAR(50) | Tipo de membresía (mensual, anual, etc.) |
| start_date | DATE | Fecha de inicio |
| end_date | DATE | Fecha de expiración |
| price | DECIMAL(10,2) | Costo |
| status | ENUM('active','expired','cancelled') | Estado |
| created_at / updated_at | TIMESTAMP | Auditoría |

---

### 🔹 Tabla `attendances`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| member_id | BIGINT (FK users.id) | Miembro |
| gym_id | BIGINT (FK gyms.id) | Gimnasio |
| check_in | DATETIME | Entrada |
| check_out | DATETIME (nullable) | Salida |
| staff_id | BIGINT (FK users.id) | Staff que registró la asistencia |

---

### 🔹 Tabla `backups`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| gym_id | BIGINT (FK gyms.id) | Gimnasio asociado |
| file_path | VARCHAR(255) | Ruta del archivo de respaldo |
| created_by | BIGINT (FK users.id) | Quién generó el respaldo |
| type | ENUM('auto','manual') | Tipo de backup |
| created_at | TIMESTAMP | Fecha del respaldo |

> ⚙️ Solo el **Admin SaaS** o el **Dueño del Gimnasio** puede restaurar un backup.  
> Staff solo puede generarlos.

---

### 🔹 Tabla `staff_assignments`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| staff_id | BIGINT (FK users.id) | Staff |
| gym_id | BIGINT (FK gyms.id) | Gimnasio asignado |
| position | VARCHAR(100) | Puesto o rol dentro del gym |
| active | BOOLEAN | Estado |
| created_at / updated_at | TIMESTAMP | Auditoría |

---

### 🔹 Tabla `fingerprints`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| member_id | BIGINT (FK users.id) | Usuario (miembro) |
| fingerprint_code | VARCHAR(255) | Código o hash generado por el lector local |
| registered_at | DATETIME | Fecha de registro de huella |
| active | BOOLEAN | Estado activo/inactivo |
| created_at / updated_at | TIMESTAMP | Auditoría |

> 🧠 Guarda solo el **hash o ID del dispositivo local**, no la imagen biométrica.

---

### 🔹 Tabla `gym_profiles`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| gym_id | BIGINT (FK gyms.id) | Gimnasio |
| logo_url | VARCHAR(255) | Ruta o URL del logo |
| banner_url | VARCHAR(255) | Imagen opcional para portada |
| phone | VARCHAR(30) | Teléfono |
| address | VARCHAR(255) | Dirección |
| opening_hours | JSON | Horarios (por día) |
| theme_color | VARCHAR(20) | Color principal (ej. `#FF0000`) |
| created_at / updated_at | TIMESTAMP | Auditoría |

---

### 🔹 Tabla `reports_cache`

| Campo | Tipo | Descripción |
|--------|------|-------------|
| id | BIGINT (PK) | Identificador |
| gym_id | BIGINT (FK gyms.id) | Gimnasio |
| report_type | ENUM('memberships','attendances','backups') | Tipo de reporte |
| file_path | VARCHAR(255) | Ruta del archivo generado (.xlsx / .csv) |
| generated_by | BIGINT (FK users.id) | Quién generó el reporte |
| generated_at | DATETIME | Fecha de creación |
| expires_at | DATETIME | Fecha en la que se elimina automáticamente |

> 📊 Permite que los usuarios visualicen y descarguen reportes en formato Excel sin recargar el servidor.

---

### 🕸️ Relaciones principales

```ascii
Admin SaaS (user.role=admin)
        |
        +--> Gyms ---< Members (users.role=member)
                 \
                  +--> Staff (users.role=staff)
                  +--> Memberships
                  +--> Attendances
                  +--> Backups
                  +--> Gym Profiles
                  +--> Fingerprints
                  +--> Reports Cache
