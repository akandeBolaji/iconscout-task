<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\Models\{
    User,
    ApiToken
};
use Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
		]);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return response()->json([
				'error' => 'invalid_input',
				'message' => $error
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && $user->password && Hash::check($request->password, $user->password)) {
            $this->generate_api_key($user);
            $this->generate_token($user);
            return response()->json([
                'api_token' => $user->api_token,
            ]);
        }
        return response()->json([
            'error' => 'invalid_credentials',
            'message' => 'Invalid Credentials'
        ], 400);
    }

    protected function generate_api_key($user)
    {
        $user->roll_api_key();
        $date = Carbon::now()->addDays(30);
        $user->api_token_expire_at = $date;
        $user->save();
    }

    protected function generate_token($user)
    {
        $token = ApiToken::where('user_id', $user->id)
                    ->first();

        $date = Carbon::now()->addDays(30);

        if ($token) {
            $token->api_token = $user->api_token;
            $token->api_token_expire_at = $date;
            $token->save();
        } else {
            $token = ApiToken::create([
                'user_id' => $user->id,
                'api_token' => $user->api_token,
                'api_token_expire_at' => $date,
            ]);
        }
    }
}
