<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .center { text-align: center; }
        .table, .table td, .table th {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 6px;
        }
        .no-border td { border: none; }
        .signature-section {
            margin-top: 60px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-box p {
            margin: 6px 0;
        }
    </style>
</head>
@php
    $bulanIndo = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
@endphp
<body>

<div style="display: flex; align-items: center; gap: 15px;">
    {{-- Logo di kiri --}}
    <div style="flex-shrink: 0;">
        <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo" style="height: 60px; margin-left: 70px">
    </div>

    {{-- Kop di kanan --}}
    <div style="flex-grow: 1; text-align: center; margin-top: -70px">
        <h4 style="margin: 5;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h4>
        <h5 style="margin: 0;">POLITEKNIK NEGERI BANYUWANGI</h5>
        <p style="margin: 0; font-size: 11px;">
            Jalan Raya Jember Kilometer 13 Labanasem, Kabat, Banyuwangi, 68461
        </p>
        <p style="margin: 0; font-size: 11px;">
            Telepon: (0333) 636780
        </p>
        <p style="margin: 0; font-size: 11px;">
            Laman: www.poliwangi.ac.id &nbsp;&nbsp;|&nbsp;&nbsp; Pos-el: poliwangi@poliwangi.ac.id
        </p>
    </div>
</div>
<hr>

<table class="no-border" width="100%">
    <tr>
        <td width="20%">NO</td>
        <td>: {{ $pegawai->id }}</td>
        <td colspan="1"></td>
        <td style="text-align:right;"><h4>SLIP GAJI</h4></td>
    </tr>
    <tr>
        <td>NAMA</td>
        <td colspan="2">: {{ $pegawai->nama }}</td>
        <td style="text-align:right;">{{ isset($bulan) && isset($bulanIndo[$bulan]) ? $bulanIndo[$bulan] . ' ' . $tahun : $tahun }}</td>
    </tr>
    <tr>
        <td>JABATAN</td>
        <td colspan="2">: {{ $pegawai->nama_staff }}</td>
    </tr>
</table>

<br>
@php
        $totalPenerimaan = 0;
        $totalPotongan = 0;
        $totalPajak = 0;
    @endphp

<table class="table" width="100%">
    <tr>
        <th width="70%">PENERIMAAN</th>
        <th>POTONGAN</th>
    </tr>
    <tr>
        <td>
            @php
                $grouped = $pendapatan->groupBy(function ($item) {
                    return $item->jenisPendapatan->nama_jenis_pendapatan;
                });

                $totalPenerimaan = 0;
            @endphp

            <ul style="list-style: none; padding-left: 0;">
                @foreach ($grouped as $namaJenis => $items)
                    @php
                        $totalNilai = $items->sum('nilai_netto');
                        $totalPenerimaan += $totalNilai;
                    @endphp
                    <li>
                        <span style="display: inline-block; min-width: 325px;">
                            {{ $namaJenis }}
                        </span>
                        : Rp{{ number_format($totalNilai, 0, ',', '.') }}
                    </li>
                @endforeach
            </ul>

        </td>
        <td>
            <ul style="list-style: none; padding-left: 0;">
                @foreach ($pendapatan as $item)
                    @php $totalPotongan += $item->potongan; @endphp
                @endforeach
                <li>
                    <span style="display: inline-block; min-width: 100px;">Potongan</span>
                    : Rp{{ number_format($totalPotongan, 0, ',', '.') }}
                </li>

                @foreach ($pendapatan as $item)
                    @php $totalPajak += $item->pajak; @endphp
                @endforeach
                <li>
                    <span style="display: inline-block; min-width: 100px;">Pajak</span>
                    : Rp{{ number_format($totalPajak, 0, ',', '.') }}
                </li>
            </ul>
        </td>
    </tr>
    <tr>
        <td><strong>TOTAL PENERIMAAN</strong> Rp{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
        <td><strong>TOTAL POTONGAN</strong> Rp{{ number_format(($totalPotongan+$totalPajak), 0, ',', '.') }}</td>
    </tr>
</table>

<br>
<p><strong>JUMLAH DITERIMA : Rp{{ number_format(($totalPenerimaan-($totalPotongan+$totalPajak)), 0, ',', '.') }}</strong></p>

<table width="100%" style="margin-top: 60px;">
    <tr>
        <td style="text-align: center; width: 50%;">
            Dibuat oleh,<br>
            PPABP<br><br><br><br>
            <strong>Nova Victor Geral Dino, S.E.</strong><br>
            NIP. 199311252019031011
        </td>
        <td style="text-align: center; width: 50%;">
            Banyuwangi, {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}<br>
            Diterima oleh,<br><br><br><br>
            <strong>{{ $pegawai->nama }}</strong><br>
            NIP. {{ $pegawai->nip }}
        </td>
    </tr>
</table>


</body>
</html>
