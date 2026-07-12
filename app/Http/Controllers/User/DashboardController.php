<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keluarga;
use App\Models\AnggotaKeluarga;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $keluarga = Keluarga::where('id_user', Auth::id())->first();

        $anggota = [];
        $kepalaKeluarga = null;
        $riwayatSingkat = [];

        /*
        |--------------------------------------------------------------------------
        | Ambil sesi sholat dari Model Presensi
        |--------------------------------------------------------------------------
        */
        $dataSesi = Presensi::getSesiSekarang();

        if ($dataSesi) {

            $sholatAktif = [
                'nama'   => strtoupper($dataSesi['nama']),
                'status' => $dataSesi['status'],
                'range'  => $dataSesi['range'],
            ];

        } else {

            $sholatAktif = [
                'nama'   => 'BELUM MASUK WAKTU',
                'status' => '-',
                'range'  => '-',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Data keluarga
        |--------------------------------------------------------------------------
        */
        if ($keluarga) {

            $anggota = AnggotaKeluarga::where('id_keluarga', $keluarga->id_keluarga)
                ->orderByRaw("
                    FIELD(
                        hubungan,
                        'Kepala Keluarga',
                        'Ibu',
                        'Anak'
                    )
                ")
                ->get();

            $kepalaKeluarga = AnggotaKeluarga::where('id_keluarga', $keluarga->id_keluarga)
                ->where('hubungan', 'Kepala Keluarga')
                ->first();

            $riwayatSingkat = Presensi::with('anggotaKeluarga')
                ->where('id_keluarga', $keluarga->id_keluarga)
                ->latest('waktu_absen')
                ->take(5)
                ->get();
        }

        return view('user.dashboard', compact(
            'keluarga',
            'anggota',
            'kepalaKeluarga',
            'riwayatSingkat',
            'sholatAktif'
        ));
    }

    public function storeAnggota(Request $request)
    {
        $request->validate([
            'nama_anggota' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.]+$/'
            ],
            'hubungan' => 'required',
        ], [
            'nama_anggota.regex' => 'Nama anggota hanya boleh mengandung huruf, spasi, dan titik.',
        ]);

        $keluarga = Keluarga::where('id_user', Auth::id())->first();

        if ($keluarga) {

            $namaExists = AnggotaKeluarga::where('id_keluarga', $keluarga->id_keluarga)
                ->where('nama_anggota', $request->nama_anggota)
                ->exists();

            if ($namaExists) {
                return back()->with(
                    'error',
                    "Gagal! Nama '{$request->nama_anggota}' sudah terdaftar di keluarga Anda."
                );
            }

            if (in_array($request->hubungan, ['Kepala Keluarga', 'Ibu'])) {

                $roleExists = AnggotaKeluarga::where('id_keluarga', $keluarga->id_keluarga)
                    ->where('hubungan', $request->hubungan)
                    ->exists();

                if ($roleExists) {
                    return back()->with(
                        'error',
                        "Gagal! Peran {$request->hubungan} sudah terdaftar dalam keluarga ini."
                    );
                }
            }

            AnggotaKeluarga::create([
                'id_keluarga' => $keluarga->id_keluarga,
                'nama_anggota' => $request->nama_anggota,
                'hubungan' => $request->hubungan,
                'face_id' => null,
            ]);

            return back()->with('success', 'Anggota berhasil ditambahkan!');
        }

        return back()->with('error', 'Data keluarga tidak ditemukan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_anggota' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\.]+$/'
            ],
            'hubungan' => 'required',
        ]);

        $anggota = AnggotaKeluarga::findOrFail($id);

        $keluarga = Keluarga::where('id_user', Auth::id())->first();

        $namaExists = AnggotaKeluarga::where('id_keluarga', $keluarga->id_keluarga)
            ->where('nama_anggota', $request->nama_anggota)
            ->where('id_anggota_keluarga', '!=', $id)
            ->exists();

        if ($namaExists) {
            return back()->with(
                'error',
                "Gagal! Nama {$request->nama_anggota} sudah terdaftar di keluarga Anda."
            );
        }

        if (in_array($request->hubungan, ['Kepala Keluarga', 'Ibu'])) {

            $roleExists = AnggotaKeluarga::where('id_keluarga', $keluarga->id_keluarga)
                ->where('hubungan', $request->hubungan)
                ->where('id_anggota_keluarga', '!=', $id)
                ->exists();

            if ($roleExists) {
                return back()->with(
                    'error',
                    "Gagal! Peran {$request->hubungan} sudah ada di anggota lain."
                );
            }
        }

        $anggota->update([
            'nama_anggota' => $request->nama_anggota,
            'hubungan' => $request->hubungan,
        ]);

        return back()->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $anggota = AnggotaKeluarga::findOrFail($id);

        $keluarga = Keluarga::where('id_user', Auth::id())->first();

        if ($anggota->id_keluarga != $keluarga->id_keluarga) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($anggota->face_id) {
            Storage::disk('public')->delete('faces/' . $anggota->face_id);
        }

        $anggota->delete();

        return back()->with('success', 'Anggota keluarga berhasil dihapus!');
    }
}