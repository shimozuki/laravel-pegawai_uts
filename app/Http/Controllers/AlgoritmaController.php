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
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

use Illuminate\Support\Facades\Auth;


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
            $output = OutputModel::where('code_jabatan', $code_jabatan)->where('nama', $nama)->first();
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
    public function downloadWord()
    {
        $data = OutputModel::joinJabatan()->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Add header
        $header = $section->addHeader();
        $table = $header->addTable();
        $table->addRow();
        $table->addCell(2000)->addImage('https://uts.ac.id/wp-content/uploads/2021/01/UTS-1000-Universal-Square-Color.png', [
            'width' => 90,
            'height' => 90,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
        ]);
        $cell = $table->addCell(8000);
        $cell->addText('KEMENTRIAN PENDIDIKAN DAN KEBUDAYAAN', ['size' => 14, 'bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell->addText('UNIVERSITAS TEKNOLOGI SUMBAWA', ['size' => 14, 'bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell->addText('Jl.RAYA OLAT MARAS BATU ALANG KABUPATEN SUMBAWA', ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell->addText('Tlp.082147004028  Website : https://uts.ac.id/en/welcome', ['size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Add line below header
        $lineStyle = array('weight' => 3, 'width' => 500, 'height' => 0, 'color' => '000000', 'alignment' => 'start');
        $header->addLine($lineStyle);

        // Add a line break
        $section->addTextBreak(1);

        // Add title
        $section->addText('HASIL SELEKSI PEJABAT STRUKTURAL UNIVERSITAS TEKNOLOGI SUMBAWA', ['size' => 16, 'bold' => true, 'underline' => 'single'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Add table
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50]);
        $table->addRow();
        $table->addCell(500)->addText('No', ['bold' => true]);
        $table->addCell(1500)->addText('NIDN', ['bold' => true]);
        $table->addCell(3500)->addText('Nama Pegawai', ['bold' => true]);
        $table->addCell(3500)->addText('Jabatan', ['bold' => true]);

        $no = 1;
        foreach ($data as $output) {
            $table->addRow();
            $table->addCell(500)->addText($no++);
            $table->addCell(1500)->addText($output->nidn);
            $table->addCell(3500)->addText($output->nama);
            $table->addCell(3500)->addText($output->jabatan);
        }

        // Add signature
        $section->addTextBreak(2);
        $section->addText('Sumbawa, ' . \Carbon\Carbon::now()->translatedFormat('d F Y'), null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
        $section->addText('Rektor', null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
        $section->addText(Auth::user()->name, ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

        // Save the document
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'hasil_seleksi.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
