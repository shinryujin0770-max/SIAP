<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kios;
use App\Models\Pasar;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class KiosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_title'] = "Kios";
        $data['page_subtitle'] = "";

        return view('pages/kios/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Kios::select('kios.*', 'pasar.nama as nama_pasar')->orderBy('id', 'desc')->leftJoin('pasar', 'kios.id_pasar', '=', 'pasar.id', 'left');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kategori', function ($row) {
                    return Str::upper($row['kategori']);
                })
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
        $data['page_title'] = "Kios";
        $data['page_subtitle'] = "Tambah Kios";
        $data['pasars'] = Pasar::all();

        return view('pages/kios/add', $data);
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
        $store = Kios::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('kios');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('kios');
    }

    public function edit($id)
    {
        $data['page_title'] = "Kios";
        $data['page_subtitle'] = "Ubah Kios";
        $data['kios'] = Kios::find($id);
        $data['pasars'] = Pasar::all();

        return view('pages/kios/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $store = Kios::where('id', $id)->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('kios');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('kios');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "Kios";
        $data['page_subtitle'] = "Hapus Kios";
        $data['kios'] = Kios::findOrFail($id);

        return view('pages/kios/delete', $data);
    }

    public function delete($id)
    {
        $store = Kios::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('kios');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('kios');
    }

    public function ajax($id)
    {
        $data = Kios::select('*')->where('id_pasar', $id)->get();
        return $data;
    }
}
