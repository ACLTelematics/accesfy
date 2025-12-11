<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_user_id')
                  ->constrained('super_users')
                  ->onDelete('cascade');

            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');

            $table->boolean('active')->default(true);
            $table->date('activated_until')->nullable();

            $table->string('gym_name', 150);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_owners');
    }
};
