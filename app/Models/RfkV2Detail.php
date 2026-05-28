<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class RfkV2Detail extends Model
{
    use LogsActivity;

    protected $table = 'rfk_v2_detail';
    protected $guarded = [];

    /**
     * Relationship to parent rfk_v2_detail
     */
    public function parent()
    {
        return $this->belongsTo(RfkV2Detail::class, 'parent_id');
    }

    /**
     * Relationship to children rfk_v2_detail
     */
    public function children()
    {
        return $this->hasMany(RfkV2Detail::class, 'parent_id');
    }

    /**
     * Relationship to rfk_v2
     */
    public function rfk()
    {
        return $this->belongsTo(RfkV2::class, 'id_rfk');
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get root records (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to load all children recursively
     */
    public function scopeWithAllChildren($query)
    {
        return $query->with('children.children.children');
    }

    /**
     * Get count of all descendants
     */
    public function getChildrenCountAttribute()
    {
        return $this->getDescendantsCount($this->id);
    }

    /**
     * Recursively count all descendants
     */
    private function getDescendantsCount($id)
    {
        $children = RfkV2Detail::where('parent_id', $id)->get();
        $count = $children->count();

        foreach ($children as $child) {
            $count += $this->getDescendantsCount($child->id);
        }

        return $count;
    }

    /**
     * Get aggregated financial data from all direct children
     */
    public function getAggregatedFinancials()
    {
        $children = $this->children;

        // Initialize aggregated values to 0
        $aggregated = [
            'pagu_anggaran' => 0,
            'bulan_lalu' => 0,
            'bulan_ini' => 0,
            'sd_bulan_ini' => 0,
            'sisa_anggaran' => 0,
            'angkas' => 0,
            'angkas_prosentase' => 0,
            'keuangan' => 0,
            'fisik' => 0,
            'fisik_rupiah' => 0,
        ];

        // If no children, return all zeros (parent should be 0 when no children)
        if ($children->isEmpty()) {
            return $aggregated;
        }

        // Sum values from all children
        foreach ($children as $child) {
            $aggregated['pagu_anggaran'] += $child->pagu_anggaran;
            $aggregated['bulan_lalu'] += $child->bulan_lalu;
            $aggregated['bulan_ini'] += $child->bulan_ini;
            $aggregated['sd_bulan_ini'] += $child->sd_bulan_ini;
            $aggregated['sisa_anggaran'] += $child->sisa_anggaran;
            $aggregated['angkas'] += $child->angkas;

            // Sum fisik_rupiah (this is the shadow/calculated value in rupiah)
            $aggregated['fisik_rupiah'] += $child->fisik_rupiah;
        }

        // Calculate fisik percentage from aggregated fisik_rupiah
        // Formula: (sum of fisik_rupiah / sum of pagu_anggaran) * 100
        if ($aggregated['pagu_anggaran'] > 0) {
            $aggregated['fisik'] = ($aggregated['fisik_rupiah'] / $aggregated['pagu_anggaran']) * 100;
            $aggregated['angkas_prosentase'] = ($aggregated['angkas'] / $aggregated['pagu_anggaran']) * 100;
            $aggregated['keuangan'] = ($aggregated['sd_bulan_ini'] / $aggregated['pagu_anggaran']) * 100;
        }

        return $aggregated;
    }

    /**
     * Check if this record has children
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Get the hierarchy breadcrumb
     */
    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $current = $this;

        while ($current) {
            array_unshift($breadcrumb, [
                'code' => $current->code,
                'uraian' => $current->uraian,
                'type' => $current->type,
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }
}

