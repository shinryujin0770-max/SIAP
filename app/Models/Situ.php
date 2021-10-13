<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Situ extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'situ';
    public $guarded = [];
}
