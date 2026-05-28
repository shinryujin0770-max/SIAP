<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Project_checklists;
use App\Models\Project_payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProjectExport implements FromView
{
    protected $params;

    function __construct($params)
    {
        $this->params = $params;
    }

    public function view(): View
    {
        $data = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name')->orderBy('id', 'desc')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left');

        if ($this->params['start'] || $this->params['end']) {
            $data->whereBetween('spk_date', [$this->params['start'] ?? now()->format('Y-m-d'), $this->params['end'] ?? now()->format('Y-m-d')]);
        }

        if ($this->params['jenis']) {
            $data->whereIn('projects.category', explode(',', $this->params['jenis']));
        }
        return view('exports.projects', [
            'projects' => $data->get()->map(function ($item) {
                $item['payments'] = Project_checklists::select('project_checklists.*', DB::raw("(SELECT SUM(project_details.amount) as payment_total FROM project_details WHERE project_details.id = project_checklists.id_detail AND project_details.id_project = $item->id) as total"))
                    ->leftJoin('project_details', 'project_checklists.id_detail', '=', 'project_details.id', 'left')
                    ->where(['id_project' => $item->id])
                    ->orderBy('project_checklists.id', 'asc')
                    ->get();

                $item['jumlah'] = $item['payments']->sum(function ($item) {
                    return $item['total'];
                });

                return $item;
            }),
        ]);
    }
}
