<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;
use App\Http\Resources\BrandCollectionResource;
use App\Http\Controllers\Controller;
use App\Models\ProductBrands;
use App\Http\Requests\Api\ProductBrandsRequest;

class ProductsBrandsController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new ProductBrands;

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
            $data = $data->where(
                function($query) use ($request){
                    $query->where('name', 'like','%'. $request->search.'%');
                }
            );
        }

        $data = $data->paginate(50);
        return new BrandCollectionResource($data);
    }

    public function store(ProductBrandsRequest $request){
        $data = ProductBrands::create([
            'name' => $request->name,
        ]);
        return new BrandResource($data);
    }

    public function update(ProductBrandsRequest $request, $id){
        $data = ProductBrands::find($id);
        $data->update([
            'name' => $request->name,
        ]);
        return new BrandResource($data);
    }

    public function destroy($id){
        ProductBrands::find($id)->delete();
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }
}
