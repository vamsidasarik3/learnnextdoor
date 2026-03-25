<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Class Next Door — API v1 Documentation</title>
  <meta name="description" content="Interactive API documentation for Class Next Door platform. Explore all v1 REST endpoints." />
  <meta name="robots" content="noindex" />

  <!-- Swagger UI CDN -->
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.18.2/swagger-ui.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

  <style>
    /* ── CSS Reset & Tokens ─────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary:      #6C63FF;
      --primary-dark: #4e46e0;
      --accent:       #FF6B6B;
      --success:      #10B981;
      --warning:      #F59E0B;
      --danger:       #EF4444;
      --bg:           #0D0E1A;
      --bg-card:      #13152b;
      --bg-elevated:  #1a1d3a;
      --surface:      #1e2247;
      --border:       rgba(108,99,255,0.2);
      --text:         #E2E8F0;
      --text-muted:   #8890B5;
      --radius:       12px;
      --font-sans:    'Inter', system-ui, sans-serif;
      --font-mono:    'JetBrains Mono', 'Fira Code', monospace;
      --glow:         0 0 40px rgba(108,99,255,0.25);
      --shadow:       0 4px 24px rgba(0,0,0,0.4);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: var(--font-sans);
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ── Top Header Bar ──────────────────────────────────────────────── */
    .api-header {
      background: linear-gradient(135deg, #0f1128 0%, #1a0e3b 50%, #0f1128 100%);
      border-bottom: 1px solid var(--border);
      padding: 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 20px rgba(0,0,0,0.5);
    }

    .api-header-inner {
      max-width: 1400px;
      margin: 0 auto;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .brand-logo {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 800;
      color: white;
      flex-shrink: 0;
    }

    .brand-text { display: flex; flex-direction: column; line-height: 1.2; }
    .brand-name { font-weight: 700; font-size: 15px; color: #fff; }
    .brand-sub  { font-size: 11px; color: var(--text-muted); letter-spacing: 0.5px; text-transform: uppercase; }

    .header-badges {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-left: auto;
      flex-wrap: wrap;
    }

    .badge {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .badge-version { background: rgba(108,99,255,0.2); color: var(--primary); border: 1px solid rgba(108,99,255,0.4); }
    .badge-oas     { background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3); }

    .header-links {
      display: flex;
      gap: 8px;
    }

    .header-link {
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .header-link-outline {
      border: 1px solid var(--border);
      color: var(--text-muted);
    }
    .header-link-outline:hover { border-color: var(--primary); color: var(--primary); background: rgba(108,99,255,0.08); }

    .header-link-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      box-shadow: 0 4px 15px rgba(108,99,255,0.3);
    }
    .header-link-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(108,99,255,0.4); }

    /* ── Info Banner ─────────────────────────────────────────────────── */
    .info-banner {
      background: linear-gradient(135deg, rgba(108,99,255,0.08), rgba(255,107,107,0.05));
      border-bottom: 1px solid var(--border);
    }

    .info-banner-inner {
      max-width: 1400px;
      margin: 0 auto;
      padding: 28px 24px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 24px;
      align-items: start;
    }

    .info-title { font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 8px; }
    .info-title span { background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .info-desc { font-size: 14px; color: var(--text-muted); line-height: 1.6; max-width: 560px; }

    .info-stats {
      display: flex;
      flex-direction: column;
      gap: 8px;
      min-width: 200px;
    }

    .stat-row {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
    }

    .stat-icon { font-size: 16px; }
    .stat-label { color: var(--text-muted); }
    .stat-value { font-weight: 600; color: var(--text); margin-left: auto; font-family: var(--font-mono); font-size: 12px; }

    /* ── Envelope Box ───────────────────────────────────────────────── */
    .envelope-box {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 24px 0;
    }

    .envelope-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px 24px;
      margin-top: 20px;
      margin-bottom: 4px;
    }

    .envelope-title {
      font-size: 13px;
      font-weight: 700;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .envelope-code {
      font-family: var(--font-mono);
      font-size: 12.5px;
      color: #a9b1d6;
      background: var(--bg);
      border-radius: 8px;
      padding: 16px 20px;
      overflow-x: auto;
      line-height: 1.7;
      border: 1px solid rgba(255,255,255,0.05);
    }

    .ec-key     { color: #7aa2f7; }
    .ec-str     { color: #9ece6a; }
    .ec-bool    { color: #ff9e64; }
    .ec-num     { color: #e0af68; }
    .ec-comment { color: #565f89; font-style: italic; }

    /* ── Rate Limit Banner ──────────────────────────────────────────── */
    .rl-banner {
      max-width: 1400px;
      margin: 8px auto 0;
      padding: 0 24px;
    }

    .rl-card {
      background: rgba(245,158,11,0.08);
      border: 1px solid rgba(245,158,11,0.25);
      border-radius: var(--radius);
      padding: 12px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 13px;
    }

    .rl-icon { font-size: 18px; }
    .rl-text { color: var(--text-muted); }
    .rl-text strong { color: #fbbf24; }
    .rl-badges { margin-left: auto; display: flex; gap: 6px; flex-wrap: wrap; }
    .rl-badge { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); border-radius: 6px; padding: 2px 8px; font-size: 11px; font-family: var(--font-mono); font-weight: 600; }

    /* ── Swagger UI Container ───────────────────────────────────────── */
    #swagger-ui-wrapper {
      max-width: 1400px;
      margin: 20px auto 0;
      padding: 0 24px 40px;
    }

    /* ── Swagger UI Overrides (dark theme) ──────────────────────────── */
    #swagger-ui {
      background: transparent;
      font-family: var(--font-sans) !important;
    }

    #swagger-ui .swagger-ui .info { display: none; }
    #swagger-ui .swagger-ui .scheme-container { display: none; }

    #swagger-ui .swagger-ui .opblock-tag {
      background: var(--bg-card) !important;
      border: 1px solid var(--border) !important;
      border-radius: var(--radius) !important;
      margin-bottom: 8px !important;
      color: var(--text) !important;
      font-family: var(--font-sans) !important;
      font-weight: 600 !important;
      font-size: 14px !important;
      padding: 12px 16px !important;
    }

    #swagger-ui .swagger-ui .opblock-tag:hover {
      background: var(--bg-elevated) !important;
    }

    #swagger-ui .swagger-ui .opblock {
      border-radius: 10px !important;
      margin-bottom: 8px !important;
      border: 1px solid var(--border) !important;
      box-shadow: none !important;
      background: var(--bg-card) !important;
    }

    #swagger-ui .swagger-ui .opblock .opblock-summary {
      border-bottom: none !important;
      padding: 12px 16px !important;
    }

    #swagger-ui .swagger-ui .opblock .opblock-summary-method {
      border-radius: 6px !important;
      font-family: var(--font-mono) !important;
      font-weight: 700 !important;
      font-size: 12px !important;
      min-width: 70px !important;
    }

    #swagger-ui .swagger-ui .opblock .opblock-summary-path {
      font-family: var(--font-mono) !important;
      font-size: 14px !important;
      color: var(--text) !important;
    }

    #swagger-ui .swagger-ui .opblock.opblock-get     { border-left: 3px solid #7aa2f7 !important; }
    #swagger-ui .swagger-ui .opblock.opblock-post    { border-left: 3px solid var(--success) !important; }
    #swagger-ui .swagger-ui .opblock.opblock-put     { border-left: 3px solid var(--warning) !important; }
    #swagger-ui .swagger-ui .opblock.opblock-patch   { border-left: 3px solid #bb9af7 !important; }
    #swagger-ui .swagger-ui .opblock.opblock-delete  { border-left: 3px solid var(--danger) !important; }

    #swagger-ui .swagger-ui table { color: var(--text) !important; }
    #swagger-ui .swagger-ui .parameter__name { color: var(--text) !important; font-family: var(--font-mono) !important; }
    #swagger-ui .swagger-ui .parameter__type { color: var(--text-muted) !important; }

    #swagger-ui .swagger-ui .opblock-body,
    #swagger-ui .swagger-ui .tab-header .tab-item.active h4 span::after,
    #swagger-ui .swagger-ui .response-control-media-type__accept-message,
    #swagger-ui .swagger-ui .response-col_status { color: var(--text) !important; }

    #swagger-ui .swagger-ui .btn.execute {
      background: var(--primary) !important;
      border-color: var(--primary) !important;
      color: white !important;
      border-radius: 8px !important;
      font-family: var(--font-sans) !important;
      font-weight: 600 !important;
      padding: 8px 20px !important;
    }

    #swagger-ui .swagger-ui .btn.execute:hover {
      background: var(--primary-dark) !important;
    }

    #swagger-ui .swagger-ui .btn.cancel {
      color: var(--text-muted) !important;
      border-color: var(--border) !important;
      border-radius: 8px !important;
    }

    #swagger-ui .swagger-ui .btn.authorize {
      background: rgba(16,185,129,0.15) !important;
      border-color: var(--success) !important;
      color: var(--success) !important;
      border-radius: 8px !important;
      font-weight: 600 !important;
    }

    #swagger-ui .swagger-ui input, #swagger-ui .swagger-ui textarea, #swagger-ui .swagger-ui select {
      background: var(--bg) !important;
      border: 1px solid var(--border) !important;
      color: var(--text) !important;
      border-radius: 6px !important;
      font-family: var(--font-mono) !important;
    }

    #swagger-ui .swagger-ui .highlight-code { background: var(--bg) !important; border-radius: 6px !important; }
    #swagger-ui .swagger-ui .microlight { font-family: var(--font-mono) !important; font-size: 12px !important; }

    /* ── Footer ────────────────────────────────────────────────────── */
    .docs-footer {
      background: var(--bg-card);
      border-top: 1px solid var(--border);
      padding: 20px 24px;
      text-align: center;
      color: var(--text-muted);
      font-size: 12px;
    }

    .docs-footer a { color: var(--primary); text-decoration: none; }
    .docs-footer a:hover { text-decoration: underline; }

    /* ── Responsive ─────────────────────────────────────────────────── */
    @media (max-width: 768px) {
      .info-grid { grid-template-columns: 1fr; }
      .info-stats { flex-direction: row; flex-wrap: wrap; min-width: auto; }
      .stat-row { flex: 1; min-width: 120px; }
      .header-links { flex-wrap: wrap; }
      .rl-badges { display: none; }
    }
  </style>
</head>
<body>

  <!-- ── Top Header ─────────────────────────────────────────────────── -->
  <header class="api-header">
    <div class="api-header-inner">
      <a href="/" class="brand">
        <div class="brand-logo">C</div>
        <div class="brand-text">
          <span class="brand-name">Class Next Door</span>
          <span class="brand-sub">API Docs</span>
        </div>
      </a>

      <div class="header-badges">
        <span class="badge badge-version">v1.0</span>
        <span class="badge badge-oas">OpenAPI 3.0</span>
      </div>

      <div class="header-links">
        <a href="/v1/openapi.json" class="header-link header-link-outline" target="_blank" title="Download OpenAPI spec">
          ⬇ openapi.json
        </a>
        <a href="/v1/health" class="header-link header-link-outline" target="_blank" title="Check API health">
          ♡ Health
        </a>
        <a href="/" class="header-link header-link-primary">
          ← Back to Site
        </a>
      </div>
    </div>
  </header>

  <!-- ── Info Banner ────────────────────────────────────────────────── -->
  <section class="info-banner">
    <div class="info-banner-inner">
      <div class="info-grid">
        <div>
          <h1 class="info-title">Class Next Door <span>REST API</span></h1>
          <p class="info-desc">
            Version 1.0 · All endpoints are under <code style="background:rgba(108,99,255,0.15);padding:2px 6px;border-radius:4px;font-size:12px;">/v1/</code>.
            Every response follows the standard JSON envelope with <code style="background:rgba(108,99,255,0.15);padding:2px 6px;border-radius:4px;font-size:12px;">success</code>,
            <code style="background:rgba(108,99,255,0.15);padding:2px 6px;border-radius:4px;font-size:12px;">data</code>, and <code style="background:rgba(108,99,255,0.15);padding:2px 6px;border-radius:4px;font-size:12px;">meta</code> fields.
            Mobile-first design; all responses are UTF-8 JSON.
          </p>
        </div>
        <div class="info-stats">
          <div class="stat-row"><span class="stat-icon">🔗</span><span class="stat-label">Base URL</span><span class="stat-value">/v1</span></div>
          <div class="stat-row"><span class="stat-icon">📄</span><span class="stat-label">Format</span><span class="stat-value">JSON</span></div>
          <div class="stat-row"><span class="stat-icon">🔐</span><span class="stat-label">Auth</span><span class="stat-value">Session Cookie</span></div>
          <div class="stat-row"><span class="stat-icon">⚡</span><span class="stat-label">Rate Limit</span><span class="stat-value">120 req/min</span></div>
          <div class="stat-row"><span class="stat-icon">🌐</span><span class="stat-label">CORS</span><span class="stat-value">Enabled</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── Standard Response Envelope ────────────────────────────────── -->
  <div class="envelope-box">
    <div class="envelope-card">
      <div class="envelope-title">📦 Standard Response Envelope</div>
      <pre class="envelope-code"><span class="ec-comment">// ✔ Success response</span>
{
  <span class="ec-key">"success"</span>:     <span class="ec-bool">true</span>,
  <span class="ec-key">"api_version"</span>: <span class="ec-str">"1.0"</span>,
  <span class="ec-key">"timestamp"</span>:   <span class="ec-str">"2026-02-25T18:30:00+05:30"</span>,
  <span class="ec-key">"data"</span>:        <span class="ec-comment">/* payload — array or object */</span>,
  <span class="ec-key">"meta"</span>:        { <span class="ec-key">"total"</span>: <span class="ec-num">120</span>, <span class="ec-key">"page"</span>: <span class="ec-num">1</span>, <span class="ec-key">"per_page"</span>: <span class="ec-num">12</span>, <span class="ec-key">"total_pages"</span>: <span class="ec-num">10</span>, <span class="ec-key">"has_next"</span>: <span class="ec-bool">true</span>, <span class="ec-key">"has_prev"</span>: <span class="ec-bool">false</span> }
}

<span class="ec-comment">// ✗ Error response</span>
{
  <span class="ec-key">"success"</span>:     <span class="ec-bool">false</span>,
  <span class="ec-key">"api_version"</span>: <span class="ec-str">"1.0"</span>,
  <span class="ec-key">"timestamp"</span>:   <span class="ec-str">"2026-02-25T18:30:00+05:30"</span>,
  <span class="ec-key">"error"</span>:       <span class="ec-str">"not_found"</span>,        <span class="ec-comment">/* machine-readable code */</span>
  <span class="ec-key">"message"</span>:     <span class="ec-str">"Listing not found."</span>  <span class="ec-comment">/* human-readable message */</span>
}</pre>
    </div>
  </div>

  <!-- ── Rate Limit Info ────────────────────────────────────────────── -->
  <div class="rl-banner">
    <div class="rl-card">
      <span class="rl-icon">⚡</span>
      <span class="rl-text">
        <strong>Rate Limiting</strong> — 120 requests per 60-second window per IP.
        Exceeding it returns HTTP 429 with <code style="font-family:monospace;font-size:11px;">"error": "rate_limit_exceeded"</code>.
        Check response headers for quota status.
      </span>
      <div class="rl-badges">
        <span class="rl-badge">X-RateLimit-Limit</span>
        <span class="rl-badge">X-RateLimit-Remaining</span>
        <span class="rl-badge">X-RateLimit-Reset</span>
      </div>
    </div>
  </div>

  <!-- ── Swagger UI ─────────────────────────────────────────────────── -->
  <div id="swagger-ui-wrapper">
    <div id="swagger-ui"></div>
  </div>

  <!-- ── Footer ────────────────────────────────────────────────────── -->
  <footer class="docs-footer">
    Class Next Door API v1.0 &nbsp;·&nbsp;
    <a href="/v1/openapi.json" target="_blank">Download Spec (OAS 3.0)</a> &nbsp;·&nbsp;
    <a href="/v1/health" target="_blank">Health Check</a> &nbsp;·&nbsp;
    Subtask 4.1 — API Design for Extensibility
  </footer>

  <!-- Swagger UI JS -->
  <script src="https://unpkg.com/swagger-ui-dist@5.18.2/swagger-ui-bundle.js"></script>
  <script src="https://unpkg.com/swagger-ui-dist@5.18.2/swagger-ui-standalone-preset.js"></script>

  <script>
    window.addEventListener('DOMContentLoaded', function () {
      const ui = SwaggerUIBundle({
        url:             '<?= base_url('v1/openapi.json') ?>',
        dom_id:          '#swagger-ui',
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset,
        ],
        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl,
        ],
        layout:          'StandaloneLayout',
        deepLinking:     true,
        defaultModelsExpandDepth: 2,
        defaultModelExpandDepth:  2,
        docExpansion:    'list',
        filter:          true,
        showExtensions:  true,
        showCommonExtensions: true,
        tryItOutEnabled: true,
        requestInterceptor: function(request) {
          // Ensure cookies are sent for session auth
          request.credentials = 'include';
          return request;
        },
      });

      window.ui = ui;
    });
  </script>
</body>
</html>
