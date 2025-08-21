<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Formulir Indikator Kinerja Utama</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .center {
            text-align: center;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            right: 0;
            font-size: 10px;
            text-align: left;
            width: 320px;
            margin: 10px;
        }
    </style>
</head>

<body>
    <h3 class="center">FORMULIR INDIKATOR KINERJA UTAMA</h3>

    <table>
        <tr>
            <th colspan="2" class="center">RESPONDEN</th>
        </tr>
        <tr>
            <td>Nama</td>
            <td>{{ $record->pembuat?->name }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>{{ $record->pembuat?->nip }}</td>
        </tr>
        <tr>
            <td>Bagian</td>
            <td>{{ $record->pembuat?->subbagian?->bagian?->nama }}</td>
        </tr>
        <tr>
            <td>Subbagian</td>
            <td>{{ $record->pembuat?->subbagian?->nama }}</td>
        </tr>
    </table>

    @php
        $mapping = [
            'Triwulan I' => ['Januari', 'Februari', 'Maret'],
            'Triwulan II' => ['April', 'Mei', 'Juni'],
            'Triwulan III' => ['Juli', 'Agustus', 'September'],
            'Triwulan IV' => ['Oktober', 'November', 'Desember'],
        ];

        $months = $mapping[$record->periode] ?? [];
    @endphp

    <table>
        <tr>
            <th rowspan="2">Indikator</th>
            <th rowspan="2">Periode</th>
            <th colspan="3">Bulan</th>
        </tr>
        <tr>
            @foreach ($months as $bulan)
                <th>{{ $bulan }}</th>
            @endforeach
        </tr>
        <tr>
            <td>{{ $record->indikator?->nama }}</td>
            <td>{{ $record->periode }}</td>
            <td>{{ $record->nilai_bulan_1 }}</td>
            <td>{{ $record->nilai_bulan_2 }}</td>
            <td>{{ $record->nilai_bulan_3 }}</td>
        </tr>
    </table>

    <div class="footer">
        @php
            $user = auth()->user();

            $nama = $user?->name ? $user->name . ($user->nip ? ' (' . $user->nip . ')' : '') : null;
            $bagian = $user?->subbagian?->bagian?->nama;
            $subbagian = $user?->subbagian?->nama;
            $parts = [$nama, $bagian, $subbagian];
            $diunduh_oleh = implode(' - ', array_filter($parts));
        @endphp
        Diunggah pada: {{ $record->created_at?->format('d-m-Y H:i:s') }}<br>
        Perubahan ke: {{ $record->perubahan_ke ?? '-' }}
        <br><br>
        Diunduh pada: {{ date('d-m-Y H:i:s') }}<br>
        Oleh: {{ $diunduh_oleh }}
        <br><br>
        @BAPEDA | Biro Umum Setda Kalimantan Tengah
    </div>
</body>

</html>
