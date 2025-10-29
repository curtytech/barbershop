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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('barber_id');
            $table->unsignedBigInteger('service_id');
            $table->string('client_name');
            $table->string('client_phone');
            $table->time('appointment_time');
            $table->date('date');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('barber_id')->references('id')->on('barbers');
            $table->foreign('service_id')->references('id')->on('services');
            $table->index(['user_id', 'date', 'appointment_time']);
            $table->index(['service_id', 'date', 'appointment_time']);
            $table->index(['status']);
            $table->unique(['user_id', 'date', 'appointment_time'], 'unique_user_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
