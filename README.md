# 🧩 ACCESFY Backend (SaaS para gimnasios)

Plataforma SaaS diseñada para gestionar múltiples gimnasios de forma independiente, con control de usuarios, membresías, asistencia, staff, y backups automáticos.

---

## 🚀 Tecnologías Principales
- **Lenguaje Backend:** PHP 8.3+  
- **Framework:** Laravel 11  
- **Base de Datos:** MySQL 8  
- **Autenticación:** Sanctum / Passport  
- **Infraestructura:** Hetzner (Servidor) + Namecheap (Dominio)  
- **Versionado:** GitHub  
- **Contenedores:** Docker (opcional)  

---

## 🧱 Arquitectura General

```ascii
+---------------------+       +-------------------+       +-------------------+
|  Frontend (React)  | <---> |  API Laravel REST | <---> |  MySQL Database   |
|  (Accesfy Web App) |       |  (Backend Server) |       | (Usuarios, Gyms)  |
+---------------------+       +-------------------+       +-------------------+
```

---

## 👥 Roles del Sistema

| Rol | Descripción | Permisos principales |
|------|--------------|---------------------|
| **Admin SaaS** | Superadministrador global | Crear, suspender gimnasios, manejar backups globales |
| **Dueño de Gimnasio** | Propietario del gimnasio | CRUD de miembros, staff, membresías, backups locales |
| **Staff** | Empleado del gimnasio | Check-in/out de clientes, no puede restaurar backups |
| **Miembro** | Cliente del gimnasio | Consultar su membresía, historial de asistencia |

---

## 💾 Paquetes disponibles

| Plan | Características |
|------|-----------------|
| **Individual** | 1 gimnasio, hasta 50 miembros |
| **Pareja** | 2 gimnasios, hasta 120 miembros |
| **Estudiantes** | 1 gimnasio pequeño, hasta 30 miembros |

Todos incluyen backups automáticos y manuales (solo restaurables por el dueño o admin).

---

## 🔗 Endpoints Principales (Resumen)

```ascii
/api/
├── auth/
│   ├── login
│   ├── register
│   ├── logout
│   └── refresh
├── gyms/
│   ├── create
│   ├── list
│   └── suspend/{id}
├── members/
│   ├── create
│   ├── update/{id}
│   └── checkin
├── backups/
│   ├── create
│   ├── list
│   └── restore/{id}
└── plans/
    └── list
```

---

## 🧰 Instalación (local con Laravel)

```bash
git clone https://github.com/tuusuario/accesfy-backend.git
cd accesfy-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

---

## 🌍 Infraestructura (Hetzner + Namecheap)

- **Namecheap:** Registro del dominio (ej. accesfy.com)  
- **Hetzner Cloud:** VPS Ubuntu 24.04 con stack LEMP  
- **Certbot (Let's Encrypt):** SSL automático  
- **Backups Hetzner:** Configurados diarios + locales (por gimnasio)

---

## 🧑‍💻 Contribuciones

1. Haz un fork del repositorio.  
2. Crea una rama (`feature/nueva-funcionalidad`).  
3. Envía un pull request.  

---

© 2025 ACCESFY SaaS
