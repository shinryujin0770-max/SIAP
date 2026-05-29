<?php

namespace App\Http\Controllers;

use App\Exports\ProjectExport;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Project_checklists;
use App\Models\Project_details;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
    }

    public function index()
    {
        $data['page_title'] = "Laporan";
        $data['page_subtitle'] = "";

        return view('pages/report/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $filterStart = request('filterStart');
            $filterEnd = request('filterEnd');
            $filterJenis = request('filterJenis');
            $data = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name')->orderBy('id', 'desc')
                ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
                ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left');

            if ($filterStart || $filterEnd) {
                $data->whereBetween('spk_date', [$filterStart ?? now()->format('Y-m-d'), $filterEnd ?? now()->format('Y-m-d')]);
            }

            if ($filterJenis) {
                $data->whereIn('projects.category', $filterJenis);
            }
            $data = DB::table($data)->select('*');
            $data = DataTables::of($data)
                ->editColumn('name', function ($row) {
                    return "<a href='" . url("/project/$row->id") . "' class='text-center'>" . $row->name . "</a>";
                })
                ->editColumn('category', function ($row) {
                    return "<span class='badge badge-pill " . ($row->category == 'perencanaan' ? 'badge-success' : ($row->category == 'konstruksi' ? 'badge-warning' : 'badge-danger')) . " text-center text-capitalize'>" . $row->category . "</span>";
                })
                ->editColumn('pagu_anggaran', function ($row) {
                    return "<div class='text-center'>" . number_format($row->pagu_anggaran, 2, ',', '.') . "</div>";
                })
                ->editColumn('hps', function ($row) {
                    return "<div class='text-center'>" . number_format($row->hps, 2, ',', '.') . "</div>";
                })
                ->editColumn('spk_amount', function ($row) {
                    return number_format($row->spk_amount, 2, ',', '.');
                })
                ->editColumn('amount', function ($row) {
                    $payment = Project_details::select(DB::raw('SUM(project_details.amount) as payment_total'))
                        ->where(['id_project' => $row->id, 'paid' => true])
                        ->groupBy('id_project')
                        ->having('payment_total', '>', 0)->first();

                    return $payment && $payment->payment_total ? number_format($payment->payment_total, 2, ',', '.') : '';
                })
                // ->editColumn('id', function ($row) {
                //     $payment = Project_details::select(DB::raw('SUM(project_details.amount) as payment_total'))
                //         ->where(['id_project' => $row->id, 'paid' => true])
                //         ->groupBy('id_project')
                //         ->having('payment_total', '>', 0)->first();

                //     return $payment && $payment->payment_total ? number_format($payment->payment_total, 2, ',', '.') : '';
                // })
                ->editColumn('spk_date', function ($row) {
                    return "<div class='text-center'>" .  Carbon::parse($row->spk_start)->isoFormat('DD MMMM YYYY') . "<br> s/d <br>" . Carbon::parse($row->spk_end)->isoFormat('DD MMMM YYYY') . "</div>";
                })
                ->editColumn('spk_start', function ($row) {
                    return "<div class='text-center'>" .  Carbon::parse($row->spk_start)->isoFormat('DD MMMM YYYY') . "</div>";
                })
                ->editColumn('spk_end', function ($row) {
                    if ($row->adendum_end) return "<div class='text-center'>" .  Carbon::parse($row->adendum_end)->isoFormat('DD MMMM YYYY') . "</div>";
                    return "<div class='text-center'>" .  Carbon::parse($row->spk_end)->isoFormat('DD MMMM YYYY') . "</div>";
                });

            return $data->rawColumns(['name', 'pagu_anggaran', 'spk_date', 'spk_start', 'spk_end', 'hps', 'category'])->make(true);
        }

        return;
    }

    public function export(Request $request)
    {
        $params = $request->all(['start', 'end', 'jenis']);
        return Excel::download(new ProjectExport($params), 'pelaporan.xlsx');
    }
}
