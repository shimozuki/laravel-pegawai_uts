<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan_model;

class JabatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['jabatan'] = Jabatan_model::orderBy('id', 'ASC')->paginate(10);
        return view('admin.jabatan.index', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code_jabatan' => 'required|string',
            'jabatan'      => 'required|string'
        ]);

        try {

            $jabatan = new Jabatan_model();
            $jabatan->code_jabatan = $request->code_jabatan;
            $jabatan->jabatan = $request->jabatan;
            $jabatan->save();
            return back()->with('msg', 'Berhasil Menambahkan Data');
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            die("Gagal");
        }
    }
}
