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

    <li class="nav-header">CLASS MANAGEMENT</li>
    <li class="nav-item">
      <a href="<?php echo url('admin/categories') ?>" class="nav-link <?php echo (@$_page->menu=='categories')?'active':'' ?>">
        <i class="nav-icon fas fa-tags"></i>
        <p>Categories</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="<?php echo url('admin/subcategories') ?>" class="nav-link <?php echo (@$_page->menu=='subcategories')?'active':'' ?>">
        <i class="nav-icon fas fa-th-list"></i>
        <p>Subcategories</p>
      </a>
    </li>
    
    <li class="nav-header">OPERATIONS & FINANCE</li>
    <li class="nav-item">
      <a href="<?php echo url('admin/bookings') ?>" class="nav-link <?php echo (@$_page->menu=='bookings')?'active':'' ?>">
        <i class="nav-icon fas fa-calendar-check"></i>
        <p>Bookings</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo url('admin/settlements') ?>" class="nav-link <?php echo (@$_page->menu=='settlements')?'active':'' ?>">
        <i class="nav-icon fas fa-wallet"></i>
        <p>Settlements</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo url('admin/refunds') ?>" class="nav-link <?php echo (@$_page->menu=='refunds')?'active':'' ?>">
        <i class="nav-icon fas fa-undo-alt"></i>
        <p>Refund Queue</p>
      </a>
    </li>

    <li class="nav-header">MARKETING & SUPPORT</li>
    <li class="nav-item">
      <a href="<?php echo url('admin/concerns') ?>" class="nav-link <?php echo (@$_page->menu=='concerns')?'active':'' ?>">
        <i class="nav-icon fas fa-headset"></i>
        <p>Provider Concerns</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo url('admin/carousel') ?>" class="nav-link <?php echo (@$_page->menu=='carousel')?'active':'' ?>">
        <i class="nav-icon fas fa-photo-video"></i>
        <p>Home Carousel</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="<?php echo url('admin/testimonials') ?>" class="nav-link <?php echo (@$_page->menu=='testimonials')?'active':'' ?>">
        <i class="nav-icon fas fa-quote-left"></i>
        <p>Testimonials</p>
      </a>
    </li>
    
  <li class="nav-header">SYSTEM OVERSIGHT</li>
  <li class="nav-item">
    <a href="<?php echo url('admin/stats') ?>" class="nav-link <?php echo (@$_page->menu=='stats')?'active':'' ?>">
      <i class="nav-icon fas fa-chart-line"></i>
      <p>Advanced Analytics</p>
    </a>
  </li>

  <li class="nav-item">
    <a href="<?php echo url('admin/activity-log') ?>" class="nav-link <?php echo (@$_page->menu=='activity_log')?'active':'' ?>">
      <i class="nav-icon fas fa-history"></i>
      <p>Audit Log</p>
    </a>
  </li>
 
  <li class="nav-item">
    <a href="<?php echo url('admin/whatsapp-logs') ?>" class="nav-link <?php echo (@$_page->menu=='whatsapp_logs')?'active':'' ?>">
      <i class="nav-icon fab fa-whatsapp"></i>
      <p>WhatsApp Logs</p>
    </a>
  </li>

  <li class="nav-item">
    <a href="<?php echo url('admin/settings') ?>" class="nav-link <?php echo (@$_page->menu=='settings')?'active':'' ?>">
      <i class="nav-icon fas fa-cogs"></i>
      <p>System Settings</p>
    </a>
  </li>
<?php endif ?>

</ul>
</nav>
<!-- /.sidebar-menu -->