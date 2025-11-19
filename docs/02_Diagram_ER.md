# ACCESFY – DIAGRAMA ENTIDAD-RELACIÓN (ER)

─────────────────────────────────────────────────────────────
                  SUPER_USERS
─────────────────────────────────────────────────────────────
id (PK)
name
email (UNIQUE)
password
active (bool)
created_at / updated_at
──────────────────────┬───────────────────────────────
                      │ 1..*
                      ▼

─────────────────────────────────────────────────────────────
                      GYM_OWNERS
─────────────────────────────────────────────────────────────
id (PK)
super_user_id (FK)
name
email (UNIQUE)
password
active (bool)
activated_until (date)
gym_name
created_at / updated_at
──────────────────────┬──────────────────────────────────────
          ┌───────────┼───────────────┬──────────────────────┐
          │           │               │                      │
          ▼           ▼               ▼                      ▼

─────────────────────────────────────────────────────────────
                        STAFF
─────────────────────────────────────────────────────────────
id (PK)
gym_owner_id (FK)
name
email (UNIQUE)
password
active (bool)
created_at / updated_at
──────────────────────┬──────────────────────────────────────

─────────────────────────────────────────────────────────────
                       CLIENTS
─────────────────────────────────────────────────────────────
id (PK)
gym_owner_id (FK)
name
email
phone
active (bool)
membership_expires
membership_id (FK)
created_at
updated_at
──────────────────────┬──────────────────────────────────────
                      │
                      ▼

─────────────────────────────────────────────────────────────
                     ATTENDANCES
─────────────────────────────────────────────────────────────
id (PK)
client_id (FK)
staff_id (FK)
check_in (datetime)
check_out (datetime)
status
created_at
updated_at
─────────────────────────────────────────────────────────────

─────────────────────────────────────────────────────────────
                     MEMBERSHIPS
─────────────────────────────────────────────────────────────
id (PK)
gym_owner_id (FK)
type (ENUM):
   - individual
   - couple
   - student
   - custom
   - visit
price (decimal)
description
daily_payment (bool)
active (bool)
created_at / updated_at
──────────────────────┬──────────────────────────────────────

─────────────────────────────────────────────────────────────
                      PAYMENTS
─────────────────────────────────────────────────────────────
id (PK)
gym_owner_id (FK)
client_id (FK)
staff_id (FK)
amount (decimal)
method (ENUM):
   - cash
   - card
   - transfer
   - other
note (text)
created_at / updated_at
──────────────────────┬──────────────────────────────────────

─────────────────────────────────────────────────────────────
                       BACKUPS
─────────────────────────────────────────────────────────────
id (PK)
gym_owner_id (FK)
created_by (FK)
file_path
restorable (bool)
created_at
updated_at
─────────────────────────────────────────────────────────────

─────────────────────────────────────────────────────────────
                   ACTIVITY_LOGS
─────────────────────────────────────────────────────────────
id (PK)
gym_owner_id (FK)
user_id (FK)
action
descri

