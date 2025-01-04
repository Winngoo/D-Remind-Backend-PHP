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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title', 100);
            $table->string('category');
            $table->string('subcategory');
            $table->date('due_date');
            $table->string('time');
            $table->string('description', 100)->nullable();
            $table->string('provider', 50)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('payment_frequency', ['Monthly', 'Quarterly', 'Half-Yearly', 'Annually'])->nullable();
            $table->string('reminder_status', ['active', 'inactive'])->default('active');
            $table->enum('email_notification_status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamp('email10days')->nullable();
            $table->timestamp('email5days')->nullable();
            $table->timestamp('email3days')->nullable();
            $table->timestamp('email1day')->nullable();
            $table->timestamp('emailcurrentday')->nullable();
            $table->enum('sms_notification_status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamp('sms10days')->nullable();
            $table->timestamp('sms5days')->nullable();
            $table->timestamp('sms3days')->nullable();
            $table->timestamp('sms1day')->nullable();
            $table->timestamp('smscurrentday')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
