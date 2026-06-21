<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Procurement;
use Inertia\Inertia;

class ProcurementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | READ DATA
    |--------------------------------------------------------------------------
    |
    */
    public function index(Request $request)
    {
        $query = Procurement::query();

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rp_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('phase', 'like', "%{$search}%");
            });
        }

        // MONTH/YEAR FILTER
        if ($request->filled('month_year')) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$request->month_year]);
        }

        // Limit results to only records with valid status
        $query->whereIn('status', ['Pending', 'Disetujui', 'Tidak Disetujui', 'RP', 'TE', 'RE-TE', 'PO']);

        $procurements = $query
            ->orderBy('tanggal_masuk', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Chart monthly trends
        $allProcurements = Procurement::whereNotNull('tanggal_masuk')->get();
        $monthlyCounts = [];
        foreach ($allProcurements as $p) {
            $ym = \Carbon\Carbon::parse($p->tanggal_masuk)->format('Y-m');
            $monthlyCounts[$ym] = ($monthlyCounts[$ym] ?? 0) + 1;
        }
        ksort($monthlyCounts);

        $chartCategories = [];
        $chartSeries = [];
        foreach ($monthlyCounts as $ym => $count) {
            $chartCategories[] = \Carbon\Carbon::createFromFormat('Y-m', $ym)->translatedFormat('F Y');
            $chartSeries[] = $count;
        }

        $chartData = [
            'categories' => $chartCategories,
            'series' => $chartSeries,
        ];

        // Available Months filter dropdown options
        $availableMonths = Procurement::whereNotNull('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk')
            ->map(function ($date) {
                $carbon = \Carbon\Carbon::parse($date);
                return [
                    'value' => $carbon->format('Y-m'),
                    'label' => $carbon->translatedFormat('F Y'),
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        // Total stats for the overview
        $totalRP = Procurement::where('phase', 'RP')->count();
        $totalTE = Procurement::where('phase', 'TE')->count();
        $totalRETE = Procurement::where('phase', 'RE-TE')->count();
        $totalPO = Procurement::where('phase', 'PO')->count();

        return Inertia::render('Dashboard/Index', [
            'procurements'    => $procurements,
            'totalRP'         => $totalRP,
            'totalTE'         => $totalTE,
            'totalRETE'       => $totalRETE,
            'totalPO'         => $totalPO,
            'chartData'       => $chartData,
            'availableMonths' => $availableMonths,
            'filters'         => $request->only(['search', 'month_year']),
        ]);
    }

    /**
     * Request Purchasing phase page.
     */
    public function requestPurchasing(Request $request)
    {
        $query = Procurement::where('phase', 'RP');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rp_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month_year')) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$request->month_year]);
        }

        $query->whereIn('status', ['Pending', 'Disetujui', 'Tidak Disetujui', 'RP', 'TE', 'RE-TE', 'PO']);

        $procurements = $query
            ->orderBy('tanggal_masuk', 'asc')
            ->paginate(15)
            ->withQueryString();

        $availableMonths = Procurement::where('phase', 'RP')
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk')
            ->map(function ($date) {
                $carbon = \Carbon\Carbon::parse($date);
                return [
                    'value' => $carbon->format('Y-m'),
                    'label' => $carbon->translatedFormat('F Y'),
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        $totalRP = Procurement::where('phase', 'RP')->count();

        return Inertia::render('RequestPurchasing/Index', [
            'procurements'    => $procurements,
            'totalRP'         => $totalRP,
            'availableMonths' => $availableMonths,
            'filters'         => $request->only(['search', 'month_year']),
        ]);
    }

    /**
     * Technical Evaluation phase page.
     */
    public function technicalEvaluation(Request $request)
    {
        $query = Procurement::where('phase', 'TE');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rp_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month_year')) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$request->month_year]);
        }

        $query->whereIn('status', ['Pending', 'Disetujui', 'Tidak Disetujui', 'RP', 'TE', 'RE-TE', 'PO']);

        $procurements = $query
            ->orderBy('tanggal_masuk', 'asc')
            ->paginate(15)
            ->withQueryString();

        $availableMonths = Procurement::where('phase', 'TE')
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk')
            ->map(function ($date) {
                $carbon = \Carbon\Carbon::parse($date);
                return [
                    'value' => $carbon->format('Y-m'),
                    'label' => $carbon->translatedFormat('F Y'),
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        $totalTE = Procurement::where('phase', 'TE')->count();

        return Inertia::render('TechnicalEvaluation/Index', [
            'procurements'    => $procurements,
            'totalTE'         => $totalTE,
            'availableMonths' => $availableMonths,
            'filters'         => $request->only(['search', 'month_year']),
        ]);
    }

    /**
     * Re-Technical Evaluation phase page.
     */
    public function reTechnicalEvaluation(Request $request)
    {
        $query = Procurement::where('phase', 'RE-TE');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rp_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month_year')) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$request->month_year]);
        }

        $query->whereIn('status', ['Pending', 'Disetujui', 'Tidak Disetujui', 'RP', 'TE', 'RE-TE', 'PO']);

        $procurements = $query
            ->orderBy('tanggal_masuk', 'asc')
            ->paginate(15)
            ->withQueryString();

        $availableMonths = Procurement::where('phase', 'RE-TE')
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk')
            ->map(function ($date) {
                $carbon = \Carbon\Carbon::parse($date);
                return [
                    'value' => $carbon->format('Y-m'),
                    'label' => $carbon->translatedFormat('F Y'),
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        $totalRETE = Procurement::where('phase', 'RE-TE')->count();

        return Inertia::render('ReTechnicalEvaluation/Index', [
            'procurements'    => $procurements,
            'totalRETE'       => $totalRETE,
            'availableMonths' => $availableMonths,
            'filters'         => $request->only(['search', 'month_year']),
        ]);
    }

    /**
     * Purchase Order phase page.
     */
    public function purchaseOrder(Request $request)
    {
        $query = Procurement::where('phase', 'PO');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rp_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month_year')) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$request->month_year]);
        }

        $query->whereIn('status', ['Pending', 'Disetujui', 'Tidak Disetujui', 'RP', 'TE', 'RE-TE', 'PO']);

        $procurements = $query
            ->orderBy('tanggal_masuk', 'asc')
            ->paginate(15)
            ->withQueryString();

        $availableMonths = Procurement::where('phase', 'PO')
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tanggal_masuk', 'desc')
            ->pluck('tanggal_masuk')
            ->map(function ($date) {
                $carbon = \Carbon\Carbon::parse($date);
                return [
                    'value' => $carbon->format('Y-m'),
                    'label' => $carbon->translatedFormat('F Y'),
                ];
            })
            ->unique('value')
            ->values()
            ->toArray();

        $totalPO = Procurement::where('phase', 'PO')->count();

        return Inertia::render('PurchaseOrder/Index', [
            'procurements'    => $procurements,
            'totalPO'         => $totalPO,
            'availableMonths' => $availableMonths,
            'filters'         => $request->only(['search', 'month_year']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return Inertia::render('Dashboard/Create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $rules = [
            'no'                                 => 'required|string|max:255',
            'rp_number'                          => 'required|string|max:255|unique:procurements,rp_number',
            'description'                        => 'required|string',
            'date_created'                       => 'required|string',
            'status'                             => 'required|in:Pending,Disetujui,Tidak Disetujui,RP,TE,RE-TE,PO',
            'phase'                              => 'required|in:RP,TE,RE-TE,PO',
            'send_for_approval_general_director' => 'nullable|string|max:255',
            'buyer'                              => 'nullable|string|max:255',
            'te_in'                              => 'nullable|string|max:255',
            'te_out'                             => 'nullable|string|max:255',
            're_te'                              => 'nullable|string|max:255',
            'po'                                 => 'nullable|string|max:255',
            'vendor'                             => 'nullable|string|max:255',
            'delivery'                           => 'nullable|string|max:255',
            'so'                                 => 'nullable|string|max:255',
            'qc'                                 => 'nullable|string|max:255',
            'rr'                                 => 'nullable|string|max:255',
        ];

        $request->validate($rules);

        $data = $request->all();
        if ($request->filled('date_created')) {
            $parsed = Procurement::parseDateString($request->date_created);
            if ($parsed) {
                $data['tanggal_masuk'] = $parsed->format('Y-m-d');
            }
        }

        Procurement::create($data);

        return redirect()->to('/dashboard')
            ->with('success', 'Data procurement berhasil ditambahkan!');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $procurement = Procurement::findOrFail($id);

        $rules = [
            'no'                                 => 'required|string|max:255',
            'rp_number'                          => 'required|string|max:255|unique:procurements,rp_number,' . $id,
            'description'                        => 'required|string',
            'date_created'                       => 'required|string',
            'status'                             => 'required|in:Pending,Disetujui,Tidak Disetujui,RP,TE,RE-TE,PO',
            'phase'                              => 'required|in:RP,TE,RE-TE,PO',
            'send_for_approval_general_director' => 'nullable|string|max:255',
            'buyer'                              => 'nullable|string|max:255',
            'te_in'                              => 'nullable|string|max:255',
            'te_out'                             => 'nullable|string|max:255',
            're_te'                              => 'nullable|string|max:255',
            'po'                                 => 'nullable|string|max:255',
            'vendor'                             => 'nullable|string|max:255',
            'delivery'                           => 'nullable|string|max:255',
            'so'                                 => 'nullable|string|max:255',
            'qc'                                 => 'nullable|string|max:255',
            'rr'                                 => 'nullable|string|max:255',
        ];

        $request->validate($rules);

        $data = $request->all();
        if ($request->filled('date_created')) {
            $parsed = Procurement::parseDateString($request->date_created);
            if ($parsed) {
                $data['tanggal_masuk'] = $parsed->format('Y-m-d');
            }
        }

        $procurement->update($data);

        return redirect()->to('/dashboard')
            ->with('success', 'Data procurement berhasil diperbarui!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $procurement = Procurement::findOrFail($id);
        $procurement->delete();

        return redirect()->to('/dashboard')
            ->with('success', 'Data procurement berhasil dihapus!');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE PHASE (AUTOMATION)
    |--------------------------------------------------------------------------
    */
    public function approvePhase(Request $request, $id)
    {
        $procurement = Procurement::findOrFail($id);
        $currentPhase = $procurement->phase;
        $nowStr = now()->format('l, F j, Y');

        if ($currentPhase === Procurement::PHASE_RP) {
            $procurement->phase = Procurement::PHASE_TE;
            if (empty($procurement->te_in)) {
                $procurement->te_in = $nowStr;
            }
        } elseif ($currentPhase === Procurement::PHASE_TE) {
            $target = $request->input('target', 'RE-TE');
            if ($target === 'RE-TE') {
                $procurement->phase = Procurement::PHASE_RETE;
                if (empty($procurement->re_te)) {
                    $procurement->re_te = $nowStr;
                }
            } else {
                $procurement->phase = Procurement::PHASE_PO;
                if (empty($procurement->po)) {
                    $procurement->po = $nowStr;
                }
            }
        } elseif ($currentPhase === Procurement::PHASE_RETE) {
            $procurement->phase = Procurement::PHASE_PO;
            if (empty($procurement->po)) {
                $procurement->po = $nowStr;
            }
        }

        $procurement->save();

        return redirect()->back()
            ->with('success', 'Fase berhasil disetujui (Approved)!');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT PHASE (AUTOMATION)
    |--------------------------------------------------------------------------
    */
    public function rejectPhase(Request $request, $id)
    {
        $procurement = Procurement::findOrFail($id);
        $currentPhase = $procurement->phase;

        if ($currentPhase === Procurement::PHASE_TE) {
            $procurement->phase = Procurement::PHASE_RP;
        } elseif ($currentPhase === Procurement::PHASE_RETE) {
            $procurement->phase = Procurement::PHASE_TE;
        } elseif ($currentPhase === Procurement::PHASE_PO) {
            $procurement->phase = Procurement::PHASE_RETE;
        }

        $procurement->save();

        return redirect()->back()
            ->with('success', 'Fase berhasil ditolak (Rejected)!');
    }
}