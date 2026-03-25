<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
.bk-success-hero {
  background: var(--cnd-gradient);
  min-height: 260px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  text-align: center;
  padding: 3rem 1.5rem;
  color: #fff;
}
.bk-success-icon {
  width: 90px; height: 90px;
  background: rgba(255,255,255,.2);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 2.8rem;
  margin-bottom: 1.2rem;
  animation: popIn .5s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes popIn { from { transform:scale(.3); opacity:0; } to { transform:scale(1); opacity:1; } }
.bk-success-title { font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 900; margin: 0 0 .5rem; }
.bk-success-sub   { opacity: .88; font-size: .95rem; }

.bk-success-card {
  background: #fff;
  border-radius: 20px;
  border: 1.5px solid var(--cnd-card-border);
  box-shadow: var(--cnd-card-shadow);
  overflow: hidden;
  max-width: 560px;
  margin: -2.5rem auto 2rem;
  position: relative;
  z-index: 2;
}
.bk-success-card-head {
  background: linear-gradient(135deg,#f5f0ff,#fff0f6);
  padding: .9rem 1.4rem;
  border-bottom: 1.5px solid var(--cnd-card-border);
  font-weight: 800; font-size: .9rem;
  display: flex; align-items: center; gap: .5rem;
}
.bk-success-card-head i { color: var(--cnd-pink); }
.bk-success-card-body { padding: 1.4rem; }
.bk-detail-row {
  display: flex; justify-content: space-between; align-items: flex-start;
  padding: .55rem 0;
  border-bottom: 1px solid var(--cnd-card-border);
  font-size: .88rem;
}
.bk-detail-row:last-child { border-bottom: none; }
.bk-detail-label { color: var(--cnd-muted); font-weight: 600; }
.bk-detail-value { color: var(--cnd-dark); font-weight: 700; text-align: right; max-width: 60%; }

.bk-success-actions { text-align: center; padding: 0 1.5rem 2rem; }
.bk-success-actions a, .bk-success-actions button {
  display: inline-flex; align-items: center; gap: .5rem;
  font-size: .9rem; font-weight: 700;
}
.confetti-canvas { position: fixed; inset: 0; pointer-events: none; z-index: 9999; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div class="bk-success-hero">
  <div class="bk-success-icon">🎉</div>
  <h1 class="bk-success-title">Booking Confirmed!</h1>
  <p class="bk-success-sub">
    <?php if (!empty($details['payment_status']) && in_array($details['payment_status'], ['paid','confirmed'])): ?>
    Payment received successfully. Your spot is secured!
    <?php else: ?>
    Your free class slot has been booked!
    <?php endif; ?>
  </p>
</div>

<!-- Details card -->
<div class="container px-3 py-2">
  <div class="bk-success-card">
    <div class="bk-success-card-head">
      <i class="bi bi-receipt-cutoff"></i> Booking Summary
    </div>
    <div class="bk-success-card-body">
      <?php if (!empty($details['id'])): ?>
      <div class="bk-detail-row">
        <span class="bk-detail-label">Booking ID</span>
        <span class="bk-detail-value">#<?= (int)$details['id'] ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($details['listing_title'])): ?>
      <div class="bk-detail-row">
        <span class="bk-detail-label">Class</span>
        <span class="bk-detail-value"><?= esc($details['listing_title']) ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($details['student_name'])): ?>
      <div class="bk-detail-row">
        <span class="bk-detail-label">Student</span>
        <span class="bk-detail-value"><?= esc($details['student_name']) ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($details['booking_type'])): ?>
      <div class="bk-detail-row">
        <span class="bk-detail-label">Type</span>
        <span class="bk-detail-value"><?= esc(ucfirst($details['booking_type'])) ?> Booking</span>
      </div>
      <?php endif; ?>

      <!-- Schedule Details -->
      <?php if (!empty($details['class_date'])): ?>
      <div class="bk-detail-row">
        <span class="bk-detail-label">Schedule</span>
        <span class="bk-detail-value">
          <?php
          $type = $details['listing_type'] ?? 'regular';
          $d1 = date('d M Y', strtotime($details['class_date']));
          $t1 = !empty($details['class_time']) ? date('g:i A', strtotime($details['class_time'])) : '';
          
          if ($type === 'course' && !empty($details['end_date'])) {
              $d2 = date('d M Y', strtotime($details['end_date']));
              echo "$d1 to $d2";
              if ($t1) echo "<br><small class='text-muted'>at $t1</small>";
          } elseif ($type === 'workshop') {
              echo "Workshop on $d1";
              if ($t1) echo "<br><small class='text-muted'>at $t1</small>";
          } else {
              // Regular
              $day = date('D', strtotime($details['class_date']));
              echo "$day, $d1";
              if ($t1) echo "<br><small class='text-muted'>at $t1</small>";
          }
          ?>
        </span>
      </div>
      <?php endif; ?>

      <?php if (!empty($details['address'])): ?>
      <div class="bk-detail-row">
        <span class="bk-detail-label">Location</span>
        <span class="bk-detail-value"><?= esc($details['address']) ?></span>
      </div>
      <?php endif; ?>

      <!-- Amount -->
      <div class="bk-detail-row">
        <span class="bk-detail-label">Amount Paid</span>
        <span class="bk-detail-value">
          <?php $amt = (float)($details['amount'] ?? 0); ?>
          <?php if($amt > 0): ?>
          <span style="color:#16a34a;">₹<?= number_format($amt) ?></span>
          <?php else: ?>
          <span style="color:#16a34a;">Free</span>
          <?php endif; ?>
        </span>
      </div>

      <!-- Status badge -->
      <div class="bk-detail-row">
        <span class="bk-detail-label">Status</span>
        <span class="bk-detail-value">
          <?php
          $ps = $details['payment_status'] ?? 'pending';
          $badge = '<span class="badge bg-secondary px-3 py-2">'.ucfirst($ps).'</span>';
          if (in_array($ps, ['paid','free','confirmed'])) {
              $badge = '<span class="badge bg-success px-3 py-2">✓ Confirmed</span>';
          } elseif ($ps === 'pending') {
              $badge = '<span class="badge bg-warning text-dark px-3 py-2">⏳ Pending</span>';
          }
          echo $badge;
          ?>
        </span>
      </div>
    </div>
  </div>

  <!-- WhatsApp notice -->
  <div class="text-center mb-3" style="font-size:.88rem;color:var(--cnd-muted);">
    <i class="bi bi-whatsapp text-success me-1" style="font-size:1rem;"></i>
    A WhatsApp confirmation has been sent to your registered number.
  </div>

  <!-- Actions -->
  <div class="bk-success-actions">
    <a href="<?= base_url('my-bookings') ?>" class="btn cnd-btn-primary px-4 py-2 me-2 mb-2">
      <i class="bi bi-calendar-check"></i> My Bookings
    </a>
    <a href="<?= base_url('classes') ?>" class="btn btn-outline-secondary px-4 py-2 mb-2">
      <i class="bi bi-search"></i> Browse More Classes
    </a>
  </div>
</div>

<!-- Confetti canvas -->
<canvas class="confetti-canvas" id="confettiCanvas"></canvas>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
/* Simple confetti burst on success page */
(function(){
  var canvas = document.getElementById('confettiCanvas');
  var ctx    = canvas.getContext('2d');
  canvas.width  = window.innerWidth;
  canvas.height = window.innerHeight;

  var colors = ['#3F3590','#FF6B9D','#F9A05E','#22c55e','#f59e0b','#06b6d4'];
  var particles = [];

  for(var i=0;i<120;i++){
    particles.push({
      x: Math.random()*canvas.width,
      y: Math.random()*canvas.height - canvas.height,
      r: Math.random()*7+3,
      d: Math.random()*150+80,
      color: colors[Math.floor(Math.random()*colors.length)],
      tilt: Math.random()*10-10,
      tiltAngle: 0,
      tiltAngleIncrement: Math.random()*.07+.05,
      opacity: 1,
    });
  }

  var angle = 0;
  var frame = 0;
  function draw(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    angle += 0.01;
    frame++;
    var alive = false;
    particles.forEach(function(p){
      p.tiltAngle += p.tiltAngleIncrement;
      p.y += (Math.cos(angle+p.d)+1+p.r/2) * 1.5;
      p.x += Math.sin(angle) * 2;
      p.tilt = Math.sin(p.tiltAngle) * 15;
      p.opacity = Math.max(0, p.opacity - (frame > 120 ? .005 : 0));
      if(p.y < canvas.height + 20) alive = true;
      ctx.globalAlpha = p.opacity;
      ctx.beginPath();
      ctx.lineWidth = p.r/2;
      ctx.strokeStyle = p.color;
      ctx.moveTo(p.x+p.tilt+p.r/4, p.y);
      ctx.lineTo(p.x+p.tilt, p.y+p.tilt+p.r/4);
      ctx.stroke();
    });
    ctx.globalAlpha = 1;
    if(alive && frame < 300){
      requestAnimationFrame(draw);
    } else {
      canvas.style.display='none';
    }
  }
  draw();
})();
</script>
<?= $this->endSection() ?>
