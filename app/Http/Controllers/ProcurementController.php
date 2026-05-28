<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Procurement;

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
                $q->where('kode_pengadaan', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
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

        return view('dashboard', compact(
            'procurements',
            'totalRP',
            'totalTE',
            'totalRETE',
            'totalPO'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('procurement.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'kode_pengadaan' => 'required|unique:procurements,kode_pengadaan',
            'nama_barang'    => 'required',
            'vendor'         => 'required',
            'tanggal_in'     => 'nullable|date',
            'tanggal_out'    => 'nullable|date',
            'tanggal_te'     => 'nullable|date',
            'tanggal_rete'   => 'nullable|date',
            'tanggal_po'     => 'nullable|date',
        ]);

        Procurement::create([
            'kode_pengadaan' => $request->kode_pengadaan,
            'nama_barang'    => $request->nama_barang,
            'vendor'         => $request->vendor,
            'tanggal_in'     => $request->tanggal_in,
            'tanggal_out'    => $request->tanggal_out,
            'tanggal_te'     => $request->tanggal_te,
            'tanggal_rete'   => $request->tanggal_rete,
            'tanggal_po'     => $request->tanggal_po,
        ]);

        return redirect('/dashboard')
            ->with('success', 'Data berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_pengadaan' => 'required|unique:procurements,kode_pengadaan,' . $id,
            'nama_barang'    => 'required',
            'vendor'         => 'required',
        ]);

        $procurement = Procurement::findOrFail($id);

        $procurement->update([
            'kode_pengadaan' => $request->kode_pengadaan,
            'nama_barang'    => $request->nama_barang,
            'vendor'         => $request->vendor,
        ]);

        return redirect('/dashboard')
            ->with('success', 'Data berhasil diupdate');
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

        return redirect('/dashboard')
            ->with('success', 'Data berhasil dihapus');
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

        // Auto set tanggal_out
        $procurement->tanggal_out = now();

        if ($currentStatus === Procurement::STATUS_RP) {
            $procurement->tanggal_te = now();
        } elseif ($currentStatus === Procurement::STATUS_TE) {
            $target = $request->input('target', 'RE-TE');
            if ($target === 'RE-TE') {
                $procurement->tanggal_rete = now();
            } else {
                $procurement->tanggal_po = now();
            }
        } elseif ($currentStatus === Procurement::STATUS_RETE) {
            $procurement->tanggal_po = now();
        }

        $procurement->save();

        return redirect()->route('dashboard', ['status' => $procurement->status])
            ->with('success', 'Berhasil di-approve!');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT PHASE (AUTOMATION)
    |--------------------------------------------------------------------------
    |*/
    public function rejectPhase(Request $request, $id)
    {
        $procurement = Procurement::findOrFail($id);
        $currentStatus = $procurement->status;

        if ($currentStatus === Procurement::STATUS_TE) {
            $procurement->tanggal_te = null;
        } elseif ($currentStatus === Procurement::STATUS_RETE) {
            $procurement->tanggal_rete = null;
        } elseif ($currentStatus === Procurement::STATUS_PO) {
            $procurement->tanggal_po = null;
        }

        $procurement->save();

        return redirect()->route('dashboard', ['status' => $procurement->status])
            ->with('success', 'Data dikembalikan!');
    }
}