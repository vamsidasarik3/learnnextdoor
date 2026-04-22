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

        if ($user->status === 'banned' || (int)$user->status === 0) {
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

        // Check if user exists (We allow sending OTP for registration too)
        $user = $this->userModel->findByPhone($phone);
        if ($user && $user->status === 'banned') {
            return $this->response->setJSON(['success' => false, 'message' => 'Your account has been suspended.']);
        }

        $notify = new \App\Services\NotificationService();
        $result = $notify->sendOtp($phone);

        $success = (bool)$result['sent'];
        $message = $success
            ? 'OTP sent to your WhatsApp number.' 
            : 'WhatsApp delivery failed: ' . ($notify->getLastError() ?: 'Server error. Please check your number.');

        // In development, we allow the flow to continue even if WhatsApp fails,
        // but we now keep the success status accurate so the UI can show the error.
        // If the user wants to use the dev_otp, they can find it in the console.
        $resp = [
            'success' => $success,
            'message' => $message,
        ];

        if (ENVIRONMENT !== 'production') {
            // If it failed but we're in dev, we could still return success=true 
            // but with a warning. For now, let's keep success reflecting reality.
            // If the user needs to bypass, they can see dev_otp in response.
            $resp['dev_otp'] = $result['otp'];
            if (!$success) {
                $resp['success'] = true; // Still allow dev to proceed
                $resp['message'] .= ' (Using Dev OTP for local testing)';
            }
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
            // Register new user via OTP
            // Ensure status/role uses numbers matching the DB (Status 1 = Active)
            $userId = $this->userModel->insert([
                'name'           => 'User-' . substr($phone, -4),
                'username'       => 'user_' . $phone, // Provide a default username
                'email'          => null, // DB is now nullable
                'phone'          => $phone,
                'role'           => 3, // Default to Parent
                'status'         => 1, // Status 1 = Active
                'phone_verified' => 1,
                'password'       => password_hash(bin2hex(random_bytes(10)), PASSWORD_BCRYPT),
            ]);
            
            if (!$userId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create your account. Please contact support.',
                ]);
            }
            $user = $this->userModel->find($userId);
        }

        if ((int)$user->status === 0) { // Check for banned status using numeric comparison
            return $this->response->setJSON(['success' => false, 'message' => 'Your account has been suspended.']);
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
        return redirect()->to('login');
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

        // Insert user (Status 1 = Active)
        $userId = $this->userModel->insert([
            'name'     => $name,
            'email'    => $email,
            'username' => $email, // Default username to email
            'phone'    => $phone,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role'     => $role,
            'status'   => 1, // Status 1 = Active
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


    /**
     * Unified Session for Legacy & New Frontend.
     */
    public function setParentSession(object $user)
    {
        $time = time();
        $login_token = sha1($user->id . $user->password . $time);

        $userData = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'role'  => $user->role,
        ];

        session()->set([
            'login'       => true,
            'login_token' => $login_token,
            'logged'      => [
                'id'   => $user->id,
                'time' => $time,
            ],
            'user_id'     => $user->id,
            'user_role'   => $user->role,
            'cnd_user'    => $userData,
            'cnd_phone'   => $user->phone ?? null,
            'logged_in'   => true, // for modern frontend checks
        ]);
    }

    public function isParentLoggedIn()

    {
        return !empty(session()->get('cnd_user'));
    }
}
