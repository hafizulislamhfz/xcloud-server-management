<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Resources\V1\Auth\LoginResource;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function store(LoginRequest $request)
    {
        try {

            $user = $request->authenticate();
            $remember = $request->validated('remember') ?? false;

            $token = $user->createToken(
                name: $request->validated('email'),
                expiresAt: $remember ? null : now()->addHours(12)
            )->plainTextToken;

            return $this->successResponse(
                new LoginResource($user, $token),
                'Login successful.'
            );

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {

            $request->user()->currentAccessToken()->delete();

            return response()->noContent();

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
