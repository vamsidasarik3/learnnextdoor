<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * FrontendAuth
 * ──────────────────────────────────────────────────────────────
 * Handles parent-facing (public) authentication:
 *   GET  /login       — show login page
 *   POST /login       — process login
 *   GET  /register    — show registration page
 *   POST /register    — process registration
 *   GET  /logout-user — destroy parent session
 */
class FrontendAuth extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ─────────────────────────────────────────────────────────
    // SHOW LOGIN
    // ─────────────────────────────────────────────────────────
    public function loginPage()
    {
        if ($this->isParentLoggedIn()) {
            return redirect()->to('/');
        }
        // Store the redirect URL from query string (used when user was redirected here from booking)
        $redirect = $this->request->getGet('redirect');
        if ($redirect) {
            session()->setFlashdata('redirect_after_login', $redirect);
        }
        return view('frontend/auth/login', [
            'page_title'       => 'Login | Class Next Door',
            'meta_description' => 'Login to your Class Next Door account to manage bookings.',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // PROCESS LOGIN
    // ─────────────────────────────────────────────────────────
    public function loginPost()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user     = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'No account found with this email address.');
        }

        if ($user->status === 'banned') {
            return redirect()->back()->withInput()->with('error', 'Your account has been suspended. Please contact support.');
        }

        // Support both sha256 (legacy admin) and password_hash (new)
        $valid = password_verify($password, $user->password)
               || ($user->password === hash('sha256', $password));

        if (!$valid) {
            return redirect()->back()->withInput()->with('error', 'Incorrect password. Please try again.');
        }

        // Set parent session
        $this->setParentSession($user);

        // Update last login
        $this->userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);

        // ── Redirect based on role ──────────────────────────────
        $redirect = session()->getFlashdata('redirect_after_login');
        if (empty($redirect) || $redirect === '/') {
            if ($user->role == 1) { $redirect = 'dashboard'; }
            elseif ($user->role == 2) { $redirect = 'provider/dashboard'; }
            elseif ($user->role == 3) { $redirect = 'activity'; }
            else { $redirect = '/'; }
        }
        
        return redirect()->to($redirect)->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Send OTP for login.
     * POST /login/otp/send
     */
    public function sendOtpPost()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $phone = $this->request->getPost('phone');
        if (!$this->validate(['phone' => 'required|regex_match[/^[6-9][0-9]{9}$/]'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid 10-digit Indian mobile number.',
            ]);
        }

        // Check if user exists
        $user = $this->userModel->findByPhone($phone);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No account found with this phone number. Please register first.',
            ]);
        }

        if ($user->status === 'banned') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Your account has been suspended.',
            ]);
        }

        // Send OTP via NotificationService
        $notify = new \App\Services\NotificationService();
        $result = $notify->sendOtp($phone);

        $resp = [
            'success' => (bool)$result['sent'],
            'message' => $result['sent'] ? 'OTP sent to your WhatsApp number.' : 'Failed to send OTP via WhatsApp. Please try again.',
        ];
        if (ENVIRONMENT !== 'production') {
            $resp['dev_otp'] = $result['otp'];
        }

        return $this->response->setJSON($resp);
    }

    /**
     * Verify OTP and login.
     * POST /login/otp/verify
     */
    public function verifyOtpPost()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $phone = $this->request->getPost('phone');
        $otp   = $this->request->getPost('otp');

        if (!$this->validate([
            'phone' => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'otp'   => 'required|min_length[6]|max_length[6]|is_natural',
        ])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid phone or OTP format.',
            ]);
        }

        // Verify OTP
        $notify = new \App\Services\NotificationService();
        if (!$notify->verifyOtp($phone, $otp)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Incorrect or expired OTP.',
            ]);
        }

        // OTP Valid - Get User
        $user = $this->userModel->findByPhone($phone);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Account not found.',
            ]);
        }

        // Set session
        $this->setParentSession($user);

        // Update last login
        $this->userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);

        // Redirect URL logic
        $redirect = session()->getFlashdata('redirect_after_login');
        if (empty($redirect) || $redirect === '/') {
            if ($user->role == 1) { $redirect = 'dashboard'; }
            elseif ($user->role == 2) { $redirect = 'provider/dashboard'; }
            elseif ($user->role == 3) { $redirect = 'activity'; }
            else { $redirect = '/'; }
        }

        return $this->response->setJSON([
            'success'      => true,
            'message'      => 'Login successful!',
            'redirect_url' => base_url($redirect),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // SHOW REGISTER
    // ─────────────────────────────────────────────────────────
    public function registerPage()
    {
        if ($this->isParentLoggedIn()) {
            return redirect()->to('/');
        }
        return view('frontend/auth/register', [
            'page_title'       => 'Create Account | Class Next Door',
            'meta_description' => 'Join Class Next Door — discover and book the best classes for your child.',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // PROCESS REGISTER
    // ─────────────────────────────────────────────────────────
    public function registerPost()
    {
        $rules = [
            'name'     => 'required|min_length[2]|max_length[150]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'password' => 'required|min_length[6]|max_length[72]',
        ];

        $messages = [
            'email'    => ['is_unique' => 'This email is already registered. Please login instead.'],
            'phone'    => ['regex_match' => 'Please enter a valid 10-digit Indian mobile number.'],
            'password' => ['min_length' => 'Password must be at least 6 characters.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name     = trim($this->request->getPost('name'));
        $email    = strtolower(trim($this->request->getPost('email')));
        $phone    = $this->request->getPost('phone');
        $password = $this->request->getPost('password');

        // Unified Flow: Always start as Parent (Role 3). 
        // Provider role (2) is earned after KYC.
        $role = 3; 

        // Insert user
        $userId = $this->userModel->insert([
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role,
            'status'   => 'active',
            'email_verified' => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }

        $user = $this->userModel->find($userId);
        $this->setParentSession($user);

        // Save intent for after login
        if ($this->request->getPost('role_intent') === 'provider') {
            session()->set('role_intent', 'provider');
            return redirect()->to('provider/verification')->with('success', 'Account created! Complete your verification to list classes.');
        }

        return redirect()->to('activity')->with('success', 'Account created! Welcome to Class Next Door.');
    }

    // ─────────────────────────────────────────────────────────
    // LOGOUT
    // ─────────────────────────────────────────────────────────
    public function logout()
    {
        session()->remove('cnd_user');
        session()->destroy();
        return redirect()->to('/')->with('success', 'You have been logged out.');
    }


    public function setParentSession(object $user)
    {
        $time = time();
        $login_token = sha1($user->id . $user->password . $time);

        session()->set([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'user_email' => $user->email,
            'user_role'  => $user->role,
            'logged_in'  => true,
            'cnd_phone'  => $user->phone ?? null,
            'cnd_user'   => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
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

    public function isParentLoggedIn()
    {
        return !empty(session()->get('cnd_user'));
    }
}
