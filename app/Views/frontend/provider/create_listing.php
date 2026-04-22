<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>
<div class="multi-step-card mt-4">
    <div class="stepper-header">
        <div class="stepper-info">
            <span class="stepper-title" id="stepTitle">Create New Class</span>
            <span class="stepper-count" id="stepCount">Step 1 of 4</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progressBar" style="width: 25%;"></div>
        </div>
    </div>

    <form id="createListingForm" class="create-listing-form" enctype="multipart/form-data" novalidate>
        
        <!-- STEP 1: Class Type & Category -->
        <div class="form-body step-content" id="step1">
            <h3 class="form-section-title">Select Class Type</h3>
            <div class="type-card-grid">
                <div class="type-card active" data-type="regular">
                    <div class="type-title">Regular Class</div>
                    <div class="type-subtitle">Ongoing recurring batches</div>
                    <input type="radio" name="type" value="regular" class="d-none" checked>
                </div>
                <div class="type-card" data-type="course">
                    <div class="type-title">Course</div>
                    <div class="type-subtitle">Fixed-duration program</div>
                    <input type="radio" name="type" value="course" class="d-none">
                </div>
                <div class="type-card" data-type="workshop">
                    <div class="type-title">Workshop</div>
                    <div class="type-subtitle">One-time intensive event</div>
                    <input type="radio" name="type" value="workshop" class="d-none">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Category <span>*</span></label>
                <select name="category_id" id="categorySelect" class="form-select" required>
                    <option value="">Select category</option>
                    <?php foreach($categories as $id => $name): ?>
                        <option value="<?= $id ?>"><?= esc($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Subcategories <span>*</span></label>
                <select name="subcategory_ids[]" id="subcategorySelect" class="form-select select2-multi" multiple required disabled>
                </select>
                <div class="form-text text-muted small mt-1">Select at least one relevant subcategory.</div>
            </div>
        </div>

        <!-- STEP 2: Institute Details -->
        <div class="form-body step-content d-none" id="step2">
            <h3 class="form-section-title">Institute Details</h3>
            
            <div class="form-group">
                <label class="form-label">Institute / Class Name <span>*</span></label>
                <input type="text" name="institute_name" id="instituteName" class="form-control" placeholder="e.g. Art & Soul Academy" required>
                <input type="hidden" name="title" id="classTitle">
            </div>

            <div class="form-group">
                <label class="form-label">About / Description <span>*</span></label>
                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Tell us about your institute and the classes you offer." required minlength="50"></textarea>
                <div class="form-text text-muted small mt-1">Minimum 50 characters required.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Address (Manual Entry) <span>*</span></label>
                <input type="text" name="manual_address" class="form-control" placeholder="Enter Full Address Manually" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nearest Landmark (Search) <span>*</span></label>
                <input type="text" id="locationInput" name="formatted_address" class="form-control" placeholder="Search for your location to pin on map..." required autocomplete="off">
                
                <!-- Hidden Location Data -->
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">
                <input type="hidden" name="city" id="city">
                <input type="hidden" name="locality" id="locality">
                <input type="hidden" name="pincode" id="pincode">
                <input type="hidden" name="address" id="full_address">
            </div>
        </div>

        <!-- STEP 3: Create Batches -->
        <div class="form-body step-content d-none" id="step3">
            
            <div id="sectionRegular">
                <div id="batchesContainer">
                    <!-- Dynamic Batches will be inserted here -->
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary-provider rounded-pill px-4 shadow-sm" id="addBatchBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Batch
                    </button>
                </div>
            </div>

            <div id="sectionWorkshop" class="d-none pt-2">
                <div class="workshop-details-wrap mb-5">
                    <h5 class="fw-bold mb-3 text-dark bg-light p-2 rounded" style="display: inline-block;">Workshop Specifics</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Registration Deadline <span>*</span></label>
                            <input type="text" name="workshop[registration_end_date]" class="form-control datepicker" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Workshop Date <span>*</span></label>
                            <input type="text" name="workshop[start_date]" class="form-control datepicker" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Start Time <span>*</span></label>
                            <input type="text" name="workshop[from_time]" class="form-control timepicker" placeholder="--:-- --">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">End Time <span>*</span></label>
                            <input type="text" name="workshop[to_time]" class="form-control timepicker" placeholder="--:-- --">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Workshop Fee (₹) <span>*</span></label>
                            <input type="number" name="workshop[price]" class="form-control" placeholder="5000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Max Students <span>*</span></label>
                            <input type="number" name="workshop[batch_size]" class="form-control" placeholder="50">
                        </div>
                        
                        <!-- Instructor for Workshop -->
                        <div class="col-12">
                            <label class="form-label fw-600 mb-2">Instructor</label>
                            <select name="instructor_option" class="form-select" onchange="toggleSharedInstructor(this, 'workshop')">
                                <option value="" disabled selected>Select instructor</option>
                                <option value="new">+ Add New Instructor</option>
                                <?php foreach($instructors as $inst): ?>
                                    <option value="<?= $inst->id ?>"><?= $inst->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Early Bird for Workshop -->
                        <div class="col-12">
                            <div class="form-check d-flex align-items-center gap-2 mb-3">
                                <input class="form-check-input mt-0" type="checkbox" id="earlyBirdCheckW" onchange="toggleEarlyBird('W')">
                                <label class="form-check-label fw-600" for="earlyBirdCheckW">Enable Early Bird Pricing</label>
                            </div>
                            <div id="earlyBirdFieldsW" class="d-none p-3 border rounded-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Price (₹)</label>
                                        <input type="number" name="workshop[early_bird_price]" class="form-control form-control-sm" placeholder="1200">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Ends</label>
                                        <input type="text" name="workshop[early_bird_end_date]" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Slots</label>
                                        <input type="number" name="workshop[early_bird_count]" class="form-control form-control-sm" placeholder="10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="workshopInstructorNew" class="d-none mb-4">
                    <h6 class="fw-bold mb-3 text-muted">New Instructor Details</h6>
                    <div class="row g-3 p-3 border rounded-3 bg-light">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Instructor Name *</label>
                            <input type="text" name="instructor_name" class="form-control form-control-sm" placeholder="Enter Full Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Experience (Years)</label>
                            <input type="text" name="experience" class="form-control form-control-sm" placeholder="e.g. 5 Years">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">KYC / Certification Document</label>
                            <input type="file" name="instructor_kyc" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <div id="sectionCourse" class="d-none pt-2">
                <div class="course-details-wrap mb-5">
                    <h5 class="fw-bold mb-3 text-dark bg-light p-2 rounded" style="display: inline-block;">Course Specifics</h5>
                    
                    <div class="row g-4">
                        <!-- Duration & Registration -->
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course Duration <span>*</span></label>
                            <div class="input-group">
                                <input type="number" name="course[duration_number]" class="form-control" placeholder="10">
                                <select name="course[duration_type]" class="form-select" style="max-width: 120px;">
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                    <option value="days">Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Registration Deadline <span>*</span></label>
                            <input type="text" name="course[registration_end_date]" class="form-control datepicker" placeholder="mm/dd/yyyy">
                        </div>

                        <!-- Start & End Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course Start Date <span>*</span></label>
                            <input type="text" name="course[start_date]" class="form-control datepicker" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course End Date <span>*</span></label>
                            <input type="text" name="course[end_date]" class="form-control datepicker" placeholder="mm/dd/yyyy">
                        </div>

                        <!-- Fee & Max Students -->
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course Fee (₹) <span>*</span></label>
                            <input type="number" name="course[price]" class="form-control" placeholder="15000">
                            <input type="hidden" name="course[price_type]" value="fixed">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Max Students <span>*</span></label>
                            <input type="number" name="course[batch_size]" class="form-control" placeholder="30">
                        </div>
                        
                        <!-- Instructor for Course -->
                        <div class="col-12">
                            <label class="form-label fw-600 mb-2">Instructor</label>
                            <select name="course_instructor_option" class="form-select course-instructor-selector" onchange="toggleSharedInstructor(this, 'course')">
                                <option value="" disabled selected>Select instructor</option>
                                <option value="new">+ Add New Instructor</option>
                                <?php foreach($instructors as $inst): ?>
                                    <option value="<?= $inst->id ?>"><?= $inst->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Early Bird for Course -->
                        <div class="col-12">
                            <div class="form-check d-flex align-items-center gap-2 mb-3">
                                <input class="form-check-input mt-0" type="checkbox" id="earlyBirdCheckC" onchange="toggleEarlyBird('C')">
                                <label class="form-check-label fw-600" for="earlyBirdCheckC">
                                    Enable Early Bird Pricing
                                </label>
                            </div>

                            <div id="earlyBirdFieldsC" class="d-none p-3 border rounded-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Price (₹)</label>
                                        <input type="number" name="course[early_bird_price]" class="form-control form-control-sm" placeholder="12000">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Ends</label>
                                        <input type="text" name="course[early_bird_end_date]" class="form-control form-control-sm datepicker" placeholder="dd-mm-yyyy">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Slots</label>
                                        <input type="number" name="course[early_bird_count]" class="form-control form-control-sm" placeholder="10">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Times for Course -->
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Daily Start Time <span>*</span></label>
                            <input type="text" name="course[from_time]" class="form-control timepicker" placeholder="--:-- --">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Daily End Time <span>*</span></label>
                            <input type="text" name="course[to_time]" class="form-control timepicker" placeholder="--:-- --">
                        </div>
                    </div>
                </div>

                <div id="courseInstructorNew" class="d-none mb-4">
                    <h6 class="fw-bold mb-3 text-muted">New Instructor Details</h6>
                    <div class="row g-3 p-3 border rounded-3 bg-light">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Instructor Name *</label>
                            <input type="text" name="course_instructor_name" class="form-control form-control-sm" placeholder="Enter Full Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Experience (Years)</label>
                            <input type="text" name="course_experience" class="form-control form-control-sm" placeholder="e.g. 5 Years">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">KYC / Certification Document</label>
                            <input type="file" name="course_instructor_kyc" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shared Instructor Information (For Workshop/Course) -->
            <div id="sharedInstructorSection" class="mt-4 pt-4 border-top d-none">
                <h4 class="fw-bold mb-4">Instructor Information</h4>
                <div class="instructor-details-wrap">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-600 mb-2">Instructor</label>
                            <select name="instructor_id" class="form-select" onchange="toggleSharedInstructor(this)">
                                <option value="new" selected>+ Add New Instructor</option>
                                <?php foreach($instructors as $inst): ?>
                                    <option value="<?= $inst->id ?>"><?= $inst->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="newInstructorSection">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2">Instructor Name *</label>
                                    <input type="text" name="instructor_name" class="form-control" placeholder="Enter Full Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 mb-2">Experience (Years)</label>
                                    <input type="text" name="experience" class="form-control" placeholder="e.g. 5 Years">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-600 mb-2">KYC / Certification Document</label>
                                    <input type="file" name="instructor_kyc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: Media & Submit -->
        <div class="form-body step-content d-none" id="step4">
            <h3 class="form-section-title">Final Submission</h3>
            
            <div class="form-group">
                <label class="form-label">Class Photos (3-5 Photos) <span>*</span></label>
                <div class="upload-area border-dashed rounded-lg p-5 text-center bg-light" id="dropZone" style="cursor: pointer; border: 2px dashed #D1D5DB; border-radius: 12px;" onclick="$('#imageInput').click()">
                    <i class="bi bi-images fs-1 text-muted opacity-50 mb-3 d-block"></i>
                    <p class="mb-1 fw-bold text-dark">Upload class photos</p>
                    <p class="small text-muted mb-0">Drag & drop or click to upload (3-5 required)</p>
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">
                </div>
                <div id="imagePreview" class="d-flex flex-wrap gap-3 mt-3"></div>
            </div>

            <div class="form-group mt-5 p-3 rounded-3" style="background: #F9FAFB;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                    <label class="form-check-label small fw-600" for="termsCheck" style="color: #4B5563;">
                        I confirm the information is accurate and I agree to the Learn Next Door Provider Terms of Service and Refund Policy.
                    </label>
                </div>
            </div>
        </div>

        <div class="form-footer mt-4">
            <button type="button" class="btn-back d-flex align-items-center gap-2" id="prevBtn" style="visibility: hidden;">
                <i class="bi bi-chevron-left"></i>
                Back
            </button>
            <div class="ms-auto d-flex gap-3">
                <button type="button" class="btn btn-primary-provider px-4 py-2 d-flex align-items-center gap-2 shadow-sm" id="nextBtn">
                    Next
                    <i class="bi bi-chevron-right"></i>
                </button>
                <button type="submit" class="btn btn-primary-provider px-4 py-2 d-none align-items-center gap-2 shadow-sm" id="submitBtn">
                    Submit Listing
                    <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </div>

    </form>
</div>

<!-- Modal for Rules -->
<div class="modal fade" id="rulesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-body p-5 text-center">
                <i class="bi bi-info-circle text-primary display-4 mb-4 d-block"></i>
                <h3 class="fw-bold mb-3">Listing Guidelines</h3>
                <p class="text-muted mb-4">Please ensure all details are accurate. Misleading information may lead to account suspension.</p>
                <button type="button" class="btn-next w-100" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    min-height: 44px;
    padding: 2px;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--provider-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: var(--provider-primary);
    color: white;
    border: none;
    border-radius: 4px;
    padding: 1px 8px;
    font-size: 0.875rem;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 5px;
}
.preview-thumb {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--provider-border);
}
.upload-area {
    transition: all 0.2s;
}
.upload-area:hover {
    background-color: #F3F4F6 !important;
    border-color: var(--provider-primary) !important;
}

/* Batch Card Refinements */
.batch-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.batch-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.05) !important;
}
.btn-batch-day {
    border-radius: 8px;
    padding: 0.5rem 0.85rem !important;
    font-size: 0.8rem !important;
    font-weight: 500;
    color: #4B5563;
    border-color: #E5E7EB;
    background: white;
}
.btn-check:checked + .btn-batch-day {
    background-color: #F3F4F6 !important;
    border-color: #9CA3AF !important;
    color: #111827 !important;
    font-weight: 700;
}
.fw-600 { font-weight: 600 !important; }
.fw-700 { font-weight: 700 !important; }

.multi-step-card {
    max-width: 100%;
    margin: 0 auto;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

@media (min-width: 992px) {
    .multi-step-card { max-width: 900px !important; }
}

.stepper-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #F3F4F6;
}

@media (max-width: 768px) {
    .multi-step-card { border-radius: 0; border: none; box-shadow: none; }
    .stepper-header { padding: 1rem; }
    .form-body { padding: 1.5rem 1rem; }
    .form-footer { padding: 1rem; }
}

.stepper-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
    display: block;
    margin-bottom: 0.25rem;
}

.stepper-count {
    font-size: 0.875rem;
    color: #6B7280;
    font-weight: 500;
}

.form-body {
    padding: 2rem;
}

.form-section-title {
    font-size: 1.125rem;
    color: #111827;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control, .form-select {
    font-size: 0.9375rem;
    font-weight: 400;
    color: #111827;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    padding: 0.625rem 0.875rem;
    background-color: #FFFFFF;
    transition: all 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    outline: none;
}

.form-control::placeholder {
    color: #9CA3AF;
}

.course-card {
    border: 1px solid #F3F4F6 !important;
    border-radius: 12px !important;
    background-color: #FFFFFF !important;
}

.btn-primary-provider {
    background-color: #5548ea !important; /* Slightly more vibrant blue/purple from screenshot */
    border: none !important;
    font-weight: 600;
    font-size: 0.9375rem;
    padding: 0.75rem 2.5rem;
    border-radius: 5px; /* Tighter rounding as per latest request */
    color: white !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary-provider:hover {
    background-color: #4338CA !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}

.btn-primary-provider:active {
    transform: translateY(0);
}

.btn-back {
    font-size: 0.9375rem;
    color: #4B5563;
    font-weight: 600;
    background: transparent;
    border: none;
    transition: color 0.15s;
}

.btn-back:hover {
    color: #111827;
}

.form-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #F3F4F6;
    margin-top: 1rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= env('GOOGLE_MAP_API_KEY') ?>&libraries=places"></script>
<script>
$(function() {
    let currentStep = 1;
    const totalSteps = 4;
    const titles = ["Create New Class", "Create New Class", "Create New Class", "Create New Class"];

    function updateStepper() {
        $('#stepTitle').text(titles[currentStep - 1]);
        $('#stepCount').text(`Step ${currentStep} of ${totalSteps}`);
        $('#progressBar').css('width', (currentStep / totalSteps) * 100 + '%');

        $('.step-content').addClass('d-none');
        $(`#step${currentStep}`).removeClass('d-none');

        if (currentStep === 1) {
            $('#prevBtn').css('visibility', 'hidden');
        } else {
            $('#prevBtn').css('visibility', 'visible');
        }

        if (currentStep === totalSteps) {
            $('#nextBtn').addClass('d-none');
            $('#submitBtn').removeClass('d-none');
        } else {
            $('#nextBtn').removeClass('d-none');
            $('#submitBtn').addClass('d-none');
        }
    }

    $('#nextBtn').on('click', function() {
        // Simple validation
        const $current = $(`#step${currentStep}`);
        const inputs = $current.find('input[required], select[required], textarea[required]');
        let valid = true;
        
        inputs.each(function() {
            if (!this.checkValidity()) {
                this.reportValidity();
                valid = false;
                return false;
            }
        });

        if (valid && currentStep < totalSteps) {
            currentStep++;
            updateStepper();
            window.scrollTo(0, 0);
        }
    });

    $('#prevBtn').on('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepper();
            window.scrollTo(0, 0);
        }
    });

    // Class Type Card Selection
    $('.type-card').on('click', function() {
        $('.type-card').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true);
        
        const type = $(this).data('type');
        handleTypeChange(type);
        updateStepper(); // Refresh total steps
    });

    function handleTypeChange(type) {
        $('#sectionRegular, #sectionWorkshop, #sectionCourse').addClass('d-none');
        if (type === 'regular') {
            $('#sectionRegular').removeClass('d-none');
        } else if (type === 'workshop') {
            $('#sectionWorkshop').removeClass('d-none');
        } else if (type === 'course') {
            $('#sectionCourse').removeClass('d-none');
        }
    }

    // Initialize Select2 for multi-select subcategories
    const $subcategorySelect = $('#subcategorySelect').select2({
        theme: 'default',
        width: '100%',
        placeholder: "Select subcategories"
    });

    // Category / Subcategory Logic
    $('#categorySelect').on('change', async function() {
        const catId = this.value;
        if (!catId) {
            $subcategorySelect.val(null).trigger('change').prop('disabled', true);
            return;
        }

        try {
            const res = await fetch(`<?= base_url('provider/api/subcategories') ?>?category_id=${catId}`);
            if (!res.ok) throw new Error('API Error');
            const data = await res.json();
            
            $subcategorySelect.empty().prop('disabled', false);
            data.forEach(sub => {
                $subcategorySelect.append(new Option(sub.name, sub.id));
            });
            $subcategorySelect.trigger('change');
        } catch (e) {
            console.error('Subcategory load error:', e);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load subcategories.' });
        }
    });

    // Sync title with name
    $('#instituteName').on('input', function() {
        $('#classTitle').val(this.value);
    });

    // Image Upload Preview Logic
    $('#imageInput').on('change', function() {
        const files = this.files;
        $('#imagePreview').empty();
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = (e) => {
                $('#imagePreview').append(`<img src="${e.target.result}" class="preview-thumb">`);
            };
            reader.readAsDataURL(files[i]);
        }
    });

    // Initialize map autocomplete
    if (typeof google !== 'undefined') {
        const input = document.getElementById('locationInput');
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                $('#lat').val(place.geometry.location.lat());
                $('#lng').val(place.geometry.location.lng());
                $('#full_address').val(place.formatted_address);
            }
        });
    }

    // Form Submit
    $('#createListingForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const btn = $('#submitBtn');
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Submitting...');

        const formData = new FormData(this);
        $.ajax({
            url: '<?= base_url('provider/listings/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Your class has been submitted and is under review.',
                        confirmButtonText: 'View My Listings'
                    }).then(() => {
                        window.location.href = '<?= base_url('provider/listings') ?>';
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops', text: res.message });
                    btn.prop('disabled', false).text('Submit Listing');
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                btn.prop('disabled', false).text('Submit Listing');
            }
        });
    });

    // ── Batch Management ──────────────────────────────────────────
    let batchCount = 0;
    const instructorsConfig = <?= json_encode($instructors) ?>;

    // Initialize global pickers
    const initPickers = (container = 'body') => {
        $(`${container} .timepicker`).flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            altInput: true,
            altFormat: "h:i K"
        });
        $(`${container} .datepicker`).flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y"
        });
    };

    function addBatch() {
        batchCount++;
        const id = `batch_${batchCount}`;
        const index = batchCount - 1;

        let instructorOptions = '<option value="new" selected>+ Add New Instructor</option>';
        instructorsConfig.forEach(inst => {
            instructorOptions += `<option value="${inst.id}">${inst.name}</option>`;
        });

        const html = `
            <div class="batch-card bg-white border border-light-subtle rounded-4 p-4 mb-4 shadow-sm position-relative" id="${id}">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.1rem;">Batch ${batchCount}</h5>
                    ${batchCount > 1 ? `<button type="button" class="btn btn-link text-danger p-0 text-decoration-none shadow-none" onclick="removeBatch('${id}')"><i class="bi bi-trash me-1"></i> Remove</button>` : ''}
                </div>
                
                <div class="row g-4">
                    <!-- Batch Name & Days -->
                    <div class="col-md-7">
                        <label class="form-label fw-600 mb-2">Batch Name</label>
                        <input type="text" name="batches[${index}][batch_name]" class="form-control" placeholder="E.g., Morning Beginners" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-600 mb-2">Days</label>
                        <div class="d-flex flex-wrap gap-2">
                            ${['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].map(day => `
                                <input type="checkbox" class="btn-check" id="${id}_${day}" name="batches[${index}][days][]" value="${day === 'Thu' ? 'Th' : (day === 'Sat' ? 'Sa' : (day === 'Sun' ? 'S' : day[0]))}" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-batch-day px-2 py-1" for="${id}_${day}">${day}</label>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Start Date & Times -->
                    <div class="col-md-4">
                        <label class="form-label fw-600 mb-2">Start Date</label>
                        <input type="text" name="batches[${index}][batch_start_date]" class="form-control datepicker" placeholder="dd-mm-yyyy" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 mb-2">Start Time</label>
                        <input type="text" name="batches[${index}][start_time]" class="form-control timepicker" placeholder="09:00 AM" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 mb-2">End Time</label>
                        <input type="text" name="batches[${index}][end_time]" class="form-control timepicker" placeholder="10:30 AM" required>
                    </div>

                    <!-- Fee & Size -->
                    <div class="col-md-6">
                        <label class="form-label fw-600 mb-2">Price/4 Weeks (₹)</label>
                        <input type="number" name="batches[${index}][price]" class="form-control" placeholder="2000" required>
                        <input type="hidden" name="batches[${index}][price_type]" value="monthly">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600 mb-2">Max Students</label>
                        <input type="number" name="batches[${index}][batch_size]" class="form-control" placeholder="20" required>
                    </div>

                    <!-- Instructor -->
                    <div class="col-12">
                        <label class="form-label fw-600 mb-2">Instructor</label>
                        <select name="batches[${index}][instructor_option]" class="form-select instructor-selector" onchange="toggleBatchInstructor(this, '${id}')" required>
                            ${instructorOptions}
                        </select>
                    </div>

                    <!-- New Instructor Fields (Visible by default for 'new') -->
                    <div class="col-12" id="${id}_new_instructor_wrap">
                         <div class="row g-3 p-3 mt-1 rounded-3 bg-light border">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Instructor Name *</label>
                                <input type="text" name="batches[${index}][instructor_name]" class="form-control form-control-sm" placeholder="Enter Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Experience (Years)</label>
                                <input type="text" name="batches[${index}][experience]" class="form-control form-control-sm" placeholder="e.g. 5 Years">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold mb-1">KYC / Certification Document</label>
                                <input type="file" name="batch_instructor_kyc[${index}]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text small opacity-75">Upload ID proof or certification for verification.</div>
                            </div>
                         </div>
                    </div>

                    <!-- Free Trial -->
                    <div class="col-12">
                        <div class="form-check d-flex align-items-center gap-2 mb-2">
                            <input class="form-check-input mt-0 free-trial-check" type="checkbox" name="batches[${index}][free_trial]" value="1" id="${id}_free_trial" onchange="toggleFreeTrial(this, '${id}')">
                            <label class="form-check-label fw-600" for="${id}_free_trial" style="font-size: 0.95rem;">
                                Offer Free Trial
                            </label>
                        </div>
                        
                        <!-- Free Trial Details (Hidden by default) -->
                        <div class="mt-2 d-none" id="${id}_trial_details_wrap">
                            <div class="p-3 rounded-3 border bg-light-subtle">
                                <label class="form-label small fw-bold mb-1">Number of Trial Sessions *</label>
                                <select name="batches[${index}][trial_sessions]" class="form-select form-select-sm">
                                    <option value="1">1 Session</option>
                                    <option value="2">2 Sessions</option>
                                    <option value="3">3 Sessions</option>
                                </select>
                                <div class="form-text small">Select how many trial sessions are offered for free.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#batchesContainer').append(html);
        initPickers(`#${id}`);
    }

    window.removeBatch = function(id) {
        $(`#${id}`).remove();
    };

    window.toggleBatchInstructor = function(select, batchId) {
        const wrap = $(`#${batchId}_new_instructor_wrap`);
        if (select.value === 'new') {
            wrap.removeClass('d-none');
            wrap.find('input[name*="instructor_name"]').prop('required', true);
        } else {
            wrap.addClass('d-none');
            wrap.find('input[name*="instructor_name"]').prop('required', false);
        }
    }

    window.toggleFreeTrial = function(checkbox, batchId) {
        const wrap = $(`#${batchId}_trial_details_wrap`);
        if (checkbox.checked) {
            wrap.removeClass('d-none');
            wrap.find('select, input').prop('required', true);
        } else {
            wrap.addClass('d-none');
            wrap.find('select, input').prop('required', false);
        }
    }

    window.toggleSharedInstructor = (select, context = 'workshop') => {
        const target = (context === 'course') ? '#courseInstructorNew' : '#workshopInstructorNew';
        $(target).toggleClass('d-none', select.value !== 'new');
    };

    window.toggleEarlyBird = function(type) {
        const check = $(`#earlyBirdCheck${type}`);
        $(`#earlyBirdFields${type}`).toggleClass('d-none', !check.is(':checked'));
    };

    $('#addBatchBtn').on('click', () => addBatch());

    // Initialize first batch and global pickers on load
    addBatch();
    initPickers();

    // Show rules on load if first time
    if (!localStorage.getItem('rules_seen')) {
        $('#rulesModal').modal('show');
        localStorage.setItem('rules_seen', 'true');
    }
});
</script>
<?= $this->endSection() ?>
