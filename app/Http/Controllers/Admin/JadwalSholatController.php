<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSholat;

class JadwalSholatController extends Controller
{
    /**
     * Urutan tampil sesuai urutan waktu sholat (bukan abjad / jam mulai),
     * supaya kartu selalu tampil: Subuh, Dzuhur, Ashar, Maghrib, Isya.
     */
    private const URUTAN_SHOLAT = ['Subuh', 'Zuhur', 'Ashar', 'Magrib', 'Isya'];

    public function index()
    {
        $urutanLower = array_map('strtolower', self::URUTAN_SHOLAT);

        $jadwal = JadwalSholat::get()
            ->sortBy(function ($item) use ($urutanLower) {
                $posisi = array_search(strtolower(trim($item->nama_sholat)), $urutanLower);
                return $posisi === false ? 99 : $posisi;
            })
            ->values();

        return view('admin.jadwal_sholat.index', compact('jadwal'));
    }

    /**
     * Hanya jam_mulai dan batas_tepat_waktu yang bisa diubah.
     * nama_sholat sengaja tidak divalidasi/diupdate dari sini karena
     * data jadwal sholat bersifat master data tetap (5 waktu wajib).
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'batas_tepat_waktu' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal = JadwalSholat::findOrFail($id);
        $jadwal->update($validated);

        return back()->with('success', 'Jadwal ' . $jadwal->nama_sholat . ' berhasil diperbarui.');
    }
}