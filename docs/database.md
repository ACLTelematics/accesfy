# 📘 ACCESFY – Base de Datos (Documentación E‑R)

Esta documentación describe la estructura oficial del modelo entidad‑relación (E‑R) del sistema **ACCESFY**, incluyendo entidades, relaciones, llaves primarias, llaves foráneas y reglas internas de integridad.

---

## 🏛️ 1. SUPER_USERS

Usuarios globales capaces de administrar dueños de gimnasios.

**Campos**

* `id` (PK)
* `name`
* `email` (UNIQUE)
* `password`
* `active` (bool)
* `created_at`
* `updated_at`

**Relaciones**

* 1..* con **GYM_OWNERS**

---

## 🏢 2. GYM_OWNERS

Dueños de gimnasio registrados bajo un Super User.

**Campos**

* `id` (PK)
* `super_user_id` (FK → super_users.id)
* `name`
* `email` (UNIQUE)
* `password`
* `active` (bool)
* `activated_until` (date)
* `gym_name`
* `created_at`
* `updated_at`

**Relaciones**

* 1..* con **STAFF**
* 1..* con **CLIENTS**
* 1..* con **MEMBERSHIPS**
* 1..* con **PAYMENTS**
* 1..* con **BACKUPS**
* 1..* con **ACTIVITY_LOGS**
* 1..* con **SETTINGS**

---

## 👥 3. STAFF

Usuarios que operan dentro de cada gimnasio.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `name`
* `email` (UNIQUE)
* `password`
* `active` (bool)
* `created_at`
* `updated_at`

**Relaciones**

* 1..* con **ATTENDANCES**
* 1..* con **PAYMENTS**
* 1..* con **ACTIVITY_LOGS**

---

## 🧍‍♂️🧍‍♀️ 4. CLIENTS

Clientes registrados en cada gimnasio.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `name`
* `email`
* `phone`
* `active` (bool)
* `membership_expires`
* `membership_id` (FK)
* `is_couple` (bool)
* `related_client_id` (FK → clients.id, nullable)
* `created_at`
* `updated_at`

**Reglas especiales – Parejas**

* Si `is_couple = true` → `related_client_id` apunta al otro cliente.
* Ambos clientes deben referenciarse mutuamente.

**Relaciones**

* 1..* con **ATTENDANCES**
* 1..* con **PAYMENTS**

---

## 🎟️ 5. MEMBERSHIPS

Planes y tipos de membresía.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `type` (ENUM: individual, couple, student, custom, visit)
* `price` (decimal)
* `description`
* `daily_payment` (bool)
* `active` (bool)
* `created_at`
* `updated_at`

---

## 🕒 6. ATTENDANCES

Registro de entradas y salidas.

**Campos**

* `id` (PK)
* `client_id` (FK)
* `staff_id` (FK)
* `check_in` (datetime)
* `check_out` (datetime)
* `status`
* `created_at`
* `updated_at`

---

## 💰 7. PAYMENTS

Pagos realizados por clientes.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `client_id` (FK)
* `staff_id` (FK)
* `amount` (decimal)
* `method` (ENUM: cash, card, transfer, other)
* `note` (text)
* `created_at`
* `updated_at`

---

## 🗄️ 8. BACKUPS

Respaldos del sistema por dueño de gimnasio.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `created_by` (FK → super_users.id o staff.id)
* `file_path`
* `restorable` (bool)
* `created_at`
* `updated_at`

---

## 📋 9. ACTIVITY_LOGS

Registro de auditoría.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `user_id` (FK)
* `action`
* `description`
* `created_at`

---

## ⚙️ 10. SETTINGS

Configuración por gimnasio.

**Campos**

* `id` (PK)
* `gym_owner_id` (FK)
* `key`
* `value`

---

## ✅ Notas finales

* El modelo está optimizado para multitenancy por **gym_owner**.
* Cada entidad mantiene trazabilidad mediante logs y backups.
* La autenticación se gestiona internamente sin necesidad de emails de recuperación.


