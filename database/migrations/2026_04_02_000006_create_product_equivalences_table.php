<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_equivalences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equivalent_product_id')->constrained('products')->cascadeOnDelete();
            $table->string('relation_type')->default('equivalent');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'equivalent_product_id'], 'product_equivalence_unique');
            $table->index('product_id');
            $table->index('equivalent_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_equivalences');
    }
};
