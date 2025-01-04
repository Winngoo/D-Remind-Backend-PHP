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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->string('full_name');
            $table->string('role_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number');
            $table->string('postcode');
            $table->string('country');
            $table->boolean('terms_agreed')->default(false);
            $table->enum('status',['active','inactive'])->default('inactive');
            $table->string('profile_picture')->nullable();
            $table->string('api_token', 100)->unique()->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
