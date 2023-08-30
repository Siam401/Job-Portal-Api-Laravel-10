<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormOption extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function getData(string $name, string $dataType = 'string')
    {
        $options = self::select('option_title', 'option_value')->where('name', $name)->get();
        $data = [];
        if ($options->count() > 0) {
            foreach ($options as $option) {
                $data[] = [
                    'text' => $option->option_title,
                    'value' => dataConvert($option->option_value, $dataType)
                ];
            }

        }
        return $data;
    }
}