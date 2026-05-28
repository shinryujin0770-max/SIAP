<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Opd;
use App\Models\Partner;
use App\Models\Project_details;
use App\Models\Project;
use App\Models\Project_checklists;
use App\Models\User;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    protected $paymentService;

    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
        $this->paymentService = new PaymentService();
    }

    public function index()
    {
        $data['page_title'] = "Paket Pekerjaan Konstruksi";
        $data['page_subtitle'] = "";

        return view('pages/project/browse', $data);
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

            if (User::find(Auth::id())->hasRole('admin_opd')) {
                $data = $data->where('id_kpa', Auth::user()->id);
            }

            if ($filterStart || $filterEnd) {
                $data->whereBetween('spk_date', [$filterStart ?? now()->format('Y-m-d'), $filterEnd ?? now()->format('Y-m-d')]);
            }

            if ($filterJenis) {
                $data->whereIn('projects.category', $filterJenis);
            }

            $data = DB::table($data)->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return "<input type='checkbox' onchange='chooseProject(this, $row->id)' class='project-checkbox' style='transform: scale(2); margin: 10px;'>";
                })
                ->editColumn('spk_date', function ($row) {
                    return Carbon::parse($row->spk_date)->format('d F Y');
                })
                ->editColumn('category', function ($row) {
                    return strtoupper($row->category);
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $btn = "<button onclick='read($id)' class='edit btn btn-fw btn-inverse-info btn-sm'><i class='fa fa-eye mr-1'></i> Lihat</button>";
                    return $btn;
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }

        return;
    }

    public function read($id)
    {
        $data['page_title'] = "Paket Pekerjaan Konstruksi";
        $data['page_subtitle'] = "Detail Paket Pekerjaan";
        $data['project'] = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name', 'partners.address as partner_address', 'partners.director as partner_director', 'kecamatan.nama as kecamatan_name', 'kelurahan.nama as kelurahan_name')->orderBy('id', 'desc')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
            ->leftJoin('kecamatan', 'projects.id_kecamatan', '=', 'kecamatan.id', 'left')
            ->leftJoin('kelurahan', 'projects.id_kelurahan', '=', 'kelurahan.id', 'left')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left')->where('projects.id', $id)->first();
        if (User::find(Auth::id())->hasRole('admin_opd') && $data['project']['id_opd'] != Auth::user()->id_opd) {
            abort(404);
        }

        $data['project_details'] = Project_details::select('project_details.*', 'project_checklists.id as id_checklist')
            ->leftJoin('project_checklists', 'project_details.id', '=', 'id_detail')
            ->where('id_project', $id)->get();
        $data['already_checklist'] = count($data['project_details']->whereNotNull('id_checklist'));
        $data['project_payments'] = $data['project_details']->where('amount', '>', 0)->groupBy('rekening');
        $data['totalPaid'] = $data['project_details']->where('amount', '>', 0)->whereNotNull('content')->sum('amount');
        $data['ringkasan_kontrak'] = $data['project_details']->where('type', 'ringkasan_kontrak')->values();
        $data['pengantar'] = $data['project_details']->where('type', 'pengantar')->values();

        return view('pages/project/read', $data);
    }

    public function modalAdd()
    {
        $data['page_title'] = "Paket Pekerjaan Konstruksi";
        $data['page_subtitle'] = "Tambah Paket Pekerjaan";

        return view('pages/project/modals/modal-add', $data);
    }

    public function add(Request $request)
    {
        if (!$category = $request->category) {
            return redirect('project');
        }

        $data['category'] = $category;
        $data['page_title'] = "Paket Pekerjaan Konstruksi";
        $data['page_subtitle'] = "Tambah Paket Pekerjaan";
        $data['partners'] = Partner::all();
        $data['kecamatan'] = Kecamatan::all();
        $data['location'] = Opd::find(Auth::user()->id_opd);

        return view('pages/project/add', $data);
    }

    private function projectDetails($category)
    {
        if ($category == 'konstruksi') {
            return [
                'laporan_selesai',
                'berita_acara',
                'bast',
                'ringkasan_kontrak',
                'pengantar',
            ];
        } else {
            return [
                'berita_acara',
                'bast',
                'ringkasan_kontrak',
                'pengantar',
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make(array_merge($attr['project'], $attr['partner'], $attr), [
            // 'type' => 'required',
            'name' => 'required',
            'spk_date' => 'required',
            'spk_number' => 'required',
            'spk_amount' => 'required',
            'hps' => 'required',
            'pagu_anggaran' => 'required',
            'cv_name' => 'required',
            'address' => 'required',
            'director' => 'required',
            'spk_start' => 'required',
            'spk_end' => 'required',
            // 'adendum_days' => 'required',
            // 'adendum_start' => 'required',
            // 'adendum_end' => 'required',
            'payment_type' => 'required',
            // 'amount' => 'required',
            'lokasi' => 'required',
            'uraian' => 'required',
        ], [
            'required' => ':attribute harus diisi.',
        ]);

        if ($validator->fails()) {
            $errors = [];
            $i = 0;
            foreach ($validator->getMessageBag()->toArray() as $key => $value) {
                $errors[$i]['code'] = $key;
                $errors[$i]['message'] = $value[0];
                $i++;
            }
            return Response::json(array(
                'success' => false,
                'errors' => $errors
            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $data = $attr['project'];
        $overtime = Carbon::now()->greaterThan(Carbon::parse($data['spk_date'])->addDays(env('OVERTIME_DAYS')));
        if ($overtime && !$attr['password']) {
            return 'overtime';
        }

        if ($overtime && $attr['password'] && $attr['password'] != env('OVERTIME_PASSWORD')) {
            return 'password';
        }

        $partner = $attr['partner'];
        $payment = $attr['payment'];
        $partner['name'] = $partner['cv_name'];
        $data['name_partner'] = $partner['name'];
        $data['address_partner'] = $partner['address'];
        $data['director_partner'] = $partner['director'];
        unset($partner['cv_name']);

        $data['spk_days'] = Carbon::parse($data['spk_start'])->diffInDays($data['spk_end']);
        $data['id_opd'] = Auth::user()->id_opd;
        $data['id_kpa'] = Auth::id();
        $data['pagu_anggaran'] = str_replace(['.', ','], ['', '.'], $data['pagu_anggaran']);
        $data['hps'] = str_replace(['.', ','], ['', '.'], $data['hps']);
        $data['spk_amount'] = str_replace(['.', ','], ['', '.'], $data['spk_amount']);

        if ($data['hps'] > $data['pagu_anggaran']) {
            return Response::json(array(
                'success' => false,
                'errors' => [
                    [
                        'code' => 'hps',
                        'message' => 'HPS tidak boleh lebih besar dari Pagu Anggaran',
                    ]
                ]
            ), 400); // 400 being the HTTP code for an invalid request.
        }

        DB::beginTransaction();
        try {
            $findPartner = Partner::where('id', $partner['id'])->first();
            if ($findPartner) {
                $data['id_partner'] = $findPartner['id'];
                // $partner['name'] = $partner['name'] != $findPartner['name'] ? $partner['name'] : $findPartner['name'];
                // $partner['address'] = $partner['address'] != $findPartner['address'] ? $partner['address'] : $findPartner['address'];
                // $partner['director'] = $partner['director'] != $findPartner['director'] ? $partner['director'] : $findPartner['director'];

                // $storePartner = Partner::where('id', $partner['id'])->update($partner);
            } else {
                $storePartner = Partner::create($partner);
                $data['id_partner'] = $storePartner['id'];
            }

            $data['created_by'] = Auth::user()->name;
            $data['user_sk_number'] = Auth::user()->sk_number;
            $data['user_sk_date'] = Auth::user()->sk_date;
            $store = Project::create($data);

            $projectDetails = $this->projectDetails($data['category']);

            $totalPayment = 0;
            foreach ($payment as $key => $rekening) {
                foreach ($projectDetails as $d) {
                    Project_details::create(['id_project' => $store->id, 'type' => $d, 'rekening' => $key]);
                }

                foreach ($rekening as $p) {
                    $p['amount'] = str_replace(['.', ','], ['', '.'], $p['amount']);
                    $totalPayment += $p['amount'] ? $p['amount'] : 0;
                    Project_details::create([
                        'id_project' => $store->id,
                        'type' => $p['type'],
                        'rekening' => $key,
                        'amount' => $p['amount'] ? $p['amount'] : 0,
                        'paid' => false
                    ]);
                }
            }

            if (round((float)$totalPayment, 2) != round((float)$totalPayment, 2)) {
                DB::rollback();
                toastr()->error('Gagal Menambah');
                return 'false';
            }
            DB::commit();

            toastr()->success('Berhasil Menambah');
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error('Gagal Menambah');
            return 'false';
        }
    }

    public function edit($id)
    {
        $data['page_title'] = "Paket Pekerjaan Konstruksi";
        $data['page_subtitle'] = "Ubah Paket Pekerjaan";
        $data['project'] = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name')->orderBy('id', 'desc')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left')->where('projects.id', $id)->first();
        $data['project']['pagu_anggaran'] = number_format($data['project']['pagu_anggaran'], 2);
        $data['project']['hps'] = number_format($data['project']['hps'], 2);
        $data['project']['spk_amount'] = number_format($data['project']['spk_amount'], 2);

        $data['project_payments'] = Project_details::select('id', 'id_project', 'type', 'rekening', 'amount')->where('id_project', $id)->whereNotNull('amount')->get()->groupBy('rekening');
        $data['project_payments'] = $data['project_payments']->map(function ($item) {
            $item->map(function ($i) {
                $i['amount'] = number_format($i['amount'], 2);
                return $i;
            });
            return $item;
        });
        // dd(json_encode($data['project_payments']));
        $data['rekening'] = $data['project_payments']->values()->map(function ($item, $key) {
            return $item[0]['rekening'];
        });

        $data['category'] = $data['project']['category'];
        $data['partners'] = Partner::all();
        $data['kecamatan'] = Kecamatan::all();

        return view('pages/project/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make(array_merge($attr['project'], $attr['partner'], $attr), [
            // 'type' => 'required',
            'name' => 'required',
            // 'spk_date' => 'required',
            'spk_number' => 'required',
            'spk_amount' => 'required',
            'hps' => 'required',
            'pagu_anggaran' => 'required',
            'cv_name' => 'required',
            'address' => 'required',
            'director' => 'required',
            'spk_start' => 'required',
            'spk_end' => 'required',
            // 'adendum_days' => 'required',
            // 'adendum_start' => 'required',
            // 'adendum_end' => 'required',
            'payment_type' => 'required',
            // 'amount' => 'required',
            'lokasi' => 'required',
            'uraian' => 'required',
        ], [
            'required' => ':attribute harus diisi.',
        ]);

        if ($validator->fails()) {
            $errors = [];
            $i = 0;
            foreach ($validator->getMessageBag()->toArray() as $key => $value) {
                $errors[$i]['code'] = $key;
                $errors[$i]['message'] = $value[0];
                $i++;
            }
            return Response::json(array(
                'success' => false,
                'errors' => $errors
            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $id = $attr['id'];
        $data = $attr['project'];
        // $overtime = Carbon::now()->greaterThan(Carbon::parse($data['spk_date'])->addDays(env('OVERTIME_DAYS')));
        // if ($overtime && !$attr['password']) {
        //     return 'overtime';
        // }

        // if ($overtime && $attr['password'] && $attr['password'] != env('OVERTIME_PASSWORD')) {
        //     return 'password';
        // }

        $partner = $attr['partner'];
        $payment = $attr['payment'];
        $partner['name'] = $partner['cv_name'];
        $data['name_partner'] = $partner['name'];
        $data['address_partner'] = $partner['address'];
        $data['director_partner'] = $partner['director'];
        unset($partner['cv_name']);

        $data['spk_days'] = Carbon::parse($data['spk_start'])->diffInDays($data['spk_end']);
        $data['pagu_anggaran'] = str_replace(['.', ','], ['', '.'], $data['pagu_anggaran']);
        $data['hps'] = str_replace(['.', ','], ['', '.'], $data['hps']);
        $data['spk_amount'] = str_replace(['.', ','], ['', '.'], $data['spk_amount']);

        if ($data['hps'] > $data['pagu_anggaran']) {
            return Response::json(array(
                'success' => false,
                'errors' => [
                    [
                        'code' => 'hps',
                        'message' => 'HPS tidak boleh lebih besar dari Pagu Anggaran',
                    ]
                ]
            ), 400); // 400 being the HTTP code for an invalid request.
        }
        DB::beginTransaction();
        try {
            $findProject = Project::find($id);
            $findPartner = Partner::where('id', $partner['id'])->first();
            if ($findPartner) {
                $data['id_partner'] = $findPartner['id'];
                // $partner['name'] = $partner['name'] != $findPartner['name'] ? $partner['name'] : $findPartner['name'];
                // $partner['address'] = $partner['address'] != $findPartner['address'] ? $partner['address'] : $findPartner['address'];
                // $partner['director'] = $partner['director'] != $findPartner['director'] ? $partner['director'] : $findPartner['director'];

                // $storePartner = Partner::where('id', $partner['id'])->update($partner);
            } else {
                $storePartner = Partner::create($partner);
                $data['id_partner'] = $storePartner['id'];
            }

            $data['updated_by'] = Auth::user()->name;
            $findProject->update($data);

            $projectDetails = $this->projectDetails($data['category']);
            // Project_details::where('id_project', $id)->delete();

            $totalPayment = 0;
            foreach ($payment as $key => $rekening) {
                foreach ($projectDetails as $d) {
                    Project_details::updateOrCreate(['id_project' => $id, 'type' => $d, 'rekening' => $key], ['id_project' => $id, 'type' => $d, 'rekening' => $key]);
                }
                foreach ($rekening as $p) {
                    $p['amount'] = str_replace(['.', ','], ['', '.'], $p['amount']);
                    $totalPayment += $p['amount'] ? $p['amount'] : 0;
                    Project_details::updateOrCreate(
                        ['id_project' => $id, 'type' => $p['type'], 'rekening' => $key],
                        [
                            'id_project' => $id,
                            'type' => $p['type'],
                            'rekening' => $key,
                            'amount' => $p['amount'] ? $p['amount'] : 0,
                            'paid' => false
                        ]
                    );
                }
            }
            if (round((float)$totalPayment, 2) != round((float)$totalPayment, 2)) {
                DB::rollback();
                toastr()->error('Gagal Mengubah');
                return 'false';
            }
            DB::commit();

            toastr()->success('Berhasil Mengubah');
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error('Gagal Mengubah');
            return 'false';
        }
    }

    public function delete($id)
    {
        $store = Project::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return 'false';
        }

        toastr()->success('Berhasil Menghapus');
        return 'true';
    }

    public function modalDelete($ids)
    {
        $data['page_title'] = "Paket Pekerjaan Konstruksi";
        $data['page_subtitle'] = "Delete Paket Pekerjaan";
        $data['ids'] = $ids;

        return view('pages/project/modals/modal-delete', $data);
    }

    public function deleteMany($ids)
    {
        $store = Project::whereIn('id', explode(',', $ids))->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return 'false';
        }

        toastr()->success('Berhasil Menghapus');
        return 'true';
    }
}
