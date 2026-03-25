<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>

<section class="min-vh-100 py-5 bg-light">
  <div class="container py-lg-5">
    
    <div class="text-center mb-5">
       <h1 class="display-4 fw-bold mb-3">Get in <span class="text-pink">Touch</span></h1>
       <p class="text-muted lead mx-auto" style="max-width: 600px;">Have questions about a class or need help with your booking? Our team is here to support you.</p>
    </div>

    <div class="row g-4 justify-content-center">
      
      <!-- ── Contact Information ── -->
      <div class="col-lg-4">
        <div class="d-flex flex-column gap-4">
          
          <div class="card border-0 shadow-sm rounded-4 p-4 card-hover">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-soft-pink text-pink rounded-circle p-3 me-3">
                <i class="bi bi-envelope-at-fill fs-3"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">Email Us</h5>
                <p class="text-muted small mb-0">Best for detailed queries</p>
              </div>
            </div>
            <a href="mailto:hello@classnextdoor.in" class="text-pink fw-bold text-decoration-none">hello@classnextdoor.in</a>
          </div>

          <div class="card border-0 shadow-sm rounded-4 p-4 card-hover">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                <i class="bi bi-whatsapp fs-3"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">WhatsApp</h5>
                <p class="text-muted small mb-0">Instant support & chat</p>
              </div>
            </div>
            <a href="https://wa.me/91XXXXXXXXXX" target="_blank" class="text-success fw-bold text-decoration-none">Chat with us on WhatsApp</a>
          </div>

          <div class="card border-0 shadow-sm rounded-4 p-4 card-hover">
            <div class="d-flex align-items-center mb-3">
              <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                <i class="bi bi-clock-history fs-3"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">Response Time</h5>
                <p class="text-muted small mb-0">Our promise to you</p>
              </div>
            </div>
            <p class="text-dark fw-bold mb-0">Within 24 hours</p>
          </div>

        </div>
      </div>

      <!-- ── Contact Form ── -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="card-body p-4 p-lg-5">
            <h3 class="fw-bold mb-4">Send us a message</h3>
            
            <form id="feedbackForm" novalidate>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-uppercase text-muted">Full Name</label>
                  <input type="text" name="name" class="form-control rounded-3 py-2 border-2" placeholder="e.g. Priya Sharma" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-uppercase text-muted">Email Address</label>
                  <input type="email" name="email" class="form-control rounded-3 py-2 border-2" placeholder="you@example.com" required>
                </div>
                <div class="col-md-12">
                  <label class="form-label small fw-bold text-uppercase text-muted">Subject</label>
                  <select name="subject" class="form-select rounded-3 py-2 border-2" required>
                    <option value="" disabled selected>Choose a topic</option>
                    <option value="General Enquiry">General Enquiry</option>
                    <option value="Booking Support">Booking Support</option>
                    <option value="Class Feedback">Class Feedback</option>
                    <option value="Provider Partnership">Provider Partnership</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label class="form-label small fw-bold text-uppercase text-muted">Message</label>
                  <textarea name="message" class="form-control rounded-3 py-2 border-2" rows="5" placeholder="How can we help you today?" required minlength="10"></textarea>
                </div>
                <div class="col-md-12 mt-4">
                  <button type="submit" id="feedbackSubmitBtn" class="btn btn-pink w-100 py-3 rounded-pill fw-bold shadow-sm">
                    <span id="feedbackSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                    <i class="bi bi-send-fill me-2"></i>Send Message
                  </button>
                </div>
              </div>
            </form>

            <div id="feedbackSuccess" class="alert alert-success d-none mt-4 rounded-4 border-0 shadow-sm">
               <i class="bi bi-check-circle-fill me-2"></i>Thank you! Your message has been sent successfully.
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<style>
.bg-soft-pink { background: rgba(255, 104, 180, 0.08); }
.text-pink { color: #FF68B4; }
.btn-pink { background: #FF68B4; color: #fff; border: none; }
.btn-pink:hover { background: #FF1493; color: #fff; }
.form-control:focus, .form-select:focus { border-color: #FF68B4; box-shadow: 0 0 0 0.25rem rgba(255, 104, 180, 0.1); }
.card-hover { transition: transform 0.3s ease; }
.card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
(function(){
  'use strict';

  const form = document.getElementById('feedbackForm');
  const btn  = document.getElementById('feedbackSubmitBtn');
  const spin = document.getElementById('feedbackSpinner');
  const succ = document.getElementById('feedbackSuccess');

  form.addEventListener('submit', function(e){
    e.preventDefault();
    if(!form.checkValidity()){ form.classList.add('was-validated'); return; }

    btn.disabled = true;
    spin.classList.remove('d-none');

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch('<?= base_url('api/feedback') ?>', {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
      btn.disabled = false;
      spin.classList.add('d-none');
      if(res.success){
        form.reset();
        form.classList.remove('was-validated');
        succ.classList.remove('d-none');
        setTimeout(() => { succ.classList.add('d-none'); }, 5000);
      } else { alert(res.message || 'Error sending feedback.'); }
    })
    .catch(() => {
      btn.disabled = false;
      spin.classList.add('d-none');
    });
  });
})();
</script>
<?= $this->endSection() ?>
