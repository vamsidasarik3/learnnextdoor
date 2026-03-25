<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
/* ── Uses same cnd-auth-* styles as login.php — inline here for self-containment ── */
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
  max-width: 480px;
  animation: authFadeUp .4s ease both;
}
@keyframes authFadeUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.cnd-auth-logo {
  display: flex; align-items: center; justify-content: center; gap: .6rem;
  margin-bottom: 1.8rem; text-decoration: none;
}
.cnd-auth-logo-icon {
  width: 44px; height: 44px;
  background: var(--cnd-gradient);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.3rem;
}
.cnd-auth-logo-text {
  font-size: 1.25rem; font-weight: 800;
  background: var(--cnd-gradient);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.cnd-auth-title { font-size: 1.6rem; font-weight: 800; color: var(--cnd-dark); margin-bottom: .3rem; text-align: center; }
.cnd-auth-subtitle { font-size: .9rem; color: var(--cnd-muted); text-align: center; margin-bottom: 1.8rem; }
.cnd-auth-label { font-size: .8rem; font-weight: 700; color: var(--cnd-dark); margin-bottom: .35rem; display: block; }
.cnd-auth-input {
  width: 100%; padding: .75rem 1rem;
  border: 2px solid var(--cnd-card-border); border-radius: 12px;
  font-size: .95rem; font-family: 'Poppins', sans-serif;
  outline: none; transition: border-color .2s, box-shadow .2s;
  background: #fafafa; color: var(--cnd-dark);
}
.cnd-auth-input:focus {
  border-color: var(--cnd-grad-start);
  box-shadow: 0 0 0 4px rgba(108,99,255,.1); background: #fff;
}
.cnd-auth-input.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239,68,68,.1); }
.cnd-auth-input-wrap { position: relative; margin-bottom: 1rem; }
.cnd-auth-input-icon {
  position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
  color: var(--cnd-muted); font-size: 1rem; pointer-events: none;
}
.cnd-auth-input-wrap .cnd-auth-input { padding-left: 2.6rem; }
.cnd-auth-toggle-pass {
  position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
  color: var(--cnd-muted); font-size: 1rem; cursor: pointer;
  border: none; background: none; padding: 0;
}
.cnd-auth-btn {
  display: block; width: 100%; padding: .9rem;
  background: var(--cnd-gradient); border: none;
  border-radius: var(--cnd-radius-pill); color: #fff;
  font-size: 1rem; font-weight: 700; font-family: 'Poppins', sans-serif;
  cursor: pointer; margin-top: 1.4rem;
  transition: opacity .2s, transform .2s, box-shadow .2s;
  box-shadow: 0 6px 20px rgba(108,99,255,.3);
}
.cnd-auth-btn:hover { opacity:.92; transform:translateY(-2px); box-shadow: 0 10px 28px rgba(108,99,255,.35); }
.cnd-auth-divider { display:flex; align-items:center; gap:.8rem; margin:1.4rem 0; color:var(--cnd-muted); font-size:.8rem; }
.cnd-auth-divider::before,.cnd-auth-divider::after { content:''; flex:1; height:1px; background:var(--cnd-card-border); }
.cnd-auth-switch { text-align:center; font-size:.88rem; color:var(--cnd-muted); margin-top:1.2rem; }
.cnd-auth-switch a { color:var(--cnd-grad-start); font-weight:700; text-decoration:none; }
.cnd-auth-switch a:hover { text-decoration: underline; }
.cnd-auth-alert-error { background:#fff0f0; border:1.5px solid #fecaca; border-radius:12px; padding:.75rem 1rem; font-size:.85rem; color:#dc2626; margin-bottom:1.2rem; }
.cnd-field-error { font-size:.78rem; color:#ef4444; margin-top:.3rem; }

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

.cnd-pass-strength { height: 4px; background: var(--cnd-card-border); border-radius: 2px; margin-top: .4rem; overflow: hidden; }
.cnd-pass-strength-bar { height: 100%; border-radius: 2px; transition: width .3s, background .3s; width: 0; }
.cnd-terms-note { font-size:.76rem; color:var(--cnd-muted); text-align:center; margin-top:.8rem; }
.cnd-terms-note a { color: var(--cnd-grad-start); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="cnd-auth-section" aria-labelledby="register-heading">
  <div class="cnd-auth-card">

    <!-- Logo -->
    <a href="<?= base_url('/') ?>" class="cnd-auth-logo" aria-label="Class Next Door home">
      <div class="cnd-auth-logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
      <span class="cnd-auth-logo-text">Class Next Door</span>
    </a>

    <!-- Title -->
    <?php if (service('request')->getGet('role') === 'provider'): ?>
      <h1 class="cnd-auth-title" id="register-heading">Join as a Provider</h1>
      <p class="cnd-auth-subtitle">List your classes, reach thousands of parents, and manage your schedule effortlessly</p>
    <?php else: ?>
      <h1 class="cnd-auth-title" id="register-heading">Create your account</h1>
      <p class="cnd-auth-subtitle">Join thousands of parents discovering great classes for their kids</p>
    <?php endif; ?>

    <!-- Flash error -->
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

    <!-- Form -->
    <form action="<?= base_url('register') ?>" method="post" id="registerForm" novalidate>
      <?= csrf_field() ?>
      <?php if (service('request')->getGet('role') === 'provider'): ?>
        <input type="hidden" name="role_intent" value="provider">
      <?php endif; ?>

      <!-- Name -->
      <div class="cnd-auth-input-wrap">
        <label for="regName" class="cnd-auth-label">Full Name</label>
        <div style="position:relative;">
          <i class="bi bi-person-fill cnd-auth-input-icon"></i>
          <input
            type="text"
            id="regName"
            name="name"
            class="cnd-auth-input <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
            placeholder="Your full name"
            value="<?= old('name') ?>"
            autocomplete="name"
            required
            aria-describedby="nameError">
        </div>
        <?php if (!empty($errors['name'])): ?>
        <p class="cnd-field-error" id="nameError"><?= esc($errors['name']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Email -->
      <div class="cnd-auth-input-wrap">
        <label for="regEmail" class="cnd-auth-label">Email Address</label>
        <div style="position:relative;">
          <i class="bi bi-envelope-fill cnd-auth-input-icon"></i>
          <input
            type="email"
            id="regEmail"
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

      <!-- Phone -->
      <div class="cnd-auth-input-wrap">
        <label for="regPhone" class="cnd-auth-label">Mobile Number</label>
        <div style="position:relative;">
          <i class="bi bi-phone-fill cnd-auth-input-icon"></i>
          <input
            type="tel"
            id="regPhone"
            name="phone"
            class="cnd-auth-input <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
            placeholder="10-digit mobile number"
            value="<?= old('phone') ?>"
            autocomplete="tel"
            maxlength="10"
            pattern="[6-9][0-9]{9}"
            required
            aria-describedby="phoneError">
        </div>
        <?php if (!empty($errors['phone'])): ?>
        <p class="cnd-field-error" id="phoneError"><?= esc($errors['phone']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Password -->
      <div class="cnd-auth-input-wrap">
        <label for="regPassword" class="cnd-auth-label">Password</label>
        <div style="position:relative;">
          <i class="bi bi-lock-fill cnd-auth-input-icon"></i>
          <input
            type="password"
            id="regPassword"
            name="password"
            class="cnd-auth-input <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
            placeholder="Min. 6 characters"
            autocomplete="new-password"
            minlength="6"
            required
            aria-describedby="passError"
            oninput="updateStrength(this.value)">
          <button type="button" class="cnd-auth-toggle-pass" aria-label="Toggle password visibility" onclick="togglePass('regPassword', this)">
            <i class="bi bi-eye" aria-hidden="true"></i>
          </button>
        </div>
        <!-- Strength meter -->
        <div class="cnd-pass-strength" role="progressbar" aria-label="Password strength">
          <div class="cnd-pass-strength-bar" id="passStrengthBar"></div>
        </div>
        <?php if (!empty($errors['password'])): ?>
        <p class="cnd-field-error" id="passError"><?= esc($errors['password']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Submit -->
      <button type="submit" class="cnd-auth-btn" id="regSubmitBtn">
        <i class="bi bi-person-plus-fill me-2"></i>Create Account
      </button>

      <p class="cnd-terms-note mt-3">
        By registering, you agree to our
        <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
      </p>
    </form>

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
      Already have an account?
      <a href="<?= base_url('login') ?>">Login here →</a>
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
function updateStrength(val) {
  const bar = document.getElementById('passStrengthBar');
  let strength = 0;
  if (val.length >= 6)  strength++;
  if (val.length >= 10) strength++;
  if (/[A-Z]/.test(val)) strength++;
  if (/[0-9]/.test(val)) strength++;
  if (/[^A-Za-z0-9]/.test(val)) strength++;
  const pct = (strength / 5) * 100;
  const colors = ['#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
  bar.style.width  = pct + '%';
  bar.style.background = colors[strength - 1] || '#ef4444';
}
document.getElementById('registerForm').addEventListener('submit', function() {
  const btn = document.getElementById('regSubmitBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating account…';
});
// Only allow digits for phone
document.getElementById('regPhone').addEventListener('input', function() {
  this.value = this.value.replace(/\D/g, '').slice(0, 10);
});
</script>
<?= $this->endSection() ?>
