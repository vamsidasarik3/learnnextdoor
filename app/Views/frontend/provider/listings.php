<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<!-- ══ PROVIDER DASHBOARD HEADER ════════════════════════════════ -->
<section class="cnd-provider-hero py-5" style="background: linear-gradient(135deg, #3F3590 0%, #7778F6 100%); padding: 4rem 0 !important;">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-8 text-white">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb cnd-breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-white opacity-75 text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Provider Portal</li>
          </ol>
        </nav>
        <h1 class="display-5 fw-900 mb-2">Workspace</h1>
        <p class="lead opacity-90 mb-0">Manage your academy, track enrollments, and scale your classes.</p>
      </div>
      <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
        <a href="<?= base_url('provider/listings/create') ?>" class="btn btn-warning btn-lg rounded-pill fw-900 shadow px-5 border-0" style="background: #fbbf24; color: #000;">
          <i class="bi bi-plus-lg me-2"></i>New Listing
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ══ LISTINGS CONTENT ══════════════════════════════════════════ -->
<section class="py-5 bg-light min-vh-100">
  <div class="container">
    
    <?php if(!$user->phone_verified || !$user->email_verified || !$user->bank_account_no): ?>
    <div class="alert bg-soft-pink border-pink border-opacity-25 rounded-4 p-4 mb-5 d-flex align-items-center gap-4">
       <div class="bg-pink text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
          <i class="bi bi-shield-lock-fill fs-4"></i>
       </div>
       <div class="flex-grow-1">
          <h6 class="fw-bold mb-1">Incomplete Verification</h6>
          <p class="small text-muted mb-0">Please complete your KYC and payout details to build trust and receive payments from parents.</p>
       </div>
       <div>
          <a href="<?= base_url('provider/verification') ?>" class="btn btn-pink rounded-pill px-4 fw-bold shadow-sm">Complete Now</a>
       </div>
    </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="row g-4 mb-5" id="listingsStats">
       <!-- Dynamic counts will be injected here -->
    </div>

    <!-- Type Tabs & Search -->
    <div class="row g-3 mb-4 align-items-center">
       <div class="col-lg-8">
          <div class="bg-white rounded-pill shadow-sm p-1 d-inline-flex gap-1" id="typeTabs">
             <button class="btn btn-pink rounded-pill px-3 py-2 btn-sm tab-btn active" data-type="all">
                All <span class="badge bg-white text-pink ms-1" id="type-count-all">0</span>
             </button>
             <button class="btn btn-light rounded-pill px-3 py-2 btn-sm tab-btn text-muted fw-bold" data-type="regular">
                Regular Classes <span class="badge bg-pink text-white ms-1" id="type-count-regular">0</span>
             </button>
             <button class="btn btn-light rounded-pill px-3 py-2 btn-sm tab-btn text-muted fw-bold" data-type="course">
                Courses <span class="badge bg-pink text-white ms-1" id="type-count-course">0</span>
             </button>
             <button class="btn btn-light rounded-pill px-3 py-2 btn-sm tab-btn text-muted fw-bold" data-type="workshop">
                Workshops <span class="badge bg-pink text-white ms-1" id="type-count-workshop">0</span>
             </button>
          </div>
       </div>
       <div class="col-lg-4 text-end">
          <div class="position-relative d-inline-block">
             <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
             <input type="text" id="listingSearch" class="form-control rounded-pill ps-5 border-2" placeholder="Search my listings..." style="width: 280px; height: 44px;">
          </div>
       </div>
    </div>

    <!-- Sub-Filter: Status -->
    <div class="mb-4 text-center text-lg-start">
        <span class="text-muted small fw-bold me-3 text-uppercase" style="letter-spacing: 0.05em;">Status:</span>
        <div class="btn-group btn-group-sm rounded-pill p-1 bg-white shadow-sm" role="group">
           <input type="radio" class="btn-check filter-btn" name="statusFilter" id="statusAll" data-status="all" checked>
           <label class="btn btn-outline-pink rounded-pill border-0 px-3 fw-bold" for="statusAll">All</label>

           <input type="radio" class="btn-check filter-btn" name="statusFilter" id="statusActive" data-status="active">
           <label class="btn btn-outline-pink rounded-pill border-0 px-3 fw-bold" for="statusActive">Active</label>

           <input type="radio" class="btn-check filter-btn" name="statusFilter" id="statusInactive" data-status="inactive">
           <label class="btn btn-outline-pink rounded-pill border-0 px-3 fw-bold" for="statusInactive">Inactive</label>
        </div>
    </div>

    <!-- Listings Grid -->
    <div id="listingsWrapper">
       <div class="text-center py-5" id="listingsLoader">
          <div class="spinner-border text-pink" role="status"></div>
          <p class="mt-3 text-muted">Fetching your listings...</p>
       </div>
       <div class="row g-4 d-none" id="listingsGrid">
          <!-- Listing cards will be injected here -->
       </div>
       
       <!-- Empty State -->
       <div class="text-center py-5 d-none" id="listingsEmpty">
          <div class="bg-white rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px;">
             <i class="bi bi-journal-x fs-1 text-muted opacity-25"></i>
          </div>
          <h3>No listings found</h3>
          <p class="text-muted">You haven't listed any classes yet. Start today!</p>
          <a href="<?= base_url('provider/listings/create') ?>" class="btn btn-pink rounded-pill px-4 mt-2">Publish Your First Class</a>
       </div>
    </div>

  </div>
</section>

<style>
.cnd-provider-hero { position: relative; overflow: hidden; }
.cnd-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }
.btn-yellow { background: #F9A05E; border: none; color: #000; transition: all 0.3s; }
.btn-yellow:hover { background: #FFC800; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(249, 160, 94,0.3); }

.listing-card-p { background: #fff; border: 1px solid rgba(0,0,0,0.05); border-radius: 1.2rem; transition: all 0.3s; height: 100%; }
.listing-card-p:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,0.08) !important; }
.status-badge { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.3rem 0.8rem; border-radius: 2rem; }
.status-active { background: #d4f8e8; color: #1a7a4a; }
.status-inactive { background: #fef4e8; color: #9c6800; }
.status-pending { background: #fff8e1; color: #f57c00; }
.status-suspended { background: #fee8e8; color: #b02a37; }

.stat-card { background: #fff; border-radius: 1.2rem; padding: 1.5rem; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
.stat-icon { width: 50px; height: 50px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
(function(){
  'use strict';
  
  const grid = document.getElementById('listingsGrid');
  const loader = document.getElementById('listingsLoader');
  const empty = document.getElementById('listingsEmpty');
  const searchInput = document.getElementById('listingSearch');
  let allListings = [];
  let currentFilters = {
    type: 'all',
    status: 'all',
    q: ''
  };

  // Fetch Listings
  async function fetchListings() {
    try {
      const res = await fetch('<?= base_url('provider/api/listings') ?>');
      const json = await res.json();
      
      if(json.success) {
        allListings = json.data;
        updateTabCounts(json.counts);
        applyFiltersAndRender();
        renderStats(allListings);
      } else {
        alert(json.message || 'Failed to load listings');
      }
    } catch (e) {
      console.error(e);
      alert('Error connecting to server.');
    } finally {
      loader.classList.add('d-none');
    }
  }

  function renderListings(items) {
    grid.innerHTML = '';
    if(!items.length) {
      grid.classList.add('d-none');
      empty.classList.remove('d-none');
      return;
    }
    
    empty.classList.add('d-none');
    grid.classList.remove('d-none');

    items.forEach(l => {
      const statusClass = 'status-' + (l.status || 'inactive').toLowerCase();
      
      let batchesHtml = '';
      if (l.type === 'regular' && l.batches && l.batches.length > 0) {
          batchesHtml = `
            <div class="mt-3 pt-3 border-top">
                <div class="small fw-800 text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing:0.05em;">Batches & Enrollments</div>
                <div class="d-grid gap-2">
          `;
          l.batches.forEach((b, idx) => {
              const students = (l.batch_students && l.batch_students[idx]) || [];
              const studentList = students.length > 0 
                  ? `<div class="mt-2 ps-2 border-start border-2 border-pink ms-1" style="font-size: 0.75rem; color: #555;">
                        <i class="bi bi-person-fill small me-1"></i>${students.join(', ')}
                     </div>`
                  : `<div class="mt-1 small text-muted fst-italic ms-1" style="font-size: 0.7rem;">No students enrolled</div>`;
              
              const bDate = b.batch_start_date ? new Date(b.batch_start_date).toLocaleDateString('en-IN', {day:'numeric', month:'short'}) : 'No date';
                  
              batchesHtml += `
                <div class="p-2 rounded-3" style="background: #f8f9fa; border: 1px solid #eee;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark" style="font-size: 0.8rem;">${b.name}</div>
                        <div class="badge bg-white text-muted border fw-normal" style="font-size: 0.65rem;">${bDate}</div>
                    </div>
                    ${studentList}
                </div>
              `;
          });
          batchesHtml += `</div></div>`;
      }

      const col = document.createElement('div');
      col.className = 'col-md-6 col-lg-4';
      col.innerHTML = `
        <div class="listing-card-p p-3 d-flex flex-column">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <span class="status-badge ${statusClass}">${(l.status || 'inactive').toUpperCase()}</span>
            <div class="dropdown">
              <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item py-2" href="<?= base_url('classes') ?>/${l.id}" target="_blank"><i class="bi bi-eye me-2"></i>View Live Page</a></li>
                <li><a class="dropdown-item py-2" href="<?= base_url('provider/listings/edit') ?>/${l.id}"><i class="bi bi-pencil me-2"></i>Edit Class</a></li>
                <li><a class="dropdown-item py-2" href="<?= base_url('provider/listings/enrollments') ?>/${l.id}"><i class="bi bi-people me-2"></i>View Enrollments</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item py-2 text-danger" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
              </ul>
            </div>
          </div>
          <h5 class="fw-bold mb-1 text-truncate" title="${l.title}">${l.title}</h5>
          <div class="text-muted small mb-1">
            <i class="bi bi-tag-fill me-1"></i> ${l.category_name || 'Uncategorized'}
            ${l.subcategory_names ? `<span class="mx-1 text-opacity-25 opacity-25">></span> ${l.subcategory_names}` : ''}
            <span class="mx-1 text-opacity-25 opacity-25">|</span>
            <span class="text-uppercase fw-700" style="font-size: 0.6rem;">${l.type}</span>
          </div>
          <div class="text-muted small mb-1">
            <i class="bi bi-geo-alt-fill me-1 text-pink"></i> ${[l.locality, l.city].filter(Boolean).join(', ') || 'Address not set'}
          </div>

          ${batchesHtml}

          <div class="d-flex gap-4 border-top pt-3 mt-auto">
            <div>
              <div class="fw-bold fs-5">${l.student_count || 0}</div>
              <div class="text-muted small">Total Students</div>
            </div>
            <div>
              <div class="fw-bold fs-5">₹${Number(l.price).toLocaleString('en-IN')}</div>
              <div class="text-muted small">Base Price</div>
            </div>
          </div>
        </div>
      `;
      grid.appendChild(col);
    });
  }

  function applyFiltersAndRender() {
    let filtered = allListings;

    // Filter by Type
    if (currentFilters.type !== 'all') {
      filtered = filtered.filter(l => l.type === currentFilters.type);
    }

    // Filter by Status
    if (currentFilters.status !== 'all') {
      filtered = filtered.filter(l => {
        const s = (l.status || 'inactive').toLowerCase();
        if (currentFilters.status === 'inactive') {
            return s === 'inactive' || s === 'pending';
        }
        return s === currentFilters.status;
      });
    }

    // Filter by Search Query
    if (currentFilters.q) {
      const q = currentFilters.q.toLowerCase();
      filtered = filtered.filter(l => 
        (l.title || '').toLowerCase().includes(q) || 
        (l.category_name && l.category_name.toLowerCase().includes(q))
      );
    }

    renderListings(filtered);
  }

  function updateTabCounts(counts) {
    if(!counts) return;
    document.getElementById('type-count-all').innerText = counts.total || 0;
    document.getElementById('type-count-regular').innerText = counts.regular || 0;
    document.getElementById('type-count-course').innerText = counts.course || 0;
    document.getElementById('type-count-workshop').innerText = counts.workshop || 0;
  }

  function renderStats(items) {
    const active = items.filter(l => l.status === 'active').length;
    const totalStudents = items.reduce((sum, l) => sum + parseInt(l.student_count || 0), 0);
    
    document.getElementById('listingsStats').innerHTML = `
      <div class="col-md-4">
        <div class="stat-card">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-soft-pink text-pink" style="background: rgba(255, 104, 180,0.1);">
              <i class="bi bi-grid"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">${items.length}</h3>
              <div class="text-muted small">Total Listings</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-success text-white" style="background: #28a745 !important;">
              <i class="bi bi-check-circle"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">${active}</h3>
              <div class="text-muted small">Active Classes</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stat-card">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-yellow text-dark" style="background: #F9A05E !important;">
              <i class="bi bi-people"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">${totalStudents}</h3>
              <div class="text-muted small">Total Students</div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Tab Logic (Type Segregation)
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('btn-pink', 'active');
        b.classList.add('btn-light', 'text-muted');
      });
      this.classList.add('btn-pink', 'active');
      this.classList.remove('btn-light', 'text-muted');
      
      currentFilters.type = this.dataset.type;
      applyFiltersAndRender();
    });
  });

  // Filter Logic (Status)
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('change', function(){
      currentFilters.status = this.dataset.status;
      applyFiltersAndRender();
    });
  });

  // Search Logic
  searchInput.addEventListener('input', e => {
    currentFilters.q = e.target.value;
    applyFiltersAndRender();
  });

  fetchListings();

})();
</script>
<?= $this->endSection() ?>
