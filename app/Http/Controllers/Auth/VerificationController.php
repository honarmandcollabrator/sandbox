<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use Auth;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;


    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        // ->route('id') gets route user id and getKey() gets current user id()
        // do not forget that you must send Authorization header to get the user from the request
        if ($request->route('id') == $request->user()->getKey() &&
            $request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            return response(['data' => new UserResource(auth()->user())], Response::HTTP_ACCEPTED);
        }

        return response(['message' => 'verification failed'], Response::HTTP_EXPECTATION_FAILED);

//        return redirect($this->redirectPath());
    }


    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('User already have verified email!', 422);
//            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json('The notification has been resubmitted');
//        return back()->with('resent', true);
    }


    /**
     * Where to redirect users after verification.
     *boo
     * @var string
     */
//    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('JWT');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
