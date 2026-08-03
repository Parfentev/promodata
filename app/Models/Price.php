<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Цена товара на конкретную дату.
 *
 * @property int $price_id
 * @property int $product_id
 * @property string $price
 * @property Carbon $price_date
 */
class Price extends Model
{
    protected $table      = 'price';
    protected $primaryKey = 'price_id';
    public    $timestamps = false;
    protected $fillable   = ['product_id', 'price', 'price_date'];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'price_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
