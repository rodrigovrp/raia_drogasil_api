<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollectionResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\BrandResource;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrands;
use App\Http\Requests\Api\ProductRequest;

class ProductsController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new Product;

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
        if($request->limit == 'all'){
            $data = $data->get();
        }else{
            $data = $data->paginate( (($request->limit) ?? 4) );
        }
        return new ProductCollectionResource($data);
    }

    public function store(ProductRequest $request){
        $data = Product::create([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'ean' => $request->ean,
            'weight' => $request->weight,
            'weight_type' => $request->weight_type,
            'quantity' => $request->quantity,
            'quantity_type' => $request->quantity_type,
            'status' => $request->status,
        ]);
        return new ProductResource($data);
    }

    public function update(ProductRequest $request, $id){
        $data = Product::find($id);
        $data->update([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'ean' => $request->ean,
            'weight' => $request->weight,
            'weight_type' => $request->weight_type,
            'quantity' => $request->quantity,
            'quantity_type' => $request->quantity_type,
            'status' => $request->status,
        ]);
        return new ProductResource($data);
    }

    public function destroy($id){
        $products = explode(",", $id);
        $products = array_intersect(Product::get()->pluck('id')->toArray(), $products);
        Product::destroy($products);
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }

    public function filters(Request $request){

        $categories = new ProductCategory();
        $brands = new ProductBrands();

        if($request->type == 'category'){
            $categories = $categories->where('name', 'like', '%'.$request->search.'%');
        }else if($request->type == 'brand'){
            $brands = $brands->where('name', 'like', '%'.$request->search.'%');
        }

        $categories = $categories->get();
        $brands = $brands->get();

        return response()->json([
            'data' => [
                'categories' => CategoryResource::collection($categories),
                'brands' => BrandResource::collection($brands),
                'weight_types' => [
                    ['name' => 'KG', 'value' => 'kg'],
                    ['name' => 'G', 'value' => 'g'],
                ],
                'quantity_types' => [
                    ['name' => 'L', 'value' => 'l'],
                    ['name' => 'ML', 'value' => 'ml'],
                ]
            ]
        ], 200);

    }

}
