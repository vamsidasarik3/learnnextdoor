<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<!-- ══ EDIT LISTING HEADER ════════════════════════════════════ -->
<section class="cnd-provider-hero py-5" style="background: linear-gradient(135deg, #3F3590 0%, #FF68B4 100%);">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-8 text-white">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb cnd-breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('provider/listings') ?>" class="text-white opacity-75">My Listings</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page">Edit Class</li>
          </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-2"><?= esc($listing->title) ?></h1>
        <p class="lead opacity-90 mb-0">Update your class details or manage availability.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══ EDIT FORM ════════════════════════════════════════════════ -->
<section class="py-5 bg-light min-vh-100">
  <div class="container">
    
    <!-- Tab Navigation -->
    <ul class="nav nav-pills cnd-nav-pills mb-4 bg-white p-2 rounded-pill shadow-sm d-inline-flex">
      <li class="nav-item">
        <button class="nav-link active rounded-pill px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#tab-general">General Info</button>
      </li>
      <li class="nav-item">
        <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#tab-avail">Availability</button>
      </li>
      <li class="nav-item">
        <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#tab-media">Media</button>
      </li>
    </ul>

    <div class="tab-content">
       <!-- ── TAB: GENERAL ── -->
       <div class="tab-pane fade show active" id="tab-general">
          <form id="editListingForm" class="bg-white rounded-4 shadow-sm p-4 p-md-5">
             <div class="row g-4">
                <div class="col-md-12">
                   <label class="form-label fw-bold small text-uppercase">Class Title</label>
                   <input type="text" name="title" class="form-control form-control-lg rounded-3 border-2" value="<?= esc($listing->title) ?>" required>
                </div>
                
                <div class="col-md-4">
                   <label class="form-label fw-bold small text-uppercase">Category</label>
                   <select name="category_id" id="categorySelect" class="form-select rounded-3 border-2" required>
                      <?php foreach($categories as $id => $name): ?>
                         <option value="<?= $id ?>" <?= $id == $listing->category_id ? 'selected' : '' ?>><?= esc($name) ?></option>
                      <?php endforeach; ?>
                   </select>
                </div>

                <div class="col-md-4">
                   <label class="form-label fw-bold small text-uppercase">Subcategories <span class="text-danger">*</span></label>
                   <select name="subcategory_ids[]" id="subcategorySelect" class="form-select rounded-3 border-2 select2-multi" multiple required data-placeholder="Choose subcategories...">
                      <option value="">Select Subcategory...</option>
                   </select>
                   <div class="form-text small">Select one or more relevant subcategories.</div>
                </div>

                <div class="col-md-4">
                   <label class="form-label fw-bold small text-uppercase">Class Type</label>
                   <select name="type" id="classType" class="form-select rounded-3 border-2" required>
                      <option value="regular" <?= $listing->type == 'regular' ? 'selected' : '' ?>>Regular / Recurring</option>
                      <option value="workshop" <?= $listing->type == 'workshop' ? 'selected' : '' ?>>One-time Workshop</option>
                      <option value="course" <?= $listing->type == 'course' ? 'selected' : '' ?>>Multi-day Course</option>
                   </select>
                </div>

                <!-- ══ DYNAMIC SCHEDULE FIELDS ══ -->
                <div class="col-md-12">
                  
                  <!-- ══ REGULAR CLASS SCHEDULE & BATCHES ══ -->
                  <div id="scheduleRegular" class="<?= $listing->type != 'regular' ? 'd-none' : '' ?> bg-light p-4 rounded-4 border-start border-4 mb-4" style="border-color:#3F3590!important;">
                     <h6 class="fw-bold mb-3 small text-uppercase">
                        <i class="bi bi-calendar-event me-2"></i>Class Schedule & Batches
                     </h6>
                     
                     <div class="row g-3 mb-4 d-none" id="regularStartDateRow">
                        <div class="col-md-6">
                           <label class="form-label small fw-bold">Class Start Date</label>
                           <input type="date" name="start_date_unused" id="regularStartDate" class="form-control rounded-3 border-2" value="<?= $listing->start_date ?>">
                        </div>
                     </div>

                     <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold small text-uppercase mb-0 text-pink">
                           <i class="bi bi-layers-fill me-1"></i> Class Batches
                        </label>
                        <button type="button" class="btn btn-sm btn-pink rounded-pill" id="addBatchBtn">
                           <i class="bi bi-plus-circle me-1"></i> Add a Batch
                        </button>
                     </div>

                     <div id="batchesContainer" class="d-grid gap-3">
                        <?php 
                           $batches = $listing->batches ?? []; 
                           if (empty($batches) && $listing->type == 'regular') {
                              // Fallback if somehow no batches exist but it's regular
                              $batches = [[
                                 'name' => 'Default Batch',
                                 'from_time' => $listing->class_time,
                                 'to_time' => $listing->class_end_time,
                                 'price' => $listing->price,
                                 'batch_size' => $listing->batch_size
                              ]];
                           }
                        ?>
                        <?php foreach($batches as $idx => $batch): ?>
                           <div class="card border-0 shadow-sm rounded-3 batch-item border-start border-3 border-pink position-relative">
                              <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 m-2 remove-batch" title="Remove Batch">
                                 <i class="bi bi-trash"></i>
                              </button>
                              <div class="card-body p-3">
                                 <div class="row g-3">
                                    <div class="col-md-4">
                                       <label class="form-label small fw-600 mb-1">Batch Start Date <span class="text-danger">*</span></label>
                                       <input type="date" name="batches[<?= $idx ?>][batch_start_date]" class="form-control form-control-sm border-2 rounded-2" value="<?= esc($batch['batch_start_date'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                       <label class="form-label small fw-600 mb-1">Batch Name <span class="text-danger">*</span></label>
                                       <input type="text" name="batches[<?= $idx ?>][name]" class="form-control form-control-sm border-2 rounded-2" value="<?= esc($batch['name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                       <label class="form-label small fw-600 mb-1">From Time <span class="text-danger">*</span></label>
                                       <input type="time" name="batches[<?= $idx ?>][from_time]" class="form-control form-control-sm border-2 rounded-2" value="<?= esc($batch['from_time'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                       <label class="form-label small fw-600 mb-1">To Time <span class="text-danger">*</span></label>
                                       <input type="time" name="batches[<?= $idx ?>][to_time]" class="form-control form-control-sm border-2 rounded-2" value="<?= esc($batch['to_time'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-6 col-md-4">
                                       <label class="form-label small fw-600 mb-1">Price (₹) <span class="text-danger">*</span></label>
                                       <div class="input-group input-group-sm">
                                          <input type="number" name="batches[<?= $idx ?>][price]" class="form-control border-2 rounded-start-2" value="<?= esc($batch['price'] ?? '') ?>" required min="0">
                                          <select name="batches[<?= $idx ?>][price_type]" class="form-select border-2 rounded-end-2" style="max-width: 90px;">
                                             <option value="monthly" <?= ($batch['price_type'] ?? 'monthly') === 'monthly' ? 'selected' : '' ?>>/mo</option>
                                             <option value="quarterly" <?= ($batch['price_type'] ?? 'monthly') === 'quarterly' ? 'selected' : '' ?>>/qt</option>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                       <label class="form-label small fw-600 mb-1">Max Students</label>
                                       <input type="number" name="batches[<?= $idx ?>][batch_size]" class="form-control form-control-sm border-2 rounded-2" value="<?= esc($batch['batch_size'] ?? '') ?>" placeholder="Unlimited" min="1">
                                    </div>
                                    <div class="col-12 col-md-4 d-flex align-items-end">
                                       <p class="small text-muted mb-1 fst-italic">This batch is active.</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        <?php endforeach; ?>
                     </div>
                  </div>

                  <!-- Workshop Extra -->
                  <div id="scheduleWorkshop" class="<?= $listing->type != 'workshop' ? 'd-none' : '' ?> bg-light p-4 rounded-4 border-start border-4 mb-3" style="border-color:#f7971e!important;">
                     <h6 class="fw-bold mb-3 small text-uppercase">Workshop Settings</h6>
                     <div class="row g-3 mb-3">
                        <div class="col-md-6">
                           <label class="form-label small fw-bold">Workshop Start Date <span class="text-danger">*</span></label>
                           <input type="date" name="start_date" id="workshopStartDate" class="form-control rounded-3 border-2" value="<?= $listing->start_date ?>">
                        </div>
                     </div>
                     <div class="row g-3">
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Reg. End Date</label>
                           <input type="date" name="registration_end_date" class="form-control rounded-3" value="<?= $listing->registration_end_date ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Early Bird Price (₹)</label>
                           <input type="number" name="early_bird_price" class="form-control rounded-3" value="<?= $listing->early_bird_price ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">EB End Date</label>
                           <input type="date" name="early_bird_date" class="form-control rounded-3" value="<?= $listing->early_bird_date ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">EB Slots</label>
                           <input type="number" name="early_bird_slots" class="form-control rounded-3" value="<?= $listing->early_bird_slots ?>">
                        </div>
                     </div>
                  </div>

                  <!-- Course -->
                  <div id="scheduleCourse" class="<?= $listing->type != 'course' ? 'd-none' : '' ?> bg-light p-4 rounded-4 border-start border-4 mb-3" style="border-color:#7C4DFF!important;">
                     <h6 class="fw-bold mb-3 small text-uppercase">Course Schedule</h6>
                     <div class="row g-3">
                        <div class="col-md-6">
                           <label class="form-label small fw-bold">Course Start Date <span class="text-danger">*</span></label>
                           <input type="date" name="start_date" id="courseStartDate" class="form-control rounded-3 border-2" value="<?= $listing->start_date ?>" required>
                        </div>
                        <div class="col-md-6">
                           <label class="form-label small fw-bold">Course End Date</label>
                           <input type="date" name="end_date" class="form-control rounded-3 border-2" value="<?= $listing->end_date ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Daily Time (From)</label>
                           <input type="time" name="class_time" class="form-control rounded-3 border-2" value="<?= $listing->class_time ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Daily Time (To)</label>
                           <input type="time" name="class_end_time" class="form-control rounded-3 border-2" value="<?= $listing->class_end_time ?>">
                        </div>
                        <div class="col-md-4">
                           <label class="form-label small fw-bold">Reg. End Date</label>
                           <input type="date" name="registration_end_date" class="form-control rounded-3" value="<?= $listing->registration_end_date ?>">
                        </div>
                     </div>
                  </div>

                </div>

                <div class="col-md-12">
                   <label class="form-label fw-bold small text-uppercase">Description</label>
                   <textarea name="description" class="form-control rounded-3 border-2" rows="5" required><?= esc($listing->description) ?></textarea>
                </div>

                <div class="col-md-12 instructor-section">
                   <h6 class="fw-bold text-pink small text-uppercase mb-3 mt-4"><i class="bi bi-person-badge me-2"></i>Instructor Details</h6>
                   <div class="row g-3">
                       <div class="col-md-12">
                          <label class="form-label small fw-bold">Select Verified Instructor or Add New</label>
                          <select name="instructor_option" class="form-select rounded-3 instructor-select" onchange="handleSharedInstructorSelect(this)">
                             <option value="new" <?= !$listing->instructor_id ? 'selected' : '' ?>>Add New Instructor</option>
                             <?php if(!empty($instructors)): ?>
                                <?php foreach($instructors as $vi): ?>
                                   <option value="<?= $vi->id ?>" 
                                           data-name="<?= esc($vi->name) ?>"
                                           data-exp="<?= esc($vi->experience) ?>" 
                                           data-social="<?= esc($vi->social_links) ?>"
                                           data-status="<?= $vi->kyc_status ?>"
                                           <?= $listing->instructor_id == $vi->id ? 'selected' : '' ?>>
                                      <?= esc($vi->name) ?> (<?= ($vi->kyc_status ?? '') === 'verified' ? '✅ Verified' : '⏳ Pending' ?>)
                                   </option>
                                <?php endforeach; ?>
                             <?php endif; ?>
                          </select>
                       </div>

                       <div class="col-md-12 instructor-kyc-box <?= $listing->instructor_id ? 'd-none' : '' ?>">
                          <div class="bg-light p-3 rounded-4 border border-dashed text-center">
                             <label class="form-label small fw-bold d-block mb-1">Instructor KYC Document (Optional)</label>
                             <input type="file" name="instructor_kyc_doc" class="form-control rounded-pill px-3" accept="image/*,.pdf">
                          </div>
                       </div>

                       <div class="col-md-6 instructor-name-box <?= $listing->instructor_id ? 'd-none' : '' ?>">
                          <label class="form-label small fw-bold">Instructor Name <span class="text-danger">*</span></label>
                          <input type="text" name="instructor_name" class="form-control rounded-3" value="<?= esc($listing->instructor_name) ?>">
                       </div>

                       <div class="col-md-6 instructor-social-box <?= $listing->instructor_id ? 'd-none' : '' ?>">
                          <label class="form-label small fw-bold">Social Link / Portfolio</label>
                          <input type="url" name="social_links" class="form-control rounded-3" value="<?= esc($listing->social_links) ?>">
                       </div>

                       <div class="col-md-12 instructor-exp-box <?= $listing->instructor_id ? 'd-none' : '' ?>">
                          <label class="form-label small fw-bold">Experience / Bio <span class="text-danger">*</span></label>
                          <textarea name="experience" class="form-control rounded-3" rows="3"><?= esc($listing->experience) ?></textarea>
                       </div>
                   </div>
                </div>

                <div class="col-md-12">
                   <label class="form-label fw-bold small text-uppercase">Address</label>
                   <textarea name="address" class="form-control rounded-3 border-2" rows="2" required><?= esc($listing->address) ?></textarea>
                </div>

                <!-- ── Base Pricing (Non-Regular Only) ── -->
                <div id="basePricingWrap" class="row g-4 <?= $listing->type == 'regular' ? 'd-none' : '' ?>">
                   <div class="col-md-4">
                      <label class="form-label fw-bold small text-uppercase">Base Price (₹)</label>
                      <input type="number" name="price" id="basePriceInput" class="form-control rounded-3 border-2" value="<?= esc($listing->price) ?>" <?= $listing->type != 'regular' ? 'required' : '' ?> min="0">
                   </div>

                   <div class="col-md-4">
                      <label class="form-label fw-bold small text-uppercase">Max. Allocated Students</label>
                      <input type="number" name="batch_size" class="form-control rounded-3 border-2" value="<?= esc($listing->batch_size) ?>" placeholder="Unlimited" min="1">
                   </div>

                   <div class="col-md-4 pt-4">
                      <div class="form-check form-switch p-3 border rounded-3 border-2 d-none">
                          <!-- Placeholders for structure alignment -->
                      </div>
                   </div>
                </div>

                <div id="freeTrialWrap" class="col-md-12 mb-4 <?= $listing->type != 'regular' ? 'd-none' : '' ?>">
                   <div class="form-check form-switch p-3 border rounded-3 border-2">
                       <input class="form-check-input ms-0 me-3" type="checkbox" name="free_trial" value="1" id="freeTrialCheck" <?= $listing->free_trial ? 'checked' : '' ?>>
                       <label class="form-check-label fw-600" for="freeTrialCheck">Offer a free first session</label>
                   </div>
                </div>
             </div>

             <div class="mt-5 pt-4 border-top">
                <button type="submit" class="btn btn-pink py-3 px-5 rounded-pill fw-bold shadow-sm" id="submitBtn">
                   <span id="submitSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                   Update Listing
                </button>
             </div>
          </form>
       </div>

       <!-- ── TAB: AVAILABILITY ── -->
       <div class="tab-pane fade" id="tab-avail">
          <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
             <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Manage Specific Sessions</h5>
                <button type="button" class="btn btn-outline-pink btn-sm rounded-pill" onclick="addNewSlot()">
                   <i class="bi bi-plus-circle me-1"></i> Add New Fixed Slot
                </button>
             </div>
             
             <p class="text-muted small mb-4">You can disable sessions if you're unavailable on specific days. Red indicates a disabled session.</p>

             <div class="row g-3" id="slotsGrid">
                <?php foreach($slots as $slot): ?>
                   <div class="col-md-4 col-lg-3">
                      <div class="card border-2 <?= $slot->is_disabled ? 'bg-light border-danger opacity-75' : 'border-success' ?> rounded-4 p-3 h-100 transition shadow-sm" style="cursor: pointer;" onclick="toggleDateDisable(this, <?= $slot->id ?>, '<?= $slot->available_date ?>', <?= $slot->is_disabled ? 0 : 1 ?>)">
                         <div class="d-flex justify-content-between">
                            <span class="small fw-bold text-uppercase <?= $slot->is_disabled ? 'text-danger' : 'text-success' ?>">
                               <?= $slot->is_disabled ? '<i class="bi bi-x-circle-fill"></i> Disabled' : '<i class="bi bi-check-circle-fill"></i> Enabled' ?>
                            </span>
                         </div>
                         <h6 class="fw-bold mt-2 mb-1"><?= date('D, d M Y', strtotime($slot->available_date)) ?></h6>
                         <div class="small fw-600 text-muted"><i class="bi bi-clock me-1"></i> <?= date('g:i A', strtotime($slot->available_time)) ?></div>
                      </div>
                   </div>
                <?php endforeach; ?>
             </div>

             <!-- Hidden Container for Form Submission Sync -->
             <div id="formSlotsSync" class="d-none">
                <?php foreach($slots as $idx => $slot): ?>
                   <input type="hidden" name="slots[<?= $idx ?>][date]" value="<?= $slot->available_date ?>">
                   <input type="hidden" name="slots[<?= $idx ?>][time]" value="<?= $slot->available_time ?>">
                <?php endforeach; ?>
             </div>
          </div>
       </div>

       <!-- ── TAB: MEDIA ── -->
       <div class="tab-pane fade" id="tab-media">
          <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
             <h5 class="fw-bold mb-4">Photos</h5>
             <div class="row g-3 mb-5">
                 <?php foreach($images as $img): ?>
                    <div class="col-6 col-md-3">
                       <div class="position-relative rounded-4 overflow-hidden shadow-sm" style="padding-top: 100%;">
                          <img src="<?= listing_img_url($img->image_path) ?>" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
                          <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" onclick="deleteExistingImage(<?= $img->id ?>)"><i class="bi bi-trash"></i></button>
                       </div>
                    </div>
                 <?php endforeach; ?>
             </div>

             <h6 class="fw-bold mb-3">Upload New Photos</h6>
             <div class="cnd-upload-zone border-2 border-dashed rounded-4 p-5 text-center cursor-pointer" onclick="document.getElementById('imageInput').click()">
                <i class="bi bi-cloud-arrow-up display-4 text-muted opacity-50 mb-3"></i>
                <p class="mb-0 text-muted">Click to add more photos (Max total 5)</p>
                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">
             </div>
             <div id="imagePreview" class="d-flex flex-wrap gap-3 mt-3"></div>
          </div>
       </div>

    </div>
  </div>
</section>

<!-- ── ADD SLOT MODAL ── -->
<div class="modal fade" id="addSlotModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4">
         <div class="modal-header border-0 pb-0">
            <h5 class="fw-bold">Add New Session</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div>
         <div class="modal-body p-4">
            <div class="mb-3">
               <label class="form-label small fw-bold text-uppercase">Date</label>
               <input type="date" id="newSlotDate" class="form-control rounded-3 border-2" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
               <label class="form-label small fw-bold text-uppercase">Time</label>
               <input type="time" id="newSlotTime" class="form-control rounded-3 border-2">
            </div>
            <button type="button" class="btn btn-pink w-100 py-3 rounded-pill fw-bold" id="confirmAddSlot">Add to Schedule</button>
         </div>
      </div>
   </div>
</div>

<style>
.cnd-nav-pills .nav-link { color: #666; transition: all 0.3s; }
.cnd-nav-pills .nav-link.active { background: var(--cnd-pink); color: #fff; box-shadow: 0 4px 12px rgba(255, 104, 180,0.3); }
.cnd-nav-pills .nav-link:not(.active):hover { background: #f8f9fa; color: var(--cnd-pink); }

.cnd-upload-zone { background: #fafafa; border-style: dashed; transition: all 0.3s; }
.cnd-upload-zone:hover { border-color: var(--cnd-pink); background: rgba(255, 104, 180,0.02); }
.preview-img { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

.btn-outline-pink { border-color: var(--cnd-pink); color: var(--cnd-pink); transition: all 0.3s; }
.btn-outline-pink:hover { background: var(--cnd-pink); color: #fff; }

.transition { transition: all 0.2s; }
.transition:hover { transform: translateY(-3px); }
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
<script>
(function(){
  'use strict';
  
  // Initialize Flatpickr for existing date inputs
  function initFlatpickr(container = document) {
    container.querySelectorAll('input[type="date"]').forEach(el => {
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
  
  const form = document.getElementById('editListingForm');
  const imageInput = document.getElementById('imageInput');
  const imagePreview = document.getElementById('imagePreview');
  const submitBtn = document.getElementById('submitBtn');
  const spinner = document.getElementById('submitSpinner');
  const addSlotModal = new bootstrap.Modal(document.getElementById('addSlotModal'));
  // ── Instructor Selection ─────────────────────────────────────────
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
         kycInput.required = false; 
      } else {
         const status = opt.getAttribute('data-status');
         nameBox.classList.add('d-none');
         socialBox.classList.add('d-none');
         expBox.classList.add('d-none');
         
         // Only hide KYC box if verified
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
         kycInput.required = false;
      } else {
         const status = opt.getAttribute('data-status');
         nameBox.classList.add('d-none');
         socialBox.classList.add('d-none');
         expBox.classList.add('d-none');
         
         // Only hide KYC box if verified
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

  // ── Subcategory Loader ──────────────────────────────────────────
  const categorySelect = document.getElementById('categorySelect');
  const subcategorySelect = document.getElementById('subcategorySelect');
  const initialSubcategoryIds = '<?= $listing->subcategory_ids ?? "" ?>'.split(',').filter(id => id.length > 0);

  // Initialize Select2
  $(document).ready(function() {
      $('#subcategorySelect').select2({
          theme: 'default',
          placeholder: $('#subcategorySelect').data('placeholder'),
          width: '100%'
      });
  });

  async function loadSubcategories(catId, selectedIds = []) {
     const $subcategorySelect = $('#subcategorySelect');
     $subcategorySelect.html('<option value="">Loading...</option>').trigger('change');
     subcategorySelect.disabled = true;

     if(!catId) {
        $subcategorySelect.html('<option value="">Select Category first...</option>').trigger('change');
        return;
     }

     try {
        const res = await fetch(`<?= base_url('provider/api/subcategories') ?>?category_id=${catId}`);
        const data = await res.json();
        
        $subcategorySelect.empty();
        data.forEach(sub => {
           const isSelected = selectedIds.includes(sub.id.toString());
           const opt = new Option(sub.name, sub.id, isSelected, isSelected);
           $subcategorySelect.append(opt);
        });
        
        subcategorySelect.disabled = false;
        $subcategorySelect.trigger('change');
     } catch(e) {
        console.error(e);
        $subcategorySelect.html('<option value="">Error loading</option>').trigger('change');
     }
  }

  if(categorySelect) {
     categorySelect.addEventListener('change', function() {
        loadSubcategories(this.value);
     });
     
     // Load initial subcategories
     loadSubcategories(categorySelect.value, initialSubcategoryIds);
  }

  // Dynamic Schedule Fields Toggle
  function applyTypeUI(type) {
     const isRegular = (type === 'regular');
     
     document.getElementById('scheduleRegular').classList.toggle('d-none', !isRegular);
     document.getElementById('scheduleWorkshop').classList.toggle('d-none', type !== 'workshop');
     document.getElementById('scheduleCourse').classList.toggle('d-none', type !== 'course');
     
     document.getElementById('basePricingWrap').classList.toggle('d-none', isRegular);
     document.getElementById('freeTrialWrap').classList.toggle('d-none', !isRegular);
     
     document.getElementById('basePriceInput').required = !isRegular;
     
     if(document.getElementById('regularStartDateRow')) {
        document.getElementById('regularStartDateRow').classList.toggle('d-none', isRegular);
     }

     const workshopStartDate = document.getElementById('workshopStartDate');
     const courseStartDate = document.getElementById('courseStartDate');
     const regularStartDate = document.getElementById('regularStartDate');

     if(workshopStartDate) workshopStartDate.required = (type === 'workshop');
     if(courseStartDate) courseStartDate.required = (type === 'course');
     if(regularStartDate) regularStartDate.required = false;
     
     // Update batch inputs requirements
     document.querySelectorAll('#batchesContainer .batch-item input').forEach(input => {
        if(!input.name.includes('[batch_size]')) { // Exclude batch_size from general required check
           input.required = isRegular;
        }
     });
  }

  classType.addEventListener('change', function(){ applyTypeUI(this.value); });
  
  // ── Regular Batch Management ──────────────────────────────────
  let batchCount = <?= count($batches) ?>;
  const addBatchBtn = document.getElementById('addBatchBtn');
  const batchesContainer = document.getElementById('batchesContainer');
  
  if (addBatchBtn) {
    addBatchBtn.addEventListener('click', () => {
       const batchRow = document.createElement('div');
       batchRow.className = 'card border-0 shadow-sm rounded-3 batch-item border-start border-3 border-pink position-relative mt-0';
       batchRow.innerHTML = `
          <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 m-2 remove-batch" title="Remove Batch">
             <i class="bi bi-trash"></i>
          </button>
          <div class="card-body p-3">
             <div class="row g-3">
                <div class="col-md-4">
                   <label class="form-label small fw-600 mb-1">Batch Start Date <span class="text-danger">*</span></label>
                   <input type="date" name="batches[${batchCount}][batch_start_date]" class="form-control form-control-sm border-2 rounded-2" required>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-600 mb-1">Batch Name <span class="text-danger">*</span></label>
                   <input type="text" name="batches[${batchCount}][name]" class="form-control form-control-sm border-2 rounded-2" placeholder="e.g. Evening Batch" required>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-600 mb-1">From Time <span class="text-danger">*</span></label>
                   <input type="time" name="batches[${batchCount}][from_time]" class="form-control form-control-sm border-2 rounded-2" required>
                </div>
                <div class="col-md-4">
                   <label class="form-label small fw-600 mb-1">To Time <span class="text-danger">*</span></label>
                   <input type="time" name="batches[${batchCount}][to_time]" class="form-control form-control-sm border-2 rounded-2" required>
                </div>
                <div class="col-6 col-md-4">
                   <label class="form-label small fw-600 mb-1">Price (₹) <span class="text-danger">*</span></label>
                   <div class="input-group input-group-sm">
                      <input type="number" name="batches[${batchCount}][price]" class="form-control border-2 rounded-start-2" placeholder="₹" required min="0">
                      <select name="batches[${batchCount}][price_type]" class="form-select border-2 rounded-end-2" style="max-width: 90px;">
                         <option value="monthly">/mo</option>
                         <option value="quarterly">/qt</option>
                      </select>
                   </div>
                </div>
                <div class="col-6 col-md-4">
                   <label class="form-label small fw-600 mb-1">Max Students</label>
                   <input type="number" name="batches[${batchCount}][batch_size]" class="form-control form-control-sm border-2 rounded-2" placeholder="Unlimited" min="1">
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                   <p class="small text-muted mb-1 fst-italic">Additional batch.</p>
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
            alert('At least one batch is required for regular classes.');
         }
      }
    });
  }

  // Sync Slots on Form Submit
  form.addEventListener('submit', async function(e){
    e.preventDefault();
    
    submitBtn.disabled = true;

    // Validation
    const type = classType.value;
    if (type === 'workshop') {
        const startDate = document.getElementById('workshopStartDate').value;
        const regEndDate = form.querySelector('input[name="registration_end_date"]').value;
        const price = parseFloat(document.getElementById('basePriceInput').value) || 0;
        const ebPrice = parseFloat(form.querySelector('input[name="early_bird_price"]').value) || 0;
        const ebEndDate = form.querySelector('input[name="early_bird_date"]').value;

        if (regEndDate && startDate && regEndDate > startDate) {
            alert('Registration must end on or before the workshop start date.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
        if (ebPrice > 0 && ebPrice >= price) {
            alert('Early bird price must be less than the standard workshop price.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
        if (ebEndDate && regEndDate && ebEndDate > regEndDate) {
            alert('Early bird offer must end on or before the registration deadline.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
    } else if (type === 'course') {
        const startDate = document.getElementById('courseStartDate').value;
        const endDate = form.querySelector('input[name="end_date"]').value;
        const regEndDate = form.querySelector('input[name="registration_end_date"]').value;
        const price = parseFloat(document.getElementById('basePriceInput').value) || 0;
        const ebPrice = parseFloat(form.querySelector('input[name="early_bird_price"]').value) || 0;
        const ebEndDate = form.querySelector('input[name="early_bird_date"]').value;

        if (startDate && endDate && startDate >= endDate) {
            alert('Course start date must be before the end date.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
        if (regEndDate && startDate && regEndDate > startDate) {
            alert('Registration must end on or before the course start date.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
        if (ebPrice > 0 && ebPrice >= price) {
            alert('Early bird price must be less than the course price.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
        if (ebEndDate && regEndDate && ebEndDate > regEndDate) {
            alert('Early bird offer must end on or before the registration deadline.');
            submitBtn.disabled = false; spinner.classList.add('d-none');
            return;
        }
    } else if (type === 'regular') {
        const batchItems = document.querySelectorAll('.batch-item');
        for(let bi of batchItems) {
            const fromTime = bi.querySelector('input[name*="[from_time]"]').value;
            const toTime = bi.querySelector('input[name*="[to_time]"]').value;
            if(fromTime && toTime && fromTime >= toTime) {
                alert('Start time must be before end time for batch details.');
                submitBtn.disabled = false; spinner.classList.add('d-none');
                return;
            }
        }
    }

    spinner.classList.remove('d-none');

    const formData = new FormData(this);
    
    // Add images from separate input
    if(imageInput.files.length) {
       for(let i=0; i<imageInput.files.length; i++) {
          formData.append('images[]', imageInput.files[i]);
       }
    }

    // Add Slots from Sync Container
    document.querySelectorAll('#formSlotsSync input').forEach(input => {
       formData.append(input.name, input.value);
    });

    try {
      const res = await fetch('<?= base_url('provider/listings/update/' . $listing->id) ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      
      if(json.success) {
        alert(json.message);
        window.location.reload();
      } else {
        if (json.errors) {
            let errorMsg = 'Please correct the following errors:\n';
            for (let field in json.errors) {
                errorMsg += `- ${json.errors[field]}\n`;
            }
            alert(errorMsg);
        } else {
            alert(json.message || 'Error updating listing.');
        }
      }
    } catch (err) { alert('Network error.'); }
    finally { submitBtn.disabled = false; spinner.classList.add('d-none'); }
  });

  // Slot Management
  window.addNewSlot = function() {
     addSlotModal.show();
  };

  document.getElementById('confirmAddSlot').addEventListener('click', function(){
      const date = document.getElementById('newSlotDate').value;
      const time = document.getElementById('newSlotTime').value;
      if(!date || !time) return alert('Please select date and time');

      const container = document.getElementById('formSlotsSync');
      const idx = container.querySelectorAll('input').length / 2;
      
      const hDate = document.createElement('input');
      hDate.type = 'hidden';
      hDate.name = `slots[${idx}][date]`;
      hDate.value = date;
      
      const hTime = document.createElement('input');
      hTime.type = 'hidden';
      hTime.name = `slots[${idx}][time]`;
      hTime.value = time;

      container.appendChild(hDate);
      container.appendChild(hTime);

      // Append to visible grid (mockup)
      const grid = document.getElementById('slotsGrid');
      const col = document.createElement('div');
      col.className = 'col-md-4 col-lg-3';
      col.innerHTML = `
         <div class="card border-2 border-warning rounded-4 p-3 h-100 shadow-sm">
            <span class="small fw-bold text-uppercase text-warning"><i class="bi bi-plus-circle-fill"></i> New Slot (Unsaved)</span>
            <h6 class="fw-bold mt-2 mb-1">${date}</h6>
            <div class="small fw-600 text-muted"><i class="bi bi-clock me-1"></i> ${time}</div>
         </div>
      `;
      grid.appendChild(col);
      addSlotModal.hide();
      alert('Slot added! Click "Update Listing" to save changes.');
  });

  // Toggle Date Disable (AJAX)
  window.toggleDateDisable = async function(el, slotId, date, status) {
    el.style.opacity = '0.5';
    el.style.pointerEvents = 'none';

    try {
      const formData = new FormData();
      formData.append('listing_id', '<?= $listing->id ?>');
      formData.append('date', date);
      formData.append('disable', status);

      const res = await fetch('<?= base_url('provider/api/listings/disable-dates') ?>', { method: 'POST', body: formData });
      const json = await res.json();

      if(json.success) {
         window.location.reload();
      } else {
         alert(json.message);
         el.style.opacity = '1';
         el.style.pointerEvents = 'auto';
      }
    } catch(e) { window.location.reload(); }
  };

  // Image Preview
  imageInput.addEventListener('change', function() {
    imagePreview.innerHTML = '';
    const files = Array.from(this.files).slice(0, 5);
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

  window.deleteExistingImage = async function(id) {
     if(!confirm('Delete this image?')) return;
     alert('Feature coming soon! For now, images remain as history.');
  };

})();
</script>
<?= $this->endSection() ?>
