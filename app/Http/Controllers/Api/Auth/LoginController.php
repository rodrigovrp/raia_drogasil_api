<?php
namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // The maximum number of attempts to allow
    protected $maxAttempts = 20;

    // The number of minutes to throttle for
    protected $decayMinutes = 1;

    /**
     * Login user and create token
     *
  	 * @param LoginRequest $request
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(LoginRequest $request)
    {
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $loginField = getLoginField($request->input('login'));
        $user = User::where($loginField, $request->login)->where('blocked', 0)->first();
        if(isset($user) && $user->id > 0){
            if (!Hash::check($request->password, $user->password, [])) {
                return response()->json(['message' => 'Email/Username ou senha estão incorretos!'], 401);
            }

            $tokenResult = $user->createToken('authToken');
            if ($request->remember_me) {
                $tokenResult->token->expires_at = Carbon::now()->addWeeks(1);
                $tokenResult->token->save();
            }

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse( $tokenResult->token->expires_at )->toDateTimeString()
            ]);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return response()->json(['message' => 'Email/Username ou senha estão incorretos!'], 401);

    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

}
