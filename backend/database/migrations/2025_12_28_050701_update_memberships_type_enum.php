<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL requiere modificar el tipo ENUM directamente
        DB::statement("ALTER TABLE memberships DROP CONSTRAINT IF EXISTS memberships_type_check");

        DB::statement("ALTER TABLE memberships ADD CONSTRAINT memberships_type_check CHECK (type IN ('individual', 'couple', 'student', 'custom', 'visit', 'weekly', 'biweekly', 'quarterly', 'semiannual', 'annual'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE memberships DROP CONSTRAINT IF EXISTS memberships_type_check");

        DB::statement("ALTER TABLE memberships ADD CONSTRAINT memberships_type_check CHECK (type IN ('individual', 'couple', 'student', 'custom', 'visit'))");
    }
};
