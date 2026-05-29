<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Partner extends Model implements HasMedia
{
    use HasMediaTrait, LogsActivity;

    protected $table = 'partners';
    public $guarded = [];
}
