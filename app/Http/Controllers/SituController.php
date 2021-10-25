<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Jenis_dagangan;
use App\Models\Kios;
use App\Models\Pasar;
use App\Models\Pedagang;
use App\Models\Situ;
use App\Services\CertificatesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SituController extends Controller
{
    protected $certificateService;

    public function __construct()
    {
        $this->middleware('auth');

        Carbon::setLocale('id');
        $this->certificateService = new CertificatesService();
    }

    public function index()
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "";

        return view('pages/situ/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Situ::select('situ.*', 'pasar.nama as nama_pasar', 'kios.nomor as nama_kios', 'jenis_dagangan.nama as nama_jenis_dagangan', 'pedagang.nama as nama_pedagang')->orderBy('id', 'desc')
                ->leftJoin('pasar', 'situ.id_pasar', '=', 'pasar.id', 'left')
                ->leftJoin('kios', 'situ.id_kios', '=', 'kios.id', 'left')
                ->leftJoin('jenis_dagangan', 'situ.id_jenis_dagangan', '=', 'jenis_dagangan.id', 'left')
                ->leftJoin('pedagang', 'situ.id_pedagang', '=', 'pedagang.id', 'left');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kode', function ($row) {
                    return $this->generateKodeSitu($row['id'], $row['id_pasar'], $row['tanggal_terbit']);
                })
                ->editColumn('tanggal_terbit', function ($row) {
                    return Carbon::parse($row['tanggal_terbit'])->isoFormat('DD MMMM YYYY') . ' s/d ' . Carbon::parse($row['berlaku_sampai'])->isoFormat('DD MMMM YYYY');
                })
                ->addColumn('action', function ($row) {
                    $id = $row['id'];
                    $btn = "<button onclick='read($id)' class='edit btn btn-block btn-info btn-sm'><i class='fa fa-eye'></i> Detail</button>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return;
    }

    public function read($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Detail SITU";
        $data['situ'] = Situ::select('situ.*', 'pasar.nama as nama_pasar', 'kios.nomor as nama_kios', 'jenis_dagangan.nama as nama_jenis_dagangan', 'pedagang.nama as nama_pedagang')->orderBy('id', 'desc')
            ->leftJoin('pasar', 'situ.id_pasar', '=', 'pasar.id', 'left')
            ->leftJoin('kios', 'situ.id_kios', '=', 'kios.id', 'left')
            ->leftJoin('jenis_dagangan', 'situ.id_jenis_dagangan', '=', 'jenis_dagangan.id', 'left')
            ->leftJoin('pedagang', 'situ.id_pedagang', '=', 'pedagang.id', 'left')->where('situ.id', $id)->first();

        return view('pages/situ/read', $data);
    }

    public function add()
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Tambah SITU";
        $data['pasars'] = Pasar::all();
        $data['kioss'] = Kios::all();
        $data['pedagangs'] = Pedagang::all();
        $data['jenis_dagangans'] = Jenis_dagangan::all();

        return view('pages/situ/add', $data);
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
        $validator = Validator::make(array_merge($attr['dt'], $attr['pedagang'], $attr), [
            'id_pasar' => 'required',
            'id_kios' => 'required',
            'id_jenis_dagangan' => 'required',
            'nik' => 'required',
            'nama' => 'required',
            'alamat' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'foto' => 'required',
        ], [
            'required' => ':attribute harus diisi.',
            'id_pasar.required' => 'Pasar harus dipilih.',
            'id_kios.required' => 'Kios harus dipilih.',
            'id_jenis_dagangan.required' => 'Jenis dagangan harus dipilih.',
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

        $data = $attr['dt'];
        $pedagang = $attr['pedagang'];

        $findPedagang = Pedagang::where('nik', $pedagang['nik'])->first();
        if ($findPedagang) {
            $data['id_pedagang'] = $findPedagang['id'];
            $pedagang['nama'] = $pedagang['nama'] != $findPedagang['nama'] ? $pedagang['nama'] : $findPedagang['nama'];
            $pedagang['alamat'] = $pedagang['alamat'] != $findPedagang['alamat'] ? $pedagang['alamat'] : $findPedagang['alamat'];
            $pedagang['tempat_lahir'] = $pedagang['tempat_lahir'] != $findPedagang['tempat_lahir'] ? $pedagang['tempat_lahir'] : $findPedagang['tempat_lahir'];
            $pedagang['tanggal_lahir'] = $pedagang['tanggal_lahir'] != $findPedagang['tanggal_lahir'] ? $pedagang['tanggal_lahir'] : $findPedagang['tanggal_lahir'];

            $storePedagang = Pedagang::where('nik', $pedagang['nik'])->update($pedagang);
        } else {
            $storePedagang = Pedagang::create($pedagang);
            $data['id_pedagang'] = $storePedagang['id'];
        }
        $data['tanggal_terbit'] = Carbon::now()->format('Y-m-d');
        $data['berlaku_sampai'] = Carbon::now()->add('years', 5)->subDay()->format('Y-m-d');

        $store = Situ::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('situ');
        }

        if (isset($attr['foto'])) {
            $store->addMedia($attr['foto'])
                ->usingFileName('foto-' . $store['id'] . '.' . pathinfo($attr['foto']->getClientOriginalName(), PATHINFO_EXTENSION))
                ->toMediaCollection('foto');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('situ');
    }

    public function edit($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Ubah SITU";
        $data['situ'] = Situ::select('situ.*', 'pasar.nama as nama_pasar', 'kios.nomor as nama_kios', 'jenis_dagangan.nama as nama_jenis_dagangan', 'pedagang.nik as nik_pedagang', 'pedagang.nama as nama_pedagang')->orderBy('id', 'desc')
            ->leftJoin('pasar', 'situ.id_pasar', '=', 'pasar.id', 'left')
            ->leftJoin('kios', 'situ.id_kios', '=', 'kios.id', 'left')
            ->leftJoin('jenis_dagangan', 'situ.id_jenis_dagangan', '=', 'jenis_dagangan.id', 'left')
            ->leftJoin('pedagang', 'situ.id_pedagang', '=', 'pedagang.id', 'left')->where('situ.id', $id)->first();
        $data['pasars'] = Pasar::all();
        $data['kioss'] = Kios::all();
        $data['pedagangs'] = Pedagang::all();
        $data['jenis_dagangans'] = Jenis_dagangan::all();

        return view('pages/situ/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $pedagang = $attr['pedagang'];

        $findSitu = Situ::find($id);
        $findPedagang = Pedagang::where('nik', $pedagang['nik'])->first();
        if ($findPedagang) {
            $data['id_pedagang'] = $findPedagang['id'];
            $pedagang['nama'] = $pedagang['nama'] != $findPedagang['nama'] ? $pedagang['nama'] : $findPedagang['nama'];
            $pedagang['alamat'] = $pedagang['alamat'] != $findPedagang['alamat'] ? $pedagang['alamat'] : $findPedagang['alamat'];
            $pedagang['tempat_lahir'] = $pedagang['tempat_lahir'] != $findPedagang['tempat_lahir'] ? $pedagang['tempat_lahir'] : $findPedagang['tempat_lahir'];
            $pedagang['tanggal_lahir'] = $pedagang['tanggal_lahir'] != $findPedagang['tanggal_lahir'] ? $pedagang['tanggal_lahir'] : $findPedagang['tanggal_lahir'];

            $storePedagang = Pedagang::where('nik', $pedagang['nik'])->update($pedagang);
        } else {
            $storePedagang = Pedagang::create($pedagang);
            $data['id_pedagang'] = $storePedagang['id'];
        }
        $data['tanggal_terbit'] = Carbon::now()->format('Y-m-d');
        $data['berlaku_sampai'] = Carbon::now()->add('years', 5)->subDay()->format('Y-m-d');

        $store = $findSitu->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('situ');
        }

        if (isset($attr['foto'])) {
            $findSitu->addMedia($attr['foto'])
                ->usingFileName('foto-' . $id . '.' . pathinfo($attr['foto']->getClientOriginalName(), PATHINFO_EXTENSION))
                ->toMediaCollection('foto');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('situ');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Hapus SITU";
        $data['situ'] = Situ::findOrFail($id);

        return view('pages/situ/delete', $data);
    }

    public function delete($id)
    {
        $store = Situ::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('situ');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('situ');
    }

    public function modalSignature($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Tanda Tangani SITU";
        $data['situ'] = Situ::findOrFail($id);

        return view('pages/situ/signature', $data);
    }

    public function signature($id)
    {
        $situ = Situ::find('id', $id);
        $situ->is_signatured = true;
        $situ->id_signature = Auth::id();
        $store = $situ->save();

        if (!$store) {
            toastr()->error('Gagal melakukan tanda tangan');
            return redirect('situ');
        }

        toastr()->success('Berhasil melakukan tanda tangan');
        return redirect('situ');
    }

    public function previewCertificates($id)
    {
        $data = Situ::select(
            'situ.*',
            'pasar.id as id_pasar',
            'pasar.nama as nama_pasar',
            'kios.nomor as nama_kios',
            'kios.ukuran as ukuran_kios',
            'jenis_dagangan.nama as nama_jenis_dagangan',
            'pedagang.nik as nik_pedagang',
            'pedagang.nama as nama_pedagang',
            'pedagang.alamat as alamat_pedagang',
            'pedagang.tempat_lahir as tempat_lahir_pedagang',
            'pedagang.tanggal_lahir as tanggal_lahir_pedagang',
        )
            ->leftJoin('pasar', 'situ.id_pasar', '=', 'pasar.id', 'left')
            ->leftJoin('kios', 'situ.id_kios', '=', 'kios.id', 'left')
            ->leftJoin('jenis_dagangan', 'situ.id_jenis_dagangan', '=', 'jenis_dagangan.id', 'left')
            ->leftJoin('pedagang', 'situ.id_pedagang', '=', 'pedagang.id', 'left')->where('situ.id', $id)->first();

        $data['nomor_situ'] = $this->generateKodeSitu($data['id'], $data['id_pasar'], $data['tanggal_terbit']);
        $data['tanggal_lahir_pedagang'] = Carbon::parse($data['tanggal_lahir_pedagang'])->isoFormat('DD MMMM YYYY');
        $data['berlaku_sampai'] = Carbon::parse($data['berlaku_sampai'])->isoFormat('DD MMMM YYYY');
        $data['tanggal_terbit'] = Carbon::parse($data['tanggal_terbit'])->isoFormat('DD MMMM YYYY');
        if (count($data->getMedia('foto')) > 0) {
            $data['foto'] = $data->getMedia('foto')->pop()->getFullUrl();
        }
        // dd($data);

        return $this->certificateService->getSurat($data);
    }

    public function generateKodeSitu($id_situ, $id_pasar, $tahun)
    {
        $id_situ = str_pad($id_situ, 5, '0', STR_PAD_LEFT);
        $id_pasar = str_pad($id_pasar, 3, '0', STR_PAD_LEFT);
        $tahun = Carbon::parse($tahun)->format('Y');

        return "511/$id_situ/$id_pasar/106/$tahun";
    }
}
