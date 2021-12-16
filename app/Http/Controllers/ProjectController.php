<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Partner;
use App\Models\Project_details;
use App\Models\Project;
use App\Models\Project_payment;
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
        $data['page_title'] = "APBD";
        $data['page_subtitle'] = "";

        return view('pages/project/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name')->orderBy('id', 'desc')
                ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
                ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row['id'];
                    $btn = "<button onclick='read($id)' class='edit btn btn-fw btn-inverse-info btn-sm'><i class='fa fa-eye mr-1'></i> Lihat</button>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return;
    }

    public function read($id)
    {
        $data['page_title'] = "APBD";
        $data['page_subtitle'] = "Detail Project";
        $data['project'] = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name', 'partners.address as partner_address', 'partners.director as partner_director')->orderBy('id', 'desc')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left')->where('projects.id', $id)->first();
        $data['project_payments'] = Project_payment::select('*', DB::raw('SUM(amount) as total'))->where('id_project', $id)->groupBy('id_project', 'type')->get();

        return view('pages/project/read', $data);
    }

    public function modalAdd()
    {
        $data['page_title'] = "APBD";
        $data['page_subtitle'] = "Tambah APBD";

        return view('pages/project/modals/modal-add', $data);
    }

    public function add(Request $request)
    {
        if (!$category = $request->category) {
            return redirect('project');
        }

        $data['category'] = $category;
        $data['page_title'] = "APBD";
        $data['page_subtitle'] = "Tambah APBD";
        $data['partners'] = Partner::all();
        $data['kecamatan'] = Kecamatan::all();

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
            'spk_days' => 'required',
            'spk_start' => 'required',
            'spk_end' => 'required',
            // 'adendum_days' => 'required',
            // 'adendum_start' => 'required',
            // 'adendum_end' => 'required',
            'payment_type' => 'required',
            // 'amount' => 'required',
            'lokasi' => 'required',
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
        $partner = $attr['partner'];
        $payment = $attr['payment'];
        $partner['name'] = $partner['cv_name'];
        unset($partner['cv_name']);

        DB::beginTransaction();
        try {
            $findPartner = Partner::where('id', $partner['id'])->first();
            if ($findPartner) {
                $data['id_partner'] = $findPartner['id'];
                $partner['name'] = $partner['name'] != $findPartner['name'] ? $partner['name'] : $findPartner['name'];
                $partner['address'] = $partner['address'] != $findPartner['address'] ? $partner['address'] : $findPartner['address'];
                $partner['director'] = $partner['director'] != $findPartner['director'] ? $partner['director'] : $findPartner['director'];

                $storePartner = Partner::where('id', $partner['id'])->update($partner);
            } else {
                $storePartner = Partner::create($partner);
                $data['id_partner'] = $storePartner['id'];
            }
            $data['id_opd'] = Auth::user()->id_opd;
            $data['id_kpa'] = Auth::id();
            $data['pagu_anggaran'] = str_replace('.', '', $data['pagu_anggaran']);
            $data['hps'] = str_replace('.', '', $data['hps']);
            $data['spk_amount'] = str_replace('.', '', $data['spk_amount']);

            $store = Project::create($data);

            $totalPayment = 0;
            foreach ($payment as $key => $rekening) {
                foreach ($rekening as $p) {
                    $p['amount'] = str_replace('.', '', $p['amount']);
                    $totalPayment += (int) $p['amount'];
                    Project_payment::create([
                        'rekening' => $key,
                        'id_project' => $store->id,
                        'type' => $p['type'],
                        'amount' => $p['amount'] ? $p['amount'] : 0,
                        'paid' => false
                    ]);
                }
                Project_details::create(['id_project' => $store->id, 'type' => 'ringkasan_kontrak', 'rekening' => $key]);
            }

            $projectDetails = $this->projectDetails($data['category']);

            foreach ($projectDetails as $d) {
                if ($d !== 'ringkasan_kontrak') {
                    Project_details::create(['id_project' => $store->id, 'type' => $d]);
                }
            }

            if ($totalPayment != $data['spk_amount']) {
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
        $data['page_title'] = "APBD";
        $data['page_subtitle'] = "Ubah APBD";
        $data['project'] = Project::select('projects.*', 'opd.name as opd_name', 'partners.name as partner_name')->orderBy('id', 'desc')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id', 'left')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id', 'left')->where('projects.id', $id)->first();
        $data['partners'] = Partner::all();

        return view('pages/project/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['project'];
        $partner = $attr['partner'];

        $findProject = Project::find($id);
        $findPartner = Partner::where('id', $partner['id'])->first();
        if ($findPartner) {
            $data['id_partner'] = $findPartner['id'];
            $partner['nama'] = $partner['nama'] != $findPartner['nama'] ? $partner['nama'] : $findPartner['nama'];
            $partner['alamat'] = $partner['alamat'] != $findPartner['alamat'] ? $partner['alamat'] : $findPartner['alamat'];
            $partner['tempat_lahir'] = $partner['tempat_lahir'] != $findPartner['tempat_lahir'] ? $partner['tempat_lahir'] : $findPartner['tempat_lahir'];
            $partner['tanggal_lahir'] = $partner['tanggal_lahir'] != $findPartner['tanggal_lahir'] ? $partner['tanggal_lahir'] : $findPartner['tanggal_lahir'];

            $storePartner = Partner::where('id', $partner['id'])->update($partner);
        } else {
            $storePartner = Partner::create($partner);
            $data['id_partner'] = $storePartner['id'];
        }
        $data['id_opd'] = Auth::user()->id_opd;
        $data['id_kpa'] = Auth::id();

        // dd($data);
        $store = $findProject->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return;
        }

        toastr()->success('Berhasil Mengubah');
        return 'true';
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "APBD";
        $data['page_subtitle'] = "Hapus APBD";
        $data['project'] = Project::findOrFail($id);

        return view('pages/project/delete', $data);
    }

    public function delete($id)
    {
        $store = Project::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('project');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('project');
    }
}
