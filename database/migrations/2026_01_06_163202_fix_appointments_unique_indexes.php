<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            // DROP pelo nome REAL do índice
            $table->dropUnique('unique_user_date_time');

            // Cria a regra correta
            $table->unique(
                ['employee_id', 'date', 'appointment_time'],
                'appointments_employee_date_time_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            $table->dropUnique('appointments_employee_date_time_unique');

            // Recria o índice antigo
            $table->unique(
                ['user_id', 'date', 'appointment_time'],
                'unique_user_date_time'
            );
        });
    }
};
    