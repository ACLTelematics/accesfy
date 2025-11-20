# ACCESFY – DIAGRAMA E-R (ACTUALIZADO 2025)

 continuación se presenta la versión **actualizada y en formato Markdown** del modelo E‑R 
 para la base de datos de ACCESFY.

---

## 🧩 ENTIDADES Y ATRIBUTOS

### **1. SUPER_USERS**

* id (PK)
* name
* email (UNIQUE)
* password
* active (bool)
* created_at
* updated_at

### **2. GYM_OWNERS**

* id (PK)
* super_user_id (FK → super_users.id)
* name
* email (UNIQUE)
* password
* active (bool)
* activated_until (date)
* gym_name
* created_at
* updated_at

### **3. STAFF**

* id (PK)
* gym_owner_id (FK → gym_owners.id)
* name
* email (UNIQUE)
* password
* active (bool)
* created_at
* updated_at

### **4. CLIENTS**

* id (PK)
* gym_owner_id (FK → gym_owners.id)
* name
* email
* phone
* active (bool)
* membership_expires (datetime)
* membership_id (FK → memberships.id)
* is_couple (bool)
* related_client_id (FK → clients.id, nullable)
* created_at
* updated_at

### **5. MEMBERSHIPS**

* id (PK)
* gym_owner_id (FK → gym_owners.id)
* type (ENUM: individual, couple, student, custom, visit)
* price (decimal)
* description
* daily_payment (bool)
* active (bool)
* created_at
* updated_at

### **6. ATTENDANCES**

* id (PK)
* client_id (FK → clients.id)
* staff_id (FK → staff.id)
* check_in (datetime)
* check_out (datetime)
* status
* created_at
* updated_at

### **7. PAYMENTS**

* id (PK)
* gym_owner_id (FK → gym_owners.id)
* client_id (FK → clients.id)
* staff_id (FK → staff.id)
* amount (decimal)
* method (ENUM: cash, card, transfer, other)
* note (text)
* created_at
* updated_at

### **8. BACKUPS**

* id (PK)
* gym_owner_id (FK)
* created_by (FK → users or staff)
* file_path
* restorable (bool)
* created_at
* updated_at

### **9. ACTIVITY_LOGS**

* id (PK)
* gym_owner_id (FK → gym_owners.id)
* user_id (FK → super_users, gym_owners o staff)
* action
* description
* created_at

### **10. SETTINGS**

* id (PK)
* gym_owner_id (FK → gym_owners.id)
* key
* value

---

## 🔗 RELACIONES

* **super_user 1 ─── N gym_owners**
* **gym_owner 1 ─── N staff**
* **gym_owner 1 ─── N clients**
* **gym_owner 1 ─── N memberships**
* **client 1 ─── N attendances**
* **client 1 ─── N payments**
* **staff 1 ─── N attendances**
* **staff 1 ─── N payments**
* **membership 1 ─── N clients**
* **gym_owner 1 ─── N backups**
* **gym_owner 1 ─── N activity_logs**
* **gym_owner 1 ─── N settings**
* **clients (parejas)**: related_client_id → clients.id

---

Si quieres agregar el **diagrama visual**, actualizar nombres, agregar nuevas entidades o generar el archivo para subirlo a GitHub, solo dímelo.
