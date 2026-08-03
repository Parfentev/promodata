<?php

use App\Http\Controllers\ReportProcessController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReportProcessController::class, 'index'])->name('processes.index');

Route::get('/processes/{process}/download', [ReportProcessController::class, 'download'])
    ->name('processes.download');
