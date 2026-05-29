<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class RfkV2 extends Model
{
    use LogsActivity;

    protected $table = 'rfk_v2';
    protected $guarded = [];

    /**
     * Relationship to OPD
     */
    public function opd()
    {
        return $this->belongsTo(Opd::class, 'id_opd');
    }

    /**
     * Relationship to RFK V2 details
     */
    public function details()
    {
        return $this->hasMany(RfkV2Detail::class, 'id_rfk');
    }

    /**
     * Get root level details (OPD level)
     */
    public function rootDetails()
    {
        return $this->hasMany(RfkV2Detail::class, 'id_rfk')
            ->whereNull('parent_id')
            ->where('type', 'opd');
    }

    /**
     * Scope to filter by OPD
     */
    public function scopeByOpd($query, $opd_id)
    {
        return $query->where('id_opd', $opd_id);
    }

    /**
     * Scope to filter by month and year
     */
    public function scopeByPeriod($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

