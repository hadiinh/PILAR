<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiLog;
use App\Services\FonnteService;
use Illuminate\Http\Request;

class NotifikasiLogController extends Controller
{
    public function __construct(protected FonnteService $fonnte) {}

    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $jenis  = $request->string('jenis')->toString();
        $q      = trim((string) $request->get('q'));

        $query = NotifikasiLog::with('user')->latest();

        if (in_array($status, ['terkirim', 'gagal'])) {
            $query->where('status', $status);
        }
        if ($jenis !== '') {
            $query->where('jenis', $jenis);
        }
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('nomor_tujuan', 'like', "%{$q}%")
                   ->orWhere('pesan', 'like', "%{$q}%")
                   ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        $items = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => NotifikasiLog::count(),
            'terkirim' => NotifikasiLog::where('status', 'terkirim')->count(),
            'gagal'    => NotifikasiLog::where('status', 'gagal')->count(),
        ];

        $jenisList = NotifikasiLog::query()->distinct()->pluck('jenis');

        return view('notifikasi.index', compact('items', 'stats', 'status', 'jenis', 'q', 'jenisList'));
    }

    public function retry(NotifikasiLog $log)
    {
        $ok = $this->fonnte->retry($log);
        return back()->with($ok ? 'success' : 'error',
            $ok ? 'Pesan berhasil dikirim ulang.' : 'Pesan gagal dikirim. Cek detail log terbaru.');
    }
}
