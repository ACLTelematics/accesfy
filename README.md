# 🏋️‍♂️ ACCESFY GYM SaaS  
Sistema inteligente para gestión de gimnasios — control de accesos, membresías, paquetes y roles administrativos.

---

## 📘 Descripción

ACCESFY es un sistema SaaS diseñado para administrar gimnasios de forma eficiente.  
Permite controlar accesos mediante **huella digital o PIN**, manejar **paquetes individuales, de pareja y estudiante**, y asignar permisos según el **rol del usuario** (super user, administrador, staff o miembro).

---

## 🧱 Arquitectura General
SUPER USER (máximo 2)
│
├── ADMINISTRADOR (1 por gimnasio)
│ ├── Crea staff
│ ├── Crea paquetes
│ ├── Gestiona membresías
│ ├── Hace y restaura backups
│ └── Enlaza / desenlaza parejas
│
├── STAFF
│ ├── Modifica clientes
│ ├── Reactiva mensualidades
│ ├── Enlaza / desenlaza parejas
│ ├── Hace backups (no restaura)
│ └── No puede crear usuarios
│
└── MIEMBROS
├── Acceden con huella o PIN
├── En pareja → comparten PIN
├── Desenlace → nuevo PIN
└── 4 intentos fallidos → bloqueo (recepción desbloquea)

## 💳 Tipos de Paquetes

| Tipo        | Descripción                         | Configurable por | Compartido |
|--------------|-------------------------------------|------------------|-------------|
| Individual   | Acceso único                        | Admin            | ❌          |
| Pareja       | Dos usuarios enlazados              | Admin / Staff    | ✅ (PIN)    |
| Estudiante   | Descuento especial                  | Admin            | ❌          |

---

## 🗄️ Estructura de Base de Datos
USERS

id (PK)

username

password

role (super, admin, staff, member)

gym_id (FK)

status (active/banned)

created_at

updated_at

GYMS

id (PK)

name

address

phone

PACKAGES

id (PK)

name

price

type (individual, pareja, estudiante)

duration_months

created_by (FK admin)

MEMBERSHIPS

id (PK)

user_id (FK)

package_id (FK)

start_date

end_date

status (active/expired/suspended)

ACCESS_CONTROL

id (PK)

member_id (FK)

access_type (huella, pin)

pin_code

failed_attempts

is_blocked

last_access

BACKUPS

id (PK)

gym_id (FK)

created_by (FK)

file_path

created_at

restored (bool)


---

## ⚙️ API Principal

| Método | Ruta | Descripción | Acceso |
|--------|-------|--------------|--------|
| POST | `/api/auth/login` | Inicia sesión | Todos |
| POST | `/api/auth/logout` | Cierra sesión | Todos |
| POST | `/api/users/create` | Crear usuario | Super / Admin |
| PUT | `/api/users/update/:id` | Actualizar usuario | Admin / Staff |
| DELETE | `/api/users/delete/:id` | Eliminar usuario | Admin |
| POST | `/api/packages/create` | Crear paquete | Admin |
| POST | `/api/packages/link-pair` | Enlazar pareja | Admin / Staff |
| POST | `/api/packages/unlink-pair` | Desenlazar pareja | Admin / Staff |
| PATCH | `/api/memberships/reactivate` | Reactivar membresía | Admin / Staff |
| PATCH | `/api/access/unlock/:id` | Desbloquear usuario | Staff |
| POST | `/api/backups/create` | Crear backup | Admin / Staff |
| POST | `/api/backups/restore` | Restaurar backup | Solo Admin |
| POST | `/api/gyms/create` | Crear gimnasio | Super User |

---

## 💾 Backup y Restauración

| Rol | Crear Backup | Restaurar Backup |
|------|----------------|------------------|
| Super User | ✅ | ✅ |
| Administrador | ✅ | ✅ |
| Staff | ✅ | ❌ |

---

## 🧠 Lógica de Acceso

- Si **huella no funciona**, se usa **PIN**.  
- En paquetes de pareja, ambos comparten el mismo PIN.  
- Al desenlazarse, se genera **nuevo PIN individual**.  
- Tras **4 intentos fallidos**, el miembro se bloquea.  
- El **staff desbloquea manualmente**.

---

## 🚀 Tecnologías Recomendadas

| Área | Tecnología |
|------|-------------|
| Backend | Node.js (Express o NestJS) |
| Base de datos | PostgreSQL / MySQL |
| ORM | Prisma o Sequelize |
| Autenticación | JWT |
| Versionado | Git / GitHub |
| Hosting | Render, Railway, AWS |

---

## 📦 Estructura del Proyecto (Backend)
accesfy-backend/
├── src/
│ ├── controllers/
│ ├── routes/
│ ├── models/
│ ├── middleware/
│ └── utils/
├── prisma/ (si usas Prisma)
├── .env
├── package.json
└── README.md

---

## 🧑‍💻 Instalación y Uso

```bash
# Clonar el repositorio
git clone https://github.com/tuusuario/accesfy-backend.git
cd accesfy-backend

# Instalar dependencias
npm install

# Crear archivo de entorno
cp .env.example .env

# Iniciar servidor
npm run dev
