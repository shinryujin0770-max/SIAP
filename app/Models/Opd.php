<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Opd extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'opd';
    public $guarded = [];

    /**
     * Get the financial realization ranking of an OPD for a given period
     *
     * @param int $opdId
     * @param string|int|null $month
     * @param string|int|null $year
     * @return array|null
     */
    public static function getRank($opdId, $month = null, $year = null)
    {
        if (!$opdId) {
            return null;
        }

        // If month or year is null, find the latest month/year with data in rfk_v2
        if (!$month || !$year) {
            $latest = \App\Models\RfkV2::join('rfk_v2_detail', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
                ->whereNull('rfk_v2_detail.parent_id')
                ->orderBy('rfk_v2.year', 'desc')
                ->orderBy('rfk_v2.month', 'desc')
                ->select('rfk_v2.month', 'rfk_v2.year')
                ->first();
            
            if ($latest) {
                $month = $latest->month;
                $year = $latest->year;
            } else {
                $month = date('m');
                $year = date('Y');
            }
        }

        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        $rankings = \DB::table('rfk_v2')
            ->join('rfk_v2_detail', 'rfk_v2_detail.id_rfk', '=', 'rfk_v2.id')
            ->whereNull('rfk_v2_detail.parent_id')
            ->where('rfk_v2.month', $month)
            ->where('rfk_v2.year', $year)
            ->orderBy('rfk_v2_detail.keuangan', 'desc')
            ->pluck('rfk_v2.id_opd')
            ->toArray();

        // Convert the plucked IDs to integers for comparison
        $rankings = array_map('intval', $rankings);
        $rank = array_search(intval($opdId), $rankings);

        if ($rank !== false) {
            return [
                'rank' => $rank + 1,
                'total' => count($rankings),
                'month' => $month,
                'year' => $year
            ];
        }

        return null;
    }
}
