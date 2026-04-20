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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();

            $table->date('birth_date');
            $table->string('parent_name');
            $table->string('parent_phone_number');
            $table->text('address')->nullable();
            $table->enum('gender', ['female', 'male']);
            $table->enum('status', ['active', 'graduated', 'transferred', 'dropped_out'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
