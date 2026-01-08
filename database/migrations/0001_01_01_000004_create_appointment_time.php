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
        // Refatorado -----------------------------------
        // Schema::create('appointment_times', function (Blueprint $table) {
            // 
            // $table->id();
            // $table->unsignedBigInteger('user_id'); // Corrigido: deve ser unsignedBigInteger para corresponder ao id da tabela users
            // $table->time('start_time');
            // $table->time('end_time')->nullable();
            // $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            // $table->date('specific_date')->nullable();
            // $table->integer('duration')->default(30);
            // $table->enum('type', ['available', 'break', 'lunch'])->default('available');
            // $table->time('break_start')->nullable();
            // $table->time('break_end')->nullable();
            // $table->boolean('is_active')->default(true);
            // $table->text('notes')->nullable();
            // $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // $table->index(['user_id', 'day_of_week']);
            // $table->index(['user_id', 'specific_date']);
            // $table->unique(['user_id', 'day_of_week', 'start_time'], 'unique_user_day_time');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('appointment_times');
    }
};
