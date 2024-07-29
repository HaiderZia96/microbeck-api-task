<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Api\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    use Response;

    public $data;

    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function login(Request $request)
    {

        $credentials = $request->all('email', 'password');

        $userEmail = trim($request->email);

        $user = User::where('email', $userEmail)->first();

        //  Invalid Credentials
        if (empty($user)) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "error" => ["Invalid Credentials"], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  User Role
        if ($user->user_role != 'A') {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "info" => ["Your User Account Role is not Admin"], 'data' => []];

            $this->setResponse($this->data);
            return $this->getResponse();
        }
        //  Verified Email
        if ($user->email_verified_at == null) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "info" => ["Please verify your email"], 'data' => []];

            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  Login
        if (auth()->attempt($credentials)) {

            $user = auth()->user();
            $user->last_login = Carbon::now();

            //  Create Auth token
            $auth_token = $user->createToken("API TOKEN")->plainTextToken;
            $user->m_login_token = $auth_token;

            $user->save();

            //  Login Successful
            $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', "success" => ["Login Successfully"], 'data' => [["id" => $user->id, "name" => $user->name, "email" => $user->email, "auth_token" => $auth_token]]];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  Invalid Credentials
        $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "error" => ["Invalid Credentials"], 'data' => []];
        $this->setResponse($this->data);
        return $this->getResponse();

    }

    public function logout(Request $request)
    {

        $userEmail = trim($request->email);

        $user = User::where('email', $userEmail)->first();

        //  User not exist
        if (empty($user)) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "error" => ["User not exist"], 'data' => []];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  Clear Auth token
        $auth_token = null;
        $user->m_login_token = $auth_token;

        $user->save();

        // Logout Successful
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', "success" => ["Logout Successfully."], 'data' => ['email' => $userEmail]];
        $this->setResponse($this->data);
        return $this->getResponse();
    }
}
