<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
/* ── Auth Page ── */
.cnd-auth-section {
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f5f0ff 0%, #fff0f6 50%, #fff8e1 100%);
}
.cnd-auth-card {
  background: #fff;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(108,99,255,.12), 0 4px 20px rgba(0,0,0,.06);
  padding: 2.5rem 2rem;
  width: 100%;
  max-width: 440px;
  animation: authFadeUp .4s ease both;
}
@keyframes authFadeUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.cnd-auth-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .6rem;
  margin-bottom: 1.8rem;
  text-decoration: none;
}
.cnd-auth-logo-icon {
  width: 44px; height: 44px;
  background: var(--cnd-gradient);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.3rem;
}
.cnd-auth-logo-text {
  font-size: 1.25rem;
  font-weight: 800;
  background: var(--cnd-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.cnd-auth-title {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--cnd-dark);
  margin-bottom: .3rem;
  text-align: center;
}
.cnd-auth-subtitle {
  font-size: .9rem;
  color: var(--cnd-muted);
  text-align: center;
  margin-bottom: 1.8rem;
}
.cnd-auth-label {
  font-size: .8rem;
  font-weight: 700;
  color: var(--cnd-dark);
  margin-bottom: .35rem;
  display: block;
}
.cnd-auth-input {
  width: 100%;
  padding: .75rem 1rem;
  border: 2px solid var(--cnd-card-border);
  border-radius: 12px;
  font-size: .95rem;
  font-family: 'Poppins', sans-serif;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  background: #fafafa;
  color: var(--cnd-dark);
}
.cnd-auth-input:focus {
  border-color: var(--cnd-grad-start);
  box-shadow: 0 0 0 4px rgba(108,99,255,.1);
  background: #fff;
}
.cnd-auth-input.is-invalid {
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239,68,68,.1);
}
.cnd-auth-input-wrap {
  position: relative;
  margin-bottom: 1rem;
}
.cnd-auth-input-icon {
  position: absolute;
  left: .9rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--cnd-muted);
  font-size: 1rem;
  pointer-events: none;
}
.cnd-auth-input-wrap .cnd-auth-input {
  padding-left: 2.6rem;
}
.cnd-auth-toggle-pass {
  position: absolute;
  right: .9rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--cnd-muted);
  font-size: 1rem;
  cursor: pointer;
  border: none;
  background: none;
  padding: 0;
}
.cnd-auth-btn {
  display: block;
  width: 100%;
  padding: .9rem;
  background: var(--cnd-gradient);
  border: none;
  border-radius: var(--cnd-radius-pill);
  color: #fff;
  font-size: 1rem;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  margin-top: 1.4rem;
  transition: opacity .2s, transform .2s, box-shadow .2s;
  box-shadow: 0 6px 20px rgba(108,99,255,.3);
}
.cnd-auth-btn:hover {
  opacity: .92;
  transform: translateY(-2px);
  box-shadow: 0 10px 28px rgba(108,99,255,.35);
}
.cnd-auth-btn:active { transform: translateY(0); }
.cnd-auth-divider {
  display: flex; align-items: center; gap: .8rem;
  margin: 1.4rem 0;
  color: var(--cnd-muted); font-size: .8rem;
}
.cnd-auth-divider::before,.cnd-auth-divider::after {
  content: ''; flex: 1; height: 1px; background: var(--cnd-card-border);
}
.cnd-auth-switch {
  text-align: center;
  font-size: .88rem;
  color: var(--cnd-muted);
  margin-top: 1.2rem;
}
.cnd-auth-switch a {
  color: var(--cnd-grad-start);
  font-weight: 700;
  text-decoration: none;
}
.cnd-auth-switch a:hover { text-decoration: underline; }
.cnd-auth-alert-error {
  background: #fff0f0;
  border: 1.5px solid #fecaca;
  border-radius: 12px;
  padding: .75rem 1rem;
  font-size: .85rem;
  color: #dc2626;
  margin-bottom: 1.2rem;
}
.cnd-auth-alert-error i { margin-right: .4rem; }
.cnd-field-error { font-size: .78rem; color: #ef4444; margin-top: .3rem; }

/* Google Button */
.cnd-google-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .8rem;
  width: 100%;
  padding: .85rem;
  background: #fff;
  border: 2px solid #e5e7eb;
  border-radius: var(--cnd-radius-pill);
  color: #374151;
  font-size: .95rem;
  font-weight: 700;
  text-decoration: none;
  transition: all .2s;
  margin-bottom: .5rem;
}
.cnd-google-btn:hover {
  background: #f9fafb;
  border-color: #d1d5db;
  color: #111827;
  transform: translateY(-1px);
}
.cnd-google-btn svg { width: 18px; height: 18px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="cnd-auth-section" aria-labelledby="login-heading">
  <div class="cnd-auth-card">

    <!-- Logo -->
    <a href="<?= base_url('/') ?>" class="cnd-auth-logo" aria-label="Class Next Door home">
      <div class="cnd-auth-logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
      <span class="cnd-auth-logo-text">Class Next Door</span>
    </a>

    <!-- Title -->
    <h1 class="cnd-auth-title" id="login-heading">Welcome back</h1>
    <p class="cnd-auth-subtitle">Login to manage your bookings &amp; discover classes</p>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('error')): ?>
    <div class="cnd-auth-alert-error" role="alert">
      <i class="bi bi-exclamation-circle-fill"></i>
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if (!empty($errors)): ?>
    <div class="cnd-auth-alert-error" role="alert">
      <i class="bi bi-exclamation-circle-fill"></i>
      <?= esc(array_values($errors)[0]) ?>
    </div>
    <?php endif; ?>

    <!-- Email Form -->
    <div id="emailLoginForm">
      <form action="<?= base_url('login') ?>" method="post" id="loginForm" novalidate>
        <?= csrf_field() ?>

        <!-- Email -->
        <div class="cnd-auth-input-wrap">
          <label for="loginEmail" class="cnd-auth-label">Email Address</label>
          <div style="position:relative;">
            <i class="bi bi-envelope-fill cnd-auth-input-icon"></i>
            <input
              type="email"
              id="loginEmail"
              name="email"
              class="cnd-auth-input <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
              placeholder="you@example.com"
              value="<?= old('email') ?>"
              autocomplete="email"
              required
              aria-describedby="emailError">
          </div>
          <?php if (!empty($errors['email'])): ?>
          <p class="cnd-field-error" id="emailError"><?= esc($errors['email']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="cnd-auth-input-wrap">
          <label for="loginPassword" class="cnd-auth-label">Password</label>
          <div style="position:relative;">
            <i class="bi bi-lock-fill cnd-auth-input-icon"></i>
            <input
              type="password"
              id="loginPassword"
              name="password"
              class="cnd-auth-input <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
              placeholder="Your password"
              autocomplete="current-password"
              required
              aria-describedby="passError">
            <button type="button" class="cnd-auth-toggle-pass" aria-label="Toggle password visibility" onclick="togglePass('loginPassword', this)">
              <i class="bi bi-eye" aria-hidden="true"></i>
            </button>
          </div>
          <?php if (!empty($errors['password'])): ?>
          <p class="cnd-field-error" id="passError"><?= esc($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Submit -->
        <button type="submit" class="cnd-auth-btn" id="loginSubmitBtn">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
      </form>
    </div>

    <!-- WhatsApp OTP Form -->
    <div id="otpLoginForm" style="display:none;">
      <div id="phoneStep">
        <div class="cnd-auth-input-wrap">
          <label for="loginPhone" class="cnd-auth-label">WhatsApp Number</label>
          <div style="position:relative;">
            <span class="cnd-auth-input-icon" style="left:1rem; color:var(--cnd-dark); font-weight:600;">+91</span>
            <input
              type="tel"
              id="loginPhone"
              class="cnd-auth-input"
              placeholder="9876543210"
              maxlength="10"
              style="padding-left:3.2rem;"
              aria-label="Phone number">
          </div>
          <p class="cnd-field-error" id="phoneError" style="display:none;"></p>
        </div>
        <button type="button" class="cnd-auth-btn" id="sendOtpBtn">
          <i class="bi bi-whatsapp me-2"></i>Send OTP via WhatsApp
        </button>
      </div>

      <div id="otpStep" style="display:none;">
        <div class="cnd-auth-input-wrap">
          <label for="loginOtp" class="cnd-auth-label">Enter 6-digit OTP</label>
          <div style="position:relative;">
            <i class="bi bi-shield-lock-fill cnd-auth-input-icon"></i>
            <input
              type="text"
              id="loginOtp"
              class="cnd-auth-input"
              placeholder="000000"
              maxlength="6"
              inputmode="numeric"
              aria-label="OTP">
          </div>
          <p class="cnd-field-error" id="otpError" style="display:none;"></p>
          <p class="text-center mt-2" style="font-size:0.8rem; color:var(--cnd-muted);">
            Didn't receive? <a href="javascript:void(0)" id="resendOtpBtn" class="text-primary font-weight-bold">Resend</a>
          </p>
        </div>
        <button type="button" class="cnd-auth-btn" id="verifyOtpBtn">
          Verify &amp; Login
        </button>
      </div>
    </div>
    
    <div class="text-center mt-3">
       <a href="javascript:void(0)" id="toggleAuthMode" class="text-muted" style="font-size:0.88rem; text-decoration:none;">
         <span id="toggleText">Login with WhatsApp OTP instead</span>
       </a>
    </div>
    
    <div class="cnd-auth-divider">or</div>

    <!-- Google Login -->
    <a href="<?= base_url('auth/google') ?>" class="cnd-google-btn">
      <svg viewBox="0 0 24 24">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/>
      </svg>
      Continue with Google
    </a>

    <div class="cnd-auth-switch">
      Don't have an account?
      <a href="<?= base_url('register') ?>">Create one free →</a>
    </div>

  </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function togglePass(fieldId, btn) {
  const input = document.getElementById(fieldId);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'bi bi-eye';
  }
}

// ── Toggle Modes ──
const emailForm = document.getElementById('emailLoginForm');
const otpForm   = document.getElementById('otpLoginForm');
const toggleBtn = document.getElementById('toggleAuthMode');
const toggleTxt = document.getElementById('toggleText');

toggleBtn.addEventListener('click', () => {
  if (emailForm.style.display === 'none') {
    emailForm.style.display = 'block';
    otpForm.style.display   = 'none';
    toggleTxt.textContent   = 'Login with WhatsApp OTP instead';
  } else {
    emailForm.style.display = 'none';
    otpForm.style.display   = 'block';
    toggleTxt.textContent   = 'Login with Email & Password instead';
  }
});

// ── WhatsApp OTP Logic ──
const sendOtpBtn   = document.getElementById('sendOtpBtn');
const verifyOtpBtn = document.getElementById('verifyOtpBtn');
const resendOtpBtn = document.getElementById('resendOtpBtn');
const phoneInput   = document.getElementById('loginPhone');
const otpInput     = document.getElementById('loginOtp');
const phoneStep    = document.getElementById('phoneStep');
const otpStep      = document.getElementById('otpStep');
const phoneError   = document.getElementById('phoneError');
const otpError     = document.getElementById('otpError');

async function handleSendOtp() {
  const phone = phoneInput.value.trim();
  if (!/^[6-9][0-9]{9}$/.test(phone)) {
    phoneError.textContent = 'Please enter a valid 10-digit phone number.';
    phoneError.style.display = 'block';
    return;
  }
  
  phoneError.style.display = 'none';
  sendOtpBtn.disabled = true;
  sendOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';

  try {
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const response = await fetch('<?= base_url('login/otp/send') ?>', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      phoneStep.style.display = 'none';
      otpStep.style.display   = 'block';
      if (data.dev_otp) {
        console.log('Dev OTP:', data.dev_otp);
        otpInput.value = data.dev_otp;
      }
    } else {
      phoneError.textContent = data.message;
      phoneError.style.display = 'block';
    }
  } catch (err) {
    phoneError.textContent = 'Something went wrong. Please try again.';
    phoneError.style.display = 'block';
  } finally {
    sendOtpBtn.disabled = false;
    sendOtpBtn.innerHTML = '<i class="bi bi-whatsapp me-2"></i>Send OTP via WhatsApp';
  }
}

sendOtpBtn.addEventListener('click', handleSendOtp);
resendOtpBtn.addEventListener('click', handleSendOtp);

verifyOtpBtn.addEventListener('click', async () => {
  const phone = phoneInput.value.trim();
  const otp   = otpInput.value.trim();
  
  if (otp.length !== 6) {
    otpError.textContent = 'Please enter the 6-digit OTP.';
    otpError.style.display = 'block';
    return;
  }

  otpError.style.display = 'none';
  verifyOtpBtn.disabled = true;
  verifyOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying…';

  try {
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('otp', otp);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const response = await fetch('<?= base_url('login/otp/verify') ?>', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      window.location.href = data.redirect_url;
    } else {
      otpError.textContent = data.message;
      otpError.style.display = 'block';
    }
  } catch (err) {
    otpError.textContent = 'Verification failed. Try again.';
    otpError.style.display = 'block';
  } finally {
    verifyOtpBtn.disabled = false;
    verifyOtpBtn.innerHTML = 'Verify & Login';
  }
});

document.getElementById('loginForm').addEventListener('submit', function(e) {
  const btn = document.getElementById('loginSubmitBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in…';
});
</script>
<?= $this->endSection() ?>
