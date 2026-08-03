<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    /**
     * Создаёт таблицу с соответствующими столбцами и свойствами.
     */
    public function up(): void
    {
        Schema::create('process_status', function (Blueprint $table): void {
            $table->smallIncrements('ps_id');
            $table->string('ps_name', 50)->unique();
        });

        DB::table('process_status')->insert([
            ['ps_id' => 1, 'ps_name' => 'Запуск'],
            ['ps_id' => 2, 'ps_name' => 'Завершен'],
            ['ps_id' => 3, 'ps_name' => 'Ошибка'],
        ]);
    }

    /**
     * Удаляет таблицу, если она существует.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_status');
    }
};
