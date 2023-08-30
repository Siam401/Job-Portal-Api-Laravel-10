<?php

namespace Modules\Frontend\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{

    protected $fillable = ['title', 'url', 'icon_image', 'is_active', 'serial'];

}