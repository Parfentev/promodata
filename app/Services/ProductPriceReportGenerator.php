<?php

namespace App\Services;

use App\Exceptions\{ReportDataNotFoundException, ReportGenerationException};
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{DB, Storage};

/**
 * Формирование CSV-отчёта по минимальным и максимальным ценам
 * товаров категории за последние 7 дней.
 */
class ProductPriceReportGenerator
{
    /** Каталог для отчётов внутри диска. */
    private const string REPORTS_DIRECTORY = 'reports';

    /** Разделитель полей CSV. */
    private const string CSV_DELIMITER = ';';

    /** Количество дней, за которые берутся цены. */
    private const int PERIOD_DAYS = 7;

    /** Заголовок таблицы отчёта. */
    private const array CSV_HEADER = ['manufacturer_name', 'product_name', 'price', 'price_date'];

    public function __construct(private readonly string $disk = 'local') {}

    /**
     * Формирует файл отчёта и возвращает его путь относительно диска.
     *
     * @param int $categoryId Идентификатор категории товаров.
     * @param int|null $manufacturerId Идентификатор производителя; null — все производители категории.
     * @param Carbon $startedAt Дата/время запуска команды, используется в имени файла.
     *
     * @return string Путь к файлу относительно диска, например reports/report_0_5_2026-08-01_12-00-00.csv.
     * @throws ReportDataNotFoundException Если в категории нет товаров с ценами за период.
     * @throws ReportGenerationException Если файл отчёта не удалось записать.
     */
    public function generate(int $categoryId, ?int $manufacturerId, Carbon $startedAt): string
    {
        $rows = $this->fetchRows($categoryId, $manufacturerId, $startedAt);

        if ($rows === []) {
            throw new ReportDataNotFoundException(
                $manufacturerId === null
                    ? "Для категории {$categoryId} не найдено товаров с ценами за последние ".self::PERIOD_DAYS.' дней.'
                    : "Для категории {$categoryId} и производителя {$manufacturerId} не найдено товаров с ценами за последние ".self::PERIOD_DAYS.' дней.'
            );
        }

        return $this->writeCsv($rows, $categoryId, $manufacturerId, $startedAt);
    }

    /**
     * Выбирает по две строки на товар — с минимальной и максимальной ценой за период.
     *
     * @return array<int, array{manufacturer_name: string, product_name: string, price: string, price_date: string}>
     */
    private function fetchRows(int $categoryId, ?int $manufacturerId, Carbon $startedAt): array
    {
        $periodEnd = $startedAt->copy()->startOfDay();
        $periodStart = $periodEnd->copy()->subDays(self::PERIOD_DAYS - 1);

        $ranked = DB::table('price as pr')
            ->join('product as p', 'p.product_id', '=', 'pr.product_id')
            ->join('manufacturer as m', 'm.manufacturer_id', '=', 'p.manufacturer_id')
            ->where('p.category_id', $categoryId)
            ->whereBetween('pr.price_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->when($manufacturerId !== null, fn (Builder $query): Builder => $query->where('p.manufacturer_id', $manufacturerId))
            ->select([
                'p.product_id',
                'm.manufacturer_name',
                'p.product_name',
                'pr.price',
                'pr.price_date',
                DB::raw('row_number() over (partition by p.product_id order by pr.price asc, pr.price_date desc) as rn_min'),
                DB::raw('row_number() over (partition by p.product_id order by pr.price desc, pr.price_date desc) as rn_max'),
            ]);

        $records = DB::query()
            ->fromSub($ranked, 't')
            ->where(fn (Builder $query): Builder => $query->where('rn_min', 1)->orWhere('rn_max', 1))
            ->orderBy('product_id')
            ->orderBy('price')
            ->get();

        $rows = [];

        foreach ($records as $record) {
            $row = [
                'manufacturer_name' => $record->manufacturer_name,
                'product_name' => $record->product_name,
                'price' => number_format((float) $record->price, 2, '.', ''),
                'price_date' => Carbon::parse($record->price_date)->toDateString(),
            ];

            // Если у товара всего одна цена за период, она является и минимальной,
            // и максимальной — по ТЗ строк всё равно должно быть две.
            $rows[] = $row;

            if ((int) $record->rn_min === 1 && (int) $record->rn_max === 1) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Записывает строки в CSV-файл.
     *
     * @param array<int, array<string, string>> $rows
     *
     * @return string Путь к файлу относительно диска.
     * @throws ReportGenerationException Если файл не удалось создать или записать.
     */
    private function writeCsv(array $rows, int $categoryId, ?int $manufacturerId, Carbon $startedAt): string
    {
        $disk = $this->disk();
        $relativePath = sprintf(
            '%s/report_%d_%d_%s.csv',
            self::REPORTS_DIRECTORY,
            $manufacturerId ?? 0,
            $categoryId,
            $startedAt->format('Y-m-d_H-i-s')
        );

        $disk->makeDirectory(self::REPORTS_DIRECTORY);
        $absolutePath = $disk->path($relativePath);

        $handle = @fopen($absolutePath, 'wb');

        if ($handle === false) {
            throw new ReportGenerationException("Не удалось создать файл отчёта: {$absolutePath}.");
        }

        try {
            // BOM, чтобы Excel корректно распознал кириллицу в UTF-8.
            if (fwrite($handle, "\xEF\xBB\xBF") === false) {
                throw new ReportGenerationException("Не удалось записать файл отчёта: {$absolutePath}.");
            }

            foreach ([self::CSV_HEADER, ...$rows] as $row) {
                if (fputcsv($handle, array_values($row), self::CSV_DELIMITER, '"', '\\') === false) {
                    throw new ReportGenerationException("Не удалось записать файл отчёта: {$absolutePath}.");
                }
            }
        } finally {
            fclose($handle);
        }

        return $relativePath;
    }

    private function disk(): Filesystem
    {
        return Storage::disk($this->disk);
    }
}
