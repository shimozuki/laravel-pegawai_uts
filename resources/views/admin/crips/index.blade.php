@extends('layouts.app')
@section('title', 'SPK Staf UTS')
@section('css')

<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@stop
@section('content')



<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#tambahkriteria" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Tambah Sub Kriteria</h6>
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

                    <form action="{{ route('crips.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="kriteria">Kriteria</label>
                            <select name="kriteria_id" id="" class="form-control" required>
                                <option>---Pilih Kriteria---</option>
                                @foreach($kriteria as $kriteriaItem)
                                <option value="{{ $kriteriaItem->id }}">{{ $kriteriaItem->nama_kriteria }}</option>
                                @endforeach
                            </select>

                            @error('kriteria')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Sub Kriteria</label>
                            <input type="text" class="form-control @error ('nama_crips') is-invalid @enderror" name="nama_crips">

                            @error('nama_crips')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>
                        <div class="form-group">
                            <label for="bobot">Bobot Sub Kriteria</label>
                            <input type="text" class="form-control @error ('bobot') is-invalid @enderror" name="bobot">

                            @error('bobot')
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>
                        <button class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#listkriteria" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">List Sub Kriteria</h6>
            </a>

            <!-- Card Content - Collapse -->
            <div class="collapse show" id="listkriteria">
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-striped table-hover" id="DataTable" data-paging="false">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kriteria</th>
                                    <th>Nama SubKriteria</th>
                                    <th>Bobot</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($crips as $row)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $row->nama_kriteria}}</td>
                                    <td>{{ $row->nama_crips }}</td>
                                    <td>{{ $row->bobot }}</td>
                                    <td>
                                        <a href="{{ route('crips.edit',$row->id) }}" class="btn btn-sm btn-circle btn-warning">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <a href="{{ route('crips.destroy',$row->id) }}" class="btn btn-sm btn-circle btn-danger hapus">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            {{ $crips->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






@stop
@section('js')

<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/sweetalert.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#DataTable').DataTable();

        $('.hapus').on('click', function() {
            swal({
                    title: "Apa anda yakin?",
                    text: "Sekali anda menghapus data, data tidak dapat dikembalikan lagi!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: $(this).attr('href'),
                            type: 'DELETE',
                            data: {
                                '_token': "{{ csrf_token() }}"
                            },
                            success: function() {
                                swal("Data berhasil dihapus!", {
                                    icon: "success",
                                }).then((willDelete) => {
                                    window.location = "{{ route('kriteria.index') }}"
                                });
                            }
                        })
                        location.reload();
                    } else {
                        swal("Data Aman!");
                    }
                });

            return false;
        })
    })
</script>

@stop