<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

/**
 * Товар.
 *
 * @property int $product_id
 * @property string $product_name
 * @property int $category_id
 * @property int $manufacturer_id
 */
class Product extends Model
{
    protected $table      = 'product';
    protected $primaryKey = 'product_id';
    public    $timestamps = false;
    protected $fillable   = ['product_name', 'category_id', 'manufacturer_id'];

    /**
     * @return BelongsTo<Manufacturer, $this>
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id', 'manufacturer_id');
    }

    /**
     * @return HasMany<Price, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'product_id', 'product_id');
    }
}
