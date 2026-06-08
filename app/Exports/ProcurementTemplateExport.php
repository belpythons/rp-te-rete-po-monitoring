<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProcurementTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * Column headings matching the import format.
     */
    public function headings(): array
    {
        return [
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
        ];
    }

    /**
     * Example and guide rows.
     */
    public function array(): array
    {
        return [
            // Row 1: Example data
            [
                '1',
                '1000003892',
                'IT Peripheral',
                'Wednesday, April 23, 2025',
                'Tuesday, May 27, 2025',
                'Tuesday, May 27, 2025',
                'Wednesday, April 23, 2025',
                '',
                'SU',
                'Monday, June 16, 2025',
                '',
                'Monday, July 7, 2025',
                'Tuesday, July 15, 2025',
                'Monday, July 7, 2025',
                'PT Alpha Cipta',
            ],
            // Row 2: Format guides
            [
                '(Wajib, misal: 1)',
                '(Wajib, unik, misal: 1000003892)',
                '(Wajib, teks)',
                '(Wajib, teks tanggal)',
                '(Opsional, teks tanggal)',
                '(Opsional, teks tanggal)',
                '(Opsional, teks tanggal)',
                '(Opsional, teks tanggal)',
                '(Opsional, inisial)',
                '(Opsional, teks tanggal)',
                '(Opsional, teks tanggal)',
                '(Opsional, teks tanggal/no)',
                '(Opsional, teks tanggal)',
                '(Opsional, teks tanggal)',
                '(Opsional, nama vendor)',
            ],
        ];
    }

    /**
     * Style the template worksheet.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row: dark blue background, white bold text
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2F3F52'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Example data row: light green background
            2 => [
                'font' => ['size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9'],
                ],
            ],
            // Guide row: light yellow background, italic
            3 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '666666'], 'size' => 10],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF9C4'],
                ],
            ],
        ];
    }
}
