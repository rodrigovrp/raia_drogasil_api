<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param ResetPasswordRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function reset(ResetPasswordRequest $request)
    {
        // Get the right login field
        $field = getLoginField($request->input('login'));
        $request->merge([$field => $request->input('login')]);
        if ($field != 'email') {
            $request->merge(['email' => $request->input('login')]);
        }

        // Go to the core process (Email)

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if($response == Password::PASSWORD_RESET) return response()->json(['message' => trans($response)], 200);
        return response()->json(['errors' => ['message' => [trans($response)]], 'message' => __('validation.error_occured')], 422);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param  string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password'       => Hash::make($password),
            'remember_token' => Str::random(60),
            'verified_email' => 1, // Email auto-verified
        ])->save();
    }
}
