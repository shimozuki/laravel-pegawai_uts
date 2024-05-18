@extends('layouts.app')
@section('title', 'SPK Staf UTS ', $alternatif->nama_alternatif)
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#tambahkriteria" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Edit Alternatif {{ $alternatif->nama_alternatif }}</h6>
            </a>

            <!-- Card Content - Collapse -->
            <div class="collapse show" id="tambahkriteria">
                <div class="card-body">
                    @if (Session::has('msg'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Infor</strong> {{ Session::get('msg') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form action="{{ route('alternatif.update', $alternatif->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <label for="nama">Nama Pegawai</label>
                            <input type="text" class="form-control @error('nama_alternatif') is-invalid @enderror" name="nama_alternatif" value="{{ old('nama_alternatif', $alternatif->nama_alternatif) }}">
                            @error('nama_alternatif')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nidn">NIDN</label>
                            <input type="number" class="form-control @error('nidn') is-invalid @enderror" name="nidn" value="{{ old('nidn', $alternatif->nidn) }}">
                            @error('nidn')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ttl">Tempat Tanggal Lahir</label>
                            <input type="text" class="form-control @error('ttl') is-invalid @enderror" name="ttl" value="{{ old('ttl', $alternatif->ttl) }}">
                            @error('ttl')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <div>
                                <input type="radio" id="laki_laki" name="jns_klamin" value="laki-laki" {{ old('jns_klamin', $alternatif->jns_klamin) == 'laki-laki' ? 'checked' : '' }}>
                                <label for="laki_laki">Laki-laki</label>
                            </div>
                            <div>
                                <input type="radio" id="perempuan" name="jns_klamin" value="perempuan" {{ old('jns_klamin', $alternatif->jns_klamin) == 'perempuan' ? 'checked' : '' }}>
                                <label for="perempuan">Perempuan</label>
                            </div>
                            @error('jns_klamin')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" class="form-control @error('alamat') is-invalid @enderror" name="alamat" value="{{ old('alamat', $alternatif->alamat) }}">
                            @error('alamat')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input type="number" class="form-control @error('telepon') is-invalid @enderror" name="telepon" value="{{ old('telepon', $alternatif->telepon) }}">
                            @error('telepon')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code_jabatan">Jabatan</label>
                            <select name="code_jabatan" id="code_jabatan" class="form-control" required>
                                <option value="">---Pilih Jabatan---</option>
                                @foreach($jabatan as $jabatans)
                                <option value="{{ $jabatans->code_jabatan }}" {{ old('code_jabatan', $alternatif->code_jabatan) == $jabatans->code_jabatan ? 'selected' : '' }}>
                                    {{ $jabatans->jabatan }}
                                </option>
                                @endforeach
                            </select>
                            @error('code_jabatan')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button class="btn btn-primary">Simpan</button>
                        <a href="{{ route('alternatif.index') }}" class="btn btn-success">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stop