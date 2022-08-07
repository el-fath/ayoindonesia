<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/login",
     *   summary="sign in with oauth 2",
     *   description="Login by email, password",
     *   operationId="authLogin",
     *   tags={"Authentication - OAuth 2"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Pass user credentials",
     *       @OA\JsonContent(
     *           required={"email","password"},
     *           @OA\Property(property="email", type="string", format="email", example="admin@mail.com"),
     *           @OA\Property(property="password", type="string", format="password", example="password"),
     *       ),
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success"
     *   ),
     *   @OA\Response(
     *      response=422,
     *      description="Unprocessable Content - Validation",
     *   )
     * )
     */
    public function login(Request $request) {       
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string'
        ],[
            'email.exists' => 'your email not found, please check again'
        ]);

        if ($validator->fails()) return response(['errors'=>$validator->errors()->all()], 422);
        
        $credentials = request(['email', 'password']);
        
        if (!Auth::attempt($credentials)) return response(['errors'=>'your password is wrong, please check again'], 422);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        
        if ($request->remember_me) $token->expires_at = Carbon::now()->addWeeks(1);
        
        $token->save();
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }


    /**
     * @OA\Post(
     * path="/logout",
     * summary="logout",
     * description="Logout user and invalidate token",
     * operationId="authLogout",
     * tags={"Authentication - OAuth 2"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthorized"
     * )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
