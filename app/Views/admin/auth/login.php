<?= $this->extend('admin/auth/layout') ?>
<?= $this->section('content') ?>
    
    <div class="login-card-body">
      <p class="login-box-msg">Authentication Required</p>

      <?php echo form_open('auth/login/check', ['method' => 'POST', 'autocomplete' => 'off', 'id' => 'quickForm']); ?> 
        <?= csrf_field() ?>
        
        <div class="form-group mb-4">
            <label class="text-xs font-weight-bold text-uppercase text-muted mb-2 d-block" style="letter-spacing: 1px;">Username or Email</label>
            <div class="input-group">
                <input type="text" class="form-control" required name="username" value="admin" placeholder="Enter your credentials" value="<?php echo old('username') ?>" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            <?= form_error('username') ?>
        </div>
        
        <div class="form-group mb-4">
            <label class="text-xs font-weight-bold text-uppercase text-muted mb-2 d-block" style="letter-spacing: 1px;">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" placeholder="••••••••" value="admin123" name="password" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <?= form_error('password') ?>
        </div>

        <?php if (setting('google_recaptcha_enabled') == '1'): ?>
          <script src="https://www.google.com/recaptcha/api.js" async defer></script>
          <div class="form-group mb-4">
            <div class="g-recaptcha" data-sitekey="<?php echo setting('google_recaptcha_sitekey') ?>"></div>
            <?php echo form_error('g-recaptcha-response', '<span style="display:block" class="error invalid-feedback">', '</span>'); ?>
          </div>
        <?php endif ?>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember" name="remember_me" checked>
                <label class="custom-control-label text-sm text-muted" for="remember">Remember me</label>
            </div>
            <a href="<?php echo url('auth/forgetPassword?username='.post('username')) ?>" class="text-sm font-weight-bold text-primary text-decoration-none">Forgot?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block shadow-lg">Sign In to Dashboard</button>
        
        <?php echo form_close(); ?>
    </div>

<?=  $this->endSection() ?>
