<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Project_details;
use App\Models\Project;
use App\Models\Project_checklists;
use App\Models\User;
use App\Services\PrintOutService;
use App\Utils\NumberToWords;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectDetailController extends Controller
{
    protected $printOutService;

    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
        $this->printOutService = new PrintOutService();
    }

    public function details($id, $rekening)
    {
        $data['project'] = Project::find($id);
        $project_details = Project_details::where(['id_project' => $id, 'rekening' => $rekening])->get();
        $data['project_details'] = $project_details->groupBy('type');
        // $data['overtime'] = ($data['project']['category'] == 'konstruksi' ? !$data['project_details']['laporan_selesai'][0]['content'] : !$data['project_details']['berita_acara'][0]['content']) && Carbon::now()->greaterThan(Carbon::parse($data['project']['spk_date'])->addDays(env('OVERTIME_DAYS'))) && $data['project']['overtime_paid'] < 2;
        $data['overtime'] = false;
        $total = $project_details->sum('amount');
        $totalPaidRekening = $project_details->whereNotNull('content')->sum('amount');
        $data['limit'] = $data['project']['payment_type'] == 'paid' ? true : $totalPaidRekening >= ($total * 75 / 100);
        // dd($data['limit']);
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

    public function create(Request $request, $id, $type)
    {
        $attr = $request->all();
        $data = $attr['data'];

        if (isset($attr['pekerjaan'])) {
            $data['pekerjaan'] = $attr['pekerjaan'];
        }

        DB::beginTransaction();
        try {
            $store = Project_details::create(['id_project' => $id, 'type' => $type, 'content' => json_encode($data)]);

            DB::commit();

            toastr()->success('Berhasil Mengirim Pengantar');
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error('Gagal Mengirim Pengantar');
            return 'false';
        }
    }

    public function edit(Request $request, $id, $id_detail)
    {
        $attr = $request->all();
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

    public function overtime($id, Request $request)
    {
        if ($request['password'] != env('OVERTIME_PASSWORD')) {
            return 'false';
        }

        $find = Project::find($id);
        DB::beginTransaction();
        try {
            $find->update([
                'overtime_paid' => 2
            ]);

            DB::commit();
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
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
            'users.dasar_surat_putusan'
        )
            ->leftJoin('projects', 'project_details.id_project', '=', 'projects.id')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id')
            ->leftJoin('users', 'projects.id_kpa', '=', 'users.id')
            ->where('project_details.id', $id)->first();
        if ($data['project_detail']['id_opd'] != Auth::user()['id_opd'] && !User::find(Auth::id())->hasRole('superadmin')) abort(404);

        $project_payments = Project_details::where(['id_project' => $data['project_detail']['id'], 'rekening' => $data['project_detail']['rekening']])->where('amount', '>', 0)->get();
        $data['project_payments']['payments'] = $project_payments->where('type', '!=', 'Uang Muka')->where('type', '!=', 'Langsung 100%');
        $data['project_payments']['data'] = $project_payments->keyBy('type');
        $data['project_payments']['total'] = $project_payments->sum('amount');

        $data['contents'] = isset($data['project_detail']['content']) ? json_decode($data['project_detail']['content'], true) : [];
        $data['pekerjaan'] = isset($data['contents']['pekerjaan']) ? array_values($data['contents']['pekerjaan']) : [];
        $type = $data['project_detail']['type'];
        $kategori = $data['project_detail']['category'];

        $data['project_details'] = Project_details::where(['id_project' => $data['project_detail']['id']])->get()->groupBy('type');
        if (in_array($type, ['Uang Muka', 'Termin 1', 'Termin 2', 'Termin 3', 'Termin 4', 'Termin 5'])) $type = 'pengantar';
        return $this->printOutService->getPrint($kategori, $type, $data);
    }

    public function modalPengantar($id, $type, $rekening)
    {
        $type = str_replace('_', ' ', $type);
        if ($type == 'Langsung 100') $type = 'Langsung 100%';

        $data['project'] = Project::find($id);
        $data['type'] = $type;
        $data['project_details'] = Project_details::where(['id_project' => $id, 'type' => $type, 'rekening' => $rekening])->first();
        $data['contents'] = isset($data['project_details']['content']) ? json_decode($data['project_details']['content'], true) : [];
        $data['pekerjaan'] = isset($data['contents']['pekerjaan']) ? array_values($data['contents']['pekerjaan']) : [];

        return view("pages/project/details/pengantar", $data);
    }

    public function modalChecklist($id, $type, $rekening)
    {
        $type = str_replace('_', ' ', $type);
        // if ($type == 'Langsung 100') $type = 'Langsung 100%';
        $data['type'] = $type;

        $data['project'] = Project_details::where(['id' => $id])->first();
        $data['canPrintChecklist'] = $data['project']['contents'] ? true : false;
        $data['checklist'] = Project_checklists::select('project_checklists.*', 'project_checklists.id as nomor')
            ->leftJoin('project_details', 'project_checklists.id_detail', '=', 'project_details.id')
            ->where(['id_detail' => $id])->first();
        $data['contents'] = $data['checklist'] ? json_decode($data['checklist']->contents, true) : [];

        $last_no = Project_checklists::select('project_checklists.*', 'project_checklists.id as nomor')
            ->leftJoin('project_details', 'project_checklists.id_detail', '=', 'project_details.id')
            ->orderBy(DB::raw('project_checklists.id + 0'), 'desc')->take(1)
            ->whereYear('tanggal', '=', Carbon::now()->format('Y'))
            ->first();
        $last_no_surat = 'Belum ada checklist';
        if ($last_no) {
            $last_no_surat = "600/" . $last_no['nomor'] . "/20.02.02/" . Carbon::parse($last_no->tanggal)->format('Y');
        }
        $data['last_no'] = $last_no_surat;

        $data['project_details'] = Project_details::select('project_details.*', 'project_checklists.id as id_checklist')
            ->leftJoin('project_checklists', 'project_details.id', '=', 'id_detail')
            ->where('id_project', $data['project']['id_project'])->get();
        $data['pengantar'] = $data['project_details']->where('type', 'pengantar')->values();

        return view("pages/project/modals/modal-checklist", $data);
    }

    public function generateChecklist(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $detail = Project_details::where(['id' => $id])->first();
            $detail->update([
                'paid' => true,
            ]);
            $checklist = Project_checklists::select('project_checklists.*', 'project_checklists.id as nomor')->where(['id_detail' => $id])->first();

            $tahun = Carbon::parse($request['tanggal'])->isoFormat('YYYY');
            $cek_no = Project_checklists::select('project_checklists.*', 'project_checklists.id as nomor')
                ->where(['id' => $request['nomor']])
                ->whereYear('tanggal', $tahun)
                ->first();

            if (!$checklist) {
                if ($cek_no) {
                    DB::rollback();
                    return 'No Checklist Sudah digunakan';
                }
                $checklist = Project_checklists::create([
                    'id' => $request['nomor'],
                    'id_detail' => $id,
                    'tanggal' => $request['tanggal'],
                    'nama' => env('SIGNATOR_NAMA'),
                    'jabatan' => env('SIGNATOR_JABATAN'),
                    'nip' => env('SIGNATOR_NIP'),
                ]);
            } else {
                if ($cek_no['nomor'] != $request['nomor']) {
                    DB::rollback();
                    return 'No Checklist Sudah digunakan';
                }
                $checklist->update([
                    'id' => $request['nomor'],
                    'tanggal' => $request['tanggal'],
                ]);
            }
            DB::commit();

            toastr()->success('Berhasil Membuka Checklist');
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            toastr()->error('Gagal Membuka Checklist');
            return 'Gagal Membuka Checklist';
        }
    }

    public function printChecklist(Request $request, $id_detail, $type)
    {
        $type = str_replace('_', ' ', $type);
        if ($type == 'Langsung 100') $type = 'Langsung 100%';

        $data['project'] = Project_details::select(
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
        )
            ->leftJoin('projects', 'project_details.id_project', '=', 'projects.id')
            ->leftJoin('opd', 'projects.id_opd', '=', 'opd.id')
            ->leftJoin('partners', 'projects.id_partner', '=', 'partners.id')
            ->leftJoin('users', 'projects.id_kpa', '=', 'users.id')
            ->where('project_details.id', $id_detail)->first();

        $data['opd'] = Opd::find($data['project']['id_opd']);
        $data['contents'] = isset($data['project']['content']) ? json_decode($data['project']['content'], true) : [];
        $data['pekerjaan'] = isset($data['contents']['pekerjaan']) ? array_values($data['contents']['pekerjaan']) : [];

        if (!$data['contents']) {
            $data['pengantar'] = Project_details::select('project_details.*', 'project_checklists.id as id_checklist')
                ->leftJoin('project_checklists', 'project_details.id', '=', 'id_detail')
                ->where('id_project', $data['project']['id'])->where('type', 'pengantar')->first();
            $data['contents'] = isset($data['pengantar']['content']) ? json_decode($data['pengantar']['content'], true) : [];
        }
        $id = $data['project']['id'];
        if (User::find(Auth::id())->hasRole('admin_opd') && (!$data['project'] || $data['project']['id_opd'] != Auth::user()['id_opd'])) abort(404);

        $data['project_details'] = Project_details::where(['id_project' => $data['project']['id']])->get()->groupBy('type');
        $kategori = $data['project']['category'];

        $data['checklist'] = Project_checklists::select('project_checklists.*', 'project_checklists.id as nomor')
            ->where(['id_detail' => $id_detail])->first();
        $data['type'] = $type;

        $numberToWords = new NumberToWords();
        $project_payments = Project_details::select('*', DB::raw('SUM(amount) as total'))->where('project_details.id', $id_detail)->first();
        $data['amount_value'] = $project_payments['amount'];
        $data['amount_word'] = str_replace('  ', ' ', $numberToWords->ctw($project_payments['amount'], 2));
        if ($kategori == 'konstruksi' && $data['project']->payment_type != 'paid') {
            $kategori = 'konstruksi_2';
        }

        return $this->printOutService->getPrintChecklist($kategori, $data);
    }
}
