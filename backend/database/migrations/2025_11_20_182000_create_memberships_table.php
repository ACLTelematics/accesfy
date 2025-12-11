<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_owner_id')
                  ->constrained('gym_owners')
                  ->onDelete('cascade');

            $table->enum('type', ['individual', 'couple', 'student', 'custom', 'visit']);
            $table->decimal('price', 10, 2);

            $table->text('description')->nullable();

            $table->boolean('daily_payment')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
