<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crips;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Support\Facades\DB;

class CripsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['crips'] = Crips::select('crips.*', 'kriteria.nama_kriteria')
            ->join('kriteria', 'crips.kriteria_id', '=', 'kriteria.id')
            ->orderBy('crips.id', 'ASC')
            ->paginate(10);

        $data['kriteria'] = Kriteria::all();
        return view('admin.crips.index', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_crips' => 'required|string',
            'bobot' => 'required|numeric'
        ]);

        try {
            $crips = new Crips();
            $crips->kriteria_id = $request->kriteria_id;
            $crips->nama_crips = $request->nama_crips;
            $crips->bobot = $request->bobot;
            $crips->save();
            return back()->with('msg', 'Berhasil Menambahkan Data!');
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            die("Gagal");
        }
    }

    public function edit($id)
    {
        $data['kriteria'] = Kriteria::all();
        $data['crips'] = Crips::findOrFail($id);

        return view('admin.crips.edit', $data);
    }

    public function update(Request $request, $id)
    {

        try {
            $crips = Crips::findOrFail($id);
            $crips->update([
                'nama_crips'    =>  $request->nama_crips,
                'bobot'         =>  $request->bobot

            ]);
            return back()->with('msg', 'Berhasil Mengubah Data!');
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            die("Gagal");
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $crips = Crips::findOrFail($id);
            $crips->delete();

            // Truncate the Penilaian table if crips deletion was successful
            Penilaian::truncate();

            DB::commit();

            return redirect()->route('crips.index')->with('success', 'Data berhasil dihapus');
        } catch (Exception $e) {
            DB::rollBack();
            Log::emergency("File: " . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());

            return redirect()->route('crips.index')->with('error', 'Gagal menghapus data');
        }
    }
}
