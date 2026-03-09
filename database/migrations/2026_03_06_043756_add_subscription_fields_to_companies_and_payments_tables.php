<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->date('cutoff_date')->nullable()->after('billing_period');
            $table->integer('grace_period_days')->default(5)->after('cutoff_date');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cutoff_date', 'grace_period_days']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id', 'payment_date', 'period_start', 'period_end',
                'status', 'amount', 'payment_method',
            ]);
        });
    }
};
