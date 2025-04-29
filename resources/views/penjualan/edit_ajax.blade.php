@empty($penjualan)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Data Tidak Ditemukan!</h5>
                    Transaksi yang Anda cari tidak tersedia.
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning mt-3">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
@else
<form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-dark">
                <h5 class="modal-title"></i>Edit Transaksi Penjualan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Data Transaksi -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="penjualan_id"><strong>ID Transaksi:</strong></label>
                            <input type="text" class="form-control" id="penjualan_id" value="{{ $penjualan->penjualan_id }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="penjualan_kode"><strong>Kode Penjualan:</strong></label>
                            <input type="text" class="form-control" id="penjualan_kode" value="{{ $penjualan->penjualan_kode }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="penjualan_tanggal"><strong>Tanggal:</strong></label>
                            <input type="text" class="form-control" id="penjualan_tanggal" value="{{ $penjualan->penjualan_tanggal }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pembeli"><strong>Nama Pembeli:</strong></label>
                            <input type="text" name="pembeli" class="form-control" value="{{ $penjualan->pembeli }}">
                        </div>
                        <div class="form-group">
                            <label for="user"><strong>Nama Pengguna:</strong></label>
                            <input type="text" class="form-control" value="{{ $penjualan->user->username }}" readonly>
                        </div>
                    </div>
                </div>

                <h6 class="text-muted mb-3">Edit Detail Barang</h6>
                <!-- Tabel Detail Barang -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->details as $index => $detail)
                                <tr>
                                    <td>
                                        {{ $detail->barang->barang_nama }}
                                        <input type="hidden" name="detail[{{ $index }}][barang_id]" value="{{ $detail->barang_id }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detail[{{ $index }}][jumlah]" class="form-control text-center" value="{{ $detail->jumlah }}" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="detail[{{ $index }}][harga]" class="form-control text-right" value="{{ $detail->harga }}" min="0" readonly>
                                    </td>
                                    <td class="text-right font-weight-bold text-success">
                                        Rp{{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $('#form-edit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: this.action,
            method: this.method,
            data: $(this).serialize(),
            success: function(res) {
                if (res.status) {
                    $('#myModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    });
                dataPenjualan.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server.'
                });
                console.error(xhr.responseText);
            }
        });
    });
</script>
@endempty
