<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('css') ?>
<style>
.cnd-auth-section {
  min-height: calc(100vh - 80px);
  display: flex; align-items: center; justify-content: center;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #f5f0ff 0%, #fff0f6 50%, #fff8e1 100%);
}
.cnd-auth-card {
  background: #fff; border-radius: 24px;
  box-shadow: 0 20px 60px rgba(108,99,255,.12), 0 4px 20px rgba(0,0,0,.06);
  padding: 2.5rem 2rem; width: 100%; max-width: 440px;
  animation: authFadeUp .4s ease both;
}
@keyframes authFadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.cnd-auth-logo { display: flex; align-items: center; justify-content: center; gap: .6rem; margin-bottom: 1.8rem; text-decoration: none; }
.cnd-auth-logo-icon { width: 44px; height: 44px; background: var(--cnd-gradient); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.3rem; }
.cnd-auth-logo-text { font-size: 1.25rem; font-weight: 800; background: var(--cnd-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.cnd-auth-title { font-size: 1.6rem; font-weight: 800; color: var(--cnd-dark); margin-bottom: .3rem; text-align: center; }
.cnd-auth-subtitle { font-size: .9rem; color: var(--cnd-muted); text-align: center; margin-bottom: 1.8rem; }
.otp-input { width: 100%; letter-spacing: 0.5rem; font-size: 2rem; text-align: center; font-weight: 800; border: 2px solid var(--cnd-card-border); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; transition: all .2s; }
.otp-input:focus { border-color: var(--cnd-grad-start); box-shadow: 0 0 0 4px rgba(108,99,255,.1); outline: none; }
.cnd-auth-btn { width: 100%; padding: .9rem; background: var(--cnd-gradient); border: none; border-radius: var(--cnd-radius-pill); color: #fff; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all .2s; box-shadow: 0 6px 20px rgba(108,99,255,.3); }
.cnd-auth-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(108,99,255,.35); opacity: .92; }
.resend-link { text-align: center; margin-top: 1.5rem; font-size: .9rem; color: var(--cnd-muted); }
.resend-link a { color: var(--cnd-grad-start); font-weight: 700; text-decoration: none; }
.resend-link a:hover { text-decoration: underline; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="cnd-auth-section">
  <div class="cnd-auth-card">
    <a href="<?= base_url('/') ?>" class="cnd-auth-logo">
      <div class="cnd-auth-logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
      <span class="cnd-auth-logo-text">verify email</span>
    </a>

    <h1 class="cnd-auth-title">Check your inbox</h1>
    <p class="cnd-auth-subtitle">We've sent a 6-digit code to <br><strong><?= esc($email) ?></strong></p>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger py-2 rounded-3 small mb-3">
      <i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 rounded-3 small mb-3">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/verify-email') ?>" method="post">
      <?= csrf_field() ?>
      <input type="text" name="otp" class="otp-input" placeholder="------" maxlength="6" required autofocus autocomplete="one-time-code">
      <button type="submit" class="cnd-auth-btn">Verify Account</button>
    </form>

    <div class="resend-link">
      Didn't receive the code? 
      <a href="javascript:void(0)" id="resendBtn">Resend OTP</a>
    </div>
  </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.getElementById('resendBtn').addEventListener('click', function(e) {
  e.preventDefault();
  const btn = this;
  btn.style.pointerEvents = 'none';
  btn.style.opacity = '0.5';
  btn.innerText = 'Sending...';

  fetch('<?= base_url('auth/resend-otp') ?>', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
    }
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      alert(res.message);
      btn.innerText = 'Sent!';
      setTimeout(() => {
        btn.style.pointerEvents = 'auto';
        btn.style.opacity = '1';
        btn.innerText = 'Resend OTP';
      }, 30000);
    } else {
      alert(res.message);
      btn.innerText = 'Resend OTP';
      btn.style.pointerEvents = 'auto';
      btn.style.opacity = '1';
    }
  });
});
</script>
<?= $this->endSection() ?>
