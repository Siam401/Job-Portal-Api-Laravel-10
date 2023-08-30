<?php

namespace Modules\Frontend\Models;

use App\Services\FileUpload\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'extra' => 'array',
    ];

    /**
     * Get image url
     */
    public function imageUrl()
    {
        return FileUpload::getUrl($this->image);
    }

    public function sectionItems(): HasMany
    {
        return $this->hasMany(SectionItem::class);
    }
}