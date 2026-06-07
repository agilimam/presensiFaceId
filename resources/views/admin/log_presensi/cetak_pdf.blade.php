<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #222; }
        .kop-surat { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #111; padding-bottom: 8px; }
        .kop-surat h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        th { background-color: #f0fdf4; color: #15803d; border: 1px solid #cbd5e1; padding: 6px; text-align: center; }
        td { border: 1px solid #cbd5e1; padding: 5px; vertical-align: top; }
        .tanggal-header { background-color: #e2e8f0; font-weight: bold; padding: 8px; }
        .box-jamaah { background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 4px; border-radius: 3px; font-size: 8px; margin-top: 2px; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h2>Laporan Hasil Pemantauan Presensi Sholat Jamaah</h2>
        <p>Periode: <strong>{{ $labelTanggal }}</strong></p>
    </div>

    @foreach($rekapPerTanggal as $tanggal => $logs)
        <table>
            <thead>
                <tr>
                    <th colspan="6" class="tanggal-header" style="text-align: left;">
                        Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </th>
                </tr>
                <tr>
                    <th style="width: 25%;">Grup / Kepala Keluarga</th>
                    <th style="width: 15%;">Subuh</th>
                    <th style="width: 15%;">Dzuhur</th>
                    <th style="width: 15%;">Ashar</th>
                    <th style="width: 15%;">Maghrib</th>
                    <th style="width: 15%;">Isya</th>
                </tr>
            </thead>
            <tbody>
                @php $dataKeluarga = $logs->groupBy('id_keluarga'); @endphp
                @foreach($dataKeluarga as $idKeluarga => $logsKeluarga)
                <tr>
                    <td>
                        <strong>{{ $logsKeluarga->first()->anggotaKeluarga->keluarga->nama_keluarga ?? 'Keluarga Baru' }}</strong><br>
                        <small>{{ $logsKeluarga->first()->anggotaKeluarga->nama_anggota ?? '-' }}</small>
                    </td>

                    @foreach(['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $sesi)
                    <td>
                        @php $absenSesi = $logsKeluarga->where('keterangan_sholat', $sesi); @endphp
                        @if($absenSesi->isNotEmpty())
                            @foreach($absenSesi as $log)
                                <div class="box-jamaah">
                                    👤 {{ $log->anggotaKeluarga->nama_anggota ?? '?' }} 
                                    <span style="float: right;">{{ \Carbon\Carbon::parse($log->waktu_absen)->format('H:i') }}</span>
                                </div>
                            @endforeach
                        @else
                            <div style="text-align:center; color:#cbd5e1; font-size: 14px;">-</div>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>