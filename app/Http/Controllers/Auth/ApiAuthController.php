<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWT;

class ApiAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','logout']]);
    }

    /**
    * @OA\Post(
    *   path="/api/auth/register",
    *   tags={"Auth"},
    *   description="Endpoint for registering an user",
    *    @OA\RequestBody(
    *       required = true,
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *               property="username",
    *               type = "string",
    *               maxLength=255,
    *               minLength=6,
    *               example="joaovictor", 
    *           ),
    *            @OA\Property(
    *               property="password",
    *               type = "string",
    *               maxLength=255,
    *               minLength = 8,
    *               example="teste@123", 
    *           ),
    *           @OA\Property(
    *               property="re_password",
    *               type = "string",
    *               maxLength=255,
    *               minLength = 8,
    *               example="teste@123", 
    *           ),
    *        ),
    *    ),
    *   @OA\Response(response="400", description="Invalid user data"),
    *   @OA\Response(response="409", description="Username already in use"),
    *   @OA\Response(response="201", description="Registered"),
    *  ),
    *   @return \Illuminate\Http\JsonResponse
    */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username'      => 'required|string|max:255|min:6',
            'password'      => 'required|string|max:255|min:8',
            're_password'   =>  'required|string|max:255|min:6|same:password',
        ]);
        if ($validator->fails()) {

            return response()->json(['message' => 'Invalid user data'], 400);
        }

        $request['password'] = Hash::make($request['password']);
        unset($request['re_password']);
        $request['remember_token'] = Str::random(10);
        if(User::where('username', $request['username'])->exists()){
            return response()->json([
                'message' => 'Username already in use',
            ], 409);
        }
        User::create($request->all());
        
        //Updated date is getting current as default 
        return response()->json([
            'message' => 'User successfully registered',
        ], 201);
    }

    /**
    * @OA\Post(
    *   path="/api/auth/login",
    *   tags={"Auth"},
    *   description="Endpoint for logging in an user",
    *    @OA\RequestBody(
    *       required = true,
    *        @OA\JsonContent(
    *            type="object",
    *            @OA\Property(
    *               property="username",
    *               type = "string",
    *               maxLength=255,
    *               minLength=6,
    *               example="joaovictor", 
    *           ),
    *            @OA\Property(
    *               property="password",
    *               type = "string",
    *               maxLength=255,
    *               minLength = 8,
    *               example="teste@123", 
    *           ),
    *        ),
    *    ),
    *   @OA\Response(response="400", description="Invalid user data"),
    *   @OA\Response(response="401", description="Wrong credentials"),
    *   @OA\Response(response="200", description="Login ok"),
    *  ),
    *   @return \Illuminate\Http\JsonResponse
    */

    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'username'      => 'required|string|max:255|min:6',
            'password'      => 'required|string|max:255|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid user data'], 400);
        }

        if (! $token = auth('api')->attempt($validator->validated())) {
            
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return $this->createNewToken($token);
    }

    /**
    * @OA\Post(
    *   path="/api/auth/logout",
    *   tags={"Auth"},
    *   description="Endpoint for logging out an user",
    *   security={{"bearerAuth":{} }},
    *   @OA\Response(response="401", description="Invalid token"),
    *   @OA\Response(response="200", description="User logout"),
    *  ),
    *   @return \Illuminate\Http\JsonResponse
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
