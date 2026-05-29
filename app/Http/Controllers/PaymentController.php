<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project_details;
use App\Models\Project_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
    }

    public function index()
    {
        $data['page_title'] = "Pembayaran";
        $data['page_subtitle'] = "";

        return view('pages/payment/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $filterTanggalStart = request('filterTanggalStart');
            $filterTanggalEnd = request('filterTanggalEnd');
            $filterJenis = request('filterJenis');
            $filterStatus = request('filterStatus');
            $data = Project_details::select('projects.*', 'project_details.rekening as payment_rekening', 'project_details.type as payment_typea', 'project_details.paid as payment_paid', 'project_details.content as content', "project_details.amount as payment_total", 'opd.name as opd_name', 'partners.name as partner_name')
                ->leftJoin('projects', 'project_details.id_project', '=', 'projects.id', 'left')
                ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
                ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left');
            $pengantar = $data->get()->where('payment_typea', 'pengantar')->values();

            if ($filterTanggalStart || $filterTanggalEnd) {
                $data->whereBetween('spk_date', [$filterTanggalStart ?? now()->format('Y-m-d'), $filterTanggalEnd ?? now()->format('Y-m-d')]);
            }

            if ($filterJenis) {
                $data->whereIn('projects.category', $filterJenis);
            }
            // dd($filterStatus);
            if ($filterStatus) {
                if (in_array('waiting', $filterStatus)) {
                    $data->where(['project_details.paid' => 0, 'content' => NULL]);
                }
                if (in_array('pending', $filterStatus)) {
                    if (count($filterStatus) > 1) $data->orWhere(['project_details.paid' => 0])->WhereNotNull('content');
                    else $data->where(['project_details.paid' => 0])->WhereNotNull('content');
                }
                if (in_array('paid', $filterStatus)) {
                    if (count($filterStatus) > 1) $data->orWhere('project_details.paid', 1);
                    else $data->where('project_details.paid', 1);
                }
            }

            $data = DB::table($data->having('payment_total', '>', 0))->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('payment_typea', function ($row) {
                    return "<div class='d-flex justify-content-between align-items-center'>
                    <div>
                        <div>$row->payment_typea</div>
                        <div class='font-weight-bold'>
                            Rp " . number_format($row->payment_total, 2, ',', '.') . "
                        </div>
                    </div>
                </div>";
                })
                ->editColumn('payment_paid', function ($row) use ($pengantar) {
                    $pengantar = $pengantar->where('id', $row->id)->values()[0];

                    if (($row->payment_type == 'paid' && $pengantar['content'] && !$row->payment_paid) || ($row->payment_type != 'paid' && !$row->payment_paid && $row->content)) {
                        return "<div class='text-center'><h6><div class='bg-warning rounded-circle py-2 px-3' style='margin: 10px 30px;'>
                            <i class='fa fa-exclamation'></i>
                        </div></h6></div>";
                    }

                    if ($row->payment_paid) {
                        return "<div class='text-center'><h5><span class='badge badge-success rounded-circle py-2 px-2'>
                            <i class='fa fa-check'></i>
                        </span></h5></div>";
                    }

                    if (!$row->payment_paid) {
                        return "<div class='text-center'><h4><span class='badge badge-danger rounded-circle px-2'>
                        <i class='fa fa-times'></i>
                    </span></h4></div>";
                    }

                    // return $html;
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $btn = "<button onclick='read($id)' class='edit btn btn-fw btn-inverse-info btn-sm'><i class='fa fa-eye mr-1'></i> Lihat</button>";
                    return $btn;
                })
                ->rawColumns(['action', 'payment_typea', 'payment_paid'])
                ->make(true);
        }

        return;
    }
}
