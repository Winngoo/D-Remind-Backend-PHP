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
        Schema::create('membership_details', function (Blueprint $table) {
            $table->id();
            $table->string('membership_name');
            $table->string('membership_benefits');
            $table->decimal('membership_fee', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->enum('validity',['year','month'])->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_details');
    }
};
