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
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // STATUS FILTER
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $procurements = $query
            ->orderBy('id', 'desc')
            ->get();

        // TOTAL CARD (using class constants)
        $totalRP = Procurement::where('status', Procurement::STATUS_RP)->count();
        $totalTE = Procurement::where('status', Procurement::STATUS_TE)->count();
        $totalRETE = Procurement::where('status', Procurement::STATUS_RETE)->count();
        $totalPO = Procurement::where('status', Procurement::STATUS_PO)->count();

        return Inertia::render('Dashboard/Index', [
            'procurements' => $procurements,
            'totalRP'      => $totalRP,
            'totalTE'      => $totalTE,
            'totalRETE'    => $totalRETE,
            'totalPO'      => $totalPO,
            'filters'      => $request->only(['search', 'status']),
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
            'status'                             => 'required|in:RP,TE,RE-TE,PO',
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

        Procurement::create($request->all());

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
            'status'                             => 'required|in:RP,TE,RE-TE,PO',
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

        $procurement->update($request->all());

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
        $currentStatus = $procurement->status;
        $nowStr = now()->format('l, F j, Y');

        if ($currentStatus === Procurement::STATUS_RP) {
            $procurement->status = Procurement::STATUS_TE;
            if (empty($procurement->te_in)) {
                $procurement->te_in = $nowStr;
            }
        } elseif ($currentStatus === Procurement::STATUS_TE) {
            $target = $request->input('target', 'RE-TE');
            if ($target === 'RE-TE') {
                $procurement->status = Procurement::STATUS_RETE;
                if (empty($procurement->re_te)) {
                    $procurement->re_te = $nowStr;
                }
            } else {
                $procurement->status = Procurement::STATUS_PO;
                if (empty($procurement->po)) {
                    $procurement->po = $nowStr;
                }
            }
        } elseif ($currentStatus === Procurement::STATUS_RETE) {
            $procurement->status = Procurement::STATUS_PO;
            if (empty($procurement->po)) {
                $procurement->po = $nowStr;
            }
        }

        $procurement->save();

        return redirect()->to('/dashboard')
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
        $currentStatus = $procurement->status;

        if ($currentStatus === Procurement::STATUS_TE) {
            $procurement->status = Procurement::STATUS_RP;
        } elseif ($currentStatus === Procurement::STATUS_RETE) {
            $procurement->status = Procurement::STATUS_TE;
        } elseif ($currentStatus === Procurement::STATUS_PO) {
            $procurement->status = Procurement::STATUS_RETE;
        }

        $procurement->save();

        return redirect()->to('/dashboard')
            ->with('success', 'Fase berhasil ditolak (Rejected)!');
    }
}