<?php

namespace App\Http\Controllers;

use App\Exports\LaporanRFK1;
use App\Exports\LaporanRFK2;
use App\Exports\LaporanRFK3;
use App\Exports\ProjectExport;
use App\Http\Controllers\Controller;
use App\Imports\RfkImport;
use App\Models\Opd;
use App\Models\Project;
use App\Models\Project_details;
use App\Models\Rfk;
use App\Models\Rfk_detail;
use App\Models\User;
use App\Services\RfkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class RfkController extends Controller
{
    protected $rfkService;

    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
        $this->rfkService = new RfkService();
    }

    public function index()
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "";
        $data['opds'] = Opd::where('rfk_code', '!=', '')->get();
        $data['id_opd'] = Auth::user()->id_opd;

        $now = Carbon::now(); // Current date and time
        $day15 = Carbon::create($now->year, $now->month, env('OVERTIME_RFK_DATE'), 23, 59, 59); // 15th of the current month at midnight
        $data['is_overtime'] = $now->greaterThan($day15);

        if (User::find(Auth::id())->hasRole('superadmin')) {
            return view('pages/rfk/browse-superadmin', $data);
        }

        return view('pages/rfk/browse', $data);
    }

    public function modalAdd()
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Tambah RFK";

        return view('pages/rfk/add', $data);
    }

    public function browse(Request $request)
    {
        $date = $request['month'];
        $bulan = Carbon::parse($date)->format('m');
        $tahun = Carbon::parse($date)->format('Y');

        // $bulan = '2';
        // $tahun = '2023';

        if (User::find(Auth::id())->hasRole('superadmin')) {
            $auth = $request['opd'];
        } else {
            $auth = Auth::user()->id_opd;
        }

        $opd = Opd::find($auth);
        if (!$code = $opd['rfk_code']) {
            return response([]);
        }
        $code = explode(',', $code);

        $rfk = Rfk_detail::select('rfk_detail.*', 'sisa', 'prosentase_fisik', 'status')->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')->where(['id_opd' => $auth, 'month' => $bulan, 'year' => $tahun])->get();
        $rfkTransformed = $rfk->keyBy('code');
        // $result = $this->rfkService->consumeGET("/rfk-service/public/api/rekap/$code[0]/$bulan/$tahun");

        $data = [];
        $arrOrganization = [];
        $arrProgram = [];
        $arrKegiatan = [];
        foreach ($rfk as $i => $item) {
            $len = strlen($item['code']);
            if ($len == 24) continue;
            if ($len <= 29) {
                $item['editable'] = true;
            } else {
                $item['fisik'] = isset($rfkTransformed[$item['code']]) ? $rfkTransformed[$item['code']]['fisik'] : null;
                $item['sisa'] = isset($rfkTransformed[$item['code']]) ? $rfkTransformed[$item['code']]['sisa'] : null;
                $item['prosentase_fisik'] = isset($rfkTransformed[$item['code']]) ? $rfkTransformed[$item['code']]['prosentase_fisik'] : null;
                $item['editable'] = true;
                $item['organization'] = substr($item['code'], 0, 20);
                $item['program'] = substr($item['code'], 0, 23);
                $item['kegiatan'] = substr($item['code'], 0, 27);
            }
            $item['keterangan'] = isset($rfkTransformed[$item['code']]) ? ($rfkTransformed[$item['code']]['keterangan'] ?? '') : '';
            $item['status'] = isset($rfkTransformed[$item['code']]) ? $rfkTransformed[$item['code']]['status'] : (isset($rfk[0]) ? $rfk[0]['status'] : 'draft');
            $data[$i] = $item;
        }

        $groupKegiatan = collect($data)->groupBy('kegiatan');
        unset($groupKegiatan['']);
        foreach ($groupKegiatan as $kegiatan => $subKegiatans) {
            $arrKegiatan[$kegiatan]['fisik'] = 0;
            $arrKegiatan[$kegiatan]['sisa'] = 0;
            $arrKegiatan[$kegiatan]['prosentase_fisik'] = 0;
            $arrKegiatan[$kegiatan]['count'] = 0;
            foreach ($subKegiatans as $i => $item) {
                $arrKegiatan[$kegiatan]['fisik'] += $item['fisik'];
                $arrKegiatan[$kegiatan]['sisa'] += $item['sisa'];
                $arrKegiatan[$kegiatan]['prosentase_fisik'] += $item['prosentase_fisik'];
                $arrKegiatan[$kegiatan]['count'] += 1;
                $arrKegiatan[$kegiatan]['program'] = $item['program'];
                $arrKegiatan[$kegiatan]['organization'] = $item['organization'];
            }
            $arrKegiatan[$kegiatan]['fisik'] /= $arrKegiatan[$kegiatan]['count'];
            $arrKegiatan[$kegiatan]['prosentase_fisik'] /= $arrKegiatan[$kegiatan]['count'];
        }

        foreach (collect($arrKegiatan)->groupBy('program') as $program => $kegiatans) {
            $arrProgram[$program]['fisik'] = 0;
            $arrProgram[$program]['sisa'] = 0;
            $arrProgram[$program]['prosentase_fisik'] = 0;
            $arrProgram[$program]['count'] = 0;
            foreach ($kegiatans as $i => $item) {
                $arrProgram[$program]['fisik'] += $item['fisik'];
                $arrProgram[$program]['sisa'] += $item['sisa'];
                $arrProgram[$program]['prosentase_fisik'] += $item['prosentase_fisik'];
                $arrProgram[$program]['count'] += 1;
                $arrProgram[$program]['program'] = $item['program'];
                $arrProgram[$program]['organization'] = $item['organization'];
            }
            $arrProgram[$program]['fisik'] /= $arrProgram[$program]['count'];
            $arrProgram[$program]['prosentase_fisik'] /= $arrProgram[$program]['count'];
        }

        foreach ($arrProgram as $item) {
            if (!isset($arrOrganization['fisik'])) {
                $arrOrganization['fisik'] = 0;
                $arrOrganization['sisa'] = 0;
                $arrOrganization['prosentase_fisik'] = 0;
                $arrOrganization['count'] = 0;
            }
            $arrOrganization['fisik'] += $item['fisik'];
            $arrOrganization['sisa'] += $item['sisa'];
            $arrOrganization['prosentase_fisik'] += $item['prosentase_fisik'];
            $arrOrganization['count'] += 1;
        }

        if (isset($arrOrganization['count'])) {
            $arrOrganization['fisik'] /= $arrOrganization['count'];
            $arrOrganization['prosentase_fisik'] /= $arrOrganization['count'];
            // dd($data[0]->toArray(), $arrOrganization);
            $data[0] = array_merge($arrOrganization, $data[0]->toArray());
        }

        foreach ($data as $i => $item) {
            foreach ($arrProgram as $program) {
                if ($item['code'] == $program['program']) {
                    $data[$i] = array_merge($program, $data[$i]->toArray());
                    continue;
                }
            }
            foreach ($arrKegiatan as $kegiatan => $dataKegiatan) {
                if ($item['code'] == $kegiatan) {
                    $data[$i] = array_merge($dataKegiatan, $data[$i]->toArray());
                    continue;
                }
            }
        }

        return response($data);
    }

    public function getChart(Request $request)
    {
        if (User::find(Auth::id())->hasRole('superadmin')) {
            $opd = $request['opd'];
        } else {
            $opd = Auth::user()->id_opd;
        }

        $rfk = Rfk_detail::select('month', DB::raw("AVG(pagu_anggaran) as pagu_anggaran"), DB::raw("AVG(keuangan) as keuangan"), DB::raw("AVG(fisik) as fisik"))
            ->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')
            ->where('uraian', 'TOTAL')
            ->where('year', Carbon::parse()->format('Y'))
            ->groupBy('month');
        if ($opd) {
            $rfk = $rfk->where('id_opd', $opd);
        }
        $rfk = $rfk->get();
        $rfkTransformed = $rfk->keyBy('month');

        $charts = [];
        for ($i = 1; $i <= 12; $i++) {
            $charts['pagu_anggaran'][] = isset($rfkTransformed[$i]) ? $rfkTransformed[$i]['pagu_anggaran'] : 0;
            $charts['keuangan'][] = isset($rfkTransformed[$i]) ? $rfkTransformed[$i]['keuangan'] : 0;
            $charts['fisik'][] = isset($rfkTransformed[$i]) ? $rfkTransformed[$i]['fisik'] : 0;
        }

        return response($charts);
    }

    public function store(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make($attr, [
            'code' => 'required',
            'uraian' => 'required',
            'pagu_anggaran' => 'required',
            'bulan_lalu' => 'required',
            'bulan_ini' => 'required',
            'sd_bulan_ini' => 'required',
            'sisa_anggaran' => 'required',
            'angkas' => 'required',
            'angkas_prosentase' => 'required',
            'keuangan' => 'required',
            'fisik' => 'required',
            'keterangan' => 'required',
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
        if (!$attr['fisik'] && !$attr['keterangan']) return;

        $id_opd = Auth::user()->id_opd;

        $attr['angkas_prosentase'] = (float) $attr['angkas_prosentase'];
        $attr['keuangan'] = (float) $attr['keuangan'];
        $attr['fisik'] = number_format((float) $attr['fisik'], 2);
        $attr['pagu_anggaran'] = (float) str_replace(['.', ','], ['', '.'], $attr['pagu_anggaran']);
        $attr['bulan_lalu'] = (float) str_replace(['.', ','], ['', '.'], $attr['bulan_lalu']);
        $attr['bulan_ini'] = (float) str_replace(['.', ','], ['', '.'], $attr['bulan_ini']);
        $attr['sd_bulan_ini'] = (float) str_replace(['.', ','], ['', '.'], $attr['sd_bulan_ini']);
        $attr['sisa_anggaran'] = (float) $attr['sisa_anggaran'];
        $attr['angkas'] = (float) str_replace(['.', ','], ['', '.'], $attr['angkas']);
        $attr['sisa'] = (float) ($attr['fisik'] * $attr['pagu_anggaran'] / 100);
        $attr['prosentase_fisik'] = (float) ($attr['sisa'] / $attr['pagu_anggaran'] * 100);

        $date = $attr['month'];
        $bulan = Carbon::parse($date)->format('m');
        $tahun = Carbon::parse($date)->format('Y');
        DB::beginTransaction();
        try {

            $rfk = Rfk::updateOrCreate(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun], ['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun]);

            unset($attr['month']);
            $rfk_detail = Rfk_detail::updateOrCreate(['code' => $attr['code'], 'id_rfk' => $rfk['id']], $attr);

            DB::commit();
            toastr()->success('Berhasil Menyimpan Data');
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            return 'Gagal Menyimpan Data';
        }
    }

    public function modalEdit($id)
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Edit RFK";
        $data['rfk_detail'] = Rfk_detail::where('rfk_detail.id', $id)->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')->first();

        $data['year_month'] = Carbon::parse($data['rfk_detail']['year'] . '-' . $data['rfk_detail']['month'])->format('Y-m');
        return view('pages/rfk/edit', $data);
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Hapus RFK";
        $data['rfk_detail'] = Rfk_detail::where('id', $id)->first();

        return view('pages/rfk/delete', $data);
    }

    public function deleteDetail($id)
    {
        try {
            Rfk_detail::where('id', $id)->delete();
            return 'true';
        } catch (\Exception $e) {
            return 'Gagal Menghapus Data';
        }
    }

    public function save(Request $request)
    {
        $attr = $request->all();

        DB::beginTransaction();
        try {
            if (User::find(Auth::id())->hasRole('superadmin')) {
                $id_opd = $attr['id_opd'];
            } else {
                $id_opd = Auth::user()->id_opd;
            }

            $opd = Opd::find($id_opd);
            if (!$code = $opd['rfk_code']) {
                return response([]);
            }

            $date = $attr['month'];
            $bulan = Carbon::parse($date)->format('m');
            $tahun = Carbon::parse($date)->format('Y');

            $rfk = Rfk::updateOrCreate(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun], ['status' => 'submit']);

            $code = explode(',', $code);
            $headRfk = Rfk_detail::select('rfk_detail.*')->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')->where(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun])->orderBy('id', 'asc')->first();
            unset($headRfk['id']);
            $headRfk['code'] = $headRfk['code'] . '.T';
            $headRfk['uraian'] = 'TOTAL';

            Rfk_detail::leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')->where('code', $headRfk['code'])->where(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun])->delete();
            Rfk_detail::create($headRfk->toArray());

            DB::commit();
            return 'true';
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return;
        }
    }


    public function storeSuperadmin(Request $request)
    {
        $attr = $request->all();
        if (!$attr['fisik'] && !$attr['keterangan'] && !$attr['id_opd']) return;

        $id_opd = $attr['id_opd'];

        DB::beginTransaction();
        try {
            $date = $request['month'];
            $bulan = Carbon::parse($date)->format('m');
            $tahun = Carbon::parse($date)->format('Y');

            $rfk = Rfk::updateOrCreate(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun], ['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun]);

            unset($attr['month']);
            unset($attr['id_opd']);
            $attr['angkas_prosentase'] = (float) $attr['angkas_prosentase'];
            $attr['keuangan'] = (float) $attr['keuangan'];
            $attr['fisik'] = number_format((float) $attr['fisik'], 2);
            $attr['sisa'] = (float) ($attr['fisik'] * $attr['pagu_anggaran'] / 100);
            $attr['prosentase_fisik'] = (float) ($attr['sisa'] / $attr['pagu_anggaran'] * 100);
            $rfk_detail = Rfk_detail::updateOrCreate(['code' => $attr['code'], 'id_rfk' => $rfk['id']], $attr);

            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            return;
        }
    }

    public function approve(Request $request)
    {
        $attr = $request->all();

        $id_opd = $attr['id_opd'];
        $date = $request['month'];
        $bulan = Carbon::parse($date)->format('m');
        $tahun = Carbon::parse($date)->format('Y');

        Rfk::updateOrCreate(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun], ['status' => 'approve']);

        toastr()->success('Berhasil Menyimpan Data');
        return 'true';
    }

    public function export(Request $request, $type)
    {
        if ($type == 1) return $this->laporanRFK_1($request);
        if ($type == 2) return $this->laporanRFK_2($request);
        if ($type == 3) return $this->laporanRFK_3($request);
    }

    private function laporanRFK_1($request)
    {
        $date = $request['month'];
        $bulan = Carbon::parse($date)->format('m');
        $tahun = Carbon::parse($date)->format('Y');

        if (User::find(Auth::id())->hasRole('superadmin')) {
            $auth = $request['opd'];
        } else {
            $auth = Auth::user()->id_opd;
        }

        if ($auth == 0) {
            $opds = Opd::where('rfk_code', '!=', '')->get();
        } else {
            $opds = Opd::where('id', $auth)->get();
        }

        $data = [];
        $total = [
            "pagu_anggaran" => 0,
            "bulan_lalu" => 0,
            "bulan_ini" => 0,
            "sd_bulan_ini" => 0,
            "sisa_anggaran" => 0,
            "angkas" => 0,
            "angkas_prosentase" => 0,
            "keuangan" => 0,
            "fisik" => 0,
            "sisa" => 0,
            "prosentase_fisik" => 0,
        ];
        $countOPD = 0;
        foreach ($opds as $o => $opd) {
            $rfk = Rfk_detail::select('rfk_detail.*')->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')->where(['id_opd' => $opd['id'], 'month' => $bulan, 'year' => $tahun])->get();
            if (!count($rfk)) {
                continue;
            }
            $countOPD++;
            $rfkIndex = empty(array_keys($rfk->toArray())) ? 0 : array_keys($rfk->toArray())[0];
            $rfkTotal = $rfk->first();
            unset($rfk[$rfkIndex]);

            $total['pagu_anggaran'] += $rfkTotal['pagu_anggaran'];
            $total['bulan_lalu'] += $rfkTotal['bulan_lalu'];
            $total['bulan_ini'] += $rfkTotal['bulan_ini'];
            $total['sd_bulan_ini'] += $rfkTotal['sd_bulan_ini'];
            $total['sisa_anggaran'] += $rfkTotal['sisa_anggaran'];
            $total['angkas'] += $rfkTotal['angkas'];
            $total['angkas_prosentase'] += $rfkTotal['angkas_prosentase'];
            $total['keuangan'] += $rfkTotal['keuangan'];
            $total['fisik'] += $rfkTotal['fisik'];
            $total['sisa'] += $rfkTotal['sisa'];
            $total['prosentase_fisik'] += $rfkTotal['prosentase_fisik'];

            $data[] = $rfk;
        }
        $total['angkas_prosentase'] = $countOPD ? $total['angkas_prosentase'] / $countOPD : 0;
        $total['keuangan'] = $countOPD ? $total['keuangan'] / $countOPD : 0;
        $total['fisik'] = $countOPD ? $total['fisik'] / $countOPD : 0;
        $total['prosentase_fisik'] = $countOPD ? $total['prosentase_fisik'] / $countOPD : 0;

        return Excel::download(new LaporanRFK1($data, $total), 'laporan_rfk_1.xlsx');
    }

    private function laporanRFK_2($request)
    {
        $date = $request['month'];
        $bulan = Carbon::parse($date)->format('m');
        $tahun = Carbon::parse($date)->format('Y');

        $data = Rfk::select('rfk_detail.*', 'opd.name as opd_name')
            ->leftJoin(DB::raw("(SELECT * FROM rfk_detail WHERE LENGTH(code) <= 22) as rfk_detail"), 'rfk.id', '=', 'rfk_detail.id_rfk')
            ->leftJoin('opd', 'rfk.id_opd', '=', 'opd.id', 'left')
            ->where(['month' => $bulan, 'year' => $tahun])->groupBy('id_opd')->get();

        return Excel::download(new LaporanRFK2($data), 'laporan_rfk_2.xlsx');
    }

    private function laporanRFK_3($request)
    {
        $date = $request['month'];
        $bulan = Carbon::parse($date)->format('m');
        $tahun = Carbon::parse($date)->format('Y');
        if (User::find(Auth::id())->hasRole('superadmin')) {
            if (!$request['opd']) return;
            $auth = $request['opd'];
        } else {
            $auth = Auth::user()->id_opd;
        }

        $opd = Opd::find($auth);
        if (!$code = $opd['rfk_code']) {
            return response([]);
        }
        $code = explode(',', $code);

        $rfk = Rfk_detail::select('rfk_detail.*', 'rfk.month')
            ->leftJoin('rfk', 'rfk_detail.id_rfk', '=', 'rfk.id')
            ->where(['id_opd' => $auth])
            ->where('year', $tahun)
            ->get()
            ->groupBy('month');
        $data = [];
        foreach ($rfk as $key => $item) {
            foreach ($item as $i => $value) {
                $data[$i]['code'] = $value['code'];
                $data[$i]['uraian'] = $value['uraian'];
                $data[$i]['angkas'][(int)$key]['angkas'] = $value['angkas'];
            }
        }

        return Excel::download(new LaporanRFK3($data), 'laporan_rfk_3.xlsx');
    }

    public function modalImport()
    {
        $data['page_title'] = "RFK";
        $data['page_subtitle'] = "Import RFK";

        return view('pages/rfk/import', $data);
    }

    public function import(Request $request)
    {
        try {
            $month = $request->month;
            $file = $request->file('file');
            $import = Excel::import(new RfkImport(['month' => $month, 'user' => Auth::user()]), $file);

            if (!$import) {
                return response()->json([
                    'success' => false,
                    'error_message' => "Gagal Import"
                ], 422);
            }

            return response()->json([
                'success' => true,
                'errors' => []
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'success' => false,
                'error_message' => "Gagal Import"
            ], 422);
        }
    }
}
