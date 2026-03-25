<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Welcome to the Admin Panel — a powerful, secure, and feature-rich management platform.">
  <title>Welcome | Admin Panel</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Font Awesome (loaded from existing admin assets) -->
  <link rel="stylesheet" href="<?= base_url('public/admin/plugins/fontawesome-free/css/all.min.css') ?>">

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --accent:   hsl(220, 90%, 60%);
      --accent2:  hsl(265, 80%, 65%);
      --accent3:  hsl(185, 75%, 50%);
      --dark:     hsl(222, 30%, 8%);
      --dark2:    hsl(222, 25%, 12%);
      --dark3:    hsl(222, 20%, 17%);
      --glass:    rgba(255,255,255,0.06);
      --glass-border: rgba(255,255,255,0.12);
      --text:     hsl(220, 20%, 92%);
      --muted:    hsl(220, 15%, 60%);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--dark);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ── Animated gradient background ── */
    .bg-orbs {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
    }
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.35;
      animation: drift 18s ease-in-out infinite alternate;
    }
    .orb-1 { width: 520px; height: 520px; background: var(--accent);  top: -120px; left: -80px;  animation-delay: 0s; }
    .orb-2 { width: 400px; height: 400px; background: var(--accent2); bottom: -80px; right: -60px; animation-delay: -6s; }
    .orb-3 { width: 300px; height: 300px; background: var(--accent3); top: 40%;  left: 55%;  animation-delay: -12s; }

    @keyframes drift {
      from { transform: translate(0, 0) scale(1); }
      to   { transform: translate(40px, 30px) scale(1.08); }
    }

    /* ── Layout ── */
    .page-wrapper {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* ── Navbar ── */
    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1.4rem 2.5rem;
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--glass-border);
      background: rgba(10,12,20,0.55);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      text-decoration: none;
    }
    .brand-icon {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      color: #fff;
      box-shadow: 0 4px 16px rgba(99,130,244,0.4);
    }
    .brand-name {
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text);
      letter-spacing: -0.3px;
    }
    .navbar-link {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      color: var(--muted);
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      padding: 0.5rem 1.1rem;
      border-radius: 8px;
      border: 1px solid var(--glass-border);
      background: var(--glass);
      transition: all 0.22s ease;
    }
    .navbar-link:hover {
      color: #fff;
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.22);
      transform: translateY(-1px);
    }

    /* ── Hero ── */
    .hero {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 6rem 1.5rem 4rem;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--accent);
      background: rgba(99,130,244,0.12);
      border: 1px solid rgba(99,130,244,0.3);
      border-radius: 100px;
      padding: 0.35rem 1rem;
      margin-bottom: 2rem;
      animation: fadeUp 0.6s ease both;
    }

    .hero-title {
      font-size: clamp(2.4rem, 6vw, 4.2rem);
      font-weight: 900;
      line-height: 1.1;
      letter-spacing: -2px;
      margin-bottom: 1.4rem;
      animation: fadeUp 0.6s 0.1s ease both;
    }
    .hero-title .grad {
      background: linear-gradient(135deg, var(--accent), var(--accent2) 55%, var(--accent3));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle {
      font-size: clamp(1rem, 2vw, 1.2rem);
      color: var(--muted);
      max-width: 560px;
      line-height: 1.75;
      margin-bottom: 2.8rem;
      font-weight: 400;
      animation: fadeUp 0.6s 0.2s ease both;
    }

    .hero-cta {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      justify-content: center;
      animation: fadeUp 0.6s 0.3s ease both;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.85rem 2rem;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.25s ease;
      cursor: pointer;
    }
    .btn-primary {
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      box-shadow: 0 6px 24px rgba(99,130,244,0.45);
      border: none;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 36px rgba(99,130,244,0.55);
      color: #fff;
    }
    .btn-outline {
      background: var(--glass);
      color: var(--text);
      border: 1px solid var(--glass-border);
    }
    .btn-outline:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.25);
      transform: translateY(-2px);
      color: #fff;
    }

    /* ── Stats strip ── */
    .stats-strip {
      display: flex;
      justify-content: center;
      gap: 3rem;
      flex-wrap: wrap;
      padding: 2.5rem 1.5rem;
      margin: 0 auto;
      max-width: 800px;
      animation: fadeUp 0.6s 0.4s ease both;
    }
    .stat-item { text-align: center; }
    .stat-value {
      font-size: 2rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      display: block;
    }
    .stat-label { font-size: 0.8rem; color: var(--muted); font-weight: 500; margin-top: 0.25rem; letter-spacing: 0.04em; text-transform: uppercase; }

    /* ── Features section ── */
    .section {
      padding: 5rem 1.5rem;
      max-width: 1100px;
      margin: 0 auto;
      width: 100%;
    }
    .section-title {
      text-align: center;
      font-size: clamp(1.6rem, 3.5vw, 2.4rem);
      font-weight: 800;
      letter-spacing: -1px;
      margin-bottom: 0.75rem;
      animation: fadeUp 0.6s ease both;
    }
    .section-sub {
      text-align: center;
      color: var(--muted);
      font-size: 1rem;
      margin-bottom: 3.5rem;
      animation: fadeUp 0.6s 0.1s ease both;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
    }

    .feature-card {
      background: var(--dark2);
      border: 1px solid var(--glass-border);
      border-radius: 18px;
      padding: 2rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .feature-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      opacity: 0;
      transition: opacity 0.3s ease;
      border-radius: inherit;
    }
    .feature-card:hover {
      transform: translateY(-5px);
      border-color: rgba(99,130,244,0.35);
      box-shadow: 0 20px 50px rgba(0,0,0,0.35);
    }
    .feature-card:hover::before { opacity: 0.05; }
    .feature-card > * { position: relative; z-index: 1; }

    .feature-icon {
      width: 52px; height: 52px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: #fff;
      margin-bottom: 1.2rem;
    }
    .fi-blue   { background: linear-gradient(135deg, hsl(220,80%,55%), hsl(240,80%,65%)); box-shadow: 0 6px 18px rgba(80,120,255,0.35); }
    .fi-purple { background: linear-gradient(135deg, hsl(265,70%,55%), hsl(295,70%,60%)); box-shadow: 0 6px 18px rgba(150,80,255,0.35); }
    .fi-teal   { background: linear-gradient(135deg, hsl(180,70%,40%), hsl(195,75%,50%)); box-shadow: 0 6px 18px rgba(30,190,190,0.35); }
    .fi-orange { background: linear-gradient(135deg, hsl(30,85%,50%),  hsl(50,90%,55%));  box-shadow: 0 6px 18px rgba(255,160,50,0.35); }
    .fi-rose   { background: linear-gradient(135deg, hsl(340,80%,55%), hsl(360,85%,65%)); box-shadow: 0 6px 18px rgba(255,80,110,0.35); }
    .fi-green  { background: linear-gradient(135deg, hsl(145,65%,40%), hsl(160,70%,50%)); box-shadow: 0 6px 18px rgba(40,185,110,0.35); }

    .feature-title {
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      color: var(--text);
    }
    .feature-desc {
      font-size: 0.88rem;
      color: var(--muted);
      line-height: 1.65;
    }

    /* ── CTA Banner ── */
    .cta-banner {
      max-width: 900px;
      margin: 0 auto 5rem;
      padding: 0 1.5rem;
    }
    .cta-inner {
      background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 100%);
      border-radius: 24px;
      padding: 3.5rem 3rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(99,130,244,0.35);
    }
    .cta-inner::after {
      content: '';
      position: absolute;
      width: 350px; height: 350px;
      border-radius: 50%;
      background: rgba(255,255,255,0.08);
      top: -120px; right: -80px;
      pointer-events: none;
    }
    .cta-heading {
      font-size: clamp(1.4rem, 3vw, 2rem);
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.5px;
      margin-bottom: 0.75rem;
    }
    .cta-text {
      color: rgba(255,255,255,0.8);
      margin-bottom: 2rem;
      font-size: 1rem;
    }
    .btn-white {
      background: #fff;
      color: hsl(220, 80%, 50%);
      border: none;
      font-weight: 700;
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    .btn-white:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      color: hsl(220, 80%, 45%);
    }

    /* ── Footer ── */
    .footer {
      text-align: center;
      padding: 1.8rem;
      border-top: 1px solid var(--glass-border);
      color: var(--muted);
      font-size: 0.82rem;
      background: rgba(0,0,0,0.2);
    }
    .footer a { color: var(--accent); text-decoration: none; }
    .footer a:hover { text-decoration: underline; }

    /* ── Animations ── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Responsive ── */
    @media (max-width: 600px) {
      .navbar { padding: 1rem 1.2rem; }
      .stats-strip { gap: 1.8rem; }
      .cta-inner { padding: 2.5rem 1.5rem; }
    }
  </style>
</head>
<body>

  <!-- Animated orbs -->
  <div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>

  <div class="page-wrapper">

    <!-- ── Navbar ── -->
    <nav class="navbar">
      <a href="<?= base_url('/') ?>" class="navbar-brand" id="nav-brand">
        <div class="brand-icon"><i class="fas fa-layer-group"></i></div>
        <span class="brand-name">AdminPro</span>
      </a>
      <a href="<?= base_url('auth/login') ?>" class="navbar-link" id="nav-login-btn">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </a>
    </nav>

    <!-- ── Hero ── -->
    <section class="hero" id="hero">
      <div class="hero-badge">
        <i class="fas fa-bolt"></i> Powerful &amp; Secure Admin Platform
      </div>
      <h1 class="hero-title" id="hero-title">
        Welcome to<br><span class="grad">AdminPro</span>
      </h1>
      <p class="hero-subtitle" id="hero-subtitle">
        A robust, feature-rich management panel built with CodeIgniter.
        Manage users, roles, permissions, and settings — all from one place.
      </p>
      <div class="hero-cta" id="hero-cta">
        <a href="<?= base_url('auth/login') ?>" class="btn btn-primary" id="cta-login">
          <i class="fas fa-sign-in-alt"></i> Go to Admin Login
        </a>
        <a href="#features" class="btn btn-outline" id="cta-features">
          <i class="fas fa-th-large"></i> Explore Features
        </a>
      </div>
    </section>

    <!-- ── Stats ── -->
    <div class="stats-strip" id="stats-strip">
      <div class="stat-item">
        <span class="stat-value">100%</span>
        <span class="stat-label">Secure</span>
      </div>
      <div class="stat-item">
        <span class="stat-value">∞</span>
        <span class="stat-label">Scalable</span>
      </div>
      <div class="stat-item">
        <span class="stat-value">24/7</span>
        <span class="stat-label">Uptime Ready</span>
      </div>
      <div class="stat-item">
        <span class="stat-value">CI4</span>
        <span class="stat-label">Powered</span>
      </div>
    </div>

    <!-- ── Features ── -->
    <section class="section" id="features">
      <h2 class="section-title">Everything You Need</h2>
      <p class="section-sub">A comprehensive suite of tools to run your application efficiently.</p>

      <div class="features-grid">

        <div class="feature-card">
          <div class="feature-icon fi-blue"><i class="fas fa-users-cog"></i></div>
          <div class="feature-title">User Management</div>
          <div class="feature-desc">Create, edit, activate or suspend user accounts with granular profile control and audit trails.</div>
        </div>

        <div class="feature-card">
          <div class="feature-icon fi-purple"><i class="fas fa-shield-alt"></i></div>
          <div class="feature-title">Roles &amp; Permissions</div>
          <div class="feature-desc">Define custom roles and fine-grained permissions to control exactly who can access what.</div>
        </div>

        <div class="feature-card">
          <div class="feature-icon fi-teal"><i class="fas fa-tachometer-alt"></i></div>
          <div class="feature-title">Analytics Dashboard</div>
          <div class="feature-desc">Real-time KPIs, charts, and interactive maps give you a bird's-eye view of your application.</div>
        </div>

        <div class="feature-card">
          <div class="feature-icon fi-orange"><i class="fas fa-cog"></i></div>
          <div class="feature-title">System Settings</div>
          <div class="feature-desc">Manage company info, email configs, API keys, and more — no code changes required.</div>
        </div>

        <div class="feature-card">
          <div class="feature-icon fi-rose"><i class="fas fa-history"></i></div>
          <div class="feature-title">Activity Logs</div>
          <div class="feature-desc">Full audit trail of every action taken by every user, with timestamps and IP tracking.</div>
        </div>

        <div class="feature-card">
          <div class="feature-icon fi-green"><i class="fas fa-database"></i></div>
          <div class="feature-title">Backup &amp; Restore</div>
          <div class="feature-desc">One-click database backups with restore capability to keep your data safe and recoverable.</div>
        </div>

      </div>
    </section>

    <!-- ── CTA Banner ── -->
    <div class="cta-banner" id="cta-banner">
      <div class="cta-inner">
        <h2 class="cta-heading">Ready to get started?</h2>
        <p class="cta-text">Sign in to your admin panel and take full control of your platform.</p>
        <a href="<?= base_url('auth/login') ?>" class="btn btn-white" id="cta-banner-btn">
          <i class="fas fa-arrow-right"></i> &nbsp;Go to Admin Login
        </a>
      </div>
    </div>

    <!-- ── Footer ── -->
    <footer class="footer">
      &copy; <?= date('Y') ?> AdminPro. Built with <i class="fas fa-heart" style="color:hsl(340,80%,60%)"></i> using
      <a href="https://codeigniter.com" target="_blank" rel="noopener">CodeIgniter 4</a>.
    </footer>

  </div><!-- /page-wrapper -->

  <!-- Smooth scroll for anchor links -->
  <script>
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
      anchor.addEventListener('click', function(e) {
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // Intersection observer for scroll animations
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.style.animation = 'fadeUp 0.55s ease both';
        }
      });
    }, { threshold: 0.12 });

    document.querySelectorAll('.feature-card, .section-title, .section-sub').forEach(function(el) {
      observer.observe(el);
    });
  </script>

</body>
</html>
