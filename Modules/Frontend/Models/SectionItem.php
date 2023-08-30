<?php

namespace Modules\Frontend\Models;

use App\Services\FileUpload\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionItem extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'items' => 'array',
    ];

    public function removeItem() {

        dd($this);
        $data = $this->items;
        if (isset($data['image_url']) && !empty($data['image_url'])) {
            FileUpload::remove($data['image']);
        }
        $this->delete();
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}