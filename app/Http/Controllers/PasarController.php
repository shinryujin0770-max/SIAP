<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pasar;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PasarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_title'] = "Pasar";
        $data['page_subtitle'] = "";

        return view('pages/pasar/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Pasar::orderBy('id', 'desc');
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
        $data['page_title'] = "Pasar";
        $data['page_subtitle'] = "Tambah Pasar";

        return view('pages/pasar/add', $data);
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
        $store = Pasar::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('pasar');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('pasar');
    }

    public function edit($id)
    {
        $data['page_title'] = "Pasar";
        $data['page_subtitle'] = "Ubah Pasar";
        $data['pasar'] = Pasar::find($id);

        return view('pages/pasar/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $store = Pasar::where('id', $id)->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('pasar');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('pasar');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "Pasar";
        $data['page_subtitle'] = "Hapus Pasar";
        $data['pasar'] = Pasar::findOrFail($id);

        return view('pages/pasar/delete', $data);
    }

    public function delete($id)
    {
        $store = Pasar::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('pasar');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('pasar');
    }
}
