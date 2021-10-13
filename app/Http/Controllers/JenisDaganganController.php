<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Jenis_dagangan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JenisDaganganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_title'] = "Jenis Dagangan";
        $data['page_subtitle'] = "";

        return view('pages/jenis_dagangan/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Jenis_dagangan::orderBy('kategori', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row['id'];
                    $btn = "<button onclick='edit($id)' class='edit btn btn-block btn-warning btn-sm'><i class='fa fa-edit'></i> Ubah</button>";
                    $btn .= "<button onclick='hapus($id)' class='delete btn btn-block btn-danger btn-sm'><i class='fa fa-trash'></i> Hapus</button>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return;
    }

    public function add()
    {
        $data['page_title'] = "Jenis Dagangan";
        $data['page_subtitle'] = "Tambah Jenis Dagangan";

        return view('pages/jenis_dagangan/add', $data);
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

        $data = $attr['dt'];
        $store = Jenis_dagangan::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('jenis_dagangan');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('jenis_dagangan');
    }

    public function edit($id)
    {
        $data['page_title'] = "Jenis Dagangan";
        $data['page_subtitle'] = "Ubah Jenis Dagangan";
        $data['jenis_dagangan'] = Jenis_dagangan::find($id);

        return view('pages/jenis_dagangan/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $store = Jenis_dagangan::where('id', $id)->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('jenis_dagangan');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('jenis_dagangan');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "Jenis Dagangan";
        $data['page_subtitle'] = "Hapus Jenis Dagangan";
        $data['jenis_dagangan'] = Jenis_dagangan::findOrFail($id);

        return view('pages/jenis_dagangan/delete', $data);
    }

    public function delete($id)
    {
        $store = Jenis_dagangan::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('jenis_dagangan');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('jenis_dagangan');
    }
}
