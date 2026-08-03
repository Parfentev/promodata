<?php

namespace App\Models;

use App\Enums\ProcessStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Журнал выполнения процессов формирования отчёта.
 *
 * @property int $rp_id
 * @property int $rp_pid
 * @property Carbon $rp_start_datetime
 * @property string|null $rp_exec_time
 * @property ProcessStatus $ps_id
 * @property string|null $rp_file_save_path
 */
class ReportProcess extends Model
{
    protected $table      = 'report_process';
    protected $primaryKey = 'rp_id';
    public    $timestamps = false;
    protected $fillable   = [
        'rp_pid',
        'rp_start_datetime',
        'rp_exec_time',
        'ps_id',
        'rp_file_save_path',
    ];

    protected function casts(): array
    {
        return [
            'rp_start_datetime' => 'datetime',
            'rp_exec_time' => 'decimal:4',
            'ps_id' => ProcessStatus::class,
        ];
    }

    /**
     * @return BelongsTo<ProcessStatusModel, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProcessStatusModel::class, 'ps_id', 'ps_id');
    }

    /**
     * Признак того, что процесс завершился ошибкой.
     *
     * @return bool
     */
    public function hasFailed(): bool
    {
        return $this->ps_id === ProcessStatus::Failed;
    }

    /**
     * Признак того, что файл отчёта сформирован и доступен для скачивания.
     *
     * @return bool
     */
    public function hasReportFile(): bool
    {
        return $this->ps_id === ProcessStatus::Finished && $this->rp_file_save_path !== null;
    }
}
