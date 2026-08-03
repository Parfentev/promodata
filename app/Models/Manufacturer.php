<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Производитель товара.
 *
 * @property int $manufacturer_id
 * @property string $manufacturer_name
 */
class Manufacturer extends Model
{
    protected $table      = 'manufacturer';
    protected $primaryKey = 'manufacturer_id';
    public    $timestamps = false;
    protected $fillable   = ['manufacturer_name'];

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'manufacturer_id', 'manufacturer_id');
    }
}
