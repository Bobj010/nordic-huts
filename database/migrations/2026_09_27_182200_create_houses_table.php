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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedSmallInteger('capacity');
            $table->unsignedSmallInteger('area');
            $table->string('image_url');
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for catalog filters and sorting (sql-pro standard)
            $table->index(['category_id', 'is_active'], 'idx_houses_cat_active');
            $table->index('price_per_night', 'idx_houses_price');
            $table->index('capacity', 'idx_houses_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
