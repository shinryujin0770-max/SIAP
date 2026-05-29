<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project_details extends Model
{
    use LogsActivity;

    protected $table = 'project_details';
    public $guarded = [];
}
