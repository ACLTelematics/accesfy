<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_owner_id')
                  ->constrained('gym_owners')
                  ->onDelete('cascade');

            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade');

            $table->foreignId('staff_id')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'card', 'transfer', 'other']);

            // Nuevos campos para pagos diarios
            $table->enum('frequency', ['daily', 'monthly', 'visit'])->default('monthly');
            $table->date('payment_date');

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};