<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .subtitle { text-align: center; }
        .meta { margin: 12px 0; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #555; padding: 6px; vertical-align: middle; }
        th { background: #1f4e78; color: #ffffff; font-weight: bold; text-align: center; }
        .total td { background: #d9eaf7; font-weight: bold; }
    </style>
</head>
<body>
    <div class="title">PEMERINTAH PROVINSI BENGKULU — DINAS KELAUTAN DAN PERIKANAN</div>
    <div class="title" style="margin-top: 8px;">{{ $title }}</div>
    <div class="meta">
        <div><strong>Tahun:</strong> {{ $filters['tahun'] }}</div>
        <div><strong>Periode:</strong> {{ $filters['periode_label'] }}</div>
        <div><strong>Tanggal ekspor:</strong> {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
        <div><strong>Penyusun:</strong> {{ auth()->user()->name ?? 'Petugas DKP' }}</div>
    </div>
    <table>
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) }}" style="text-align: center;">Tidak ada data untuk parameter laporan yang dipilih.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total">
                @foreach($totalsRow as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</body>
</html>
