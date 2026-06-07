<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Procurement</title>
    <style>
        @page {
            margin: 20px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #111;
            background-color: #ffffff;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 4px solid #000000;
            padding-bottom: 12px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 10px;
            color: #444;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 15px;
        }
        th {
            background-color: #000000;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            padding: 8px 4px;
            font-size: 9px;
            border: 2px solid #000000;
            word-wrap: break-word;
        }
        td {
            padding: 8px 4px;
            border: 2px solid #000000;
            font-size: 9px;
            text-align: center;
            word-wrap: break-word;
        }
        .text-left {
            text-align: left;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 4px;
            border: 1px solid #000;
        }
        .status-rp {
            background-color: #FACC15;
            color: #000;
        }
        .status-te {
            background-color: #22D3EE;
            color: #000;
        }
        .status-rete {
            background-color: #FF80FF;
            color: #000;
        }
        .status-po {
            background-color: #4ADE80;
            color: #000;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 8px;
            font-family: 'Courier New', Courier, monospace;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN MONITORING PENGADAAN (RP-TE-RETE-PO)</h1>
        <p>PT. KAWASAN INDUSTRI BONTANG — DEPARTEMEN PENGADAAN</p>
        <p>Tanggal Cetak: {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; word-wrap: break-word;">No</th>
                <th style="width: 15%; word-wrap: break-word;">Kode Pengadaan</th>
                <th style="width: 25%; word-wrap: break-word;">Deskripsi / Nama Barang</th>
                <th style="width: 18%; word-wrap: break-word;">Vendor</th>
                <th style="width: 10%; word-wrap: break-word;">Tgl IN</th>
                <th style="width: 10%; word-wrap: break-word;">Tgl TE</th>
                <th style="width: 10%; word-wrap: break-word;">Tgl RE-TE</th>
                <th style="width: 7%; word-wrap: break-word;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $index => $item)
                <tr>
                    <td style="width: 5%; word-wrap: break-word;" class="font-mono">{{ $index + 1 }}</td>
                    <td style="width: 15%; word-wrap: break-word;" class="font-mono">{{ $item->kode_pengadaan }}</td>
                    <td style="width: 25%; word-wrap: break-word;" class="text-left">{{ $item->nama_barang }}</td>
                    <td style="width: 18%; word-wrap: break-word;" class="text-left">{{ $item->vendor ?? '—' }}</td>
                    <td style="width: 10%; word-wrap: break-word;" class="font-mono">{{ $item->tanggal_in?->format('Y-m-d') ?? '—' }}</td>
                    <td style="width: 10%; word-wrap: break-word;" class="font-mono">{{ $item->tanggal_te?->format('Y-m-d') ?? '—' }}</td>
                    <td style="width: 10%; word-wrap: break-word;" class="font-mono">{{ $item->tanggal_rete?->format('Y-m-d') ?? '—' }}</td>
                    <td style="width: 7%; word-wrap: break-word;">
                        @switch($item->status)
                            @case('RP')
                                <span class="status-badge status-rp">RP</span>
                                @break
                            @case('TE')
                                <span class="status-badge status-te">TE</span>
                                @break
                            @case('RE-TE')
                                <span class="status-badge status-rete">RE-TE</span>
                                @break
                            @case('PO')
                                <span class="status-badge status-po">PO</span>
                                @break
                            @default
                                <span class="status-badge" style="background-color: #eee;">{{ $item->status }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #555; padding: 25px; font-family: 'Courier New', Courier, monospace;">
                        TIDAK ADA DATA PROCUREMENT.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        PT. KMI Procurement System — Dokumen ini digenerate secara otomatis pada {{ $generatedAt }}
    </div>

</body>
</html>
