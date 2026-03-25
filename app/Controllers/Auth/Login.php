<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

use App\Models\UserModel;

class Login extends BaseController
{

    public function index()
    {
        return view('admin/auth/login');
    }

    public function check()
    {

        // validate
        if (! $this->validate([
            'username' => 'required|usernameValidation[username]',
            'password' => 'required',
            'g-recaptcha-response' => 'validateRecaptcha[g-recaptcha-response]',
        ],[
            'username' => [
                'usernameValidation' => 'User does\'nt exists'
            ],
            'g-recaptcha-response' => [
                'required' => 'Recaptcha is required',
                'validateRecaptcha' => 'Google Recaptcha not valid !'
            ]
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $user = (new UserModel)->where('username', post('username'))->orWhere('email', post('username'))->first();
        
        // verify password
        if( $user->password != hash( "sha256", post('password') ) ){
            return redirect()->back()->withInput()->with('errors', [
                'password' => 'Invalid Password'
            ]);
        }
        
        // set session
        $time = time();

		// encypting userid and password with current time $time
		$login_token = sha1($user->id.$user->password.$time);

        $remember = post('remember_me');

        // ── 1. Set Unified Session ──────────────────────────────
        $tokenData = [
            'login'       => true,
            'login_token' => $login_token,
            'logged'      => [
                'id'   => $user->id,
                'time' => $time,
            ],
            'user_id'     => $user->id, // Shortcut for navbar
            'cnd_user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'role'  => $user->role,
            ]
        ];
        $this->session->set($tokenData);

        // ── 2. Determine Redirect Path ───────────────────────────
        $redirectPath = 'dashboard';
        if ($user->role == 2) {
            $redirectPath = 'provider/listings';
        } elseif ($user->role == 3) {
            $redirectPath = 'activity';
        }

        // ── 3. Handle Remember Me vs Standard ────────────────────
        if (empty($remember)) {
            return redirect()->to($redirectPath)->with('notifySuccess', "Welcome " . $user->name);
        } else {
            $expiry = strtotime('+7 days');
            $data = [
                'id'   => $user->id,
                'time' => $time,
            ];

            return redirect()->to($redirectPath)
                ->setCookie('login', true, $expiry)
                ->setCookie('logged', json_encode($data), $expiry)
                ->setCookie('login_token', $login_token, $expiry)
                ->with('notifySuccess', "Welcome " . $user->name);
        }
    }
}
