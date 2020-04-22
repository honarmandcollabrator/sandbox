<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\User;
use Config;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;



    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $restUrlForReplace = urldecode(Config::get('app.url') . '/hse/password-reset/<token>/<email>');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response(['message' => trans('passwords.user')], Response::HTTP_NOT_FOUND);
        }

        $token = $this->broker()->createToken($user);
        $resetUrl = str_replace(['<token>', '<email>'], [$token, $email], $restUrlForReplace);
        Mail::to([
            'email' => $email
        ])->send(new ForgotPassword($resetUrl));

        return response(['message' => 'reset link sent'], Response::HTTP_ACCEPTED);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
