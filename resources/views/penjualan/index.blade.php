@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar transaksi penjualan</h3>
        <div class="card-tools">
            <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan </a>
            <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Penjualan </a>
            <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-success">Tambah Penjualan</button>
        </div>
    </div>
    <div class="card-body">

        <!-- untuk Filter data -->
        <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-sm table-striped table-hover" id="table-penjualan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Nama Pembeli</th>
                    <th>Kode Penjualan</th>
                    <th>Tanggal Penjualan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    var dataPenjualan;
    $(document).ready(function() {
        dataPenjualan = $('#table-penjualan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('penjualan/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.filter_pembeli = $('.filter_pembeli').val();
                }
            },
            columns: [{
                    data: null,
                    className: "text-center",
                    width: "5%",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "user.nama",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "pembeli",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "penjualan_kode",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "penjualan_tanggal",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "text-center",
                    width: "14%",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#table-penjualan_filter input').unbind('keyup').on('keyup', function(e) {
            if (e.keyCode == 13) {
                dataPenjualan.search(this.value).draw();
            }
        });

        $('.filter_pembeli').change(function() {
            dataPenjualan.ajax.reload();
        });
    });
</script>
@endpush