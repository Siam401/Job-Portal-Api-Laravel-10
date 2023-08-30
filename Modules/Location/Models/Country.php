<?php

namespace Modules\Location\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    protected $fillable = [];
    public static function nationalityOptions()
    {
        $data = self::select('nationality as text')->where('nationality', '<>', '')->get();

        return $data->map(function ($item) {
            $item->value = $item->text;
            return $item->toArray();
        })->toArray();
    }

}