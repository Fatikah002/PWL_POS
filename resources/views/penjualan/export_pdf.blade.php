<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            padding: 8px;
        }

        th {
            text-align: left;
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 2px solid #000;
        }

        .border-all, .border-all th, .border-all td {
            border: 1px solid #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .main-table {
            width: 100%;
            margin-top: 15px;
        }

        .detail-table {
            width: 100%;
            margin-top: 10px;
        }

        .sub-total {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        /* Flexbox untuk memisahkan tabel dengan detail penjualan */
        .flex-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        /* Menata tampilan detail di bawah tabel utama */
        .detail-container {
            width: 100%;
            margin-top: 15px;
        }

    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="1%">
                <img src="{{ asset('polinema-bw.jpeg') }}" class="image">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">
                    KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI
                </span>
                <span class="text-center d-block font-13 font-bold mb-1">
                    POLITEKNIK NEGERI MALANG
                </span>
                <span class="text-center d-block font-10">
                    Jl. Soekarno-Hatta No. 9 Malang 65141
                </span>
                <span class="text-center d-block font-10">
                    Telepon (0341) 484424 Pes. 101-105, 0341-484428, Fax. (0341) 484428
                </span>
                <span class="text-center d-block font-10">
                    Laman: www.polinema.ac.id
                </span>
            </td>
        </tr>
    </table>

    <div class="header">
        <h3>LAPORAN DATA PENJUALAN</h3>
    </div>

    @php
        $totalKeseluruhan = 0;
    @endphp

    <div class="flex-container">
        <table class="border-all main-table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Penjualan</th>
                    <th class="text-center">Tanggal Penjualan</th>
                    <th class="text-center">Pembeli</th>
                    <th class="text-center">Total Pembayaran</th>
                    <th class="text-center">Detail Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan as $p)
                    @php
                        $totalTransaksi = $p->getTotalAmount();
                        $totalKeseluruhan += $totalTransaksi;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $p->penjualan_kode }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($p->penjualan_tanggal)->format('d-m-Y') }}</td>
                        <td class="text-center">{{ $p->pembeli }}</td>
                        <td class="text-right">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <table class="border-all detail-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Kode Barang</th>
                                        <th class="text-left">Nama Barang</th>
                                        <th class="text-right">Harga</th>
                                        <th class="text-right">Jumlah</th>
                                        <th class="text-right">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($p->details as $detail)
                                    <tr>
                                        <td class="text-center">{{ $detail->barang->barang_kode }}</td>
                                        <td>{{ $detail->barang->barang_nama }}</td>
                                        <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ $detail->jumlah }}</td>
                                        <td class="text-right">{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="sub-total">
        <h4>Total Keseluruhan: Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</h4>
    </div>

</body>
</html>
