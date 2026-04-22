# Login Page & OTP Functionality Analysis

This document provides a detailed overview of the authentication system, focusing on the WhatsApp OTP flow.

## 1. Frontend Architecture
- **View File**: `app\Views\frontend\auth\login.php`
- **Styling**: Uses custom CSS classes (prefixed with `cnd-`) for a modern, responsive design with glassmorphism effects.
- **Interactive Elements**:
    - **Phone Input**: Restricted to 10 digits, validated for Indian mobile patterns.
    - **OTP Input**: Hidden by default, appears after successfully sending the OTP.
    - **Persistence**: Remembers redirect intents (e.g., if a user was trying to book a class before logging in).

### JavaScript Flow
1. **`handleSendOtp()`**: 
    - Validates phone number format.
    - Sends an AJAX POST request to `/login/otp/send`.
    - Handles success by switching the UI to the OTP verification step.
2. **`verifyOtpBtn` handler**:
    - Validates OTP length (6 digits).
    - Sends an AJAX POST request to `/login/otp/verify`.
    - If successful, redirects the user to their dashboard or previous intent.

## 2. Backend Logic
- **Controller**: `app\Controllers\Auth\FrontendAuth.php`
- **Key Methods**:
    - `sendOtpPost()`: 
        - Validates the phone number.
        - Calls `NotificationService` to generate and send the OTP.
        - Provides `dev_otp` in the response when not in production.
    - `verifyOtpPost()`:
        - Verifies the OTP via the service.
        - If the user doesn't exist, **automatically registers them** as a Parent (Role 3).
        - Initializes a **Unified Session** compatible with both legacy and modern modules.
    - `setParentSession()`: Sets critical session keys: `login`, `login_token`, `logged`, `user_id`, `user_role`, `cnd_user`, `cnd_phone`, and `logged_in`.

## 3. OTP Service
- **Service**: `app\Services\NotificationService.php`
- **Configuration**: Uses `.env` variables `WHATSAPP_TOKEN` and `WHATSAPP_PHONE_NUMBER_ID`.
- **Security**:
    - OTPs are 6-digit random integers.
    - Stored in the session with a **5-minute TTL** (300 seconds).
    - Uses `hash_equals` for constant-time comparison.
- **WhatsApp Integration**: Sends messages via Meta Cloud API using the `learnnextdoorv1` authentication template.

## 4. Summary Table

| Feature | Description | Implementation Detail |
| :--- | :--- | :--- |
| **Primary Login** | WhatsApp OTP | Password-less, friction-reduced entry. |
| **Secondary Login** | Email/Password | Maintained for legacy and administrative users. |
| **Social Login** | Google OAuth | Integrated via `Auth\GoogleAuth`. |
| **Auto-Registration** | Yes | New users are created on-the-fly during OTP verification. |
| **Dev Mode** | Yes | Shows OTP in browser console/response in non-production environments. |

---

> [!TIP]
> To test the OTP flow locally, check the browser's Network tab for the `/login/otp/send` response, which will contain the `dev_otp` for authentication.
