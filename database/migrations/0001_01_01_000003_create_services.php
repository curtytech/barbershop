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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->string('image')->nullable();
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->json('days_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->date('specific_date')->nullable();
            $table->integer('duration')->default(30);
            $table->enum('type', ['available', 'break', 'lunch'])->default('available');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'day_of_week']);
            $table->index(['employee_id', 'specific_date']);
            $table->unique(['employee_id', 'day_of_week', 'start_time'], 'unique_employee_day_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};