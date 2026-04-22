<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\EmailVerificationModel;
use App\Models\UserModel;

class EmailVerification extends BaseController
{
    public function verify()
    {
        $token = $this->request->getGet('token');
        
        if (!$token) {
            return $this->showError('Invalid verification link.');
        }

        $verifyModel = new EmailVerificationModel();
        $record = $verifyModel->findByToken($token);

        if (!$record) {
            return $this->showError('The verification link is invalid or has already been used.');
        }

        if (strtotime($record->expires_at) < time()) {
            return $this->showError('The verification link has expired. Please request a new one from your profile.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($record->user_id);

        if (!$user) {
            return $this->showError('User not found.');
        }

        // Mark as verified
        $userModel->update($user->id, [
            'email_verified'    => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'email'             => $record->email // Ensure we verify the email that was in the token
        ]);

        // Clear the token
        $verifyModel->delete($record->id);

        // Update session if user is logged in
        $cndUser = session()->get('cnd_user');
        if ($cndUser && $cndUser['id'] == $user->id) {
            $cndUser['email'] = $record->email;
            $cndUser['email_verified'] = 1;
            session()->set('cnd_user', $cndUser);
        }

        return $this->showSuccess('Your email has been verified successfully!');
    }

    private function showSuccess($message)
    {
        return view('frontend/auth/verification_result', [
            'success' => true,
            'message' => $message,
            'page_title' => 'Email Verified | Class Next Door'
        ]);
    }

    private function showError($message)
    {
        return view('frontend/auth/verification_result', [
            'success' => false,
            'message' => $message,
            'page_title' => 'Verification Error | Class Next Door'
        ]);
    }
}
