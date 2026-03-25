
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= setting('company_name') ?> | Admin Portal</title>

  <!-- Google Fonts: Outfit & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= admin_assets() ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= admin_assets() ?>/css/adminlte.min.css">

  <style>
    :root {
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --dark: #0f172a;
      --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
      --glass-bg: rgba(255, 255, 255, 0.95);
      --radius: 20px;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-gradient);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      overflow: hidden;
    }

    .login-wrapper {
      width: 100%;
      max-width: 450px;
      padding: 20px;
      z-index: 10;
    }

    .login-card {
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
      border-radius: var(--radius);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 40px;
      position: relative;
      overflow: hidden;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), #ec4899);
    }

    .brand-logo {
      text-align: center;
      margin-bottom: 30px;
    }

    .logo-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--primary), #818cf8);
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      color: white;
      font-size: 28px;
      font-family: 'Outfit';
      font-weight: 800;
      box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }

    .brand-name {
      font-family: 'Outfit';
      font-weight: 800;
      font-size: 24px;
      color: var(--dark);
      letter-spacing: -0.5px;
    }

    .login-card-body {
        padding: 0 !important;
        background: transparent !important;
    }

    .form-control {
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      padding: 12px 16px;
      height: auto;
      font-size: 15px;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-group-text {
      background: transparent;
      border: none;
      color: #94a3b8;
    }

    .btn-primary {
      background: var(--primary);
      border: none;
      border-radius: 12px;
      padding: 12px;
      font-weight: 700;
      font-family: 'Outfit';
      font-size: 16px;
      letter-spacing: 0.5px;
      transition: all 0.3s;
      margin-top: 10px;
    }

    .btn-primary:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }

    .login-box-msg {
      color: #64748b;
      font-weight: 500;
      margin-bottom: 25px;
      text-align: center;
    }

    /* Decorative Orbs */
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      z-index: 1;
      opacity: 0.5;
    }
    .orb-1 {
      width: 400px;
      height: 400px;
      background: rgba(99, 102, 241, 0.3);
      top: -200px;
      left: -200px;
    }
    .orb-2 {
      width: 300px;
      height: 300px;
      background: rgba(236, 72, 153, 0.2);
      bottom: -150px;
      right: -150px;
    }

    .error.invalid-feedback {
        font-size: 12px;
        margin-top: 5px;
        font-weight: 600;
    }
  </style>
</head>
<body class="hold-transition">
    
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-logo">
                <div class="logo-icon">C</div>
                <div class="brand-name"><?= setting('company_name') ?></div>
            </div>

            <?= $this->include('admin/layout/partials/notification') ?>
            <?= $this->renderSection("content") ?>
        </div>
        
        <p class="text-center mt-4 text-white-50" style="font-size: 13px;">
            &copy; <?= date('Y') ?> <?= setting('company_name') ?>. Secure Administration.
        </p>
    </div>

<!-- jQuery -->
<script src="<?= admin_assets() ?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= admin_assets() ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= admin_assets() ?>/js/adminlte.min.js"></script>

<!-- jquery-validation -->
<script src="<?= admin_assets() ?>/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= admin_assets() ?>/plugins/jquery-validation/additional-methods.min.js"></script>

<script>
$(function () {
  $.validator.setDefaults({
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').after(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
  $('#quickForm').validate();
});
</script>
</body>
</html>
