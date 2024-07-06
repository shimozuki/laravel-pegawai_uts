<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\Penilaian;
use App\Models\Jabatan_model;
use PDF;
use Carbon\Carbon;

class AlternatifController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $data['alternatif'] = Alternatif::latest()->paginate(10);
        $data['jabatan'] = Jabatan_model::all();
        return view('admin.alternatif.index', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_alternatif' => 'required|string',
            'nidn' => 'required|string|unique:alternatif,nidn',
            'alamat' => 'required|string',
            'ttl' => 'required|string',
            'jns_klamin' => 'required|string',
            'code_jabatan' => 'required|string',
            'telepon' => 'required|string',
        ]);

        try {
            $alternatif = new Alternatif();
            $alternatif->nama_alternatif = $request->nama_alternatif;
            $alternatif->nidn = $request->nidn;
            $alternatif->alamat = $request->alamat;
            $alternatif->ttl = $request->ttl;
            $alternatif->jns_klamin = $request->jns_klamin;
            $alternatif->code_jabatan = $request->code_jabatan;
            $alternatif->telepon = $request->telepon;
            $alternatif->save();

            return back()->with('msg', 'Berhasil Menambahkan Data');
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
            return back()->with('error', 'Gagal Menambahkan Data');
        }
    }



    public function edit($id)
    {
        $data['alternatif'] = Alternatif::findOrFail($id);
        $data['jabatan'] = Jabatan_model::all();
        return view('admin.alternatif.edit', $data);
    }


    public function update(Request $request, $id)
    {

        $this->validate($request, [

            'nama_alternatif' => 'required|string',
            'nidn' => 'required|string|unique:alternatif,nidn',
            'alamat' => 'required|string',
            'ttl' => 'required|string',
            'jns_klamin' => 'required|string',
            'code_jabatan' => 'required|string',
            'telepon' => 'required|string',

        ]);

        try {

            $alternatif = Alternatif::findOrFail($id);
            $alternatif->update([
                'nama_alternatif' => $request->nama_alternatif,
                'nidn' => $request->nidn,
                'alamat' => $request->alamat,
                'ttl' => $request->ttl,
                'jns_klamin' => $request->jns_klamin,
                'code_jabatan' => $request->code_jabatan,
                'telepon' => $request->telepon,
            ]);
            return back()->with('msg', 'Berhasil Mengubah Data');
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            die("Gagal");
        }
    }

    public function destroy($id)
    {

        try {

            $alternatif = Alternatif::findOrFail($id);
            $alternatif->delete();
            Penilaian::truncate();
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            die("Gagal");
        }
    }

    public function downloadPDF()
    {
        setlocale(LC_ALL, 'IND');
        $tanggal = Carbon::now()->formatLocalized('%A, %d %B %Y');
        $alternatif = Alternatif::with('penilaian.crips')->get();

        $pdf = PDF::loadView('admin.alternatif.alternatif-pdf', compact('alternatif', 'tanggal'));
        $pdf->setPaper('A3', 'potrait');
        return $pdf->stream('alternatif.pdf');
    }
}
