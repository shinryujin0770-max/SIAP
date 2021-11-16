<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Project_payment extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'project_payments';
    public $guarded = [];
}
