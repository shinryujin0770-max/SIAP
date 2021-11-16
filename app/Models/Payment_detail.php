<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Payment_detail extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'payment_detail';
    public $guarded = [];
}
