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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table -> text('name');
            $table -> text('slug');
            $table -> decimal('price');
            $table -> integer('stock');
            $table->enum('status', ['available', 'out_of_stock']) -> default('available');
            $table -> foreignId('category_id') -> constrained();
            $table -> timestamp('deleted_at') -> nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
