<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создаёт таблицу с соответствующими столбцами и свойствами.
     */
    public function up(): void
    {
        Schema::create('manufacturer', function (Blueprint $table): void {
            $table->id('manufacturer_id');
            $table->string('manufacturer_name');
        });
    }

    /**
     * Удаляет таблицу, если она существует.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturer');
    }
};
