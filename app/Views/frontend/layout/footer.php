<footer class="cnd-footer" role="contentinfo" style="background: #1A1640; color: rgba(255,255,255,.75); font-family: 'Outfit', sans-serif;">

  <div class="container-fluid px-3 px-lg-5" style="max-width: 1300px; margin: 0 auto;">

    <!-- ── Top Section: Brand + Links ── -->
    <div class="row gy-5 py-5">

      <!-- Brand & Tagline -->
      <div class="col-12 col-lg-4 col-md-12">
        <a class="cnd-footer-brand d-flex align-items-center gap-3 mb-4" href="<?= base_url('/') ?>" style="text-decoration:none;">
           <div style="background:#fff; padding: 8px; border-radius: 12px; display: flex; align-items:center; justify-content:center;">
             <img src="<?= base_url('assets/frontend/img/logo-icon-new.png') ?>" alt="Learn Next Door" height="32">
           </div>
           <span style="font-size:1.4rem;font-weight:800;color:#fff;letter-spacing:-.5px;">Learn <span style="color:var(--cnd-accent);">Next Door</span></span>
        </a>
        <p class="cnd-footer-tagline opacity-75" style="max-width: 320px; line-height: 1.7; font-size: 0.95rem;">
          The leading marketplace to discover premium classes, workshops, and courses for all age groups in your city.
        </p>
        <!-- Social Links -->
        <div class="cnd-social-links mt-4 d-flex gap-3">
          <a href="#" class="cnd-social-btn" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; transition: all 0.3s;" onmouseover="this.style.background='var(--cnd-accent)'; this.style.color='#1A1640'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#fff'">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="#" class="cnd-social-btn" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; transition: all 0.3s;" onmouseover="this.style.background='var(--cnd-accent)'; this.style.color='#1A1640'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#fff'">
            <i class="bi bi-facebook"></i>
          </a>
          <a href="#" class="cnd-social-btn" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; transition: all 0.3s;" onmouseover="this.style.background='var(--cnd-accent)'; this.style.color='#1A1640'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#fff'">
            <i class="bi bi-whatsapp"></i>
          </a>
        </div>
      </div>

      <!-- Links Columns -->
      <div class="col-6 col-md-3 col-lg-2">
        <h4 class="fw-bold text-white mb-4" style="font-size: 1rem;">Explore</h4>
        <ul class="list-unstyled d-flex flex-column gap-3" style="font-size: 0.9rem;">
          <li><a href="<?= base_url('classes') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Browse Classes</a></li>
          <li><a href="<?= base_url('classes?type=workshop') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Workshops</a></li>
          <li><a href="<?= base_url('classes?type=course') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Full Courses</a></li>
          <li><a href="<?= base_url('classes?sort=rating') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Top Rated</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h4 class="fw-bold text-white mb-4" style="font-size: 1rem;">For Providers</h4>
        <ul class="list-unstyled d-flex flex-column gap-3" style="font-size: 0.9rem;">
          <li><a href="<?= base_url('provider/login') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">List your Class</a></li>
          <li><a href="<?= base_url('provider/login') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Provider Dashboard</a></li>
          <li><a href="<?= base_url('provider/register') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Join as Partner</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h4 class="fw-bold text-white mb-4" style="font-size: 1rem;">Company</h4>
        <ul class="list-unstyled d-flex flex-column gap-3" style="font-size: 0.9rem;">
          <li><a href="<?= base_url('about') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">About Us</a></li>
          <li><a href="<?= base_url('privacy') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Privacy Policy</a></li>
          <li><a href="<?= base_url('terms') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Terms of Service</a></li>
          <li><a href="<?= base_url('contact') ?>" class="text-white text-opacity-50 text-decoration-none hover-white">Contact Us</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h4 class="fw-bold text-white mb-4" style="font-size: 1rem;">Contact</h4>
        <div class="d-flex flex-column gap-3" style="font-size: 0.9rem;">
          <a href="mailto:hello@learnnextdoor.com" class="text-white text-opacity-50 text-decoration-none hover-white">hello@learnnextdoor.com</a>
          <span class="text-white text-opacity-50">+91 99999 99999</span>
        </div>
      </div>

    </div>

    <!-- ── Bottom Bar ── -->
    <div class="border-top border-white border-opacity-10 py-4 mt-4">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <p class="mb-0 small text-white text-opacity-50">
          &copy; <?= date('Y') ?> Learn Next Door. All rights reserved.
        </p>
        <div class="d-flex gap-4">
           <a href="<?= base_url('terms') ?>" class="small text-white text-opacity-50 text-decoration-none">Terms</a>
           <a href="<?= base_url('privacy') ?>" class="small text-white text-opacity-50 text-decoration-none">Privacy</a>
           <a href="<?= base_url('cookies') ?>" class="small text-white text-opacity-50 text-decoration-none">Cookies</a>
        </div>
      </div>
    </div>

  </div>
</footer>

<style>
.hover-white:hover { color: #fff !important; opacity: 1 !important; transform: translateX(5px); }
.hover-white { transition: all 0.3s ease; display: inline-block; }
</style>
