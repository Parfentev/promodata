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
        Schema::create('report_process', function (Blueprint $table): void {
            $table->id('rp_id');
            $table->unsignedInteger('rp_pid');
            $table->dateTime('rp_start_datetime');
            $table->decimal('rp_exec_time', 10, 4)->nullable();
            $table->unsignedSmallInteger('ps_id');
            $table->string('rp_file_save_path')->nullable();

            $table->foreign('ps_id')
                ->references('ps_id')
                ->on('process_status')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index('rp_start_datetime');
            $table->index('ps_id');
        });
    }

    /**
     * Удаляет таблицу, если она существует.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_process');
    }
};
