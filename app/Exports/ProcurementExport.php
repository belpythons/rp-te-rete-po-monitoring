<?php

namespace App\Exports;

use App\Models\Procurement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class ProcurementExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Query all procurement data ordered by latest.
     */
    public function collection(): Enumerable
    {
        return Procurement::orderBy('id', 'desc')->get();
    }

    /**
     * Column headings for the first row.
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
     * Map each Procurement model to a row.
     */
    public function map($procurement): array
    {
        return [
            $procurement->no,
            $procurement->rp_number,
            $procurement->description,
            $procurement->date_created,
            $procurement->te_in,
            $procurement->te_out,
            $procurement->send_for_approval_general_director,
            $procurement->re_te,
            $procurement->buyer,
            $procurement->po,
            $procurement->so,
            $procurement->qc,
            $procurement->delivery,
            $procurement->rr,
            $procurement->vendor,
        ];
    }

    /**
     * Style the header row.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2F3F52'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
