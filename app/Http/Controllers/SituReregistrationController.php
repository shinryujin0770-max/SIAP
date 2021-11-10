<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Jenis_dagangan;
use App\Models\Kios;
use App\Models\Pasar;
use App\Models\Pedagang;
use App\Models\Situ;
use App\Models\Situ_registration;
use App\Services\CertificatesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SituReregistrationController extends Controller
{
    protected $certificateService;
    protected $situController;

    public function __construct()
    {
        $this->middleware('auth');

        Carbon::setLocale('id');
        $this->certificateService = new CertificatesService();
        $this->situController = new SituController();
    }

    public function index()
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Registrasi Ulang";

        return view('pages/situ/reregistration/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::select("select situ.*, situ_registration.id as id_registration, pasar.nama as nama_pasar, kios.nomor as nama_kios, pedagang.nama as nama_pedagang, situ_registration.registration as ke, DATE_ADD(tanggal_terbit, INTERVAL situ_registration.registration YEAR) as tanggal from situ_registration
			left join situ on situ_registration.id_situ = situ.id
            left join pasar on situ.id_pasar = pasar.id
            left join kios on situ.id_kios = kios.id
            left join pedagang on situ.id_pedagang = pedagang.id
            where NOW() > DATE_ADD(tanggal_terbit, INTERVAL situ_registration.registration YEAR) AND is_registered = false GROUP by id order by registration asc");

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kode', function ($row) {
                    $kode = $this->situController->generateKodeSitu($row->id, $row->id_pasar, $row->tanggal_terbit);
                    $url = url('situ') . '/' . $row->id;
                    return "<a href='$url'>$kode</a>";
                })
                ->editColumn('nama_pedagang', function ($row) {
                    $url = url('pedagang') . '/' . $row->id_pedagang;
                    return "<a href='$url'>$row->nama_pedagang</a>";
                })
                ->editColumn('tanggal_terbit', function ($row) {
                    return Carbon::parse($row->tanggal_terbit)->isoFormat('DD MMMM YYYY') . ' s/d ' . Carbon::parse($row->berlaku_sampai)->isoFormat('DD MMMM YYYY');
                })
                ->editColumn('ke', function ($row) {
                    return "<span class='badge badge-danger text-center badge-pill' style='font-size: 12pt'>$row->ke</span>";
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id_registration;
                    // $btn = "<button onclick='ajukan($id)' class='edit btn btn-block btn-info btn-sm'><i class='fa fa-paper-plane'></i> Ajukan</button>";
                    $btn = "<button onclick='signature($id)' class='edit btn btn-block btn-info btn-sm'><i class='fa fa-paper-plane'></i> Tandatangani</button>";
                    return $btn;
                })
                ->rawColumns(['action', 'nama_pedagang', 'kode', 'ke'])
                ->make(true);
        }

        return;
    }

    public function modalRequestReregistration($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Pengajuan Registrasi Ulang";
        $data['situ'] = Situ::findOrFail($id);
        $data['situ']['kode'] = $this->situController->generateKodeSitu($id, $data['situ']['id_pasar'], $data['situ']['tanggal_terbit']);

        return view('pages/situ/reregistration/request', $data);
    }

    public function requestReregistration($id_situ)
    {
        $situ_registration = Situ_registration::where('id_situ', $id_situ)->get()->pop();

        $registration = 1;
        if ($situ_registration && $situ_registration['registration'] > 0) {
            $registration = $situ_registration['registration'] + 1;
        }

        $store = Situ_registration::create(['id_situ' => $id_situ, 'registration' => $registration, 'is_registered' => false]);

        if (!$store) {
            toastr()->error('Gagal mengajukan registrasi ulang');
            return redirect('situ_reregistration');
        }

        toastr()->success('Berhasil mengajukan registrasi ulang');
        return redirect('situ_reregistration');
    }

    public function modalSignature($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Tanda Tangan Registrasi Ulang";
        $data['situ_registration'] = Situ_registration::findOrFail($id);
        $data['situ'] = Situ::findOrFail($data['situ_registration']['id_situ']);
        $data['situ']['kode'] = $this->situController->generateKodeSitu($data['situ']['id'], $data['situ']['id_pasar'], $data['situ']['tanggal_terbit']);

        return view('pages/situ/reregistration/signature', $data);
    }

    public function signature($id)
    {
        $situ = Situ_registration::find($id);
        $situ->is_registered = true;
        $situ->id_signature = Auth::id();
        $store = $situ->save();

        if (!$store) {
            toastr()->error('Gagal melakukan registrasi ulang');
            return redirect('situ_reregistration');
        }

        toastr()->success('Berhasil melakukan registrasi ulang');
        return redirect('situ_reregistration');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Hapus SITU";
        $data['situ'] = Situ_registration::findOrFail($id);

        return view('pages/situ/reregistration/delete', $data);
    }

    public function delete($id)
    {
        $store = Situ_registration::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('situ_reregistration');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('situ_reregistration');
    }
}
