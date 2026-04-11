<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_list_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('currency', 3)->default('MXN');
            $table->decimal('cost', 12, 4)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->boolean('apply_discount')->default(true);
            $table->timestamps();

            $table->unique(['price_list_id', 'product_id', 'supplier_id'], 'price_list_product_supplier_unique');
            $table->index('price_list_id');
            $table->index('product_id');
            $table->index('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_list_products');
    }
};
