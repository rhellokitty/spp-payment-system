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
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('teacher_id')->nullable()->constrained('teachers');

            $table->string('school_level');
            $table->string('name');
            $table->string('grade');
            $table->year('start_year');
            $table->year('end_year');

            $table->unique(['name', 'grade', 'start_year']);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
