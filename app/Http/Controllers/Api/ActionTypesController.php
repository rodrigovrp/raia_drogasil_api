<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\ActionTypeResource;
use App\Http\Resources\ActionTypeCollectionResource;
use App\Http\Controllers\Controller;
use App\Models\ActionType;
use App\Http\Requests\Api\ActionTypesRequest;

class ActionTypesController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new ActionType;

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
                    $query->where('name', 'like','%'. $request->search.'%')->orWhere('description', 'like', '%'. $request->search.'%');
                }
            );
        }

        $data = $data->paginate(50);
        return new ActionTypeCollectionResource($data);
    }

    public function store(ActionTypesRequest $request){
        $data = ActionType::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        return new ActionTypeResource($data);
    }

    public function update(ActionTypesRequest $request, $id){
        $data = ActionType::find($id);
        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        return new ActionTypeResource($data);
    }

    public function destroy($id){
        ActionType::find($id)->delete();
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }
}
