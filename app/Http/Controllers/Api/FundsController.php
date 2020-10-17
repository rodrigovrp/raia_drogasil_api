<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\FundResource;
use App\Http\Resources\FundCollectionResource;
use App\Http\Controllers\Controller;
use App\Models\ProductFunds;
use App\Models\Product;
use App\Http\Requests\Api\ProductFundsRequest;
use App\Http\Requests\Api\ProductFundsExportRequest;
use App\Http\Requests\Api\ProductFundsImportRequest;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FundsProductExport;
use Cocur\Slugify\Slugify;

class FundsController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new ProductFunds;

        //FILTROS SORT E SEARCH
        if(isset($request->sort) && !empty($request->sort)) {
            $sort = json_decode($request->sort);
            $keySort = key($sort);
            $valueSort = $sort->{$keySort};
            if(isset($valueSort) && !empty($valueSort) && $valueSort != 'null'){
                $data = $data->orderBy($keySort, $valueSort);
            }
        }else{
            $data = $data->orderBy('id', 'ASC');
        }

        if(isset($request->search) && !empty($request->search)) {
            $data = $data->whereHas('product', function(Builder $query) use ($request){
                $query->where('name', 'like','%'. $request->search.'%');
            });
        }
        $data = $data->paginate(50);
        return new FundCollectionResource($data);
    }

    public function store(ProductFundsRequest $request){
        $existsProduct = ProductFunds::where('year', $request->year)->where('product_id', $request->product_id)->first();
        if(isset($existsProduct) && !empty($existsProduct)) return response()->json([ 'message' => __('validation.information_exists')], 422);
        $data = ProductFunds::create([
            'product_id' => $request->product_id,
            'year' => $request->year,
            'amount' => $request->amount,
        ]);
        return new FundResource($data);
    }

    public function update(ProductFundsRequest $request, $id){
        $data = ProductFunds::find($id);
        $data->update([
            'product_id' => $request->product_id,
            'year' => $request->year,
            'amount' => $request->amount,
        ]);
        return new FundResource($data);
    }

    public function destroy($id){
        ProductFunds::find($id)->delete();
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }

    public function export(ProductFundsExportRequest $request)
    {
        $pathFull = 'exports/funds/product/fundos_por_produto_'.date('YmdHis').uniqid().'.xlsx';
        Excel::store(new FundsProductExport($request), $pathFull, 'public');
        return response()->json([
            'data' => url('storage/'.$pathFull),
            'message' => 'Exportação feita com sucesso.'
        ], 200);
    }

    public function import(ProductFundsImportRequest $request)
    {
        $imports = [];
        $info = [];
        $i = 0;
        $total_created = 0;
        $total_edited = 0;
        $total_warning = 0;
        $total_error = 0;
        $total_rows = 0;
        foreach($request->data as $data){
            $product_id = $product_name = $year = $amount = '';
            $data = json_decode(json_encode($data));
            foreach($data as $key => $value){
                $slug = (new Slugify(['separator' => '_']))->slugify($key);
                if($slug == 'id_do_produto'){
                    $product_id = $value;
                    $total_rows++;
                }else if($slug == 'nome_do_produto'){
                    $product_name = $value;
                    $total_rows++;
                }else if($slug == 'ano'){
                    $year = $value;
                    $total_rows++;
                }else if($slug == 'valor'){
                    $amount = $value;
                    $total_rows++;
                }
            }

            if($total_rows < 4){
                $imports[$i]['msg'] = "Campos estão incorretos";
                $imports[$i]['status'] = 'danger';
                $total_error++;
            }

            $imports[$i] = [
                'year' => $year,
                'product_name' => $product_name,
                'amount' => $amount,
            ];

            if(isset($amount) && $amount > 0){
                $product = Product::find($product_id);
                if(isset($product) && !empty($product)){
                    $existsProduct = ProductFunds::where('year', $year)->where('product_id', $product_id)->first();
                    if(isset($existsProduct) && !empty($existsProduct)) {
                        $existsProduct->amount = $amount;
                        $existsProduct->save();
                        $imports[$i]['msg'] = "Editado com sucesso";
                        $total_edited++;
                    }else{
                        ProductFunds::create([
                            'product_id' => $product_id,
                            'year' => $year,
                            'amount' => $amount,
                        ]);
                        $imports[$i]['msg'] = "Criado com sucesso";
                        $total_created++;
                    }
                    $imports[$i]['status'] = 'success';


                }else{
                    $imports[$i]['msg'] = "Produto não Existente";
                    $imports[$i]['status'] = 'danger';
                    $total_error++;
                }

            }else{
                $imports[$i]['msg'] = "Valor está zerado.";
                $imports[$i]['status'] = 'warning';
                $total_warning++;
            }
            $i++;
        }
        $info['total_created'] = $total_created;
        $info['total_edited'] = $total_edited;
        $info['total_warning'] = $total_warning;
        $info['total_error'] = $total_error;

        return response()->json([
            'data' => $imports,
            'info' => $info,
            'message' => 'Exportação feita com sucesso.'
        ], 200);
    }

    public function years()
    {
        return response()->json([
            'data' => [
                'years' => [date('Y'), date('Y', strtotime('+1 year'))]
            ]
        ], 200);
    }
}
