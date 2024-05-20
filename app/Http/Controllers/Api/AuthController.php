<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        return response()->json([
            "status" => false,
            "message" => "harap login terlebih dahulu"
        ], 401);
    }

    /**
     * Login user and create token
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // failed validation
        if ($valdator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "harap login terlebih dahulu"
            ], 401);
        }

        // attempt to login
        $credentials = $request->only('email', 'password');

        // attem token auth jwt api
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                "status" => false,
                "message" => "Login failed"
            ], 401);
        }

        // success login
        return response()->json([
            "status" => true,
            "message" => "login success",
            "token" => $token
        ], 200);
    }

    public function register(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        // failed validation
        if ($valdator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "harap login terlebih dahulu"
            ], 401);
        }

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // success login
        if ($user) {
            return response()->json([
                "status" => true,
                "message" => "register success",
                "data" => $user
            ], 200);
        }

        // failed
        return response()->json([
            "status" => false,
            "message" => "register failed"
        ], 401);
    }

    public function registerWithGoogle(Request $request)
    {
        $accessToken = $request->bearerToken();
        try {
            $response = Http::withHeaders(
                ['Authorization' => 'Bearer ' . $accessToken]
            )->get('https://www.googleapis.com/oauth2/v3/userinfo');

            $userdata = $response->json();

            $email = $userdata['email'];
            $name = $userdata['name'];
            $password = bcrypt('123456dummy');

            $user = User::where('email', $email)->first();

            if ($user) {
                $token = auth()->guard('api')->login($user);
                return response()->json([
                    "status" => true,
                    "message" => "login success with google",
                    "token" => $token
                ], 200);
            }else{
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ]);
                $token = auth()->guard('api')->login($user);
                return response()->json([
                    "status" => true,
                    "message" => "register with google success",
                    "token" => $token
                ], 200);
            }


        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 401);
        }
    }
}
