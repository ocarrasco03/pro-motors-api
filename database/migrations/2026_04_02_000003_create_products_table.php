<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('alternative_sku')->nullable();
            $table->string('application')->nullable();
            $table->string('ean')->nullable();
            $table->string('sat_code')->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['sku', 'brand_id', 'description'], 'products_sku_brand_description_unique');
            $table->index('sku');
            $table->index('is_active');
            $table->index(['sku', 'brand_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
