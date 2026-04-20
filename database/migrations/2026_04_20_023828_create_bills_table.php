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
            $table->date('due_date');
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->year('start_year');
            $table->year('end_year');

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
