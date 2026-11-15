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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedSmallInteger('guests_count');
            $table->decimal('total_price', 10, 2);
            $table->string('status', 50)->default('pending'); // pending, confirmed, completed, cancelled
            $table->string('guest_name');
            $table->string('guest_phone');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Indexes for fast querying by user, status, and house dates
            $table->index('user_id', 'idx_bookings_user_id');
            $table->index('status', 'idx_bookings_status');
            $table->index(['house_id', 'check_in_date', 'check_out_date'], 'idx_bookings_house_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
