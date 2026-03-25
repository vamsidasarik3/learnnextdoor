<!-- Sidebar Menu -->
<nav class="mt-2">

<ul class="nav nav-pills nav-sidebar flex-column text-sm" data-widget="treeview" role="menu" data-accordion="false">

  <li class="nav-item">
    <a href="<?php echo url('dashboard') ?>" class="nav-link <?php echo (@$_page->menu=='dashboard')?'active':'' ?>">
      <i class="nav-icon fas fa-chart-pie"></i>
      <p>
        <?php echo lang('App.dashboard') ?>
      </p>
    </a>
  </li>

  <?php if (hasPermissions('users_list')): ?>
    <li class="nav-item">
      <a href="<?php echo url('users') ?>" class="nav-link <?php echo (@$_page->menu=='users')?'active':'' ?>">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>
        <?php echo lang('App.users') ?>
        </p>
      </a>
    </li>
  <?php endif ?>

  <?php if (hasPermissions('listings_view')): ?>
    <li class="nav-header">LISTING MANAGEMENT</li>
    <li class="nav-item">
      <a href="<?php echo url('admin/listings') ?>" class="nav-link <?php echo (@$_page->menu=='management')?'active':'' ?>">
        <i class="nav-icon fas fa-list"></i>
        <p>Moderation Queue</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo url('admin/verifications') ?>" class="nav-link <?php echo (@$_page->menu=='verifications')?'active':'' ?>">
        <i class="nav-icon fas fa-user-check"></i>
        <p>Provider KYC</p>
      </a>
    </li>

    <li class="nav-header">CLASSES ARCHITECTURE</li>
    <li class="nav-item <?= in_array(@$_page->menu, ['categories', 'subcategories']) ? 'menu-open' : '' ?>">
      <a href="#" class="nav-link <?= in_array(@$_page->menu, ['categories', 'subcategories']) ? 'active' : '' ?>">
        <i class="nav-icon fas fa-sitemap"></i>
        <p>
          Classes Management
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="<?php echo url('admin/categories') ?>" class="nav-link <?php echo (@$_page->menu=='categories')?'active':'' ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Categories</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo url('admin/subcategories') ?>" class="nav-link <?php echo (@$_page->menu=='subcategories')?'active':'' ?>">
            <i class="far fa-circle nav-icon"></i>
            <p>Subcategories</p>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="nav-item">
      <a href="<?php echo url('admin/settlements') ?>" class="nav-link <?php echo (@$_page->menu=='settlements')?'active':'' ?>">
        <i class="nav-icon fas fa-wallet"></i>
        <p>Settlements</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo url('admin/carousel') ?>" class="nav-link <?php echo (@$_page->menu=='carousel')?'active':'' ?>">
        <i class="nav-icon fas fa-photo-video"></i>
        <p>Home Carousel</p>
      </a>
    </li>
  <?php endif ?>

  <!-- Settings, Logs, Roles, Backup, etc. are hidden per requirement -->

</ul>
</nav>
<!-- /.sidebar-menu -->