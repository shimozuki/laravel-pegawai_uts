<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\Jabatan_model;
use App\Models\Kriteria;
use App\Models\Penilaian;
use PDF;
use Carbon\Carbon;
use App\Models\OutputModel;

class AlgoritmaController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $code_jabatan = $request->input('code_jabatan');

        // Ambil semua kode jabatan yang unik untuk dropdown
        $allCodeJabatan = Jabatan_model::all();

        // Jika code_jabatan belum ada, tampilkan form drop down
        if (empty($code_jabatan)) {
            return view('admin.perhitungan.index', compact('allCodeJabatan'));
        }

        $jabatan = Jabatan_model::where('code_jabatan', $code_jabatan)->select('jabatan')->first();
        $jns_jabatan = Jabatan_model::where('code_jabatan', $code_jabatan)->select('jns_jabatan')->first();
        $array = [$jns_jabatan->jns_jabatan, 'global'];
        $alternatif = Alternatif::with('penilaian.crips')->where('code_jabatan', $code_jabatan)->get();
        $kriteria = Kriteria::with('crips')->whereIn('kriteria.jns_jabatan', $array)->orderBy('nama_kriteria', 'ASC')->get();
        $penilaian = Penilaian::with(['crips', 'alternatif' => function ($query) use ($code_jabatan) {
            $query->where('code_jabatan', $code_jabatan);
        }])->whereHas('alternatif', function ($query) use ($code_jabatan) {
            $query->where('code_jabatan', $code_jabatan);
        })->get();

        if ($penilaian->isEmpty()) {
            return redirect()->route('perhitungan.index')
                ->with('warning', 'Data penilaian tidak ditemukan!');
        }

        // Mencari min max normalisasi
        foreach ($kriteria as $key => $value) {
            foreach ($penilaian as $key_1 => $value_1) {
                if ($value->id == $value_1->crips->kriteria_id) {
                    if ($value->attribut == 'Benefit') {
                        $minMax[$value->id][] = $value_1->crips->bobot;
                    } elseif ($value->attribut == 'Cost') {
                        $minMax[$value->id][] = $value_1->crips->bobot;
                    }
                }
            }
        }

        // Normalisasi
        foreach ($penilaian as $key_1 => $value_1) {
            foreach ($kriteria as $key => $value) {
                if ($value->id == $value_1->crips->kriteria_id) {
                    if ($value->attribut == 'Benefit') {
                        $normalisasi[$value_1->alternatif->nama_alternatif][$value->id] = $value_1->crips->bobot / max($minMax[$value->id]);
                    } elseif ($value->attribut == 'Cost') {
                        $normalisasi[$value_1->alternatif->nama_alternatif][$value->id] = min($minMax[$value->id]) / $value_1->crips->bobot;
                    }
                }
            }
        }

        // Perangkingan
        foreach ($normalisasi as $key => $value) {
            foreach ($kriteria as $key_1 => $value_1) {
                $rank[$key][] = $value[$value_1->id] * $value_1->bobot;
            }
        }

        $ranking = $normalisasi;
        foreach ($normalisasi as $key => $value) {
            $ranking[$key][] = array_sum($rank[$key]);
        }

        $sortedData = collect($ranking)->sortByDesc(function ($value) {
            return array_sum($value);
        })->toArray();

        if (!empty($sortedData)) {
            $highestRanking = reset($sortedData); // Mendapatkan elemen pertama dari array
            $nama = key($sortedData); // Mendapatkan nama dari elemen pertama
            $rank = $highestRanking[count($highestRanking) - 1]; // Mendapatkan nilai rank
            $output = OutputModel::where('code_jabatan', $code_jabatan)->first();
            if ($output) {
                // Jika data dengan code_jabatan yang sama sudah ada, lakukan pembaruan (update)
                $output->nama = $nama;
                $output->rank = $rank;
                $output->save();
            } else {
                // Jika tidak ada, lakukan penyisipan (insert)
                OutputModel::create([
                    'nama' => $nama,
                    'rank' => $rank,
                    'code_jabatan' => $code_jabatan
                ]);
            }
        }

        return view('admin.perhitungan.index', compact('alternatif', 'kriteria', 'normalisasi', 'sortedData', 'jabatan', 'penilaian'));
    }

    public function downloadPDF()
    {
        setlocale(LC_ALL, 'IND');
        $tanggal = Carbon::now()->formatLocalized('%A, %d %B %Y');
        $data = OutputModel::joinJabatan()->get();
        $pdf = PDF::loadView('admin.perhitungan.perhitungan-pdf', compact('data'));
        $pdf->setPaper('A3', 'potrait');
        return $pdf->stream('hasil_selection.pdf');
    }
}
