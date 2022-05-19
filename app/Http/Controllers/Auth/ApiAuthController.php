<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        //Validation
        $validator = Validator::make($request->all(),[
            'username'      => 'required|string|max:255|min:6',
            'password'      => 'required|string|max:255|min:8',
            're_password'   =>  'required|string|max:255|min:6|same:password',
        ]);
        if ($validator->fails()) {

            return response(['errors'   =>  $validator->errors()->all()], 400);
        }

        $request['password'] = Hash::make($request['password']);
        unset($request['re_password']);
        $request['remember_token'] = Str::random(10);
        if(User::where('username', $request['username'])->exists()){
            return response()->json([
                'message' => 'User already in use',
            ], 409);
        }
        User::create($request->all());
        
        //Updated date is getting current as default 
        return response()->json([
            'message' => 'User successfully registered',
        ], 201);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'username'      => 'required|string|max:255|min:6',
            'password'      => 'required|string|max:255|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return $this->createNewToken($token);
    }

     /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token
        ]);
    }
}
