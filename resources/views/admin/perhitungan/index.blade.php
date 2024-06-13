@extends('layouts.app')
@section('title', 'SPK Staf UTS')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
@stop
@section('content')
@if (empty(request('code_jabatan')))
<!-- Form Drop-down untuk memilih code_jabatan -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pilih Code Jabatan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('perhitungan.index') }}" method="GET">
            <div class="form-group">
                <label for="code_jabatan">Code Jabatan</label>
                <select name="code_jabatan" id="code_jabatan" class="form-control">
                    @foreach ($allCodeJabatan as $jabatan)
                    <option value="{{ $jabatan->code_jabatan }}">{{ $jabatan->jabatan }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@else
<div class="mb-4">
    <!-- Card Header - Accordion -->
    <div class="row">
        <div class="col">
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm float-right" data-toggle="modal" data-target="#downloadModal">
                <i class="fas fa-download fa-sm text-white-50"></i>Download Laporan
            </button>
        </div>
    </div>
</div>

<!-- Modal for download options -->
<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">Pilih Format Download</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Pilih format file yang ingin Anda unduh:</p>
                <a href="{{ URL::to('download-perhitungan-pdf') }}" target="_blank" class="btn btn-primary">PDF</a>
                <a href="{{ URL::to('download-perhitungan-word') }}" target="_blank" class="btn btn-primary">Word</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#listkriteria" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Tahap Analisa</h6>
    </a>

    <!-- Card Content - Collapse -->
    <div class="collapse show" id="listkriteria">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Alternatif</th>
                            @foreach ($kriteria->sortBy('id') as $key => $value)
                            <th>{{ $value->nama_kriteria }}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alternatif as $alt => $valt)
                        <tr>
                            <td>{{ $valt->nama_alternatif }}</td>
                            @if (count($valt->penilaian) > 0)
                            @foreach($valt->penilaian as $key => $value)
                            <td>
                                {{ $value->crips->bobot }}
                            </td>
                            @endforeach
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $kriteria->count() + 1 }}">Tidak ada data!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#normalisasi" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Tahap Normalisasi</h6>
    </a>

    <!-- Card Content - Collapse -->
    <div class="collapse show" id="normalisasi">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Alternatif / Kriteria</th>
                            @foreach ($kriteria->sortBy('id') as $key => $value)
                            <th>{{ $value->nama_kriteria }}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($normalisasi as $alternatif => $values)
                        <tr>
                            <td>{{ $alternatif }}</td>
                            @foreach($kriteria as $kriteriaItem)
                            <td>
                                {{-- Tampilkan nilai, atau '-' jika nilai tidak ada --}}
                                {{ isset($values[$kriteriaItem->id]) ? $values[$kriteriaItem->id] : '-' }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#rank" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Tahap Perangkingan Jabatan {{ $jabatan->jabatan }}</h6>
    </a>

    <!-- Card Content - Collapse -->
    <div class="collapse show" id="rank">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach ($kriteria->sortBy('id') as $key => $value)
                            <th>{{ $value->nama_kriteria }}</th>
                            @endforeach
                            <th rowspan="2" style="text-align: center; padding-bottom: 45px">Total</th>
                            <th rowspan="2" style="text-align: center; padding-bottom: 45px">Rank</th>
                        </tr>
                        <tr>
                            <th>Nama / Bobot</th>
                            @foreach ($kriteria->sortBy('id') as $key => $value)
                            <th>{{ $value->bobot }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($sortedData as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            @foreach($value as $key_1 => $value_1)
                            <td>{{ number_format($value_1, 1) }}</td>
                            @endforeach
                            <td>{{ $no++ }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Narasi Kesimpulan -->
<div class="card shadow mb-4">
    <div class="card-body">
        @if (!empty($sortedData))
        @php
        $topRank = array_key_first($sortedData);
        @endphp
        <center><H2>Kesimpulan</H2></center>
        <p>Berdasarkan hasil perhitungan dan perengkingan, dapat disimpulkan bahwa yang terpilih di jabatan <strong>{{ $jabatan->jabatan }}</strong> adalah <Strong>{{ $topRank }}</Strong>.</p>
        @endif
    </div>
</div>
@endif
@stop
@if (session('warning'))
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>
    swal({
        title: "Peringatan",
        text: "Data penilaian tidak ditemukan!",
        icon: "warning",
        button: "OK",
    }).then(function() {
        window.location = "{{ route('perhitungan.index') }}";
    });
</script>
@stop
@endif
@section('js')
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
@stop
