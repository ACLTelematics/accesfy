<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_owner_id')
                  ->constrained('gym_owners')
                  ->onDelete('cascade');

            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
