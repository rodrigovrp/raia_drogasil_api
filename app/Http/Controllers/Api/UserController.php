<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\Api\ChangePasswordRequest;

class UserController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index(Request $request)
    {
        return new UserResource(auth()->guard('api')->user());
    }

    public function change_password(ChangePasswordRequest $request){
        if(Hash::check($request->input('old_password'), auth()->user()->password)){
            // update
            $user = User::find(auth()->user()->id);
            if ($request->filled('new_password')) {
                $user->password =  Hash::make($request->input('new_password'));
                $user->save();
            }
            return response()->json(['message'=> __('validation.password_saved_successfully')]);

        }
        return response()->json(['message'=> __('validation.incorrect_old_password')], 422);
    }

    public function theme(Request $request){
        // update
        if (count($request->only(['theme'])) > 0) {
            $user = User::find(auth()->user()->id);
            $user->setThemeConfig($request->only(['theme']));
            $user->save();
        }
        return response()->json(['message'=> __('validation.saved_successfully')]);

    }
}
