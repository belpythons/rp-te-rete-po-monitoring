<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestPurchasing;
use App\Models\TechnicalEvaluation;
use App\Models\ReTechnicalEvaluation;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    public function index()
    {
        // ======================
        // TOTAL DATA (Untuk Angka di Card)
        // ======================
        $totalRP = RequestPurchasing::count();
        $totalTE = TechnicalEvaluation::count(); dd($totalTE);
        $totalRETE = ReTechnicalEvaluation::count();
        $totalPO = PurchaseOrder::count();

        $semuaData = $totalRP + $totalTE + $totalRETE + $totalPO;

        // ======================
        // REQUEST PURCHASING
        // ======================
        $rp = RequestPurchasing::select('id', 'kode_rp as kode', 'nama_barang as barang', 'tanggal')
            ->get()
            ->map(function($item){
                return [
                    'id'       => $item->id,
                    'kode'     => $item->kode,
                    'barang'   => $item->barang,
                    'status'   => 'Request Purchasing',
                    'tanggal'  => $item->tanggal,
                    'tipe'     => 'rp'
                ];
            });

        // ======================
        // TECHNICAL EVALUATION
        // ======================
        $te = TechnicalEvaluation::select('id', 'kode_te as kode', 'vendor as barang', 'tanggal')
            ->get()
            ->map(function($item){
                return [
                    'id'       => $item->id,
                    'kode'     => $item->kode,
                    'barang'   => $item->barang,
                    'status'   => 'Technical Evaluation', // <--- DI SINI KUNCINYA!
                    'tanggal'  => $item->tanggal,
                    'tipe'     => 'te'
                ];
            });

        // ======================
        // RE-TECHNICAL EVALUATION
        // ======================
        $rete = ReTechnicalEvaluation::select('id', 'kode_rete as kode', 'vendor as barang', 'tanggal')
            ->get()
            ->map(function($item){
                return [
                    'id'       => $item->id,
                    'kode'     => $item->kode,
                    'barang'   => $item->barang,
                    'status'   => 'Re-Technical Evaluation',
                    'tanggal'  => $item->tanggal,
                    'tipe'     => 'rete'
                ];
            });

        // ======================
        // PURCHASE ORDER
        // ======================
        $po = PurchaseOrder::select('id', 'kode_po as kode', 'vendor as barang', 'tanggal')
            ->get()
            ->map(function($item){
                return [
                    'id'       => $item->id,
                    'kode'     => $item->kode,
                    'barang'   => $item->barang,
                    'status'   => 'Purchase Order',
                    'tanggal'  => $item->tanggal,
                    'tipe'     => 'po'
                ];
            });

        // ======================
        // GABUNGKAN SEMUA DATA
        // ======================
        $procurements = collect()
            ->merge($rp)
            ->merge($te)
            ->merge($rete)
            ->merge($po)
            ->sortByDesc('tanggal')
            ->values();

        return view('dashboard', compact(
            'procurements',
            'semuaData',
            'totalRP',
            'totalTE',
            'totalRETE',
            'totalPO'
        ));
    }

    // ======================
    // FUNGSI UPDATE DATA (DARI MODAL EDIT)
    // ======================
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required',
            'barang' => 'required',
            'status' => 'required',
            'tanggal' => 'required|date',
        ]);

        // Cek status baru untuk menentukan tabel mana yang harus diupdate
        if ($request->status == 'Request Purchasing') {
            $data = RequestPurchasing::findOrFail($id);
            $data->update([
                'kode_rp' => $request->kode,
                'nama_barang' => $request->barang,
                'tanggal' => $request->tanggal
            ]);
        } 
        elseif ($request->status == 'Technical Evaluation') {
            $data = TechnicalEvaluation::findOrFail($id);
            $data->update([
                'kode_te' => $request->kode,
                'vendor' => $request->barang,
                'tanggal' => $request->tanggal
            ]);
        } 
        elseif ($request->status == 'Re-Technical Evaluation') {
            $data = ReTechnicalEvaluation::findOrFail($id);
            $data->update([
                'kode_rete' => $request->kode,
                'vendor' => $request->barang,
                'tanggal' => $request->tanggal
            ]);
        } 
        elseif ($request->status == 'Purchase Order') {
            $data = PurchaseOrder::findOrFail($id);
            $data->update([
                'kode_po' => $request->kode,
                'vendor' => $request->barang,
                'tanggal' => $request->tanggal
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Data Procurement berhasil diperbarui!');
    }

    // ======================
    // FUNGSI HAPUS DATA
    // ======================
    public function destroy($id)
    {
        // Cari dan hapus data di salah satu tabel tempat ID tersebut berada
        $deleted = RequestPurchasing::where('id', $id)->delete();
        
        if (!$deleted) {
            $deleted = TechnicalEvaluation::where('id', $id)->delete();
        }
        if (!$deleted) {
            $deleted = ReTechnicalEvaluation::where('id', $id)->delete();
        }
        if (!$deleted) {
            $deleted = PurchaseOrder::where('id', $id)->delete();
        }

        return redirect()->route('dashboard')->with('success', 'Data Procurement berhasil dihapus!');
    }
}