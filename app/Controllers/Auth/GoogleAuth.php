<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use Google\Client as GoogleClient;
use Google\Service\Oauth2 as GoogleOauth2;
use App\Models\UserModel;

if (file_exists(ROOTPATH . 'vendor/autoload.php')) {
    require_once ROOTPATH . 'vendor/autoload.php';
}

class GoogleAuth extends BaseController
{
    private $googleClient;
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        
        $this->googleClient = new GoogleClient();
        $this->googleClient->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->googleClient->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->googleClient->setRedirectUri(base_url('auth/google/callback'));
        $this->googleClient->addScope("email");
        $this->googleClient->addScope("profile");
    }

    public function login()
    {
        $authUrl = $this->googleClient->createAuthUrl();
        return redirect()->to($authUrl);
    }

    public function callback()
    {
        $code = $this->request->getGet('code');
        if (!$code) {
            return redirect()->to('login')->with('error', 'Google authentication failed.');
        }

        try {
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);
            $this->googleClient->setAccessToken($token);

            $googleService = new GoogleOauth2($this->googleClient);
            $googleUser = $googleService->userinfo->get();

            // 1. Retrieve user information from Google
            $name = $googleUser->name;
            $email = $googleUser->email;
            $googleId = $googleUser->id;
            $emailVerified = $googleUser->verifiedEmail;

            // 2. Extract and check verification status
            if (!$emailVerified) {
                return redirect()->to('login')->with('error', 'Your Google email is not verified. Please verify it or use another login method.');
            }

            // 3. Check if user exists
            $user = $this->userModel->where('email', $email)->orWhere('provider_id', $googleId)->first();

            if ($user) {
                // Update provider_id if it's missing (e.g., they had a standard account)
                if (empty($user->provider_id)) {
                    $this->userModel->update($user->id, [
                        'provider' => 'google',
                        'provider_id' => $googleId,
                        'email_verified' => 1,
                        'email_verified_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                // Register new user
                $userId = $this->userModel->insert([
                    'name'              => $name,
                    'email'             => $email,
                    'provider'          => 'google',
                    'provider_id'       => $googleId,
                    'email_verified'    => 1,
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'role'              => 3, // Student
                    'status'            => 'active',
                    'password'          => password_hash(bin2hex(random_bytes(10)), PASSWORD_BCRYPT), // Random password
                ]);
                $user = $this->userModel->find($userId);
            }

            if ($user->status === 'banned') {
                return redirect()->to('login')->with('error', 'Your account has been suspended.');
            }

            // 4. Log the user in (Reuse existing session logic)
            $this->setParentSession($user);
            
            return redirect()->to(base_url('/'))->with('success', 'Logged in successfully with Google!');

        } catch (\Exception $e) {
            log_message('error', 'Google Auth Error: ' . $e->getMessage());
            return redirect()->to('login')->with('error', 'An error occurred during Google login.');
        }
    }

    private function setParentSession(object $user): void
    {
        $time = time();
        $login_token = sha1($user->id . $user->password . $time);

        session()->set([
            'user_id'    => $user->id,
            'cnd_user'   => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'role'  => $user->role,
            ],
            'login'       => true,
            'login_token' => $login_token,
            'logged'      => [
                'id'   => $user->id,
                'time' => $time,
            ]
        ]);
    }
}
