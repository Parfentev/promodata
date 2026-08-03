<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Справочник статусов процесса.
 *
 * Класс назван ProcessStatusModel, чтобы не конфликтовать
 * с перечислением {@see \App\Enums\ProcessStatus}.
 *
 * @property int $ps_id
 * @property string $ps_name
 */
class ProcessStatusModel extends Model
{
    protected $table      = 'process_status';
    protected $primaryKey = 'ps_id';
    public    $timestamps = false;
    protected $fillable   = ['ps_name'];

    /**
     * @return HasMany<ReportProcess, $this>
     */
    public function processes(): HasMany
    {
        return $this->hasMany(ReportProcess::class, 'ps_id', 'ps_id');
    }
}
