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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_id')->constrained('membership_details')->onDelete('cascade');
            $table->timestamp('payment_date');
            $table->timestamp('end_date'); 
            $table->decimal('amount', 10, 2);
            $table->string('plan_type');
            $table->enum('status', ['completed', 'failed', 'cancelled', 'expired'])->default('completed');
            $table->string('stripe_payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};