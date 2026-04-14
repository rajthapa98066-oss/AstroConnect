<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->string('khalti_pidx')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->string('transaction_id')->nullable(); // From Khalti after verification
            $table->json('response_data')->nullable(); // Full response from Khalti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
