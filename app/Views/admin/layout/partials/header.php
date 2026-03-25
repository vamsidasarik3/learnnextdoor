  <!-- Preloader -->
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?php echo base_url('assets/admin') ?>/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- User Account -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <img src="<?php echo userProfile(logged('id')) ?>" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline"><?php echo logged('name') ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- User image -->
          <li class="user-header bg-dark">
            <img src="<?php echo userProfile(logged('id')) ?>" class="img-circle elevation-2" alt="User Image">
            <p>
              <?php echo logged('name') ?>
              <small>Member since <?= date('M, Y', strtotime(logged('created_at'))) ?></small>
            </p>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer">
            <a href="<?php echo url('profile') ?>" class="btn btn-default"><?php echo lang('App.profile') ?></a>
            <a href="<?php echo url('/auth/logout') ?>" class="btn btn-default float-right"><?php echo lang('App.signout') ?></a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-danger elevation-4">
    <!-- Brand Logo -->
    <!-- <a href="index3.html" class="brand-link">
      <img src="<?php echo base_url('assets/admin') ?>/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a> -->
    <!-- Brand Logo -->
    <a href="<?php echo url('/') ?>" class="brand-link" style="border: none !important; background: var(--sidebar-bg) !important;">
      <div class="d-flex align-items-center justify-content-center p-2 rounded-lg" style="background: linear-gradient(135deg, var(--primary), var(--danger)); border-radius: 12px; width: 40px; height: 40px; margin-right: 12px;">
        <span class="text-white fw-bold" style="font-family: 'Outfit'; font-size: 20px;">C</span>
      </div>
      <span class="brand-text font-weight-bold text-white text-uppercase tracking-wider" style="font-size: 14px; letter-spacing: 1px;"><?php echo setting('company_name') ?></span>
    </a>


    <!-- Sidebar -->
    <div class="sidebar">
      
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo userProfile(logged('id')) ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?= logged('name') ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <?= $this->include('admin/layout/partials/aside-nav') ?>
    </div>
    <!-- /.sidebar -->
  </aside>
