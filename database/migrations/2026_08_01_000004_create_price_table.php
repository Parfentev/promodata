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
        Schema::create('price', function (Blueprint $table): void {
            $table->id('price_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 12, 2);
            $table->date('price_date');

            $table->foreign('product_id')
                ->references('product_id')
                ->on('product')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->index(['product_id', 'price_date']);
        });
    }

    /**
     * Удаляет таблицу, если она существует.
     */
    public function down(): void
    {
        Schema::dropIfExists('price');
    }
};
