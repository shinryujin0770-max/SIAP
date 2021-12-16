<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Project_details;
use App\Models\Project;
use App\Models\Project_payment;
use App\Services\PrintOutService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProjectDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
        $this->printOutService = new PrintOutService();
    }

    public function details($id)
    {
        $data['project_details'] = Project_details::where('id_project', $id)->get();

        return view("pages/project/details/browse", $data);
    }

    public function modalDetail($id, $id_detail)
    {
        $data['project_details'] = Project_details::where('id', $id_detail)->first();
        $type = $data['project_details']['type'];
        $data['contents'] = isset($data['project_details']['content']) ? json_decode($data['project_details']['content'], true) : [];
        $data['pekerjaan'] = isset($data['contents']['pekerjaan']) ? array_values($data['contents']['pekerjaan']) : [];

        return view("pages/project/details/$type", $data);
    }

    public function edit(Request $request, $id, $id_detail)
    {
        $attr = $request->all();
        // $validator = Validator::make(array_merge($attr['data']), [
        //     'nomor' => 'required',
        //     'tanggal' => 'required',
        // ], [
        //     'required' => ':attribute harus diisi.',
        // ]);
        // if ($validator->fails()) {
        //     $errors = [];
        //     $i = 0;
        //     foreach ($validator->getMessageBag()->toArray() as $key => $value) {
        //         $errors[$i]['code'] = $key;
        //         $errors[$i]['message'] = $value[0];
        //         $i++;
        //     }
        //     return Response::json(array(
        //         'success' => false,
        //         'errors' => $errors
        //     ), 400); // 400 being the HTTP code for an invalid request.
        // }

        $data = $attr['data'];

        if (isset($attr['pekerjaan'])) {
            $data['pekerjaan'] = $attr['pekerjaan'];
        }

        DB::beginTransaction();
        try {
            $find = Project_details::find($id_detail);
            $find->update([
                'content' => json_encode($data)
            ]);

            DB::commit();

            toastr()->success('Berhasil Menyimpan Dokumen');
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error('Gagal Menyimpan Dokumen');
            return 'false';
        }
    }

    public function print(Request $request, $id)
    {
        $data['project_detail'] = Project_details::select(
            'projects.*',
            'project_details.content',
            'project_details.rekening',
            'project_details.type',
            'opd.name as opd_nama',
            'opd.code as opd_code',
            'opd.address as opd_address',
            'partners.name as partner_name',
            'partners.address as partner_address',
            'partners.director as partner_director',
            'users.name as user_name',
            'users.nip as user_nip',
            'users.jabatan as user_jabatan',
            'users.sk_number as user_sk_number',
            'users.sk_date as user_sk_date',
        )
            ->leftJoin('projects', 'project_details.id_project', '=', 'projects.id')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id')
            ->leftJoin('users', 'projects.id_kpa', '=', 'users.id')
            ->where('project_details.id', $id)->first();
        if ($data['project_detail']['id_opd'] != Auth::user()['id_opd']) abort(404);

        $project_payments = Project_payment::where('rekening', $data['project_detail']['rekening'])->get();
        $data['project_payments']['data'] = $project_payments->keyBy('type');
        $data['project_payments']['total'] = $project_payments->sum('amount');

        $data['contents'] = isset($data['project_detail']['content']) ? json_decode($data['project_detail']['content'], true) : [];
        $data['pekerjaan'] = isset($data['contents']['pekerjaan']) ? array_values($data['contents']['pekerjaan']) : [];
        $type = $data['project_detail']['type'];
        $kategori = $data['project_detail']['category'];

        return $this->printOutService->getPrint($kategori, $type, $data);
    }
}
