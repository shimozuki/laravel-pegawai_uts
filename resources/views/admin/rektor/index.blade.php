@extends('layouts.app')
@section('title', 'SPK Staf UTS')
@section('css')

<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@stop
@section('content')


<div class="mb-4">
    <!-- Card Header - Accordion -->
    <div class="row">
        <!-- <div class="col">
            <a href="{{ URL::to('download-perhitungan-pdf') }}" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm float-right"><i
                class="fas fa-download fa-sm text-white-50"></i>Download Laporan</a>
        </div> -->
    </div>

</div>

<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#listkriteria" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Hasil Seleksi</h6>
    </a>

    <!-- Card Content - Collapse -->
    <div class="collapse show" id="listkriteria">
        <div class="card-body">
            <div class="table-responsive">
                <a href="{{ URL::to('download-perhitungan-pdf') }}" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i>Download Laporan pdf</a>
                <a href="{{ URL::to('download-perhitungan-word') }}" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i>Download Laporan word</a>
                <br><br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Jabatan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($pegawai as $row)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->jabatan }}</td>
                            <td> <b>Terpilih<b> </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                                    window.location = "{{ route('user.index') }}"
                                });
                            }
                        })
                    } else {
                        swal("Data Aman!");
                    }
                });

            return false;
        })
    })
</script>

@stop