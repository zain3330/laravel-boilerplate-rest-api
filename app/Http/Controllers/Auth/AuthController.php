<?php

namespace App\Http\Controllers\Auth;


use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends BaseController
{
    /**
     * @param AuthRegisterRequest $request
     * @return JsonResponse
     * */
    public function register(AuthRegisterRequest $request):JsonResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $token = $user->createToken($user->name);

        return response()->json([
            'token' => $token->plainTextToken,
            'data' => $user],
            201);
    }

    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
    */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            $token = $user->createToken($user->name);

            return response()->json([
                'user'  => $user,
                'token' => $token->plainTextToken,
            ], 201);
        }
        return response()->json([
            'message' => 'The provided credentials are incorrect'
        ], 401);
    }
    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out'
        ], 201);
    }

}
