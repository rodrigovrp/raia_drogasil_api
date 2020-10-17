<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FundsProductExport implements FromCollection, WithColumnFormatting, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'ID do Produto',
            'Nome do Produto',
            'Ano',
            'Valor',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            (($product->name) ?? '' ),
            (($product->fund->year) ?? $this->request->year ),
            (($product->fund->amount) ?? '0.00' ),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '0',
            'D' => '"R$ "#,##0.00_-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 13]],
        ];
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return 'Fundos por Produto';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request = $this->request;
        return Product::with(['fund' => function($query) use($request) {
            $query->where('year', $request->year);
        }])->get();
    }
}
