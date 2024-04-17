<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'staff_id';
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Overwrite the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function login_through_api(Request $request)
    {
        $request->validate([
            "username" => ["required"],
            "password" => ["required"],
        ]);

        try {
            $auth = Auth::attempt([
                "staff_id" => $request->input("username"),
                "password" => $request->input("password")
            ]);

            if (!$auth) {
                return response([
                    "status" => "error",
                    "message" => "Unauthorized user",
                    "data" => []
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = Auth::user();
            $tokenResult = $user->createToken('jwt');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'status' => 'success',
                'accessToken' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            return response([
                "status" => "error",
                "message" => $th->getMessage(),
                "data" => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout_through_api(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }
}
