<?php

namespace App\Exceptions;

/**
 * Для указанной категории (и производителя) нет товаров с ценами за период.
 */
class ReportDataNotFoundException extends ReportGenerationException {}
