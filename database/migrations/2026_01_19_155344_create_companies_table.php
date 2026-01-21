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
        Schema::create('company_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('email')->nullable()->index();
            $table->string('owner_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->foreignId('company_group_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained();
            $table->enum('license', ['individual', 'corporate'])->default('corporate')->index();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->index();
            $table->enum('billing_period', ['monthly', 'bimonthly', 'quarterly', 'annual', 'biannual'])->default('monthly')->index();
            $table->boolean('is_protected')->default(false);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('company_groups');
    }
};
