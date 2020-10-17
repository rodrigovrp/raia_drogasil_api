<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\NetworkResource;
use App\Http\Resources\NetworkCollectionResource;
use App\Http\Controllers\Controller;
use App\Models\Networks;
use App\Http\Requests\Api\NetworksRequest;

class NetworksController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        $data = new Networks;

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
        return new NetworkCollectionResource($data);
    }

    public function store(NetworksRequest $request){
        $data = Networks::create([
            'name' => $request->name,
        ]);
        return new NetworkResource($data);
    }

    public function update(NetworksRequest $request, $id){
        $data = Networks::find($id);
        $data->update([
            'name' => $request->name,
        ]);
        return new NetworkResource($data);
    }

    public function destroy($id){
        Networks::find($id)->delete();
        return response()->json([
            'message' => 'Deletado com successo.'
        ]);
    }
}
