<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Api\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

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


        if (empty($credentials['email'])) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["Email is required."],
                'data' => [

                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        if (empty($credentials['password'])) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["Password is required."],
                'data' => [

                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        $userEmail = trim($request->email);
        $userPass = trim($request->password);

        $user = User::where('email', $userEmail)->first();

        if (empty($user)) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["Email is incorrect."],
                'data' => [

                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }


        if (Hash::check($userPass, $user->password) !== true) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["Password is incorrect."],
                'data' => [

                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  User Role
        if ($user->user_role != 'A') {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["Your User Account Role is not Admin."],
                'data' => [

                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }
        //  Verified Email
        if ($user->email_verified_at == null) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "success" => ["Please verify your email"], 'data' => []];

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

            // Get the default avatar image
            $avatarPath = public_path('img/user_avatar.png');
            $avatarUrl = asset('img/user_avatar.png');

            $file = File::get($avatarPath);
            $mimeType = File::mimeType($avatarPath);

            // Base64 encode the image
            $encodedImage = base64_encode($file);

            $this->data = [
                'status_code' => 200,
                'code' => 100200,
                'response' => '',
                "success" => ["User Logged in Successfully"],
                "auth_token" => $auth_token,
                'data' => [
                    [
                        "id" => $user->id,
                        "name" => $user->name,
                        "email" => $user->email,
                        'image' => [
                            'url' => $avatarUrl,
                            'mime_type' => $mimeType,
                        ]
                    ]
                ]
            ];

            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  Invalid Credentials
        $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '', "success" => ["Invalid Credentials"], 'data' => []];
        $this->setResponse($this->data);
        return $this->getResponse();

    }

    public function logout(Request $request)
    {

        $userEmail = trim($request->email);

        if (empty($userEmail)) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["E-mail is required."],
                'data' => [
                    []
                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        $user = User::where('email', $userEmail)->first();

        //  User not exist
        if (empty($user)) {
            $this->data = ['status_code' => 200, 'code' => 100401, 'response' => '',
                "success" => ["User is not logged in."],
                'data' => [
                    []
                ]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }

        //  Clear Auth token
        $auth_token = null;
        $user->m_login_token = $auth_token;

        $user->save();

        $user->tokens()->delete();

        // Logout Successful
        $this->data = ['status_code' => 200, 'code' => 100200, 'response' => '', "success" => ["Logout Successfully."], 'data' => ['email' => $userEmail]];
        $this->setResponse($this->data);
        return $this->getResponse();
    }
}
