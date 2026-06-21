<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Procurement</title>
    <style>
        @page {
            margin: 15px;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7px;
            color: #111;
            background-color: #ffffff;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #000000;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            font-size: 8px;
            color: #444;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }
        th {
            background-color: #0F172A;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            padding: 4px 2px;
            font-size: 6.5px;
            border: 1px solid #475569;
            word-wrap: break-word;
        }
        td {
            padding: 4px 2px;
            border: 1px solid #cbd5e1;
            font-size: 6.5px;
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
            padding: 1px 3px;
            border: 1px solid #000;
            font-size: 5.5px;
        }
        .status-disetujui {
            background-color: #DEF7EC;
            color: #03543F;
            border-color: #03543F;
        }
        .status-tidak-disetujui {
            background-color: #FDE8E8;
            color: #9B1C1C;
            border-color: #9B1C1C;
        }
        .status-pending {
            background-color: #FEF08A;
            color: #713F12;
            border-color: #713F12;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 6px;
            font-family: 'Courier New', Courier, monospace;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN MONITORING PENGADAAN (RP-TE-RETE-PO)</h1>
        <p>PT. KALTIM METHANOL INDUSTRI — DEPARTEMEN PENGADAAN</p>
        <p>Tanggal Cetak: {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 7%;">RP</th>
                <th style="width: 14%;">Description</th>
                <th style="width: 8%;">Date Created</th>
                <th style="width: 8%;">Send Gen Dir</th>
                <th style="width: 5%;">Buyer</th>
                <th style="width: 8%;">TE In</th>
                <th style="width: 8%;">TE Out</th>
                <th style="width: 8%;">RE-TE</th>
                <th style="width: 8%;">PO</th>
                <th style="width: 8%;">SO</th>
                <th style="width: 8%;">QC</th>
                <th style="width: 8%;">Delivery</th>
                <th style="width: 8%;">RR</th>
                <th style="width: 8%;">Vendor</th>
                <th style="width: 5%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $item)
                <tr>
                    <td class="font-mono">{{ $item->no }}</td>
                    <td class="font-mono">{{ $item->rp_number }}</td>
                    <td class="text-left">{{ $item->description }}</td>
                    <td class="font-mono">{{ $item->date_created ?? '—' }}</td>
                    <td class="font-mono">{{ $item->send_for_approval_general_director ?? '—' }}</td>
                    <td>{{ $item->buyer ?? '—' }}</td>
                    <td class="font-mono">{{ $item->te_in ?? '—' }}</td>
                    <td class="font-mono">{{ $item->te_out ?? '—' }}</td>
                    <td class="font-mono">{{ $item->re_te ?? '—' }}</td>
                    <td class="font-mono">{{ $item->po ?? '—' }}</td>
                    <td class="font-mono">{{ $item->so ?? '—' }}</td>
                    <td class="font-mono">{{ $item->qc ?? '—' }}</td>
                    <td class="font-mono">{{ $item->delivery ?? '—' }}</td>
                    <td class="font-mono">{{ $item->rr ?? '—' }}</td>
                    <td class="text-left">{{ $item->vendor ?? '—' }}</td>
                    <td>
                        @switch($item->status)
                            @case('Disetujui')
                                <span class="status-badge status-disetujui">Disetujui</span>
                                @break
                            @case('Tidak Disetujui')
                                <span class="status-badge status-tidak-disetujui">Tidak Disetujui</span>
                                @break
                            @case('Pending')
                                <span class="status-badge status-pending">Pending</span>
                                @break
                            @default
                                <span class="status-badge" style="background-color: #eee;">{{ $item->status ?? '—' }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" style="text-align: center; color: #555; padding: 15px; font-family: 'Courier New', Courier, monospace;">
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
