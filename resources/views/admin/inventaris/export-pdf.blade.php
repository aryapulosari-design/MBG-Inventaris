<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris MBG - {{ now()->format('d M Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1B6CA8;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            color: #1B6CA8;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .btn-print {
            background: #1B6CA8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
        <button class="btn-print" style="background:#6c757d; margin-left: 10px" onclick="window.history.back()">Kembali ke Dasbor</button>
    </div>

    <div class="header">
        <h1>LAPORAN ASET INVENTARIS FISIK</h1>
        <p>PROGRAM MAKAN BERGIZI GRATIS (MBG)</p>
    </div>

    <div class="meta-info">
        <div>Tanggal Cetak: {{ now()->locale('id')->isoFormat('D MMMM Y - HH:mm') }}</div>
        <div>Dicetak Oleh: {{ auth()->user()->name }} ({{ auth()->user()->role_label }})</div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($data['headers'] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $row)
                <tr>
                    @foreach($row as $index => $cell)
                        <td class="{{ in_array($index, [4,6,7,8]) ? 'text-right' : '' }}">{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini di-generate secara otomatis oleh sistem MBG Insights Hub.</p>
        <p>Dokumen Sah. Halaman 1 dari 1</p>
    </div>

    <script>
        // Auto trigger print dialog when opened
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
