<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Corrigido: deve ser unsignedBigInteger para corresponder ao id da tabela users
            $table->foreign('user_id')->references('id')->on('users'); // Adiciona a chave estrangeira
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('barber');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
