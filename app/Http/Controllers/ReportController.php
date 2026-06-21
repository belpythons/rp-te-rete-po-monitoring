<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\ImportLog;
use App\Imports\ProcurementImport;
use App\Exports\ProcurementExport;
use App\Exports\ProcurementTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Report index page — rendered via Inertia (Vue).
     * Passes procurements data and import history to the frontend.
     */
    public function index(Request $request)
    {
        $query = Procurement::orderBy('tanggal_masuk', 'asc');

        if ($request->filled('month_year')) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$request->month_year]);
        }

        $procurements = $query->paginate(15)->withQueryString();

        $importLogs = ImportLog::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(fn (ImportLog $log) => [
                'id'             => $log->id,
                'file_name'      => $log->file_name,
                'total_rows'     => $log->total_rows,
                'processed_rows' => $log->processed_rows,
                'success_count'  => $log->success_count,
                'failure_count'  => $log->failure_count,
                'has_error_log'  => $log->hasErrorLog(),
                'status'         => $log->status,
                'created_at'     => $log->created_at->format('d M Y H:i'),
            ]);

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

        // Summary counts for stats cards
        $stats = [
            'total' => Procurement::count(),
            'rp'    => Procurement::where('phase', 'RP')->count(),
            'te'    => Procurement::where('phase', 'TE')->count(),
            'rete'  => Procurement::where('phase', 'RE-TE')->count(),
            'po'    => Procurement::where('phase', 'PO')->count(),
        ];

        return Inertia::render('Report/Index', [
            'procurements'    => $procurements,
            'importLogs'      => $importLogs,
            'stats'           => $stats,
            'availableMonths' => $availableMonths,
            'filters'         => $request->only(['month_year']),
        ]);
    }

    /**
     * Handle Excel file upload and dispatch queued import job.
     * Returns the import_log ID for WebSocket progress tracking.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,xls', 'max:51200'], // max 50MB
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $file = $request->file('file');

        // Create ImportLog record
        $importLog = ImportLog::create([
            'user_id'   => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'status'    => ImportLog::STATUS_PROCESSING,
        ]);

        // Store the uploaded file temporarily
        $storedPath = $file->storeAs(
            'imports',
            "upload_{$importLog->id}." . $file->getClientOriginalExtension(),
            'local'
        );

        // Dispatch queued import job
        $import = new ProcurementImport($importLog->id);
        Excel::queueImport($import, $storedPath, 'local');

        return back()->with('flash', [
            'import_log_id' => $importLog->id,
            'message'       => 'Import sedang diproses di background.',
        ]);
    }

    /**
     * Download empty Excel template with headers and guide row.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new ProcurementTemplateExport(),
            'template_import_procurement.xlsx'
        );
    }

    /**
     * Export all procurement data as Excel file.
     */
    public function exportExcel(Request $request)
    {
        $monthYear = $request->query('month_year');
        $phase = $request->query('phase');
        return Excel::download(
            new ProcurementExport($monthYear, $phase),
            'laporan_procurement_' . ($phase ? strtolower($phase) . '_' : '') . ($monthYear ?: now()->format('Y-m')) . '_' . now()->format('His') . '.xlsx'
        );
    }

    /**
     * Export all procurement data as PDF.
     * Paper: Folio/F4 Landscape — ensures wide table is not clipped.
     */
    public function exportPdf(Request $request)
    {
        $monthYear = $request->query('month_year');
        $phase = $request->query('phase');
        $query = Procurement::orderBy('tanggal_masuk', 'asc');
        if ($monthYear) {
            $query->whereRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?", [$monthYear]);
        }
        if ($phase) {
            $query->where('phase', $phase);
        }
        $procurements = $query->get();

        $pdf = Pdf::loadView('exports.procurement-pdf', [
            'procurements' => $procurements,
            'generatedAt'  => now()->format('d M Y H:i:s'),
        ]);

        // Folio/F4 Landscape: [0, 0, 612.00, 936.00]
        $pdf->setPaper([0, 0, 612.00, 936.00], 'landscape');

        return $pdf->download(
            'laporan_procurement_' . ($phase ? strtolower($phase) . '_' : '') . ($monthYear ?: now()->format('Y-m')) . '_' . now()->format('His') . '.pdf'
        );
    }

    /**
     * Download the error log Excel for a specific import job.
     */
    public function downloadErrorLog(ImportLog $importLog)
    {
        // Authorization: only the owner can download their error log
        if ($importLog->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        if (!$importLog->hasErrorLog()) {
            abort(404, 'Error log tidak tersedia.');
        }

        $path = Storage::disk('local')->path($importLog->error_file_path);

        if (!file_exists($path)) {
            abort(404, 'File error log tidak ditemukan.');
        }

        return response()->download(
            $path,
            "error_log_import_{$importLog->id}.xlsx"
        );
    }
}
