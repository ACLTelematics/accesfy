<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_owner_id')
                  ->constrained('gym_owners')
                  ->onDelete('cascade');

            // Solo SuperUser y GymOwner pueden crear backups
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('super_users')
                  ->nullOnDelete();

            $table->string('file_path');
            $table->boolean('restorable')->default(true);

            // Control de aplicación de backup
            $table->boolean('is_applied')->default(false);

            $table->foreignId('applied_by')
                  ->nullable()
                  ->constrained('super_users')
                  ->nullOnDelete();

            $table->dateTime('applied_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};