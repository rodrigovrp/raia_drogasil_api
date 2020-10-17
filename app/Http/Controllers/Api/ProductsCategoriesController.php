<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollectionResource;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Http\Requests\Api\ProductCategoriesRequest;
use Cocur\Slugify\Slugify;

class ProductsCategoriesController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new ProductCategory;

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
        if($request->limit === 'all'){
            $data = $data->get();
        }else{
            $data = $data->paginate(50);
        }
        return new CategoryCollectionResource($data);
    }

    public function store(ProductCategoriesRequest $request){
        $data = ProductCategory::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'slug' => (new Slugify())->slugify($request->name),
            'status' => $request->status,
        ]);
        return new CategoryResource($data);
    }

    public function update(ProductCategoriesRequest $request, $id){
        $data = ProductCategory::find($id);
        $data->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'slug' => (new Slugify())->slugify($request->name),
            'status' => $request->status,
        ]);
        return new CategoryResource($data);
    }

    public function destroy($id){
        ProductCategory::find($id)->delete();
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }
}
