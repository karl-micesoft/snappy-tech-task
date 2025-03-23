<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Store extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'open',
        'store_type',
        'max_delivery_distance',
    ];

    /**
     * @param numeric|numeric-string $latitude
     * @param numeric|numeric-string $longitude
     * @return Builder<static>
     */
    public static function queryWithDistance($latitude, $longitude): Builder
    {
        $point = "POINT($latitude,$longitude)";
        return self::query()->addSelect([
            'stores.*',
            DB::raw("ST_Distance_Sphere(POINT(stores.latitude, stores.longitude), $point) as `distance`")
        ]);
    }
}
