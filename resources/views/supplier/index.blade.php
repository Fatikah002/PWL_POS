@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar supplier</h3>
            <div class="card-tools">
                <button onclick="modalAction(`{{ url('/supplier/import') }}`)" class="btn btn-info">Import Supplier</button>
                <a href="{{ url('/supplier/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export
                    Supplier </a>
                    <a href="{{ url('/supplier/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export
                        Supplier</a>
                <button onclick="modalAction(`{{ url('/supplier/create_ajax') }}`)" class="btn btn-success">Tambah
                    Ajax</button>
            </div>
        </div>
        <div class="card-body">

            <!-- untuk Filter data -->
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_supplier" class="form-control form-control-sm filter_supplier">
                                    <option value="">- Semua -</option>
                                    @foreach ($supplier as $l)
                                        <option value="{{ $l->supplier_id }}">{{ $l->supplier_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Supplier Barang</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-sm table-striped table-hover" id="table-supplier">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Alamat Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var dataSupplier;
        $(document).ready(function() {
            dataSupplier = $('#table-supplier').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('supplier/list') }}",
                    dataType: "json",
                    type: "POST",
                    data: function(d) {
                        d.filter_supplier = $('.filter_supplier').val();
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
                        data: "supplier_kode",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "supplier_nama",
                        className: "",
                        width: "25%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "supplier_alamat",
                        className: "",
                        width: "25%",
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

            $('#table-supplier_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) {
                    dataSupplier.search(this.value).draw();
                }
            });

            $('.filter_supplier').change(function() {
                dataSupplier.reload();
            });
        });
    </script>
@endpush
