<?php

namespace App\Console\Commands;

use App\Enums\ProcessStatus;
use App\Exceptions\ReportDataNotFoundException;
use App\Models\ReportProcess;
use App\Services\ProductPriceReportGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Формирует CSV-отчёт по минимальным и максимальным ценам товаров категории
 * за последние 7 дней и ведёт журнал выполнения в таблице report_process.
 */
class GenerateProductPriceReport extends Command
{
    protected $signature = 'report:products
                            {category_id : Идентификатор категории товаров}
                            {--manufacturer= : Идентификатор производителя; по умолчанию — все производители категории}';

    protected $description = 'Формирует CSV-отчёт по минимальным и максимальным ценам товаров категории за последние 7 дней';

    /**
     * Обрабатывает генерацию отчёта по ценам товаров.
     *
     * @param ProductPriceReportGenerator $generator
     *
     * @return int
     */
    public function handle(ProductPriceReportGenerator $generator): int
    {
        $categoryId         = (int)$this->argument('category_id');
        $manufacturerOption = $this->option('manufacturer');
        $manufacturerId     = $manufacturerOption === null ? null : (int)$manufacturerOption;

        $startedAt          = Carbon::now();
        $startedAtMicrotime = microtime(true);

        $process = ReportProcess::create([
            'rp_pid'            => getmypid(),
            'rp_start_datetime' => $startedAt,
            'ps_id'             => ProcessStatus::Started
        ]);

        try {
            $path = $generator->generate($categoryId, $manufacturerId, $startedAt);
        } catch (ReportDataNotFoundException $e) {
            // Не фатальная ошибка: данных нет, в лог приложения не пишем.
            $this->failProcess($process, $startedAtMicrotime);
            $this->error($e->getMessage());

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->failProcess($process, $startedAtMicrotime);

            Log::error('Ошибка формирования отчёта по ценам товаров.', [
                'category_id'     => $categoryId,
                'manufacturer_id' => $manufacturerId,
                'rp_id'           => $process->rp_id,
                'exception'       => $e,
            ]);

            $this->error("Не удалось сформировать отчёт: {$e->getMessage()}");

            return self::FAILURE;
        }

        $process->update([
            'ps_id' => ProcessStatus::Finished,
            'rp_exec_time' => $this->execTime($startedAtMicrotime),
            'rp_file_save_path' => $path,
        ]);

        $this->info("Отчёт сформирован: $path");

        return self::SUCCESS;
    }

    /**
     * Переводит процесс в статус «Ошибка» и фиксирует время выполнения.
     *
     * @param ReportProcess $process
     * @param float $startedAtMicrotime
     */
    private function failProcess(ReportProcess $process, float $startedAtMicrotime): void
    {
        $process->update([
            'ps_id'        => ProcessStatus::Failed,
            'rp_exec_time' => $this->execTime($startedAtMicrotime)
        ]);
    }

    /**
     * Время выполнения процесса в секундах.
     *
     * @param float $startedAtMicrotime
     *
     * @return float
     */
    private function execTime(float $startedAtMicrotime): float
    {
        return round(microtime(true) - $startedAtMicrotime, 4);
    }
}
