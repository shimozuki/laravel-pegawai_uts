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

    public function edit($id)
    {
        try {
            $jabatan = Jabatan_model::findOrFail($id);
            return view('admin.jabatan.edit', compact('jabatan'));
        } catch (Exception $e) {
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data jabatan');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code_jabatan' => 'required|string',
            'jabatan'      => 'required|string'
        ]);

        try {
            $jabatan = Jabatan_model::findOrFail($id);
            $jabatan->code_jabatan = $request->code_jabatan;
            $jabatan->jabatan = $request->jabatan;
            $jabatan->save();
            return redirect()->route('jabatan.index')->with('msg', 'Data jabatan berhasil diperbarui');
        } catch (Exception $e) {
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data jabatan');
        }
    }

    public function destroy($id)
    {
        try {
            $jabatan = Jabatan_model::findOrFail($id);
            $jabatan->delete();
            return redirect()->route('admin.jabatan.index')->with('msg', 'Data jabatan berhasil dihapus');
        } catch (Exception $e) {
            \Log::error("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data jabatan');
        }
    }
}
