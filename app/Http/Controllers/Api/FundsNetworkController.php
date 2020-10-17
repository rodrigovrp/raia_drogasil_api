<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\FundNetworkResource;
use App\Http\Resources\FundNetworkCollectionResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\NetworkResource;
use App\Http\Resources\ActionTypeResource;
use App\Http\Controllers\Controller;
use App\Models\ProductFundsNetwork;
use App\Models\Product;
use App\Models\Networks;
use App\Models\ActionType;
use App\Http\Requests\Api\ProductFundsNetworkRequest;
use App\Http\Requests\Api\ProductFundsExportRequest;
use App\Http\Requests\Api\ProductFundsImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FundsProductNetworkExport;
use Illuminate\Database\Eloquent\Builder;
use Cocur\Slugify\Slugify;

class FundsNetworkController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new ProductFundsNetwork;

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
        return new FundNetworkCollectionResource($data);
    }

    public function store(ProductFundsNetworkRequest $request){
        $existsProduct = ProductFundsNetwork::where('year', $request->year)->where('product_id', $request->product_id)->where('network_id', $request->network_id)->where('action_type_id', $request->action_type_id)->first();
        if(isset($existsProduct) && !empty($existsProduct)) return response()->json([ 'message' => __('validation.information_exists')], 422);
        $data = ProductFundsNetwork::create([
            'product_id' => $request->product_id,
            'network_id' => $request->network_id,
            'action_type_id' => $request->action_type_id,
            'year' => $request->year,
            'amount' => $request->amount,
        ]);
        return new FundNetworkResource($data);
    }

    public function update(ProductFundsNetworkRequest $request, $id){
        $data = ProductFundsNetwork::find($id);
        $data->update([
            'product_id' => $request->product_id,
            'network_id' => $request->network_id,
            'action_type_id' => $request->action_type_id,
            'year' => $request->year,
            'amount' => $request->amount,
        ]);
        return new FundNetworkResource($data);
    }

    public function destroy($id){
        ProductFundsNetwork::find($id)->delete();
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }

    public function filters(Request $request){

        $products = new Product();
        $networks = new Networks();
        $types_actions = new ActionType();

        if($request->type == 'product'){
            $products = $products->where('name', 'like', '%'.$request->search.'%');
        }else if($request->type == 'network'){
            $networks = $networks->where('name', 'like', '%'.$request->search.'%');
        }else if($request->type == 'action_type'){
            $types_actions = $types_actions->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $products->get();
        $networks = $networks->get();
        $types_actions = $types_actions->get();

        return response()->json([
            'data' => [
                'products' => ProductResource::collection($products),
                'networks' => NetworkResource::collection($networks),
                'types_actions' => ActionTypeResource::collection($types_actions),
                'years' => [date('Y'), date('Y', strtotime('+1 year'))]
            ]
        ], 200);

    }

    public function export(ProductFundsExportRequest $request)
    {
        $pathFull = 'exports/funds/network/fundos_por_rede_'.date('YmdHis').uniqid().'.xlsx';
        Excel::store(new FundsProductNetworkExport($request), $pathFull, 'public');
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
            $action_id = $action_name = $network_id = $network_name = $product_id = $product_name = $year = $amount = '';
            $data = json_decode(json_encode($data));
            foreach($data as $key => $value){
                $slug = (new Slugify(['separator' => '_']))->slugify($key);
                if($slug == 'id_da_acao'){
                    $action_id = $value;
                    $total_rows++;
                }else if($slug == 'nome_da_acao'){
                    $action_name = $value;
                    $total_rows++;
                }else if($slug == 'id_da_rede'){
                    $network_id = $value;
                    $total_rows++;
                }else if($slug == 'nome_da_rede'){
                    $network_name = $value;
                    $total_rows++;
                }else if($slug == 'id_do_produto'){
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

            if($total_rows < 8){
                $imports[$i]['msg'] = "Campos estão incorretos";
                $imports[$i]['status'] = 'danger';
                $total_error++;
            }

            $imports[$i] = [
                'year' => $year,
                'product_name' => $product_name,
                'network_name' => $network_name,
                'action_name' => $action_name,
                'amount' => $amount,
            ];

            if(isset($amount) && $amount > 0){
                $product = Product::find($product_id);
                if(isset($product) && !empty($product)){
                    $network = Networks::find($network_id);
                    if(isset($network) && !empty($network)){
                        $types_action = ActionType::find($action_id);
                        if(isset($types_action) && !empty($types_action)){
                            $existsProduct = ProductFundsNetwork::where('year', $year)->where('product_id', $product_id)->where('network_id', $network_id)->where('action_type_id', $action_id)->first();
                            if(isset($existsProduct) && !empty($existsProduct)) {
                                $existsProduct->amount = $amount;
                                $existsProduct->import_at = date('Y-m-d H:i:s');
                                $existsProduct->save();
                                $imports[$i]['msg'] = "Editado com sucesso";
                                $total_edited++;
                            }else{
                                ProductFundsNetwork::create([
                                    'product_id' => $product_id,
                                    'network_id' => $network_id,
                                    'action_type_id' => $action_id,
                                    'year' => $year,
                                    'amount' => $amount,
                                    'import_at' => date('Y-m-d H:i:s'),
                                ]);
                                $imports[$i]['msg'] = "Criado com sucesso";
                                $total_created++;
                            }
                            $imports[$i]['status'] = 'success';
                        }else{
                            $imports[$i]['msg'] = "Tipo de Ação não Existente";
                            $imports[$i]['status'] = 'danger';
                            $total_error++;
                        }

                    }else{
                        $imports[$i]['msg'] = "Rede não Existente";
                        $imports[$i]['status'] = 'danger';
                        $total_error++;
                    }

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

}
