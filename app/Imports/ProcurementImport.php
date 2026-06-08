<?php

namespace App\Imports;

use App\Models\Procurement;
use App\Models\ImportLog;
use App\Events\ImportProgressEvent;
use App\Events\ImportCompletedEvent;
use App\Exports\FailedRowsExport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterChunk;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProcurementImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithValidation,
    WithUpserts,
    WithBatchInserts,
    WithEvents,
    SkipsOnError,
    SkipsOnFailure,
    ShouldQueue
{
    /**
     * ImportLog ID for tracking progress.
     */
    public int $importLogId;

    /**
     * Counter for rows processed within current chunk.
     */
    private int $chunkRowCount = 0;

    public function __construct(int $importLogId)
    {
        $this->importLogId = $importLogId;
    }

    /**
     * Intercept raw Excel numerical dates before validation rules are applied.
     */
    public function prepareForValidation(array $data, int $index): array
    {
        $dateFields = [
            'date_created',
            'te_in',
            'te_out',
            'send_for_approval_general_director',
            're_te',
            'po',
            'delivery',
            'rr'
        ];

        foreach ($dateFields as $field) {
            if (isset($data[$field]) && is_numeric($data[$field])) {
                $data[$field] = Date::excelToDateTimeObject($data[$field])->format('Y-m-d');
            }
        }
        return $data;
    }

    /**
     * Map each Excel row to a Procurement model.
     */
    public function model(array $row): Procurement
    {
        $this->chunkRowCount++;

        $dateCreated = $row['date_created'] ?: null;
        if (is_numeric($dateCreated)) {
            $dateCreated = Date::excelToDateTimeObject($dateCreated)->format('Y-m-d');
        }

        $teIn = $row['te_in'] ?: null;
        if (is_numeric($teIn)) {
            $teIn = Date::excelToDateTimeObject($teIn)->format('Y-m-d');
        }

        $teOut = $row['te_out'] ?: null;
        if (is_numeric($teOut)) {
            $teOut = Date::excelToDateTimeObject($teOut)->format('Y-m-d');
        }

        $sendGen = $row['send_for_approval_general_director'] ?: null;
        if (is_numeric($sendGen)) {
            $sendGen = Date::excelToDateTimeObject($sendGen)->format('Y-m-d');
        }

        $reTe = $row['re_te'] ?: null;
        if (is_numeric($reTe)) {
            $reTe = Date::excelToDateTimeObject($reTe)->format('Y-m-d');
        }

        $po = $row['po'] ?: null;
        if (is_numeric($po)) {
            $po = Date::excelToDateTimeObject($po)->format('Y-m-d');
        }

        $delivery = $row['delivery'] ?: null;
        if (is_numeric($delivery)) {
            $delivery = Date::excelToDateTimeObject($delivery)->format('Y-m-d');
        }

        $rr = $row['rr'] ?: null;
        if (is_numeric($rr)) {
            $rr = Date::excelToDateTimeObject($rr)->format('Y-m-d');
        }

        $so = $row['so'] ?: null;
        if (is_numeric($so)) {
            $so = Date::excelToDateTimeObject($so)->format('Y-m-d');
        }

        // Compute status based on fields presence
        if ($po || $so || $delivery || $rr) {
            $status = 'PO';
        } elseif ($reTe) {
            $status = 'RE-TE';
        } elseif ($teIn || $teOut) {
            $status = 'TE';
        } else {
            $status = 'RP';
        }

        return new Procurement([
            'no'                                 => (string)$row['no'],
            'rp_number'                          => (string)$row['rp'],
            'description'                        => (string)$row['description'],
            'date_created'                       => $dateCreated,
            'send_for_approval_general_director' => $sendGen,
            'buyer'                              => $row['buyer'] ?: null,
            'te_in'                              => $teIn,
            'te_out'                             => $teOut,
            're_te'                              => $reTe,
            'po'                                 => $po,
            'vendor'                             => $row['vendor'] ?: null,
            'delivery'                           => $delivery,
            'so'                                 => $so,
            'qc'                                 => $row['qc'] ?: null,
            'rr'                                 => $rr,
            'status'                             => $status,
        ]);
    }

    /**
     * Unique key for upsert — prevents duplicate rp_number.
     */
    public function uniqueBy(): string
    {
        return 'rp_number';
    }

    /**
     * Batch insert size for efficiency.
     */
    public function batchSize(): int
    {
        return 500;
    }

    /**
     * Chunk reading size.
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'no'          => ['required'],
            'rp'          => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages (Indonesian).
     */
    public function customValidationMessages(): array
    {
        return [
            'no.required'          => 'Nomor baris (No) wajib diisi.',
            'rp.required'          => 'Kolom RP (Kode Pengadaan) wajib diisi.',
            'description.required' => 'Description (Deskripsi) wajib diisi.',
        ];
    }

    /**
     * Handle validation failures — persist to JSONL file.
     */
    public function onFailure(Failure ...$failures): void
    {
        $lines = '';
        foreach ($failures as $failure) {
            $lines .= json_encode([
                'row'       => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors'    => $failure->errors(),
                'values'    => $failure->values(),
            ]) . "\n";
        }

        $path = $this->getFailuresFilePath();
        file_put_contents($path, $lines, FILE_APPEND | LOCK_EX);

        ImportLog::where('id', $this->importLogId)
            ->increment('failure_count', count($failures));
    }

    /**
     * Handle model/database errors.
     */
    public function onError(Throwable $e): void
    {
        $line = json_encode([
            'row'       => null,
            'attribute' => 'database',
            'errors'    => [$e->getMessage()],
            'values'    => [],
        ]) . "\n";

        $path = $this->getFailuresFilePath();
        file_put_contents($path, $line, FILE_APPEND | LOCK_EX);

        ImportLog::where('id', $this->importLogId)
            ->increment('failure_count');

        Log::error("ProcurementImport error on ImportLog #{$this->importLogId}: {$e->getMessage()}");
    }

    /**
     * Register import lifecycle events.
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $firstSheetRows = count($totalRows) > 0 ? reset($totalRows) : 0;
                $total = max(0, $firstSheetRows - 1);

                ImportLog::where('id', $this->importLogId)
                    ->update(['total_rows' => $total]);
            },

            AfterChunk::class => function (AfterChunk $event) {
                $importLog = ImportLog::find($this->importLogId);
                if ($importLog) {
                    $processed = min($importLog->total_rows, $importLog->processed_rows + $this->chunkRowCount);
                    $importLog->update(['processed_rows' => $processed]);

                    try {
                        event(new ImportProgressEvent(
                            $this->importLogId,
                            $processed,
                            $importLog->total_rows
                        ));
                    } catch (Throwable $e) {
                        Log::warning("Could not broadcast ImportProgressEvent: {$e->getMessage()}");
                    }
                }

                $this->chunkRowCount = 0;
            },

            AfterImport::class => function (AfterImport $event) {
                $this->handleImportCompletion();
            },
        ];
    }

    /**
     * After all chunks complete.
     */
    private function handleImportCompletion(): void
    {
        $importLog = ImportLog::find($this->importLogId);
        if (!$importLog) {
            return;
        }

        $failures = $this->readFailuresFromFile();
        $failureCount = count($failures);
        $successCount = max(0, $importLog->total_rows - $failureCount);

        $errorFilePath = null;
        if ($failureCount > 0) {
            $errorFileName = "imports/error_log_{$this->importLogId}.xlsx";
            Excel::store(new FailedRowsExport($failures), $errorFileName, 'local');
            $errorFilePath = $errorFileName;
        }

        $importLog->update(['processed_rows' => $importLog->total_rows]);
        $importLog->markCompleted($successCount, $failureCount, $errorFilePath);

        try {
            event(new ImportCompletedEvent(
                $this->importLogId,
                $successCount,
                $failureCount,
                $errorFilePath
            ));
        } catch (Throwable $e) {
            Log::warning("Could not broadcast ImportCompletedEvent: {$e->getMessage()}");
        }

        $tempPath = $this->getFailuresFilePath();
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }

    private function getFailuresFilePath(): string
    {
        $dir = storage_path('app/imports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . "/failures_{$this->importLogId}.jsonl";
    }

    private function readFailuresFromFile(): array
    {
        $path = $this->getFailuresFilePath();
        if (!file_exists($path)) {
            return [];
        }

        $failures = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded) {
                $failures[] = $decoded;
            }
        }

        return $failures;
    }
}
