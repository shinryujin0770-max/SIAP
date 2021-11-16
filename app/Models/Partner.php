<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Partner extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'partners';
    public $guarded = [];
}
