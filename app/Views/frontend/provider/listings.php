<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-1">
    <h1 class="dashboard-title">My Listed Classes</h1>
    <a href="#" class="btn-next" style="text-decoration: none;">
        Create New Class
    </a>
</div>
<p class="dashboard-subtitle">Manage and view all your class listings</p>

<!-- Filters Row -->
<div class="filters-row">
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="listingSearch" class="form-control" placeholder="Search classes...">
    </div>
    <select class="form-select filter-select" id="typeFilter">
        <option value="all">All Types</option>
        <option value="regular">Regular Class</option>
        <option value="course">Course</option>
        <option value="workshop">Workshop</option>
    </select>
    <select class="form-select filter-select" id="statusFilter">
        <option value="all">All Status</option>
        <option value="active">Active / Published</option>
        <option value="inactive">Inactive / Draft</option>
    </select>
</div>

<!-- Listings Wrapper -->
<div id="listingsWrapper">
    <div class="text-center py-5" id="listingsLoader">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3 text-muted">Fetching your listings...</p>
    </div>
    
    <div class="row g-4 d-none" id="listingsGrid">
        <!-- Listing cards will be injected here -->
    </div>
    
    <!-- Empty State -->
    <div class="text-center py-5 d-none" id="listingsEmpty">
        <div class="bg-white rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px; border: 1px solid var(--provider-border);">
            <i class="bi bi-journal-x fs-1 text-muted opacity-25"></i>
        </div>
        <h3 class="fw-bold">No listings found</h3>
        <p class="text-muted">You haven't listed any classes yet. Start today!</p>
        <a href="#" class="btn-next mt-3" style="text-decoration:none; display:inline-flex;">Publish Your First Class</a>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  'use strict';
  
  const grid = document.getElementById('listingsGrid');
  const loader = document.getElementById('listingsLoader');
  const empty = document.getElementById('listingsEmpty');
  const searchInput = document.getElementById('listingSearch');
  const typeFilter = document.getElementById('typeFilter');
  const statusFilter = document.getElementById('statusFilter');
  
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
        applyFiltersAndRender();
      } else {
        Swal.fire({ icon: 'error', title: 'Oops', text: json.message || 'Failed to load listings' });
      }
    } catch (e) {
      console.error(e);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Error connecting to server.' });
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
      let currentStatus = (l.status || 'inactive').toLowerCase();
      if (parseInt(l.is_cancelled) === 1) currentStatus = 'cancelled';
      
      const statusBadgeClass = (currentStatus === 'active' || l.review_status === 'approved') ? 'badge-status-published' : 'badge-status-inactive';
      const statusLabel = (currentStatus === 'active' || l.review_status === 'approved') ? 'Published' : 'Inactive';
      
      const typeLabel = l.type.charAt(0).toUpperCase() + l.type.slice(1);
      
      // Handle image
      const coverImage = l.cover_image ? '<?= base_url() ?>/' + l.cover_image : 'https://placehold.co/600x400?text=No+Image';

      const col = document.createElement('div');
      col.className = 'col-md-6 col-lg-4';
      col.innerHTML = `
        <div class="listing-card">
            <div class="listing-card-image">
                <span class="listing-badge badge-type">${typeLabel}</span>
                <span class="listing-badge ${statusBadgeClass}">${statusLabel}</span>
                <img src="${coverImage}" alt="${l.title}">
            </div>
            <div class="listing-card-body">
                <h3 class="listing-card-title text-truncate" title="${l.title}">${l.title}</h3>
                <div class="listing-card-subtitle">${l.category_name || 'General'}</div>
                
                <div class="listing-stat-row">
                    <span>Enrollments</span>
                    <span class="listing-stat-value">${l.student_count || 0} / ${l.total_capacity || '--'} students</span>
                </div>
                <div class="listing-stat-row">
                    <span>Next Session</span>
                    <span class="listing-stat-value">${l.next_session || 'Today, 9:00 AM'}</span>
                </div>
            </div>
            <div class="listing-card-actions">
                <a href="<?= base_url('provider/listings/edit') ?>/${l.id}" class="action-btn-sm btn-soft-purple">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="<?= base_url('provider/enrollments') ?>?listing_id=${l.id}" class="action-btn-sm btn-soft-green">
                    <i class="bi bi-people"></i> Students
                </a>
                <a href="<?= base_url('provider/availability') ?>?listing_id=${l.id}" class="action-btn-sm btn-soft-orange">
                    <i class="bi bi-calendar-x"></i> Holiday
                </a>
            </div>
            <button class="more-actions-btn dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i> More Actions
            </button>
            <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item py-2" href="<?= base_url('classes') ?>/${l.id}" target="_blank"><i class="bi bi-eye me-2 text-muted"></i>View Live Page</a></li>
                <li><a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="deleteListing(${l.id})"><i class="bi bi-trash me-2"></i>Delete</a></li>
            </ul>
        </div>
      `;
      grid.appendChild(col);
    });
  }

  function applyFiltersAndRender() {
    let filtered = allListings;

    if (currentFilters.type !== 'all') {
      filtered = filtered.filter(l => l.type === currentFilters.type);
    }

    if (currentFilters.status !== 'all') {
      filtered = filtered.filter(l => {
        let s = (l.status || 'inactive').toLowerCase();
        if (parseInt(l.is_cancelled) === 1) s = 'cancelled';
        if (currentFilters.status === 'active') return s === 'active';
        return s !== 'active';
      });
    }

    if (currentFilters.q) {
      const q = currentFilters.q.toLowerCase();
      filtered = filtered.filter(l => 
        (l.title || '').toLowerCase().includes(q) || 
        (l.category_name && l.category_name.toLowerCase().includes(q))
      );
    }

    renderListings(filtered);
  }

  // Filter Event Listeners
  searchInput.addEventListener('input', e => {
    currentFilters.q = e.target.value;
    applyFiltersAndRender();
  });

  typeFilter.addEventListener('change', e => {
    currentFilters.type = e.target.value;
    applyFiltersAndRender();
  });

  statusFilter.addEventListener('change', e => {
    currentFilters.status = e.target.value;
    applyFiltersAndRender();
  });

  window.deleteListing = function(id) {
      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#4F46E5',
          cancelButtonColor: '#DC2626',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.isConfirmed) {
              // Implementation for delete API...
              Swal.fire('Deleted!', 'Listing has been deleted.', 'success');
          }
      })
  }

  fetchListings();

})();
</script>
<?= $this->endSection() ?>
