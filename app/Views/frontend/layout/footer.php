<!--
  FOOTER PARTIAL — frontend/layout/footer.php
  ─────────────────────────────────────────────
  Responsive 4-column grid on desktop → stacked on mobile.
  Uses Bootstrap 5 grid: col-12 → col-md-6 → col-lg-3
  Complementary color to navbar, WCAG-accessible contrast.
  ─────────────────────────────────────────────
-->
<footer class="cnd-footer" role="contentinfo">

  <div class="container-fluid px-3 px-lg-5">

    <!-- ── Top Section: Brand + Links ── -->
    <div class="row gy-4 py-5">

      <!-- Brand & Tagline (full-width on mobile, 4/12 on lg) -->
      <div class="col-12 col-lg-4 col-md-6">
        <a class="cnd-footer-brand d-flex align-items-center gap-2 mb-2" href="<?= base_url('/') ?>" aria-label="Learn Next Door Home" style="text-decoration:none;">
           <img src="<?= base_url('assets/frontend/img/logo-icon.png') ?>" alt="Learn Next Door icon" height="36" class="flex-shrink-0">
           <span style="font-size:1.1rem;font-weight:800;color:#fff;letter-spacing:-.3px;line-height:1;">Learn <span style="color:var(--cnd-accent);">Next Door</span></span>
        </a>
        <p class="cnd-footer-tagline mt-2">
          Connecting parents with the best local class providers — sports, arts, academics, and more.
        </p>
        <!-- Social Links -->
        <div class="cnd-social-links mt-3 d-flex gap-3" role="list" aria-label="Social media links">
          <a href="#" class="cnd-social-btn" aria-label="Follow us on Instagram" role="listitem">
            <i class="bi bi-instagram" aria-hidden="true"></i>
          </a>
          <a href="#" class="cnd-social-btn" aria-label="Follow us on Facebook" role="listitem">
            <i class="bi bi-facebook" aria-hidden="true"></i>
          </a>
          <a href="#" class="cnd-social-btn" aria-label="Chat with us on WhatsApp" role="listitem">
            <i class="bi bi-whatsapp" aria-hidden="true"></i>
          </a>
          <a href="#" class="cnd-social-btn" aria-label="Follow us on X / Twitter" role="listitem">
            <i class="bi bi-twitter-x" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <!-- Explore Links -->
      <div class="col-6 col-md-3 col-lg-2 offset-lg-1">
        <h3 class="cnd-footer-heading">Explore</h3>
        <ul class="list-unstyled cnd-footer-links" role="list">
          <li><a href="<?= base_url('classes') ?>">Browse Classes</a></li>
          <li><a href="<?= base_url('classes?type=workshop') ?>">Workshops</a></li>
          <li><a href="<?= base_url('classes?type=course') ?>">Courses</a></li>
          <li><a href="<?= base_url('classes?sort=rating') ?>">Top Rated</a></li>
          <li><a href="<?= base_url('classes?sort=distance') ?>">Near Me</a></li>
        </ul>
      </div>

      <!-- For Providers Links -->
      <div class="col-6 col-md-3 col-lg-2">
        <h3 class="cnd-footer-heading">Providers</h3>
        <ul class="list-unstyled cnd-footer-links" role="list">
          <li><a href="<?= base_url('provider/login') ?>">List a Class</a></li>
          <li><a href="<?= base_url('provider/login') ?>">Provider Login</a></li>
          <li><a href="<?= base_url('provider/register') ?>">Register</a></li>
          <li><a href="<?= base_url('contact') ?>">Help Centre</a></li>
        </ul>
      </div>

      <!-- Company Links -->
      <div class="col-6 col-md-3 col-lg-2">
        <h3 class="cnd-footer-heading">Company</h3>
        <ul class="list-unstyled cnd-footer-links" role="list">
          <li><a href="<?= base_url('about') ?>">About Us</a></li>
          <li><a href="<?= base_url('contact') ?>">Contact Us</a></li>
          <li><a href="<?= base_url('privacy') ?>">Privacy Policy</a></li>
          <li><a href="<?= base_url('terms') ?>">Terms &amp; Conditions</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="col-6 col-md-3 col-lg-2">
        <h3 class="cnd-footer-heading">Contact</h3>
        <address class="cnd-footer-contact">
          <p>
            <i class="bi bi-envelope-fill me-1 text-primary" aria-hidden="true"></i>
            <a href="mailto:hello@classnextdoor.in">hello@classnextdoor.in</a>
          </p>
          <p>
            <i class="bi bi-whatsapp me-1 text-success" aria-hidden="true"></i>
            <a href="https://wa.me/91XXXXXXXXXX" rel="noopener">WhatsApp Us</a>
          </p>
        </address>
      </div>

    </div>
    <!-- /.row -->

    <!-- ── Bottom Bar ── -->
    <div class="cnd-footer-bottom">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <p class="mb-0 small">
          &copy; <?= date('Y') ?> Class Next Door. All rights reserved.
        </p>
        <p class="mb-0 small text-muted">
          Made with <span aria-label="love">❤</span> in India
        </p>
      </div>
    </div>

  </div>
  <!-- /.container-fluid -->

</footer>
