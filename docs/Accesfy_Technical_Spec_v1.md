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
