<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Transaksi Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Input utama --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Kode Transaksi</label>
                            <input type="text" name="penjualan_kode" id="penjualan_kode" class="form-control">
                            <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <input type="hidden" name="penjualan_tanggal" id="penjualan_tanggal">
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nama Pembeli</label>
                            <input type="text" name="pembeli" id="pembeli" class="form-control" required maxlength="50">
                            <small id="error-pembeli" class="error-text form-text text-danger"></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Kasir</label>
                            <input type="text" value="{{ Auth::user()->nama }}" class="form-control" readonly>
                            <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}">
                            <small id="error-user_id" class="error-text form-text text-danger"></small>
                        </div>
                    </div>
                </div>

                {{-- Detail Barang --}}
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5>Detail Barang</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pilih Barang</label>
                                    <select id="add_barang_id" class="form-control">
                                        <option value="" disabled selected>- Pilih Barang -</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->barang_id }}" 
                                                data-nama="{{ $b->barang_nama }}" 
                                                data-kode="{{ $b->barang_kode }}" 
                                                data-harga="{{ $b->harga_jual }}">
                                                {{ $b->barang_nama }} - {{ $b->barang_kode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga</label>
                                    <input type="number" id="add_harga" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" id="add_jumlah" class="form-control" min="1" value="1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn-add-item" class="btn btn-success btn-block">Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Barang --}}
                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table table-bordered table-sm" id="table-items">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Nama Barang</th>
                                    <th width="15%">Harga</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total:</th>
                                    <th id="total-amount">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <small id="error-items" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

{{-- SCRIPT --}}
<script>
$(document).ready(function() {
    $('#penjualan_tanggal').val(new Date().toISOString().slice(0, 10)); // Format: yyyy-mm-dd
    var items = [];
    var totalAmount = 0;

    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/[^,\d]/g, '').replace(/(\d)(?=(\d{3})+\b)/g, '$1.');
    }

    $('#add_barang_id').change(function() {
        var harga = $('#add_barang_id option:selected').data('harga');
        $('#add_harga').val(harga);
    });

    $('#btn-add-item').click(function() {
        var barangId = $('#add_barang_id').val();
        var harga = parseInt($('#add_harga').val());
        var jumlah = parseInt($('#add_jumlah').val());

        if (!barangId || isNaN(harga) || isNaN(jumlah) || jumlah <= 0) {
            Swal.fire('Error', 'Pilih barang dan masukkan jumlah yang valid', 'error');
            return;
        }

        var barangNama = $('#add_barang_id option:selected').data('nama');
        var subtotal = harga * jumlah;

        var existingItem = items.find(item => item.barang_id == barangId);
        if (existingItem) {
            existingItem.jumlah += jumlah;
            existingItem.subtotal = existingItem.harga * existingItem.jumlah;
        } else {
            items.push({barang_id: barangId, nama: barangNama, harga: harga, jumlah: jumlah, subtotal: subtotal});
        }

        $('#add_barang_id').val('');
        $('#add_harga').val('');
        $('#add_jumlah').val(1);
        renderTable();
    });

    $(document).on('click', '.btn-remove-item', function() {
        var index = $(this).data('index');
        items.splice(index, 1);
        renderTable();
    });

    function renderTable() {
        var tbody = $('#table-items tbody');
        tbody.empty();
        totalAmount = 0;

        if (items.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center">Tidak ada barang</td></tr>');
        } else {
            items.forEach(function(item, index) {
                totalAmount += item.subtotal;

                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama}</td>
                        <td>${formatRupiah(item.harga)}</td>
                        <td>${item.jumlah}</td>
                        <td>${formatRupiah(item.subtotal)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-remove-item" data-index="${index}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        $('#total-amount').text(formatRupiah(totalAmount));
    }

    renderTable();

    $("#form-tambah").validate({
        rules: {
            penjualan_kode: {required: true},
            pembeli: {required: true},
            user_id: {required: true, number: true},
            penjualan_tanggal: {required: true}
        },
        submitHandler: function(form) {
            if (items.length === 0) {
                Swal.fire('Error', 'Tambahkan minimal satu barang ke transaksi', 'error');
                return false;
            }

            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            var formData = $(form).serializeArray();
            items.forEach(function(item, index) {
                formData.push({name: `items[${index}][barang_id]`, value: item.barang_id});
                formData.push({name: `items[${index}][harga]`, value: item.harga});
                formData.push({name: `items[${index}][jumlah]`, value: item.jumlah});
            });

            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                success: function(response) {
                    Swal.close(); // Tutup loading
                    console.log(response); // Debug responsenya

                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        dataPenjualan.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        if (response.msgField) {
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-'+prefix).text(val[0]);
                            });
                        }
                        Swal.fire('Terjadi Kesalahan', response.message || 'Gagal menyimpan data', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error(xhr.responseText);
                    Swal.fire('Gagal', 'Terjadi kesalahan pada server', 'error');
                }
            });
            return false;
        }
    });
});
</script>
