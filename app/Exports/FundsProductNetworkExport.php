<?php

namespace App\Exports;

use App\Models\ActionType;
use App\Models\Networks;
use App\Models\Product;
use App\Models\ProductFundsNetwork;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FundsProductNetworkExport implements FromCollection, WithColumnFormatting, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'ID da Ação',
            'Nome da Ação',
            'ID da Rede',
            'Nome da Rede',
            'ID do Produto',
            'Nome do Produto',
            'Ano',
            'Valor',
        ];
    }

    public function map($product): array
    {
        $product = json_decode(json_encode($product));
        return [
            (($product->action_id) ?? '' ),
            (($product->action_name) ?? '' ),
            (($product->network_id) ?? '' ),
            (($product->network_name) ?? '' ),
            (($product->product_id) ?? '' ),
            (($product->product_name) ?? '' ),
            (($product->year) ?? '' ),
            (($product->amount) ?? '' ),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '0',
            'H' => '"R$ "#,##0.00_-',
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
        return 'Fundos por Rede';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request = $this->request;
        $collection = [];
        foreach(ActionType::get() as $action){
            foreach(Networks::get() as $network){
               foreach(Product::get() as $prod){
                $fund = ProductFundsNetwork::where('product_id', $prod->id)->where('network_id', $network->id)->where('action_type_id', $action->id)->where('year', $request->year)->first();
                $collection[] = [
                    "product_id" => $prod->id,
                    "product_name" => $prod->name,
                    "action_id" => $action->id,
                    "action_name" => $action->name,
                    "network_id" => $network->id,
                    "network_name" => $network->name,
                    "year" => $request->year,
                    "amount" => (($fund->amount) ?? '0.00'),
                ];
               }
            }
        }
        return collect($collection);
    }
}
