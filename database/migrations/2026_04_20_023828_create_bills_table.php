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
        Schema::create('bills', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('student_id')
                ->constrained('students')
                ->restrictOnDelete();
            $table->foreignUuid('payment_type_id')
                ->constrained('payment_types')
                ->restrictOnDelete();

            $table->decimal('amount', 10, 2);
            $table->decimal('amount_snapshot', 10, 2)->nullable();
            $table->string('payment_type_name_snapshot')->nullable();
            $table->unsignedTinyInteger('billing_month')->nullable();
            $table->year('billing_year')->nullable();
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('status')->default('pending');

            $table->unique(
                ['student_id', 'payment_type_id', 'billing_month', 'billing_year'],
                'bills_student_payment_period_unique'
            );

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
