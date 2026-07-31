<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogPemeliharaanController extends Controller
{
    public function index()
    {
        $logList = LogPemeliharaan::with(['alkes.ruangan'])
            ->latest()
            ->paginate(30);

        return view('pemeliharaan.index', compact('logList'));
    }

    public function create(Request $request)
    {
        $selectedAlkesId = $request->query('alkes_id');
        $alkesList = Alkes::with(['ruangan'])->get();

        return view('pemeliharaan.create', compact('alkesList', 'selectedAlkesId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alkes_id' => 'required|exists:alkes,id',
            'jenis_tindakan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'pelaksana_vendor' => 'nullable|string',
            'deskripsi_kerusakan' => 'nullable|string',
            'tindakan_perbaikan' => 'nullable|string',
            'biaya' => 'nullable|numeric',
            'status_hasil' => 'required|string',
            'update_status_alkes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $alkes = Alkes::findOrFail($validated['alkes_id']);

            LogPemeliharaan::create([
                'alkes_id' => $alkes->id,
                'jenis_tindakan' => $validated['jenis_tindakan'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'pelaksana_vendor' => $validated['pelaksana_vendor'] ?? 'Tim Bio-Medis RS',
                'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                'tindakan_perbaikan' => $validated['tindakan_perbaikan'],
                'biaya' => $validated['biaya'] ?? 0,
                'status_hasil' => $validated['status_hasil'],
            ]);

            if ($validated['status_hasil'] === 'Selesai') {
                $alkes->update([
                    'status' => StatusAlkes::TERSEDIA->value,
                    'kondisi' => KondisiAlkes::BAIK->value,
                ]);
            } elseif ($validated['status_hasil'] === 'Proses') {
                $alkes->update([
                    'status' => StatusAlkes::DALAM_PERBAIKAN->value,
                ]);
            }

            // Automatic Audit Trail Logging
            ActivityLog::record(
                'Lapor Perbaikan',
                "Melaporkan tindakan {$validated['jenis_tindakan']} untuk '{$alkes->nama_barang}' ({$alkes->kode_inventaris}).",
                $alkes->ruangan->nama_ruangan ?? 'RS'
            );
        });

        return redirect()->route('pemeliharaan.index')->with('success', 'Catatan pemeliharaan/perbaikan berhasil disimpan!');
    }
}
