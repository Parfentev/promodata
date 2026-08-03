<?php

namespace App\Enums;

/**
 * Статусы процесса формирования отчёта.
 *
 * Значения соответствуют записям таблицы process_status.
 */
enum ProcessStatus: int
{
    /** Запуск. */
    case Started = 1;

    /** Завершен. */
    case Finished = 2;

    /** Ошибка. */
    case Failed = 3;
}
