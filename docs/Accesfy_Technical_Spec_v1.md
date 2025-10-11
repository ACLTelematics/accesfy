# 📘 Especificación Técnica Backend - ACCESFY SaaS

Versión: 1.0 
Framework: Laravel 11  
DB: MySQL 8  
Infraestructura: Hetzner Cloud + Namecheap

---

## 🧩 Arquitectura

```ascii
Frontend (React / Next.js)
     ↓ REST API
Backend (Laravel 11)
     ↓
Database (MySQL 8)
```

---

## 🗂️ Módulos Principales

### 1. Autenticación
- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `POST /api/auth/refresh`

Middleware: `auth:sanctum`  
Roles: Admin, Owner, Staff, Member

### 2. Gimnasios
- `POST /api/gyms/create` → Crea un nuevo gimnasio.
- `GET /api/gyms/list` → Lista todos los gimnasios (según permisos).
- `PATCH /api/gyms/suspend/{id}` → Suspende un gimnasio.

Modelo: `Gym`  
Relaciones: `hasMany(Member)`, `hasMany(Staff)`

### 3. Miembros
- `POST /api/members/create`
- `PATCH /api/members/update/{id}`
- `POST /api/members/checkin`

Modelo: `Member`  
Relaciones: `belongsTo(Gym)`

### 4. Backups
- `POST /api/backups/create` → Crear backup local o remoto.
- `GET /api/backups/list` → Listar backups del gimnasio.
- `POST /api/backups/restore/{id}` → Restaurar (solo owner o admin).

Modelo: `Backup`  
Campos: `gym_id`, `file_path`, `created_at`

---

## 💾 Estructura de Base de Datos

```ascii
[users]
- id
- name
- email
- password
- role (admin, owner, staff, member)

[gyms]
- id
- name
- owner_id
- plan_type
- status (active/suspended)

[members]
- id
- gym_id
- name
- membership_type
- checkins_count

[staff]
- id
- gym_id
- name
- role

[backups]
- id
- gym_id
- file_path
- created_at
```

---

## 🧰 Comandos Artisan útiles

```bash
php artisan make:model Gym -mcr
php artisan make:model Member -mcr
php artisan make:controller API/AuthController
php artisan migrate --seed
```

---

## 🔒 Políticas de seguridad
- Encriptación de contraseñas con `bcrypt()`  
- Tokens de sesión via `Sanctum`  
- Middleware `role:admin|owner`  
- Validaciones vía `FormRequest`  
- Backups cifrados (`storage/app/backups/`)

---

## ☁️ Infraestructura Hetzner + Namecheap

- VPS Ubuntu 24.04, 2vCPU, 4GB RAM  
- Nginx + PHP-FPM  
- Certbot SSL automatizado  
- Backup diario automático + local por gimnasio  
- Dominio en Namecheap apuntando a Hetzner IP

---

## 🧩 Integración futura
- Envío de correos vía Mailgun o SES  
- Pagos Stripe (planes SaaS)  
- API para app móvil (Flutter o React Native)

---

© 2025 ACCESFY — Documento técnico interno.
