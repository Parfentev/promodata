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
        Schema::create('product', function (Blueprint $table): void {
            $table->id('product_id');
            $table->string('product_name');
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('manufacturer_id');

            $table->foreign('manufacturer_id')
                ->references('manufacturer_id')
                ->on('manufacturer')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Удаляет таблицу, если она существует.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
