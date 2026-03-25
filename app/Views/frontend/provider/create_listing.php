<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<!-- ══ CREATE LISTING HEADER ════════════════════════════════════ -->
<section class="cnd-provider-hero py-5" style="background: linear-gradient(135deg, #3F3590 0%, #FF68B4 100%);">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-8 text-white">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb cnd-breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('provider/listings') ?>" class="text-white opacity-75">My Listings</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page">New Class</li>
          </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-2">List a New Class</h1>
        <p class="lead opacity-90 mb-0">Fill in the details below to reach thousands of parents.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══ MULTI-STEP FORM ══════════════════════════════════════════ -->
<section class="py-5 bg-light min-vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-8">
        
        <!-- Step Progress Indicator -->
        <div class="cnd-step-indicator mb-5 d-flex justify-content-between">
           <div class="cnd-step-item active" data-step="1">
              <div class="cnd-step-dot">1</div>
              <div class="cnd-step-label">Type & Category</div>
           </div>
           <div class="cnd-step-item" data-step="2">
              <div class="cnd-step-dot">2</div>
              <div class="cnd-step-label">Institute Details</div>
           </div>
           <div class="cnd-step-item" data-step="3">
              <div class="cnd-step-dot">3</div>
              <div class="cnd-step-label">Class Details</div>
           </div>
           <div class="cnd-step-item" data-step="4">
              <div class="cnd-step-dot">4</div>
              <div class="cnd-step-label">Submit</div>
           </div>
        </div>

        <!-- Form Container -->
        <form id="createListingForm" class="bg-white rounded-4 shadow-sm p-4 p-md-5 overflow-hidden" enctype="multipart/form-data" novalidate>
           
           <!-- STEP 1: Class Type Selection -->
           <div class="cnd-form-step active" id="step1">
              <h4 class="fw-bold mb-4">Class Type & Category</h4>
              
              <div class="row g-4 mb-4">
                 <div class="col-md-12">
                    <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Class Type <span class="text-danger">*</span></label>
                    <select name="type" id="classType" class="form-select form-select-lg rounded-3 border-2" required>
                       <option value="regular">Regular Class (Recurring batches)</option>
                       <option value="workshop">Workshop (One-time event)</option>
                       <option value="course">Course (Fixed duration program)</option>
                    </select>
                 </div>
                 <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="categorySelect" class="form-select form-select-lg rounded-3 border-2" required>
                       <option value="">Choose a category...</option>
                       <?php foreach($categories as $id => $name): ?>
                          <option value="<?= $id ?>"><?= esc($name) ?></option>
                       <?php endforeach; ?>
                    </select>
                 </div>
                 <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Subcategories <span class="text-danger">*</span></label>
                    <select name="subcategory_ids[]" id="subcategorySelect" class="form-select form-select-lg rounded-3 border-2 select2-multi" multiple required disabled data-placeholder="Choose subcategories...">
                       <option value="">Select Category...</option>
                    </select>
                    <div class="form-text small">Select at least one relevant subcategory.</div>
                 </div>
              </div>

              <div class="d-flex justify-content-end mt-5">
                 <button type="button" class="btn btn-pink py-3 px-5 rounded-pill fw-bold next-step">Next Step <i class="bi bi-arrow-right ms-2"></i></button>
              </div>
           </div>

           <!-- STEP 2: Institute Details -->
           <div class="cnd-form-step" id="step2">
              <h4 class="fw-bold mb-4" id="step2Header">Institute Details</h4>
              <div class="mb-4">
                 <label class="form-label fw-bold small text-uppercase letter-spacing-sm" id="labelInstituteName">Institute Name <span class="text-danger">*</span></label>
                 <input type="text" name="institute_name" id="instituteName" class="form-control form-control-lg rounded-3 border-2" placeholder="e.g. Art & Soul Academy" required>
                 <!-- Title is synced with institute name for search -->
                 <input type="hidden" name="title" id="classTitle">
              </div>

              <div class="mb-4">
                 <label class="form-label fw-bold small text-uppercase letter-spacing-sm" id="labelDescription">Description</label>
                 <textarea name="description" id="description" class="form-control rounded-3 border-2" rows="5" placeholder="Tell us about your institute and the classes you offer."></textarea>
              </div>

              <div class="mb-4">
                 <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Address (Manual Entry) <span class="text-danger">*</span></label>
                 <input type="text" name="manual_address" class="form-control form-control-lg rounded-3 border-2" placeholder="Enter Full Address Manually" required>
              </div>

              <div class="mb-4">
                 <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Map Location (Search) <span class="text-danger">*</span></label>
                 <input type="text" id="locationInput" name="formatted_address" class="form-control form-control-lg rounded-3 border-2" placeholder="Search for your location to pin on map..." required autocomplete="off">
                 <div class="form-text small">Select your location from the suggestions to store coordinates.</div>
                 
                 <!-- Hidden Location Data -->
                 <input type="hidden" name="latitude" id="lat">
                 <input type="hidden" name="longitude" id="lng">
                 <input type="hidden" name="city" id="city">
                 <input type="hidden" name="locality" id="locality">
                 <input type="hidden" name="pincode" id="pincode">
                 <input type="hidden" name="address" id="full_address">
              </div>

              <div class="d-flex justify-content-between mt-5">
                 <button type="button" class="btn btn-outline-secondary py-3 px-4 rounded-pill fw-bold prev-step"><i class="bi bi-arrow-left me-2"></i> Back</button>
                  <button type="button" class="btn btn-pink py-3 px-5 rounded-pill fw-bold next-step">Next Step <i class="bi bi-arrow-right ms-2"></i></button>
               </div>
            </div>

            <!-- STEP 3: Class Details (Dynamic) — Subtask 3.1 -->
            <div class="cnd-form-step" id="step3">
               <h4 class="fw-bold mb-4">Class Specifics</h4>
               
               <!-- ── REGULAR CLASS SECTION ── -->
               <div id="sectionRegular">
                  <div class="mb-4">
                     <h6 class="fw-bold text-uppercase letter-spacing-sm text-primary mb-0">
                        <i class="bi bi-layers-fill me-2"></i> Class Batches
                     </h6>
                  </div>

                  <div id="batchesContainer" class="d-grid gap-4">
                     <!-- Default First Batch -->
                     <div class="card border-0 shadow-sm rounded-4 batch-item border-start border-4 border-primary">
                        <div class="card-body p-4">
                           <div class="row g-3">
                              <div class="col-md-6">
                                 <label class="form-label small fw-bold">Batch Name <span class="text-danger">*</span></label>
                                 <input type="text" name="batches[0][name]" class="form-control rounded-3" placeholder="e.g. Weekend Beginners" required>
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label small fw-bold">Days of Week <span class="text-danger">*</span></label>
                                 <div class="d-flex flex-wrap gap-2 mt-1">
                                    <?php $days = ['S','M','T','W','Th','F','Sa']; foreach($days as $d): ?>
                                       <div class="day-check">
                                          <input type="checkbox" name="batches[0][days][]" value="<?= $d ?>" id="day_0_<?= $d ?>" class="d-none">
                                          <label for="day_0_<?= $d ?>" class="day-label"><?= $d ?></label>
                                       </div>
                                    <?php endforeach; ?>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <label class="form-label small fw-bold">Start Date <span class="text-danger">*</span></label>
                                 <input type="date" name="batches[0][batch_start_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                              </div>
                              <div class="col-md-4">
                                 <label class="form-label small fw-bold">From Time <span class="text-danger">*</span></label>
                                 <input type="time" name="batches[0][from_time]" class="form-control rounded-3" required>
                              </div>
                              <div class="col-md-4">
                                 <label class="form-label small fw-bold">To Time <span class="text-danger">*</span></label>
                                 <input type="time" name="batches[0][to_time]" class="form-control rounded-3" required>
                              </div>
                              <div class="col-md-4">
                                 <label class="form-label small fw-bold">Price (₹) <span class="text-danger">*</span></label>
                                 <div class="input-group">
                                    <input type="number" name="batches[0][price]" class="form-control rounded-start-3" placeholder="₹" required min="0">
                                    <select name="batches[0][price_type]" class="form-select rounded-end-3" style="max-width: 130px;">
                                       <option value="monthly">Monthly</option>
                                       <option value="quarterly">Quarterly</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <label class="form-label small fw-bold">Batch Size <span class="text-danger">*</span></label>
                                 <input type="number" name="batches[0][batch_size]" class="form-control rounded-3" placeholder="Number of Students" required min="1">
                              </div>
                              <div class="col-md-4">
                                 <label class="form-label small fw-bold">Batch Image Upload</label>
                                 <input type="file" name="batch_images[0]" class="form-control rounded-3" accept="image/*">
                              </div>
                              <div class="col-12 mt-2">
                                 <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="batches[0][free_trial]" value="1" id="freeTrial_0">
                                    <label class="form-check-label small fw-bold" for="freeTrial_0">Free Trial Toggle</label>
                                 </div>
                              </div>

                              <!-- Instructor & KYC Section -->
                              <div class="col-12 mt-3 pt-3 border-top">
                                 <h6 class="fw-bold text-pink small text-uppercase mb-3">Instructor & KYC</h6>
                                 <div class="row g-3">
                                    <div class="col-md-12">
                                       <label class="form-label small fw-bold">Select Instructor</label>
                                       <select name="batches[0][instructor_option]" class="form-select rounded-3 instructor-select" onchange="handleInstructorSelect(this, 0)">
                                          <option value="new">Add New Instructor</option>
                                          <?php if(!empty($instructors)): ?>
                                             <?php foreach($instructors as $vi): ?>
                                                 <option value="<?= $vi->id ?>" 
                                                         data-name="<?= esc($vi->name) ?>"
                                                         data-exp="<?= esc($vi->experience) ?>" 
                                                         data-social="<?= esc($vi->social_links) ?>"
                                                         data-status="<?= $vi->kyc_status ?>">
                                                    <?= esc($vi->name) ?> (<?= $vi->kyc_status === 'verified' ? '✅ Verified' : '⏳ Pending' ?>)
                                                 </option>
                                              <?php endforeach; ?>
                                          <?php endif; ?>
                                       </select>
                                    </div>
                                    <div class="col-md-6 instructor-name-box">
                                       <label class="form-label small fw-bold">Instructor Name <span class="text-danger">*</span></label>
                                       <input type="text" name="batches[0][instructor_name]" class="form-control rounded-3" required>
                                    </div>
                                    <div class="col-md-6 instructor-social-box">
                                       <label class="form-label small fw-bold">Social Link</label>
                                       <input type="url" name="batches[0][social_links]" class="form-control rounded-3" placeholder="https://...">
                                    </div>
                                    <div class="col-md-12 instructor-exp-box">
                                       <label class="form-label small fw-bold">Instructor Experience <span class="text-danger">*</span></label>
                                       <textarea name="batches[0][experience]" class="form-control rounded-3" rows="2" required></textarea>
                                    </div>
                                    <div class="col-md-12 instructor-kyc-box">
                                       <div class="bg-light p-3 rounded-4 border border-dashed shadow-sm">
                                          <label class="form-label small fw-bold d-block mb-1">Instructor KYC Document (Optional)</label>
                                          <p class="small text-muted mb-2 fs-tiny">Optional proof of ID or certs. (Aadhaar/PAN/PortfolioPDF)</p>
                                          <input type="file" name="batch_instructor_kyc[0]" class="form-control rounded-pill px-3" accept="image/*,.pdf">
                                          <div class="mt-2 small italic text-muted">Status: <span class="badge bg-warning text-dark rounded-pill">Pending</span></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Add Batch Button (Now at bottom) -->
                  <div class="mt-4">
                     <button type="button" class="btn btn-outline-primary py-2 px-4 rounded-pill fw-bold" id="addBatchBtn">
                        <i class="bi bi-plus-circle me-1"></i> Add Another Batch
                     </button>
                  </div>
               </div>

               <!-- ── WORKSHOP SECTION ── -->
               <div id="sectionWorkshop" class="d-none">
                  <h6 class="fw-bold mb-3 text-uppercase letter-spacing-sm text-warning"><i class="bi bi-lightning-fill me-1"></i>Workshop Details</h6>
                  <div class="row g-3 mb-4">
                     <div class="col-md-4">
                        <label class="form-label small fw-bold">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="workshop[start_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold">From Time <span class="text-danger">*</span></label>
                        <input type="time" name="workshop[from_time]" class="form-control rounded-3" required>
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold">To Time <span class="text-danger">*</span></label>
                        <input type="time" name="workshop[to_time]" class="form-control rounded-3" required>
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold">Price <span class="text-danger">*</span></label>
                        <input type="number" name="workshop[price]" class="form-control rounded-3" required min="0">
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold">Max Allowed Students <span class="text-danger">*</span></label>
                        <input type="number" name="workshop[batch_size]" class="form-control rounded-3" required min="1">
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold">Registration End Date <span class="text-danger">*</span></label>
                        <input type="date" name="workshop[registration_end_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                     </div>
                  </div>

                  <div class="bg-light p-4 rounded-4 mb-4 border-start border-4 border-warning shadow-sm">
                     <h6 class="fw-bold mb-3 text-uppercase letter-spacing-sm">Early Bird Offer <small class="text-muted fw-normal">(Optional)</small></h6>
                     <div class="row g-3">
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird End Date</label>
                           <input type="date" name="workshop[early_bird_end_date]" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird Count</label>
                           <input type="number" name="workshop[early_bird_count]" class="form-control rounded-3" placeholder="Slots count">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird Price (₹)</label>
                           <input type="number" name="workshop[early_bird_price]" class="form-control rounded-3" placeholder="₹">
                        </div>
                     </div>
                  </div>
               </div>

               <!-- ── COURSE SECTION ── -->
               <div id="sectionCourse" class="d-none">
                  <h6 class="fw-bold mb-4 text-uppercase letter-spacing-sm text-info"><i class="bi bi-journal-bookmark-fill me-2"></i>Course Information</h6>
                  
                  <div class="row g-4 mb-4">
                     <div class="col-md-6 border-bottom pb-4 mb-2">
                        <label class="form-label fw-bold small text-uppercase">Course Duration <span class="text-danger">*</span></label>
                        <div class="input-group">
                           <input type="number" name="course[duration_number]" class="form-control rounded-start-3" placeholder="e.g. 3" required min="1">
                           <select name="course[duration_type]" class="form-select rounded-end-3" style="max-width: 140px;" required>
                              <option value="weeks">Weeks</option>
                              <option value="months">Months</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="row g-4 mb-4">
                     <div class="col-md-6">
                        <label class="form-label small fw-bold">From Date <span class="text-danger">*</span></label>
                        <input type="date" name="course[start_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                     </div>
                     <div class="col-md-6">
                        <label class="form-label small fw-bold">To Date <span class="text-danger">*</span></label>
                        <input type="date" name="course[end_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                     </div>
                     <div class="col-md-6">
                        <label class="form-label small fw-bold">From Time <span class="text-danger">*</span></label>
                        <input type="time" name="course[from_time]" class="form-control rounded-3" required>
                     </div>
                     <div class="col-md-6">
                        <label class="form-label small fw-bold">To Time <span class="text-danger">*</span></label>
                        <input type="time" name="course[to_time]" class="form-control rounded-3" required>
                     </div>
                     <div class="col-md-12">
                        <label class="form-label small fw-bold d-block mb-2">Days of Week <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                           <?php foreach($days as $d): ?>
                              <div class="day-check">
                                 <input type="checkbox" name="course[days][]" value="<?= $d ?>" id="cday_<?= $d ?>" class="d-none">
                                 <label for="cday_<?= $d ?>" class="day-label"><?= $d ?></label>
                              </div>
                           <?php endforeach; ?>
                        </div>
                     </div>
                  </div>

                  <div class="row g-3 mb-4 bg-light p-4 rounded-4 mx-0 border">
                     <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="course[price]" class="form-control rounded-3" required min="0">
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase">Max Allowed Students <span class="text-danger">*</span></label>
                        <input type="number" name="course[batch_size]" class="form-control rounded-3" required min="1">
                     </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase">Registration End Date <span class="text-danger">*</span></label>
                        <input type="date" name="course[registration_end_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                     </div>
                  </div>

                  <div class="bg-white p-4 rounded-4 mb-4 border border-info shadow-sm">
                     <h6 class="fw-bold mb-3 text-uppercase letter-spacing-sm text-info">Early Bird Offer <small class="text-muted fw-normal">(Optional)</small></h6>
                     <div class="row g-3">
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird End Date</label>
                           <input type="date" name="course[early_bird_end_date]" class="form-control rounded-3">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird Count</label>
                           <input type="number" name="course[early_bird_count]" class="form-control rounded-3" placeholder="Remaining Slots">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird Price</label>
                           <input type="number" name="course[early_bird_price]" class="form-control rounded-3" placeholder="Discounted Price">
                        </div>
                     </div>
                  </div>
               </div>

               <!-- ── SHARED INSTRUCTOR SECTION (For Workshop/Course) ── -->
               <div id="sharedInstructorSection" class="instructor-section mt-5 border-top pt-5 d-none">
                  <div class="d-flex align-items-center mb-4">
                     <div class="bg-soft-pink text-pink rounded-circle p-3 me-3">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                     </div>
                     <div>
                        <h5 class="fw-bold mb-0">Instructor Details</h5>
                        <p class="small text-muted mb-0">Provide info about the expert leading the class.</p>
                     </div>
                  </div>
                  
                  <div class="row g-4 bg-light p-4 rounded-4 border mx-0 shadow-sm">
                     <div class="col-md-12">
                         <label class="form-label small fw-bold">Select Verified Instructor or Add New</label>
                         <select name="instructor_option" class="form-select rounded-3 instructor-select-shared" onchange="handleSharedInstructorSelect(this)">
                            <option value="new">Add New Instructor</option>
                             <?php if(!empty($instructors)): ?>
                                <?php foreach($instructors as $vi): ?>
                                   <option value="<?= $vi->id ?>" 
                                           data-name="<?= esc($vi->name) ?>"
                                           data-exp="<?= esc($vi->experience) ?>" 
                                           data-social="<?= esc($vi->social_links) ?>"
                                           data-status="<?= $vi->kyc_status ?>">
                                      <?= esc($vi->name) ?> (<?= ($vi->kyc_status ?? '') === 'verified' ? '✅ Verified' : '⏳ Pending' ?>)
                                   </option>
                                <?php endforeach; ?>
                             <?php endif; ?>
                         </select>
                     </div>
                     <div class="col-md-6 instructor-name-box">
                        <label class="form-label small fw-bold text-uppercase letter-spacing-sm">Instructor Name <span class="text-danger">*</span></label>
                        <input type="text" name="instructor_name" class="form-control form-control-lg rounded-3 border-2" placeholder="e.g. Ms. Jane Doe" required>
                     </div>
                     <div class="col-md-6 instructor-social-box">
                        <label class="form-label small fw-bold text-uppercase letter-spacing-sm">Social Link (LinkedIn/Portfolio)</label>
                        <input type="url" name="social_links" class="form-control form-control-lg rounded-3 border-2" placeholder="https://linkedin.com/in/...">
                     </div>
                     <div class="col-md-12 instructor-exp-box">
                        <label class="form-label small fw-bold text-uppercase letter-spacing-sm">Instructor Experience <span class="text-danger">*</span></label>
                        <textarea name="experience" class="form-control rounded-3 border-2" rows="3" placeholder="Brief about instructor's background and achievements." required></textarea>
                     </div>
                     <div class="col-md-12 mt-4 instructor-kyc-box">
                        <div class="bg-white p-4 rounded-4 border border-dashed text-center">
                           <label class="form-label small fw-bold d-block mb-3 text-uppercase letter-spacing-sm text-pink">
                              <i class="bi bi-file-earmark-lock-fill me-1"></i> Instructor KYC Document (Optional)
                           </label>
                           <p class="small text-muted mb-4 fs-tiny">Upload any ID proof or certifications (Aadhaar/PAN, Max 2MB).</p>
                           <div class="d-flex justify-content-center">
                              <input type="file" name="instructor_kyc_doc" class="form-control w-auto rounded-pill px-4" accept="image/*,.pdf">
                           </div>
                           <div class="mt-3 small text-muted italic">Status: <span class="badge bg-warning text-dark rounded-pill">Pending</span></div>
                        </div>
                     </div>
                  </div>
               </div>

                <div class="d-flex justify-content-between mt-5">
                   <button type="button" class="btn btn-outline-secondary py-3 px-4 rounded-pill fw-bold prev-step"><i class="bi bi-arrow-left me-2"></i> Back</button>
                   <button type="button" class="btn btn-pink py-3 px-5 rounded-pill fw-bold next-step">Next Step <i class="bi bi-arrow-right ms-2"></i></button>
                </div>
             </div>

            <!-- STEP 4: Media & Submission -->
            <div class="cnd-form-step" id="step4">
               <h4 class="fw-bold mb-4">Final Submission</h4>
               
               <!-- Image Upload -->
               <div class="mb-4">
                  <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Class Photos (3-5 Photos) <span class="text-danger">*</span></label>
                  <div class="cnd-upload-zone border-2 border-dashed rounded-4 p-5 text-center cursor-pointer" onclick="document.getElementById('imageInput').click()">
                     <i class="bi bi-images display-4 text-muted opacity-50 mb-3 block"></i>
                     <p class="mb-0 text-muted">Upload between 3 to 5 images (Max 2MB each)</p>
                     <p class="small text-pink fw-600">The first image will be the primary cover.</p>
                     <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">
                  </div>
                  <div id="imagePreview" class="d-flex flex-wrap gap-3 mt-3"></div>
               </div>

               <!-- Batch Images Sync (Requested) -->
               <div id="batchImagesSection" class="mb-4 d-none">
                  <label class="form-label fw-bold small text-uppercase letter-spacing-sm">Batch Photos (From Step 3)</label>
                  <div id="batchImagesPreview" class="d-flex flex-wrap gap-3"></div>
                  <div class="form-text small italic">These photos are linked to your specific batches.</div>
               </div>

               <!-- T&C Agreement -->
               <div class="bg-light p-4 rounded-4 mb-4 border-start border-4 border-pink">
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" id="termsCheck" required>
                     <label class="form-check-label small fw-bold" for="termsCheck">
                        I agree to the <a href="<?= base_url('terms') ?>" target="_blank" class="text-pink">Terms and Conditions</a> and represent that all information provided is accurate.
                     </label>
                  </div>
               </div>

               <div class="d-flex justify-content-between mt-5">
                  <button type="button" class="btn btn-outline-secondary py-3 px-4 rounded-pill fw-bold prev-step"><i class="bi bi-arrow-left me-2"></i> Back</button>
                  <button type="submit" class="btn btn-pink py-3 px-5 rounded-pill fw-bold shadow-sm" id="submitBtn">
                     <span id="submitSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                     Submit for Approval
                  </button>
               </div>
            </div>

        </form>

      </div>
    </div>
  </div>
</section>

<!-- ══ RULES MODAL ════════════════════════════════════════════ -->
<div class="modal fade" id="rulesModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
         <div class="modal-header bg-light border-0 py-3">
            <h5 class="modal-title fw-bold">Listing Guidelines</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body p-4 p-md-5">
            <div class="row g-4">
               <div class="col-md-6">
                  <div class="d-flex gap-3 mb-4">
                     <div class="bg-soft-pink text-pink rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-check2"></i></div>
                     <div>
                        <h6 class="fw-bold mb-1">Accurate Information</h6>
                        <p class="small text-muted mb-0">Ensure all class details, pricing, and dates are 100% accurate at the time of listing.</p>
                     </div>
                  </div>
                  <div class="d-flex gap-3 mb-4">
                     <div class="bg-soft-pink text-pink rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-image"></i></div>
                     <div>
                        <h6 class="fw-bold mb-1">Quality Photos</h6>
                        <p class="small text-muted mb-0">Use clear, bright photos of the actual class environment. No stock photos or watermarks.</p>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="d-flex gap-3 mb-4">
                     <div class="bg-soft-pink text-pink rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-shield-check"></i></div>
                     <div>
                        <h6 class="fw-bold mb-1">Safety First</h6>
                        <p class="small text-muted mb-0">Providers are responsible for the safety of children during the class. Mention safety measures.</p>
                     </div>
                  </div>
                  <div class="d-flex gap-3 mb-0">
                     <div class="bg-soft-pink text-pink rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-chat-heart"></i></div>
                     <div>
                        <h6 class="fw-bold mb-1">Responsive Communication</h6>
                        <p class="small text-muted mb-0">Respond to parent queries within 24 hours to maintain a high provider rating.</p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="text-center mt-4 pt-4 border-top">
               <button type="button" class="btn btn-pink rounded-pill px-5" data-bs-dismiss="modal">I Understand</button>
            </div>
         </div>
      </div>
   </div>
</div>

<style>
.cnd-step-indicator { position: relative; }
.cnd-step-indicator::before { content: ""; position: absolute; top: 18px; left: 10%; right: 10%; height: 2px; background: #e0d4f7; z-index: 0; }
.cnd-step-item { text-align: center; position: relative; z-index: 1; flex: 1; }
.cnd-step-dot { width: 36px; height: 36px; border-radius: 50%; background: #e0d4f7; color: #7C4DFF; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: 800; transition: all 0.3s; border: 4px solid #fff; }
.cnd-step-label { font-size: 0.72rem; font-weight: 700; color: #a0a0a0; text-transform: uppercase; letter-spacing: 0.05rem; }
.cnd-step-item.active .cnd-step-dot { background: var(--cnd-pink); color: #fff; transform: scale(1.1); }
.cnd-step-item.active .cnd-step-label { color: var(--cnd-pink); }
.cnd-step-item.done .cnd-step-dot { background: #2ECC71; color: #fff; }

.cnd-form-step { display: none; }
.cnd-form-step.active { display: block; animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }

.cnd-upload-zone { background: #fafafa; transition: all 0.3s; border-style: dashed; }
.cnd-upload-zone:hover { border-color: var(--cnd-pink); background: rgba(255, 104, 180,0.02); }
.preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.btn-outline-pink { border-color: var(--cnd-pink); color: var(--cnd-pink); }
.btn-outline-pink:hover { background: var(--cnd-pink); color: #fff; }

#createListingForm input:focus, #createListingForm textarea:focus, #createListingForm select:focus {
   border-color: var(--cnd-pink); box-shadow: 0 0 0 0.25rem rgba(255, 104, 180,0.1);
}

.day-check { margin-bottom: 5px; }
.day-label { 
    width: 34px; height: 34px; border-radius: 50%; border: 2px solid #ddd; 
    display: flex; align-items: center; justify-content: center; 
    font-size: 0.75rem; font-weight: bold; cursor: pointer; transition: 0.2s;
}
.day-check input:checked + .day-label { 
    background: var(--cnd-pink); border-color: var(--cnd-pink); color: #fff; 
}
</style>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.select2-container--default .select2-selection--multiple {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    min-height: 48px;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--cnd-pink);
    box-shadow: 0 0 0 0.25rem rgba(255, 104, 180,0.1);
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: var(--cnd-pink);
    border: none;
    color: #fff;
    border-radius: 4px;
    padding: 2px 8px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    margin-right: 5px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background: rgba(0,0,0,0.1);
    color: #fff;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= env('GOOGLE_MAP_API_KEY') ?>&libraries=places"></script>
<script>
(function(){
  'use strict';

  // Initialize Flatpickr for existing date inputs
  function initFlatpickr(container = document) {
    container.querySelectorAll('input[type="date"]').forEach(el => {
      // We convert native date inputs to text so flatpickr can take over with custom format
      // Or we can just let flatpickr handle it. Flatpickr works better on text inputs.
      if (el.type === 'date') {
          el.type = 'text';
      }
      flatpickr(el, {
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "Y-m-d", // This keeps the actual value as Y-m-d for the server
        minDate: el.min || "today",
        allowInput: true
      });
    });
  }
  
  initFlatpickr();
  
  // ── Google Places Autocomplete ────────────────────────────────
  let autocomplete;
  function initAutocomplete() {
    const input = document.getElementById('locationInput');
    autocomplete = new google.maps.places.Autocomplete(input, {
      componentRestrictions: { country: "in" },
      fields: ["address_components", "geometry", "formatted_address"],
    });

    autocomplete.addListener("place_changed", onPlaceSelected);
    
    // Prevent form submission on Enter in the location field
    input.addEventListener('keydown', (e) => {
       if (e.key === 'Enter') e.preventDefault();
    });
  }

  function onPlaceSelected() {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
      alert("Please select a location from the dropdown suggestions.");
      document.getElementById('locationInput').value = "";
      return;
    }

    // Set Lat/Lng
    document.getElementById('lat').value = place.geometry.location.lat();
    document.getElementById('lng').value = place.geometry.location.lng();
    document.getElementById('full_address').value = place.formatted_address;

    // Parse Address Components
    let city = '', locality = '', pincode = '';
    place.address_components.forEach(component => {
      const types = component.types;
      if (types.includes('locality') || types.includes('administrative_area_level_3')) {
        city = component.long_name;
      }
      if (types.includes('sublocality_level_1') || types.includes('neighborhood')) {
        locality = component.long_name;
      }
      if (types.includes('postal_code')) {
        pincode = component.long_name;
      }
    });

    document.getElementById('city').value = city;
    document.getElementById('locality').value = locality;
    document.getElementById('pincode').value = pincode;
  }

  initAutocomplete();

  const form = document.getElementById('createListingForm');
  const steps = document.querySelectorAll('.cnd-form-step');
  const stepIndicators = document.querySelectorAll('.cnd-step-item');
  
  const classTypeSelect = document.getElementById('classType');
  const sectionRegular = document.getElementById('sectionRegular');
  const sectionWorkshop = document.getElementById('sectionWorkshop');
  const sectionCourse = document.getElementById('sectionCourse');
  
  const addBatchBtn = document.getElementById('addBatchBtn');
  const batchesContainer = document.getElementById('batchesContainer');
  
  const imageInput = document.getElementById('imageInput');
  const imagePreview = document.getElementById('imagePreview');
  const submitBtn = document.getElementById('submitBtn');
  const spinner = document.getElementById('submitSpinner');
  
  const instituteNameInput = document.getElementById('instituteName');
  const classTitleHidden = document.getElementById('classTitle');

  // Sync title with Institute Name
  if(instituteNameInput && classTitleHidden) {
      instituteNameInput.addEventListener('input', () => {
          classTitleHidden.value = instituteNameInput.value;
      });
  }

  // Subcategory Loader
  const categorySelect = document.getElementById('categorySelect');
  const subcategorySelect = document.getElementById('subcategorySelect');

  if(categorySelect) {
     $(document).ready(function() {
         $('#subcategorySelect').select2({
            theme: 'default',
            placeholder: 'Choose subcategories...',
            width: '100%',
            allowClear: true
         });
     });

     categorySelect.addEventListener('change', async function() {
        const catId = this.value;
        const $sc = $('#subcategorySelect');
        $sc.html('<option value="">Loading...</option>').trigger('change');
        $sc.prop('disabled', true);

        if(!catId) {
           $sc.empty().append('<option value="">Select Category...</option>').prop('disabled', true).trigger('change');
           return;
        }

        try {
           const res = await fetch(`<?= base_url('provider/api/subcategories') ?>?category_id=${catId}`);
           const data = await res.json();
           
           if(data && data.length > 0) {
              const select2Data = data.map(sub => ({ id: sub.id, text: sub.name }));
              $sc.empty().select2({
                 data: select2Data,
                 theme: 'default',
                 placeholder: 'Choose subcategories...',
                 width: '100%',
                 allowClear: true
              });
              $sc.prop('disabled', false).trigger('change');
           } else {
              $sc.empty().append('<option value="">No subcategories found</option>').trigger('change');
           }
        } catch(e) {
           console.error("Error fetching subcategories:", e);
        }
     });
  }

  let currentStep = 1;

  // ── Step Navigation ───────────────────────────────────────────
  function goToStep(n) {
    if(n < 1 || n > 4) return;
    
    if(n > currentStep) {
      const inputs = steps[currentStep-1].querySelectorAll('[required]');
      let valid = true;
      inputs.forEach(i => {
         let isVisible = true;
         let curr = i;
         while(curr && curr !== form) {
            if(curr.classList.contains('d-none') || curr.style.display === 'none') {
               isVisible = false; break;
            }
            curr = curr.parentElement;
         }

         if(isVisible || i.classList.contains('flatpickr-input')) {
            if(!i.value || (i.type === 'checkbox' && !i.checked)) {
               // For checkboxes in day-check, we handle them differently
               if(!i.closest('.day-check')) {
                  // For flatpickr, we might want to highlight the alt input
                  if(i._flatpickr && i._flatpickr.altInput) {
                      i._flatpickr.altInput.classList.add('is-invalid');
                  } else {
                      i.classList.add('is-invalid');
                  }
                  valid = false;
               }
            } else {
               i.classList.remove('is-invalid');
               if(i._flatpickr && i._flatpickr.altInput) {
                   i._flatpickr.altInput.classList.remove('is-invalid');
               }
            }
         }
      });

      // Special check for Multi-select Category (Step 1)
      if(currentStep === 1) {
          const scValue = $('#subcategorySelect').val();
          if(!scValue || scValue.length === 0) {
              alert('Please select at least one subcategory.');
              valid = false;
          }
      }

      // Special check for Step 2: Map Location
      if(currentStep === 2) {
          const lat = document.getElementById('lat').value;
          const lng = document.getElementById('lng').value;
          if(!lat || !lng) {
              alert('Please select a location from the dropdown suggestions to pin it on the map.');
              valid = false;
          }
      }

      // Special check for Step 3: Days selection for Regular/Course
      if(currentStep === 3) {
          if(classTypeSelect.value === 'regular') {
              const batchItems = document.querySelectorAll('.batch-item');
              for(let bi of batchItems) {
                  const checked = bi.querySelectorAll('input[name*="[days]"]:checked');
                  if(checked.length === 0) {
                      alert('Please select at least one day for each batch.');
                      valid = false;
                      break;
                  }
                  
                  // Batch timing check
                  const fromTime = bi.querySelector('input[name*="[from_time]"]').value;
                  const toTime = bi.querySelector('input[name*="[to_time]"]').value;
                  if(fromTime && toTime && fromTime >= toTime) {
                      alert('Start time must be before end time for batch details.');
                      valid = false;
                      break;
                  }
              }
          } else if(classTypeSelect.value === 'course') {
              const checked = sectionCourse.querySelectorAll('input[name*="[days]"]:checked');
              if(checked.length === 0) {
                  alert('Please select at least one day for the course.');
                  valid = false;
              }

              const startDate = sectionCourse.querySelector('input[name="course[start_date]"]').value;
              const endDate = sectionCourse.querySelector('input[name="course[end_date]"]').value;
              const regEndDate = sectionCourse.querySelector('input[name="course[registration_end_date]"]').value;
              const price = parseFloat(sectionCourse.querySelector('input[name="course[price]"]').value) || 0;
              const ebPrice = parseFloat(sectionCourse.querySelector('input[name="course[early_bird_price]"]').value) || 0;
              const ebEndDate = sectionCourse.querySelector('input[name="course[early_bird_end_date]"]').value;

              if(startDate && endDate && startDate >= endDate) {
                  alert('Course start date must be before the end date.');
                  valid = false;
              }
              if(regEndDate && startDate && regEndDate > startDate) {
                  alert('Registration must end on or before the course start date.');
                  valid = false;
              }
              if(ebPrice > 0 && ebPrice >= price) {
                  alert('Early bird price must be less than the regular course price.');
                  valid = false;
              }
              if(ebEndDate && regEndDate && ebEndDate > regEndDate) {
                  alert('Early bird offer must end on or before the registration deadline.');
                  valid = false;
              }

          } else if(classTypeSelect.value === 'workshop') {
              const startDate = sectionWorkshop.querySelector('input[name="workshop[start_date]"]').value;
              const regEndDate = sectionWorkshop.querySelector('input[name="workshop[registration_end_date]"]').value;
              const price = parseFloat(sectionWorkshop.querySelector('input[name="workshop[price]"]').value) || 0;
              const ebPrice = parseFloat(sectionWorkshop.querySelector('input[name="workshop[early_bird_price]"]').value) || 0;
              const ebEndDate = sectionWorkshop.querySelector('input[name="workshop[early_bird_end_date]"]').value;

              if(regEndDate && startDate && regEndDate > startDate) {
                  alert('Registration must end on or before the workshop start date.');
                  valid = false;
              }
              if(ebPrice > 0 && ebPrice >= price) {
                  alert('Early bird price must be less than the standard workshop price.');
                  valid = false;
              }
              if(ebEndDate && regEndDate && ebEndDate > regEndDate) {
                  alert('Early bird offer must end on or before the registration deadline.');
                  valid = false;
              }
          }
      }

      if(!valid) {
          const firstInvalid = steps[currentStep-1].querySelector('.is-invalid');
          if(firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
      }
    }

    steps.forEach((s, idx) => s.classList.toggle('active', idx === n-1));
    stepIndicators.forEach((ind, idx) => {
      ind.classList.toggle('active', idx === n-1);
      ind.classList.toggle('done', idx < n-1);
    });
    currentStep = n;
    window.scrollTo(0, 300);
  }

  document.querySelectorAll('.next-step').forEach(btn => btn.addEventListener('click', () => goToStep(currentStep + 1)));
  document.querySelectorAll('.prev-step').forEach(btn => btn.addEventListener('click', () => goToStep(currentStep - 1)));

  // ── Class Type Switcher ────────────────────
  function applyTypeUI(type) {
    const s2Header = document.getElementById('step2Header');
    const labelName = document.getElementById('labelInstituteName');
    const inputName = document.getElementById('instituteName');
    const labelDesc = document.getElementById('labelDescription');
    const inputDesc = document.getElementById('description');

    if (type === 'workshop') {
       if(s2Header) s2Header.innerText = 'Workshop Details';
       if(labelName) labelName.innerHTML = 'Workshop Name <span class="text-danger">*</span>';
       if(inputName) inputName.placeholder = 'e.g. Pottery Workshop for Kids';
       if(labelDesc) labelDesc.innerText = 'Workshop Description';
       if(inputDesc) inputDesc.placeholder = 'Tell us about your workshop.';
    } else if (type === 'course') {
       if(s2Header) s2Header.innerText = 'Course Details';
       if(labelName) labelName.innerHTML = 'Course Name <span class="text-danger">*</span>';
       if(inputName) inputName.placeholder = 'e.g. 3-Month Music Certificate Course';
       if(labelDesc) labelDesc.innerText = 'Course Description';
       if(inputDesc) inputDesc.placeholder = 'Tell us about your course.';
    } else {
       if(s2Header) s2Header.innerText = 'Institute Details';
       if(labelName) labelName.innerHTML = 'Institute Name <span class="text-danger">*</span>';
       if(inputName) inputName.placeholder = 'e.g. Art & Soul Academy';
       if(labelDesc) labelDesc.innerText = 'Description';
       if(inputDesc) inputDesc.placeholder = 'Tell us about your institute and the classes you offer.';
    }

    sectionRegular.classList.toggle('d-none', type !== 'regular');
    sectionWorkshop.classList.toggle('d-none', type !== 'workshop');
    sectionCourse.classList.toggle('d-none', type !== 'course');
    
    // Instructor section is shared for workshop/course, but per-batch for regular
    document.getElementById('sharedInstructorSection').classList.toggle('d-none', type === 'regular');

    // Toggle required attributes for sectional fields
    const sReg = sectionRegular.querySelectorAll('[required]');
    const sWork = sectionWorkshop.querySelectorAll('[required]');
    const sCour = sectionCourse.querySelectorAll('[required]');
    const sSharedInst = document.getElementById('sharedInstructorSection').querySelectorAll('[required]');
    
    sReg.forEach(i => i.required = (type === 'regular'));
    sWork.forEach(i => i.required = (type === 'workshop'));
    sCour.forEach(i => i.required = (type === 'course'));
    sSharedInst.forEach(i => i.required = (type !== 'regular'));
  }

  // Initial UI Setup
  if(classTypeSelect) {
      const initUI = () => {
          console.log("Initializing UI for type:", classTypeSelect.value);
          applyTypeUI(classTypeSelect.value);
          if (typeof syncBatchPreviews === 'function') syncBatchPreviews();
      };
      classTypeSelect.addEventListener('change', (e) => {
          applyTypeUI(e.target.value);
          if (typeof syncBatchPreviews === 'function') syncBatchPreviews();
      });
      if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initUI);
      } else {
          initUI();
      }
  }

  // Handle Instructor Selection within a Batch
  window.handleInstructorSelect = function(select, index) {
     const card = select.closest('.batch-item');
     const opt = select.options[select.selectedIndex];
     const nameBox = card.querySelector('.instructor-name-box');
     const socialBox = card.querySelector('.instructor-social-box');
     const expBox = card.querySelector('.instructor-exp-box');
     const kycBox = card.querySelector('.instructor-kyc-box');
     
     const nameField = card.querySelector(`input[name="batches[${index}][instructor_name]"]`);
     const socialField = card.querySelector(`input[name="batches[${index}][social_links]"]`);
     const expField = card.querySelector(`textarea[name="batches[${index}][experience]"]`);
     const kycInput = kycBox.querySelector('input[type="file"]');

     if(select.value === 'new') {
        nameBox.classList.remove('d-none');
        socialBox.classList.remove('d-none');
        expBox.classList.remove('d-none');
        kycBox.classList.remove('d-none');
        
        nameField.required = true;
        expField.required = true;
        kycInput.required = false; // Optional
     } else {
        const status = opt.getAttribute('data-status');
        nameBox.classList.add('d-none');
        socialBox.classList.add('d-none');
        expBox.classList.add('d-none');
        
        // Hide KYC box for existing instructors
        kycBox.classList.add('d-none');
        
        nameField.value = opt.getAttribute('data-name');
        socialField.value = opt.getAttribute('data-social') || '';
        expField.value = opt.getAttribute('data-exp') || '';
        
        nameField.required = false;
        expField.required = false;
        kycInput.required = false; 
        if(status === 'verified') kycInput.value = ''; 
     }
  }

  // Handle Shared Instructor Selection (Workshop/Course)
  window.handleSharedInstructorSelect = function(select) {
     const container = select.closest('.instructor-section');
     const opt = select.options[select.selectedIndex];
     
     const nameBox = container.querySelector('.instructor-name-box');
     const socialBox = container.querySelector('.instructor-social-box');
     const expBox = container.querySelector('.instructor-exp-box');
     const kycBox = container.querySelector('.instructor-kyc-box');
     
     const nameField = container.querySelector('input[name="instructor_name"]');
     const socialField = container.querySelector('input[name="social_links"]');
     const expField = container.querySelector('textarea[name="experience"]');
     const kycInput = kycBox.querySelector('input[type="file"]');

     if(select.value === 'new') {
        nameBox.classList.remove('d-none');
        socialBox.classList.remove('d-none');
        expBox.classList.remove('d-none');
        kycBox.classList.remove('d-none');
        
        nameField.required = true;
        expField.required = true;
        kycInput.required = false; // Optional
     } else {
        const status = opt.getAttribute('data-status');
        nameBox.classList.add('d-none');
        socialBox.classList.add('d-none');
        expBox.classList.add('d-none');
        
        // Hide KYC box for existing instructors
        kycBox.classList.add('d-none');
        
        nameField.value = opt.getAttribute('data-name');
        socialField.value = opt.getAttribute('data-social') || '';
        expField.value = opt.getAttribute('data-exp') || '';
        
        nameField.required = false;
        expField.required = false;
        kycInput.required = false;
        if(status === 'verified') kycInput.value = '';
     }
  }

  // ── Batch Management ──────────────────────────────────
  let batchCount = 1;
  if (addBatchBtn) {
    addBatchBtn.addEventListener('click', () => {
       const batchItemsCount = document.querySelectorAll('.batch-item').length;
       if (batchItemsCount >= 5) {
           alert('You can add a maximum of 5 batches.');
           return;
       }
       
       const prevBatch = document.querySelectorAll('.batch-item');
       const lastBatch = prevBatch[prevBatch.length - 1];
       
       // Pre-populate instructor details from last batch
       const lastInstOpt = lastBatch.querySelector('.instructor-select').value;
       const lastInstName = lastBatch.querySelector('input[name*="[instructor_name]"]').value;
       const lastInstSocial = lastBatch.querySelector('input[name*="[social_links]"]').value;
       const lastInstExp = lastBatch.querySelector('textarea[name*="[experience]"]').value;

       const batchRow = document.createElement('div');
       batchRow.className = 'card border-0 shadow-sm rounded-4 batch-item border-start border-4 border-primary position-relative mb-4';
       batchRow.innerHTML = `
          <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 m-3 remove-batch"><i class="bi bi-trash fs-5"></i></button>
          <div class="card-body p-4">
             <div class="row g-3">
                <div class="col-md-6">
                   <label class="form-label small fw-bold">Batch Name <span class="text-danger">*</span></label>
                   <input type="text" name="batches[${batchCount}][name]" class="form-control rounded-3" placeholder="e.g. Evening Batch" required>
                </div>
                <div class="col-md-6">
                   <label class="form-label small fw-bold">Days of Week <span class="text-danger">*</span></label>
                   <div class="d-flex flex-wrap gap-2 mt-1">
                      ${['S','M','T','W','Th','F','Sa'].map(d => `
                         <div class="day-check">
                            <input type="checkbox" name="batches[${batchCount}][days][]" value="${d}" id="day_${batchCount}_${d}" class="d-none">
                            <label for="day_${batchCount}_${d}" class="day-label">${d}</label>
                         </div>
                      `).join('')}
                   </div>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-bold">Start Date <span class="text-danger">*</span></label>
                   <input type="date" name="batches[${batchCount}][batch_start_date]" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-bold">From Time <span class="text-danger">*</span></label>
                   <input type="time" name="batches[${batchCount}][from_time]" class="form-control rounded-3" required>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-bold">To Time <span class="text-danger">*</span></label>
                   <input type="time" name="batches[${batchCount}][to_time]" class="form-control rounded-3" required>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-bold">Price (₹) <span class="text-danger">*</span></label>
                   <div class="input-group">
                      <input type="number" name="batches[${batchCount}][price]" class="form-control rounded-start-3" placeholder="₹" required min="0">
                      <select name="batches[${batchCount}][price_type]" class="form-select rounded-end-3" style="max-width: 130px;">
                         <option value="monthly">Monthly</option>
                         <option value="quarterly">Quarterly</option>
                      </select>
                   </div>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-bold">Batch Size <span class="text-danger">*</span></label>
                   <input type="number" name="batches[${batchCount}][batch_size]" class="form-control rounded-3" placeholder="e.g. 15" required min="1">
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-bold">Batch Image</label>
                   <input type="file" name="batch_images[${batchCount}]" class="form-control rounded-3" accept="image/*">
                </div>
                <div class="col-12 mt-2">
                   <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="batches[${batchCount}][free_trial]" value="1" id="freeTrial_${batchCount}">
                      <label class="form-check-label small fw-bold" for="freeTrial_${batchCount}">Offer Free Trial</label>
                   </div>
                </div>

                <!-- Instructor Section -->
                <div class="col-12 mt-3 pt-3 border-top">
                   <h6 class="fw-bold text-pink small text-uppercase mb-3"><i class="bi bi-person-badge me-2"></i>Instructor for this Batch</h6>
                   <div class="row g-3">
                      <div class="col-md-12">
                         <label class="form-label small fw-bold">Select Verified Instructor or Add New</label>
                         <select name="batches[${batchCount}][instructor_option]" class="form-select rounded-3 instructor-select" onchange="handleInstructorSelect(this, ${batchCount})">
                            <option value="new">Add New Instructor</option>
                             <?php if(!empty($instructors)): ?>
                                <?php foreach($instructors as $vi): ?>
                                   <option value="<?= $vi->id ?>" 
                                           data-name="<?= esc($vi->name) ?>"
                                           data-exp="<?= esc($vi->experience) ?>" 
                                           data-social="<?= esc($vi->social_links) ?>"
                                           data-status="<?= $vi->kyc_status ?>"
                                           ${lastInstOpt == '<?= $vi->id ?>' ? 'selected' : ''}>
                                      <?= esc($vi->name) ?> (<?= ($vi->kyc_status ?? '') === 'verified' ? '✅ Verified' : '⏳ Pending' ?>)
                                   </option>
                                <?php endforeach; ?>
                             <?php endif; ?>
                         </select>
                      </div>
                      <div class="col-md-12 instructor-kyc-box ${lastInstOpt !== 'new' ? 'd-none' : ''}">
                         <div class="bg-light p-3 rounded-4 border border-dashed">
                            <label class="form-label small fw-bold d-block mb-1">Instructor KYC Document (Optional)</label>
                            <p class="small text-muted mb-2 fs-tiny">Optional proof of ID or certs. (Aadhaar/PAN/PDF).</p>
                            <input type="file" name="batch_instructor_kyc[${batchCount}]" class="form-control rounded-pill px-3" accept="image/*,.pdf">
                         </div>
                      </div>
                      <div class="col-md-6 instructor-name-box ${lastInstOpt !== 'new' ? 'd-none' : ''}">
                         <label class="form-label small fw-bold">Instructor Name <span class="text-danger">*</span></label>
                         <input type="text" name="batches[${batchCount}][instructor_name]" class="form-control rounded-3" value="${lastInstName}" ${lastInstOpt === 'new' ? 'required' : ''}>
                      </div>
                      <div class="col-md-6 instructor-social-box ${lastInstOpt !== 'new' ? 'd-none' : ''}">
                         <label class="form-label small fw-bold">Social Link</label>
                         <input type="url" name="batches[${batchCount}][social_links]" class="form-control rounded-3" value="${lastInstSocial}">
                      </div>
                      <div class="col-md-12 instructor-exp-box ${lastInstOpt !== 'new' ? 'd-none' : ''}">
                         <label class="form-label small fw-bold">Experience / Bio <span class="text-danger">*</span></label>
                         <textarea name="batches[${batchCount}][experience]" class="form-control rounded-3" rows="2" ${lastInstOpt === 'new' ? 'required' : ''}>${lastInstExp}</textarea>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       `;
       batchesContainer.appendChild(batchRow);
       initFlatpickr(batchRow); // Initialize Flatpickr for the new batch
       batchCount++;
    });
  }

  if (batchesContainer) {
    batchesContainer.addEventListener('click', e => {
      if(e.target.closest('.remove-batch')) {
         if(document.querySelectorAll('.batch-item').length > 1) {
            e.target.closest('.batch-item').remove();
         } else {
            alert('At least one batch is required.');
         }
      }
    });
  }
  
  function getBatchImageCount() {
    const batchFiles = document.querySelectorAll('input[name^="batch_images"]');
    let count = 0;
    batchFiles.forEach(input => {
      if (input.files && input.files[0]) count++;
    });
    return count;
  }

  function updateImageUploadInstructions() {
    const batchCount = getBatchImageCount();
    const minNeeded = Math.max(0, 3 - batchCount);
    const maxAllowed = 5 - batchCount;
    const msgEl = document.querySelector('.cnd-upload-zone p.text-muted');
    
    if (batchCount > 0) {
      if (maxAllowed <= 0) {
        msgEl.innerHTML = `You have already added ${batchCount} batch images (Max limit reached).`;
        imageInput.disabled = true;
      } else {
        msgEl.innerHTML = `You added ${batchCount} batch images. Please upload <strong>${minNeeded} to ${maxAllowed} more</strong> images.`;
        imageInput.disabled = false;
      }
    } else {
      msgEl.innerHTML = `Upload between 3 to 5 images (Max 2MB each)`;
      imageInput.disabled = false;
    }
  }

  // Image Preview
  imageInput.addEventListener('change', function() {
    imagePreview.innerHTML = '';
    const files = Array.from(this.files);
    const batchCount = getBatchImageCount();
    const total = files.length + batchCount;

    if(total < 3 || total > 5) {
        let errMsg = '';
        if (batchCount >= 5) {
           errMsg = 'You already have 5 batch images. You cannot add more main images.';
        } else if (batchCount > 0) {
           errMsg = `With ${batchCount} batch images, you must upload <strong>between ${Math.max(0, 3-batchCount)} and ${5-batchCount} more</strong> images.`;
        } else {
           errMsg = 'Please upload <strong>between 3 and 5</strong> images.';
        }
        
        const msgEl = document.getElementById('imageUploadMsg');
        if(msgEl) {
           msgEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${errMsg}</span>`;
        } else {
           alert(errMsg);
        }
        // We will still show previews so they know what they selected
    } else {
        updateImageUploadInstructions();
    }

    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'preview-img';
        imagePreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });

  // ── Batch Image Sync Logic ────────────────────────────────────
  function syncBatchPreviews() {
      const batchPreviewContainer = document.getElementById('batchImagesPreview');
      const batchSection = document.getElementById('batchImagesSection');
      if(!batchPreviewContainer) return;

      batchPreviewContainer.innerHTML = '';
      const batchFiles = document.querySelectorAll('input[name^="batch_images"]');
      let hasFiles = false;

      batchFiles.forEach((input, index) => {
          if (input.files && input.files[0]) {
              hasFiles = true;
              const reader = new FileReader();
              reader.onload = e => {
                  const wrap = document.createElement('div');
                  wrap.className = 'position-relative';
                  wrap.innerHTML = `
                      <img src="${e.target.result}" class="preview-img" style="border: 2px solid var(--cnd-primary);">
                      <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary" style="font-size:0.6rem;">Batch ${index + 1}</span>
                  `;
                  batchPreviewContainer.appendChild(wrap);
              };
              reader.readAsDataURL(input.files[0]);
          }
      });

      if(batchSection) {
          batchSection.classList.toggle('d-none', !hasFiles || classTypeSelect.value !== 'regular');
      }
      updateImageUploadInstructions();
  }

  // Listen for changes on any batch image input (Delegated)
  document.addEventListener('change', e => {
      if (e.target.matches('input[name^="batch_images"]')) {
          syncBatchPreviews();
      }
  });

  // Also sync when switching to Step 4
  const nextBtnStep3 = steps[2].querySelector('.next-step');
  if(nextBtnStep3) {
      nextBtnStep3.addEventListener('click', syncBatchPreviews);
  }

  // Submit Handler
  form.addEventListener('submit', async function(e){
    e.preventDefault();
    
    // Check checkboxes for Regular Class batches
    if(classTypeSelect.value === 'regular') {
        const batchItems = document.querySelectorAll('.batch-item');
        for(let bi of batchItems) {
            const checked = bi.querySelectorAll('input[type="checkbox"]:checked');
            if(checked.length === 0) {
                alert('Please select at least one day for each batch.');
                return;
            }
        }
    }
    // Check Course days
    if(classTypeSelect.value === 'course') {
        const checked = sectionCourse.querySelectorAll('input[type="checkbox"]:checked');
        if(checked.length === 0) {
            alert('Please select at least one day for the course.');
            return;
        }
    }

    // Final Image Count Check
    const batchCount = getBatchImageCount();
    const mainCount = imageInput.files.length;
    const totalCount = batchCount + mainCount;

    if(totalCount < 3 || totalCount > 5) {
       if (batchCount > 0) {
           alert(`Total images (including batch images) must be between 3 and 5. You have ${batchCount} batch images and ${mainCount} main images.`);
       } else {
           alert('Please upload between 3 and 5 photos.');
       }
       goToStep(4);
       return;
    }

    // T&C Check
    const termsChecked = document.getElementById('termsCheck').checked;
    if(!termsChecked) {
        alert('You must agree to the Terms and Conditions to proceed.');
        return;
    }

    submitBtn.disabled = true;
    spinner.classList.remove('d-none');

    const formData = new FormData(this);
    
    try {
      const res = await fetch('<?= base_url('provider/listings/store') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      
      if(json.success) {
        alert(json.message);
        window.location.href = '<?= base_url('provider/listings') ?>';
      } else {
        if (json.errors) {
            let errorMsg = 'Please correct the following errors:\n';
            for (let field in json.errors) {
                errorMsg += `- ${json.errors[field]}\n`;
            }
            alert(errorMsg);
        } else {
            alert(json.message || 'Error creating listing.');
        }
      }
    } catch (err) {
      alert('Network error. Please try again.');
    } finally {
      submitBtn.disabled = false;
      spinner.classList.add('d-none');
    }
  });

})();
</script>
<?= $this->endSection() ?>
