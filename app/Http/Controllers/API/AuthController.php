<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{


    public function login(Request $request): JsonResponse
    {
        //validate request
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            /** @var \App\Models\User $user **/

            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user'] =  $user;

            return $this->sendResponse($success, 'User login successful.', 200);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }


    public function logout()
    {
        try {
            //code...
            Auth::logout();
            return $this->sendResponse([], 'User logged out successfully', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('Error', $th->getMessage(), 500);
        }
    }
}
