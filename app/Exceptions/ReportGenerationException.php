<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Ошибка формирования отчёта: нет данных, либо файл не удалось записать.
 */
class ReportGenerationException extends RuntimeException {}
