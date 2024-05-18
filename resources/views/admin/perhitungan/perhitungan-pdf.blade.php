<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style type="text/css">
        .garis1 {
            border-top: 3px solid black;
            height: 2px;
            border-bottom: 1px solid black;

        }

        #camat {
            text-align: center;
        }

        #nama-camat {
            margin-top: 100px;
            text-align: center;
        }

        #ttd {
            position: absolute;
            bottom: 10;
            right: 20;
        }
    </style>


</head>

<body>
    <div>
        <table>
            <tr>
                <td style="padding-right: 240px; padding-left: 10px"><img src="https://uts.ac.id/wp-content/uploads/2021/01/UTS-1000-Universal-Square-Color.png" width="90" height="90"></td>
                <td>
                    <center>
                        <font size="4">KEMENTRIAN PENDIDIKAN DAN KEBUDAYAAN</font><br>
                        <font size="4">UNIVERSITAS TEKNOLOGI SUMBAWA</font><br>
                        <font size="2">Jl.RAYA OLAT MARAS BATU ALANG KABUPATEN SUMBAWA</font><br>
                        <font size="2">Tlp.082147004028  Website : https://uts.ac.id/en/welcome</font><br>
                    </center>
                </td>
            </tr>
        </table>

        <hr class="garis1" />
        <div style="margin-top: 25px; margin-bottom: 25px;">
            <center><strong><u>HASIL SELEKSI</u></strong></center>
        </div>

        <div class="collapse show" id="rank">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIDN</th>
                                <th>Nama Pegawai</th>
                                <th>Jabatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($data as $output)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $output->nidn }}</td>
                                <td>{{ $output->nama }}</td>
                                <td>{{ $output->jabatan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</body>
</html>