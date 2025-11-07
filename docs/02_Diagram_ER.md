┌──────────────────────────┐
│       SUPER_USERS        │
├──────────────────────────┤
│ id (PK)                  │
│ name                     │
│ email (UNIQUE)           │
│ password                 │
│ active (bool)            │
│ created_at / updated_at  │
└────────────┬─────────────┘
             │ 1..*
             │
             ▼
┌──────────────────────────┐
│        GYM_OWNERS        │
├──────────────────────────┤
│ id (PK)                  │
│ super_user_id (FK)       │
│ name                     │
│ email (UNIQUE)           │
│ password                 │
│ active (bool)            │
│ activated_until (date)   │
│ gym_name                 │
│ created_at / updated_at  │
└────────────┬─────────────┘
      ┌──────┼────────┬───────────────┐
      │      │        │               │
      ▼      ▼        ▼               ▼

┌──────────────────────┐   ┌──────────────────────┐   ┌──────────────────────┐   ┌──────────────────────┐
│        STAFF         │   │       CLIENTS        │   │     MEMBERSHIPS      │   │       BACKUPS         │
├──────────────────────┤   ├──────────────────────┤   ├──────────────────────┤   ├──────────────────────┤
│ id (PK)             │   │ id (PK)             │   │ id (PK)              │   │ id (PK)              │
│ gym_owner_id (FK)   │   │ gym_owner_id (FK)   │   │ gym_owner_id (FK)    │   │ gym_owner_id (FK)    │
│ name                │   │ name                │   │ type (ENUM):         │   │ created_by (FK)      │
│ email (UNIQUE)      │   │ email               │   │  - individual        │   │ file_path            │
│ password            │   │ phone               │   │  - couple            │   │ restorable (bool)    │
│ active (bool)       │   │ active (bool)       │   │  - student           │   │ created_at           │
│ created_at /        │   │ membership_expires  │   │  - custom            │   │ updated_at           │
│ updated_at          │   │ membership_id (FK)  │   │  - visit             │   └──────────────────────┘
└──────────┬──────────┘   │ created_at          │   │ price (decimal)      │
           │              │ updated_at          │   │ description          │
           │              └────────────┬────────┘   │ daily_payment (bool) │
           │                           │            │ active (bool)        │
           │                           │            │ created_at /         │
           ▼                           ▼            │ updated_at           │
┌──────────────────────┐    ┌──────────────────────┐   └──────────┬─────────┘
│     ATTENDANCES      │    │       PAYMENTS       │              │
├──────────────────────┤    ├──────────────────────┤              │
│ id (PK)             │    │ id (PK)             │              │
│ client_id (FK)      │    │ gym_owner_id (FK)   │              │
│ staff_id (FK)       │    │ client_id (FK)      │              │
│ check_in (datetime) │    │ staff_id (FK)       │              │
│ check_out (datetime)│    │ amount (decimal)    │              │
│ status              │    │ method (ENUM):      │              │
│ created_at          │    │  - cash             │              │
│ updated_at          │    │  - card             │              │
└──────────────────────┘    │  - transfer         │              │
                            │  - other            │              │
                            │ note (text)         │              │
                            │ created_at /        │              │
                            │ updated_at          │              │
                            └──────────┬──────────┘              │
                                       │                         │
                                       ▼                         ▼
                      ┌────────────────────────────┐    ┌────────────────────────┐
                      │      ACTIVITY_LOGS         │    │        SETTINGS        │
                      ├────────────────────────────┤    ├────────────────────────┤
                      │ id (PK)                   │    │ id (PK)               │
                      │ gym_owner_id (FK)         │    │ gym_owner_id (FK)     │
                      │ user_id (FK)              │    │ key                   │
                      │ action                    │    │ value                 │
                      │ description               │    │ created_at / updated_at│
                      │ created_at / updated_at   │    └────────────────────────┘
                      └────────────────────────────┘


─────────────────────────────────────────────────────────────────────────────
                     ACCESFY RELATION SUMMARY
─────────────────────────────────────────────────────────────────────────────
SUPER_USERS ──< GYM_OWNERS ──< STAFF
                         │
                         ├──< CLIENTS ──< ATTENDANCES
                         │         │
                         │         └──< PAYMENTS
                         │
                         ├──< MEMBERSHIPS
                         │
                         ├──< BACKUPS
                         │
                         ├──< ACTIVITY_LOGS
                         │
                         └──< SETTINGS

─────────────────────────────────────────────────────────────────────────────
NOTES:
- Added membership type: 'visit'
- Added field: daily_payment (bool)
- Each gym owner operates under their subdomain (e.g., gym1.accesfy.com)
─────────────────────────────────────────────────────────────────────────────

