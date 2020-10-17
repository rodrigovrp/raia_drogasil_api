<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
         // Get the right login field
         $field = getLoginField($request->input('login'));
         $request->merge([$field => $request->input('login')]);
         if ($field != 'email') {
             $request->merge(['email' => $request->input('login')]);
         }
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        if($response == Password::RESET_LINK_SENT) return response()->json(['message' => trans($response)], 200);

        throw ValidationException::withMessages(['email' => [trans($response)]]);

    }



}
