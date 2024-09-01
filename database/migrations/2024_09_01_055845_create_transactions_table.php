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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('listing_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('price_per_day')->default(0);
            $table->unsignedInteger('total_days')->default(0);
            $table->unsignedInteger('fee')->default(0);
            $table->unsignedInteger('total_price')->default(0);
            $table->enum('status', ['waiting', 'approved', 'canceled'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
