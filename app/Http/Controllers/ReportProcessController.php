<?php

namespace App\Http\Controllers;

use App\Models\ReportProcess;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Страница контроля выполнения процессов и скачивание готовых отчётов.
 */
class ReportProcessController extends Controller
{
    /** Список процессов формирования отчётов. */
    public function index(): View
    {
        $processes = ReportProcess::with('status')
            ->orderByDesc('rp_start_datetime')
            ->orderByDesc('rp_id')
            ->get();

        return view('reports.index', ['processes' => $processes]);
    }

    /**
     * Отдаёт файл отчёта по пути из rp_file_save_path.
     *
     * @throws NotFoundHttpException Если процесс не завершён успешно либо файл отсутствует на диске.
     */
    public function download(ReportProcess $process): StreamedResponse
    {
        if (! $process->hasReportFile()) {
            throw new NotFoundHttpException('Файл отчёта для этого процесса недоступен.');
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($process->rp_file_save_path)) {
            throw new NotFoundHttpException('Файл отчёта не найден на диске.');
        }

        return $disk->download($process->rp_file_save_path);
    }
}
