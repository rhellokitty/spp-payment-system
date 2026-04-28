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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('bill_id')->constrained('bills')->restrictOnDelete();

            $table->string('transaction_code')->unique();
            $table->string('gateway_reference')->nullable();
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method');
            $table->string('status')->default('pending');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expired_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
