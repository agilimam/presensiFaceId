<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSholat;

class JadwalSholatController extends Controller
{
    
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

   public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
            'batas_tepat_waktu' => 'required|date_format:H:i',
        ]);

        $jadwal = JadwalSholat::findOrFail($id);
        if (
            strtolower($jadwal->nama_sholat) != 'isya' &&
            strtotime($validated['batas_tepat_waktu']) <= strtotime($validated['jam_mulai'])
        ) {
            return back()->withErrors([
                'batas_tepat_waktu' => 'Batas tepat waktu harus setelah jam mulai.'
            ])->withInput();
        }
        $jadwal->update([
            'jam_mulai' => $validated['jam_mulai'],
            'batas_tepat_waktu' => $validated['batas_tepat_waktu'],
        ]);

        return back()->with('success', 'Jadwal ' . $jadwal->nama_sholat . ' berhasil diperbarui.');
    }
}