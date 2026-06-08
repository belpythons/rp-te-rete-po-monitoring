<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FailedRowsExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    private array $failures;

    public function __construct(array $failures)
    {
        $this->failures = $failures;
    }

    /**
     * Column headings — mirrors import columns + error reason column.
     */
    public function headings(): array
    {
        return [
            'Baris ke-',
            'No',
            'RP',
            'Description',
            'Date Created',
            'TE In',
            'TE Out',
            'Send for Approval General Director',
            'RE-TE',
            'Buyer',
            'PO',
            'SO',
            'QC',
            'Delivery',
            'RR',
            'Vendor',
            'Alasan Gagal / Error',
        ];
    }

    /**
     * Map failure data to rows.
     */
    public function array(): array
    {
        return array_map(function (array $failure) {
            $values = $failure['values'] ?? [];

            return [
                $failure['row'] ?? '-',
                $values['no'] ?? '-',
                $values['rp'] ?? '-',
                $values['description'] ?? '-',
                $values['date_created'] ?? '-',
                $values['te_in'] ?? '-',
                $values['te_out'] ?? '-',
                $values['send_for_approval_general_director'] ?? '-',
                $values['re_te'] ?? '-',
                $values['buyer'] ?? '-',
                $values['po'] ?? '-',
                $values['so'] ?? '-',
                $values['qc'] ?? '-',
                $values['delivery'] ?? '-',
                $values['rr'] ?? '-',
                $values['vendor'] ?? '-',
                implode('; ', $failure['errors'] ?? ['Unknown error']),
            ];
        }, $this->failures);
    }

    /**
     * Style the error log for readability.
     */
    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->failures) + 1; // +1 for header

        return [
            // Header: red background, white bold text
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC3545'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Error reason column (Q): light red background for emphasis
            "Q2:Q{$lastRow}" => [
                'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFEBEE'],
                ],
            ],
        ];
    }
}
