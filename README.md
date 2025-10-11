# 🏋️‍♂️ ACCESFY GYM SaaS  
Sistema inteligente para control de accesos y membresías de gimnasios.  
Versión inicial (v1.2) — Arquitectura técnica y backend Laravel.

---

## 📘 Descripción General

**ACCESFY** es un sistema SaaS diseñado para administrar gimnasios de forma segura y modular.  
Permite controlar accesos mediante **huella digital o PIN**, gestionar **membresías, paquetes, usuarios y respaldos**.  
Cada gimnasio opera de forma independiente, pero gestionado globalmente por un **Super Usuario**.

---

## 📦 Entorno Técnico (Stack)

| Componente | Tecnología | Descripción |
|-------------|-------------|--------------|
| 🧠 Lenguaje backend | **PHP 8.3** | Lenguaje principal |
| ⚙️ Framework backend | **Laravel 11** | API REST, autenticación y lógica de negocio |
| 🗄️ Base de datos | **PostgreSQL 16** | Datos de usuarios, membresías y accesos |
| 🌐 Servidor web | **NGINX + Certbot (SSL)** | Proxy reverso + HTTPS |
| 🧰 SO / Hosting | **Ubuntu 24.04 LTS (VPS Hetzner)** | Entorno productivo |
| 🌍 DNS / Dominio | **Namecheap** | Dominio `accesfy.app` y subdominios |
| 💻 Frontend | **Blade / Vue.js** | Panel administrativo y dashboard |
| 🔐 Respaldos | **pg_dump diario** | Copias automáticas `/backups/accesfy.sql` |

---

## 🌍 Infraestructura

╔══════════════════════════════════════════════════════════════════╗
║ PROYECTO ACCESFY ║
║ Control de acceso y membresías ║
╚══════════════════════════════════════════════════════════════════╝

🌐 Namecheap
─────────────
Dominio: accesfy.app
DNS:

A → IP del VPS Hetzner

CNAME → www.accesfy.app

┌──────────────────────────────┐
│ VPS Hetzner (Ubuntu 24.04) │
│ IP Pública: xx.xx.xx.xx │
│ Firewall: 22, 80, 443 │
│ │
│ ┌────────────────────────┐ │
│ │ NGINX + Certbot (SSL) │ │
│ │ Laravel 11 (PHP 8.3) │ │
│ │ PostgreSQL 16 │ │
│ │ API REST HTTPS │ │
│ └────────────────────────┘ │
│ │
│ Backups diarios: /backups/ │
│ Script: pg_dump accesfy.sql │
└──────────────────────────────┘

## 🧱 Roles y Jerarquía
SUPER USER (máximo 3)
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
└── 4 intentos fallidos → bloqueo

## 💳 Tipos de Paquetes

| Tipo        | Descripción                         | Configurable por | Compartido |
|--------------|-------------------------------------|------------------|-------------|
| Individual   | Acceso único                        | Admin            | ❌ |
| Pareja       | Dos usuarios enlazados              | Admin / Staff    | ✅ (PIN) |
| Estudiante   | Descuento especial                  | Admin            | ❌ |

---

## 🗄️ Estructura de Base de Datos

---

## 💳 Tipos de Paquetes

| Tipo        | Descripción                         | Configurable por | Compartido |
|--------------|-------------------------------------|------------------|-------------|
| Individual   | Acceso único                        | Admin            | ❌ |
| Pareja       | Dos usuarios enlazados              | Admin / Staff    | ✅ (PIN) |
| Estudiante   | Descuento especial                  | Admin            | ❌ |

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

## ⚙️ API REST

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

## 🔐 Política de Seguridad

- Todos los accesos usan HTTPS con **certificados Let's Encrypt** (Certbot).  
- Las contraseñas se almacenan con **bcrypt** (Laravel Hash).  
- No se guardan huellas ni imágenes biométricas en la nube.  
- El sistema usa **roles y permisos** para cada acción (Laravel Gates/Policies).  

---

## 💾 Backups

| Rol | Crear Backup | Restaurar Backup |
|------|----------------|------------------|
| Super User | ✅ | ✅ |
| Administrador | ✅ | ✅ |
| Staff | ✅ | ❌ |

Backups automáticos con cron diario:  
```bash
0 3 * * * pg_dump accesfy > /backups/accesfy_$(date +%F).sql * * *


