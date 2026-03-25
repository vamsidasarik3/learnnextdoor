  <!-- Google Fonts: Outfit & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/toastr/toastr.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/select2/css/select2.min.css" />

  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo assets_url('admin') ?>/css/adminlte.min.css">

  <!-- ── MODERN DASHBOARD OVERRIDES ── -->
  <style>
    :root {
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --secondary: #64748b;
      --success: #10b981;
      --info: #0ea5e9;
      --warning: #f59e0b;
      --danger: #ef4444;
      --dark: #0f172a;
      --light: #f8fafc;
      --white: #ffffff;
      
      --bg-main: #f1f5f9;
      --bg-card: #ffffff;
      --sidebar-bg: #0f172a;
      
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-xl: 24px;
      
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--bg-main);
      color: #334155;
    }

    h1, h2, h3, h4, h5, h6, .brand-text, .nav-link, .small-box h3 {
      font-family: 'Outfit', sans-serif;
    }

    /* Sidebar Refinement */
    .main-sidebar {
      background-color: var(--sidebar-bg) !important;
      box-shadow: var(--shadow-lg);
    }
    .brand-link {
      border-bottom: 1px solid rgba(255,255,255,0.05) !important;
      padding: 1.5rem 1rem !important;
    }
    .nav-sidebar .nav-item .nav-link {
      border-radius: var(--radius-sm);
      margin: 0.2rem 0.8rem;
      padding: 0.8rem 1rem;
      transition: all 0.3s ease;
      font-weight: 500;
      font-size: 14px;
    }
    .nav-sidebar .nav-item .nav-link i {
      font-size: 18px;
      margin-right: 12px;
      width: 24px;
      text-align: center;
    }
    .nav-sidebar .nav-link.active {
      background-color: var(--primary) !important;
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    /* Header Refinement */
    .main-header {
      border-bottom: 1px solid #e2e8f0 !important;
      padding: 0.5rem 1rem;
      background: rgba(255, 255, 255, 0.8) !important;
      backdrop-filter: blur(8px);
    }

    /* Content Wrapper */
    .content-wrapper {
      background-color: var(--bg-main);
    }

    /* Cards & Glassmorphism */
    .card {
      border: none;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      margin-bottom: 1.5rem;
      overflow: hidden;
    }
    .card-header {
      background-color: transparent;
      border-bottom: 1px solid #f1f5f9;
      padding: 1.25rem 1.5rem;
    }
    .card-title {
      font-weight: 700;
      color: var(--dark);
      font-size: 1.1rem;
    }

    /* Luxury Stat Boxes */
    .small-box {
      border-radius: var(--radius-lg);
      padding: 2rem 1.5rem;
      background: var(--white);
      border: 1px solid rgba(0,0,0,0.05);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      z-index: 1;
    }
    
    .small-box:hover {
      transform: translateY(-10px);
      box-shadow: var(--shadow-lg);
    }

    .small-box .inner {
      position: relative;
      z-index: 3;
    }

    .small-box h3 {
      font-size: 2.5rem;
      font-weight: 800;
      margin: 0 0 4px 0;
      color: var(--dark);
      letter-spacing: -1px;
    }

    .small-box p {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--secondary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
    }

    .small-box .icon {
      position: absolute;
      right: 1.5rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 4rem;
      opacity: 0.1;
      transition: all 0.4s ease;
      z-index: 2;
    }

    .small-box:hover .icon {
      opacity: 0.2;
      transform: translateY(-50%) scale(1.1) rotate(-5deg);
    }

    /* Soft Gradients for Stat Boxes */
    .bg-info-soft { background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%); border-bottom: 4px solid var(--info) !important; }
    .bg-success-soft { background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-bottom: 4px solid var(--success) !important; }
    .bg-warning-soft { background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%); border-bottom: 4px solid var(--warning) !important; }
    .bg-primary-soft { background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%); border-bottom: 4px solid var(--primary) !important; }

    .small-box-footer {
      display: none; /* Hide footer in modern design */
    }

    /* Buttons */
    .btn {
      border-radius: var(--radius-sm);
      font-weight: 600;
      padding: 0.6rem 1.2rem;
      transition: all 0.2s;
    }
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
      box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
    }
    .btn-primary:hover {
      background-color: var(--primary-hover);
      transform: translateY(-1px);
    }

    /* Custom Table Style */
    .table thead th {
      background-color: #f8fafc;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      font-weight: 700;
      color: #64748b;
      border-top: none;
      border-bottom: 2px solid #f1f5f9;
      padding: 1rem;
    }
    .table td {
      padding: 1rem;
      vertical-align: middle;
      border-top: 1px solid #f1f5f9;
    }

    /* Dashboard Layout */
    .dashboard-header { padding: 2rem 0; }
    .stats-grid { margin-bottom: 2.5rem; }

    /* Utility Gradients */
    .bg-gradient-primary { background: linear-gradient(90deg, #6366f1, #818cf8) !important; }
    .bg-gradient-success { background: linear-gradient(90deg, #10b981, #34d399) !important; }
    .bg-gradient-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24) !important; }
    .bg-gradient-danger { background: linear-gradient(90deg, #ef4444, #f87171) !important; }
    
    .badge-soft-primary { background: #e0e7ff; color: #4338ca; }
    .badge-soft-success { background: #dcfce7; color: #15803d; }
    .badge-soft-warning { background: #fef9c3; color: #a16207; }
    .badge-soft-danger { background: #fee2e2; color: #b91c1c; }
  </style>

  <!-- jQuery -->
  <script src="<?php echo assets_url('admin') ?>/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="<?php echo assets_url('admin') ?>/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="<?php echo assets_url('admin') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>


