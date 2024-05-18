@extends('layouts.app')
@section('title', 'SPK Staf UTS')
@section('content')


<style>
    .fixed-width {
        width: 150px;
        /* Adjust the width as needed */
    }
</style>
@if (empty(request('jns_jabatan')))
<!-- Form Drop-down untuk memilih jns_jabatan -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pilih Code Jabatan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('penilaian.index') }}" method="GET">
            <div class="form-group">
                <label for="jns_jabatan">Code Jabatan</label>
                <select name="jns_jabatan" id="jns_jabatan" class="form-control">
                    <option value="">---PILIH JENIS JABATAN---</option>
                    <option value="struktural">Struktural</option>
                    <option value="administratif">Administratif</option>
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
        <!-- <div class="col">
            <a href="{{ URL::to('download-penilaian-pdf') }}" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i>Download Laporan</a>
        </div> -->
    </div>
</div>

<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#listkriteria" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Penilaian Alternatif</h6>
    </a>

    <!-- Card Content - Collapse -->
    <div class="collapse show" id="listkriteria">
        <div class="card-body">
            @if (Session::has('msg'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Info</strong> {{ Session::get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="table-responsive">

                <form action="{{ route('penilaian.store')}}" method="post">
                    @csrf
                    <div class="float-right">
                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                    <br><br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Alternatif</th>
                                @foreach ($kriteria as $key => $value)
                                <th>{{ $value->nama_kriteria }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($alternatif as $alt => $valt)
                            <tr>
                                <td>{{ $valt->nama_alternatif }}</td>
                                @if (count($valt->penilaian) > 0)
                                @foreach($kriteria as $key => $value)
                                <td>
                                    <select name="crips_id[{{$valt->id}}][]" class="form-control fixed-width">
                                        @foreach($value->crips as $k_1 => $v_1)
                                        <option value="{{ $v_1->id }}" {{ isset($valt->penilaian[$key]) && $v_1->id == $valt->penilaian[$key]->crips_id ? 'selected' : '' }}>
                                            {{ $v_1->nama_crips }}
                                        </option>
                                        @endforeach
                                    </select>

                                </td>
                                @endforeach
                                @else
                                @foreach($kriteria as $key => $value)
                                <td>
                                    <select name="crips_id[{{$valt->id}}][]" class="form-control">
                                        @foreach($value->crips as $k_1 => $v_1)
                                        <option value="{{ $v_1->id }}">
                                            {{ $v_1->nama_crips }}
                                        </option>

                                        @endforeach
                                    </select>
                                </td>
                                @endforeach
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td>Tidak ada data!</td>
                            </tr>


                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@stop