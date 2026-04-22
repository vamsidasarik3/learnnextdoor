<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>
<div class="multi-step-card mt-4">
    <div class="stepper-header">
        <div class="stepper-info">
            <span class="stepper-title" id="stepTitle">Edit Your Class</span>
            <span class="stepper-count" id="stepCount">Step 1 of 4</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progressBar" style="width: 25%;"></div>
        </div>
    </div>

    <form id="editListingForm" class="create-listing-form" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="<?= $listing->id ?>">
        
        <!-- STEP 1: Class Type & Category -->
        <div class="form-body step-content" id="step1">
            <h3 class="form-section-title">Class Type</h3>
            <div class="type-card-grid">
                <div class="type-card <?= $listing->type == 'regular' ? 'active' : '' ?>" data-type="regular">
                    <div class="type-title">Regular Class</div>
                    <div class="type-subtitle">Ongoing recurring batches</div>
                    <input type="radio" name="type" value="regular" class="d-none" <?= $listing->type == 'regular' ? 'checked' : '' ?>>
                </div>
                <div class="type-card <?= $listing->type == 'course' ? 'active' : '' ?>" data-type="course">
                    <div class="type-title">Course</div>
                    <div class="type-subtitle">Fixed-duration program</div>
                    <input type="radio" name="type" value="course" class="d-none" <?= $listing->type == 'course' ? 'checked' : '' ?>>
                </div>
                <div class="type-card <?= $listing->type == 'workshop' ? 'active' : '' ?>" data-type="workshop">
                    <div class="type-title">Workshop</div>
                    <div class="type-subtitle">One-time intensive event</div>
                    <input type="radio" name="type" value="workshop" class="d-none" <?= $listing->type == 'workshop' ? 'checked' : '' ?>>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Category <span>*</span></label>
                <select name="category_id" id="categorySelect" class="form-select" required>
                    <?php foreach($categories as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $listing->category_id == $id ? 'selected' : '' ?>><?= esc($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Subcategories <span>*</span></label>
                <select name="subcategory_ids[]" id="subcategorySelect" class="form-select select2-multi" multiple required>
                </select>
                <div class="form-text text-muted small mt-1">Select at least one relevant subcategory.</div>
            </div>
        </div>

        <!-- STEP 2: Institute Details -->
        <div class="form-body step-content d-none" id="step2">
            <h3 class="form-section-title">Institute / Class Details</h3>
            
            <div class="form-group">
                <label class="form-label">Institute / Class Name <span>*</span></label>
                <input type="text" name="institute_name" id="instituteName" class="form-control" value="<?= esc($listing->institute_name ?: $listing->title) ?>" required>
                <input type="hidden" name="title" id="classTitle" value="<?= esc($listing->title) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">About / Description <span>*</span></label>
                <textarea name="description" id="description" class="form-control" rows="5" required minlength="50"><?= esc($listing->description) ?></textarea>
                <div class="form-text text-muted small mt-1">Minimum 50 characters required.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Address (Manual Entry) <span>*</span></label>
                <input type="text" name="manual_address" class="form-control" value="<?= esc($listing->manual_address ?: $listing->address) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nearest Landmark (Search) <span>*</span></label>
                <input type="text" id="locationInput" name="formatted_address" class="form-control" value="<?= esc($listing->formatted_address ?: $listing->address) ?>" required autocomplete="off">
                
                <!-- Hidden Location Data -->
                <input type="hidden" name="latitude" id="lat" value="<?= $listing->latitude ?>">
                <input type="hidden" name="longitude" id="lng" value="<?= $listing->longitude ?>">
                <input type="hidden" name="city" id="city" value="<?= $listing->city ?>">
                <input type="hidden" name="locality" id="locality" value="<?= $listing->locality ?>">
                <input type="hidden" name="pincode" id="pincode" value="<?= $listing->pincode ?>">
                <input type="hidden" name="address" id="full_address" value="<?= esc($listing->address) ?>">
            </div>
        </div>

        <!-- STEP 3: Details -->
        <div class="form-body step-content d-none" id="step3">
            
            <div id="sectionRegular">
                <div id="batchesContainer">
                    <!-- Batches will be synced from JS -->
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary-provider rounded-pill px-4 shadow-sm" id="addBatchBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Batch
                    </button>
                </div>
            </div>

            <div id="sectionWorkshop" class="d-none pt-2">
                <div class="workshop-details-wrap mb-4">
                    <h5 class="fw-bold mb-3 text-dark bg-light p-2 rounded" style="display: inline-block;">Workshop Specifics</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Registration Deadline <span>*</span></label>
                            <input type="text" name="workshop[registration_end_date]" class="form-control datepicker" value="<?= $listing->registration_end_date ?>" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Workshop Date <span>*</span></label>
                            <input type="text" name="workshop[start_date]" class="form-control datepicker" value="<?= $listing->start_date ?>" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Start Time <span>*</span></label>
                            <input type="text" name="workshop[from_time]" class="form-control timepicker" value="<?= $listing->class_time ?>" placeholder="--:-- --">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">End Time <span>*</span></label>
                            <input type="text" name="workshop[to_time]" class="form-control timepicker" value="<?= $listing->class_end_time ?>" placeholder="--:-- --">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Workshop Fee (₹) <span>*</span></label>
                            <input type="number" name="workshop[price]" class="form-control" value="<?= $listing->price ?>" placeholder="5000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Max Students <span>*</span></label>
                            <input type="number" name="workshop[batch_size]" class="form-control" value="<?= $listing->batch_size ?>" placeholder="50">
                        </div>
                        
                        <!-- Instructor for Workshop -->
                        <div class="col-12">
                            <label class="form-label fw-600 mb-2">Instructor</label>
                            <select name="instructor_option" class="form-select" onchange="toggleSharedInstructor(this, 'workshop')">
                                <option value="new" <?= !$listing->instructor_id ? 'selected' : '' ?>>+ Add New Instructor</option>
                                <?php foreach($instructors as $inst): ?>
                                    <option value="<?= $inst->id ?>" <?= $listing->instructor_id == $inst->id ? 'selected' : '' ?>><?= $inst->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Early Bird for Workshop -->
                        <div class="col-12">
                            <div class="form-check d-flex align-items-center gap-2 mb-3">
                                <input class="form-check-input mt-0" type="checkbox" id="earlyBirdCheckW" onchange="toggleEarlyBird('W')" <?= $listing->early_bird_price ? 'checked' : '' ?>>
                                <label class="form-check-label fw-600" for="earlyBirdCheckW">Enable Early Bird Pricing</label>
                            </div>
                            <div id="earlyBirdFieldsW" class="<?= $listing->early_bird_price ? '' : 'd-none' ?> p-3 border rounded-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Price (₹)</label>
                                        <input type="number" name="workshop[early_bird_price]" class="form-control form-control-sm" value="<?= $listing->early_bird_price ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Ends</label>
                                        <input type="text" name="workshop[early_bird_end_date]" class="form-control form-control-sm datepicker" value="<?= $listing->early_bird_date ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Slots</label>
                                        <input type="number" name="workshop[early_bird_count]" class="form-control form-control-sm" value="<?= $listing->early_bird_slots ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="workshopInstructorNew" class="<?= !$listing->instructor_id ? '' : 'd-none' ?> mb-4">
                    <h6 class="fw-bold mb-3 text-muted">New Instructor Details</h6>
                    <div class="row g-3 p-3 border rounded-3 bg-light">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Instructor Name *</label>
                            <input type="text" name="instructor_name" class="form-control form-control-sm" value="<?= esc($listing->instructor_name) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Experience (Years)</label>
                            <input type="text" name="experience" class="form-control form-control-sm" value="<?= esc($listing->experience) ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">KYC / Certification Document</label>
                            <input type="file" name="instructor_kyc" class="form-control form-control-sm">
                            <?php if($listing->instructor_kyc_doc): ?>
                                <div class="mt-1 small"><a href="<?= base_url('uploads/kyc/'.$listing->instructor_kyc_doc) ?>" target="_blank" class="text-primary"><i class="bi bi-file-earmark-check me-1"></i> View Current KYC</a></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sectionCourse" class="d-none pt-2">
                <div class="course-details-wrap mb-5">
                    <h5 class="fw-bold mb-3 text-dark bg-light p-2 rounded" style="display: inline-block;">Course Specifics</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course Duration <span>*</span></label>
                            <div class="input-group">
                                <input type="number" name="course[duration_number]" class="form-control" value="<?= $listing->course_duration ?>" placeholder="10">
                                <select name="course[duration_type]" class="form-select" style="max-width: 120px;">
                                    <option value="weeks" <?= $listing->course_duration_type == 'weeks' ? 'selected' : '' ?>>Weeks</option>
                                    <option value="months" <?= $listing->course_duration_type == 'months' ? 'selected' : '' ?>>Months</option>
                                    <option value="days" <?= $listing->course_duration_type == 'days' ? 'selected' : '' ?>>Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Registration Deadline <span>*</span></label>
                            <input type="text" name="course[registration_end_date]" class="form-control datepicker" value="<?= $listing->registration_end_date ?>" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course Start Date <span>*</span></label>
                            <input type="text" name="course[start_date]" class="form-control datepicker" value="<?= $listing->start_date ?>" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course End Date <span>*</span></label>
                            <input type="text" name="course[end_date]" class="form-control datepicker" value="<?= $listing->end_date ?>" placeholder="mm/dd/yyyy">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Course Fee (₹) <span>*</span></label>
                            <input type="number" name="course[price]" class="form-control" value="<?= $listing->price ?>" placeholder="15000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Max Students <span>*</span></label>
                            <input type="number" name="course[batch_size]" class="form-control" value="<?= $listing->batch_size ?>" placeholder="30">
                        </div>
                        
                        <!-- Instructor for Course -->
                        <div class="col-12">
                            <label class="form-label fw-600 mb-2">Instructor</label>
                            <select name="course_instructor_option" class="form-select" onchange="toggleSharedInstructor(this, 'course')">
                                <option value="new" <?= !$listing->instructor_id ? 'selected' : '' ?>>+ Add New Instructor</option>
                                <?php foreach($instructors as $inst): ?>
                                    <option value="<?= $inst->id ?>" <?= $listing->instructor_id == $inst->id ? 'selected' : '' ?>><?= $inst->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Early Bird for Course -->
                        <div class="col-12">
                            <div class="form-check d-flex align-items-center gap-2 mb-3">
                                <input class="form-check-input mt-0" type="checkbox" id="earlyBirdCheckC" onchange="toggleEarlyBird('C')" <?= $listing->early_bird_price ? 'checked' : '' ?>>
                                <label class="form-check-label fw-600" for="earlyBirdCheckC">Enable Early Bird Pricing</label>
                            </div>
                            <div id="earlyBirdFieldsC" class="<?= $listing->early_bird_price ? '' : 'd-none' ?> p-3 border rounded-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Price (₹)</label>
                                        <input type="number" name="course[early_bird_price]" class="form-control form-control-sm" value="<?= $listing->early_bird_price ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Ends</label>
                                        <input type="text" name="course[early_bird_end_date]" class="form-control form-control-sm datepicker" value="<?= $listing->early_bird_date ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Early Bird Slots</label>
                                        <input type="number" name="course[early_bird_count]" class="form-control form-control-sm" value="<?= $listing->early_bird_slots ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Daily Start Time <span>*</span></label>
                            <input type="text" name="course[from_time]" class="form-control timepicker" value="<?= $listing->class_time ?>" placeholder="--:-- --">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 mb-2">Daily End Time <span>*</span></label>
                            <input type="text" name="course[to_time]" class="form-control timepicker" value="<?= $listing->class_end_time ?>" placeholder="--:-- --">
                        </div>
                    </div>
                </div>

                <div id="courseInstructorNew" class="<?= !$listing->instructor_id ? '' : 'd-none' ?> mb-4">
                    <h6 class="fw-bold mb-3 text-muted">New Instructor Details</h6>
                    <div class="row g-3 p-3 border rounded-3 bg-light">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Instructor Name *</label>
                            <input type="text" name="course_instructor_name" class="form-control form-control-sm" value="<?= esc($listing->instructor_name) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Experience (Years)</label>
                            <input type="text" name="course_experience" class="form-control form-control-sm" value="<?= esc($listing->experience) ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold mb-1">KYC / Certification Document</label>
                            <input type="file" name="course_instructor_kyc" class="form-control form-control-sm">
                            <?php if($listing->instructor_kyc_doc): ?>
                                <div class="mt-1 small"><a href="<?= base_url('uploads/kyc/'.$listing->instructor_kyc_doc) ?>" target="_blank" class="text-primary"><i class="bi bi-file-earmark-check me-1"></i> View Current KYC</a></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shared Instructor Section -->
            <div id="sharedInstructorSection" class="mt-4 pt-4 border-top d-none">
                <h4 class="fw-bold mb-4">Instructor Information</h4>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-600 mb-2">Instructor</label>
                        <select name="instructor_option" id="instructorSelectShared" class="form-select" onchange="toggleSharedInstructor(this)">
                            <option value="new" <?= !$listing->instructor_id ? 'selected' : '' ?>>+ Add New Instructor</option>
                            <?php foreach($instructors as $inst): ?>
                                <option value="<?= $inst->id ?>" <?= $listing->instructor_id == $inst->id ? 'selected' : '' ?>><?= $inst->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="newInstructorSection" class="<?= $listing->instructor_id ? 'd-none' : '' ?>">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-600 mb-2">Instructor Name *</label>
                                <input type="text" name="instructor_name" class="form-control" value="<?= esc($listing->instructor_name) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 mb-2">Experience (Years)</label>
                                <input type="text" name="experience" class="form-control" value="<?= esc($listing->experience) ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-600 mb-2">KYC / Certification Document</label>
                                <input type="file" name="instructor_kyc" class="form-control">
                                <?php if($listing->instructor_kyc_doc): ?>
                                    <div class="mt-1 small"><a href="<?= base_url('uploads/kyc/'.$listing->instructor_kyc_doc) ?>" target="_blank" class="text-primary"><i class="bi bi-file-earmark-check me-1"></i> View Current KYC</a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: Media -->
        <div class="form-body step-content d-none" id="step4">
            <h3 class="form-section-title">Final Submission</h3>
            
            <div class="form-group mb-5">
                <label class="form-label">Existing Class Photos</label>
                <div class="d-flex flex-wrap gap-3">
                    <?php if(empty($images)): ?>
                        <div class="text-muted small">No photos uploaded yet.</div>
                    <?php endif; ?>
                    <?php foreach($images as $img): ?>
                        <div class="position-relative" id="img_wrap_<?= $img->id ?>">
                            <img src="<?= base_url($img->image_path) ?>" class="preview-thumb">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle" onclick="deleteImage(<?= $img->id ?>)" style="width:24px;height:24px;padding:0;line-height:24px;"><i class="bi bi-x"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Add More Photos (3-5 Photos) <span>*</span></label>
                <div class="upload-area border-dashed rounded-lg p-5 text-center bg-light" id="dropZone" style="cursor: pointer; border: 2px dashed #D1D5DB; border-radius: 12px;" onclick="$('#imageInput').click()">
                    <i class="bi bi-images fs-1 text-muted opacity-50 mb-3 d-block"></i>
                    <p class="mb-1 fw-bold text-dark">Upload new photos</p>
                    <p class="small text-muted mb-0">Drag & drop or click to upload</p>
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">
                </div>
                <div id="imagePreview" class="d-flex flex-wrap gap-3 mt-3"></div>
            </div>

            <div class="form-group mt-5 p-3 rounded-3" style="background: #F9FAFB;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="termsCheck" checked required>
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
                    Save Changes
                    <i class="bi bi-check-lg"></i>
                </button>
            </div>
        </div>

    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* CSS copied from create_listing.php to match design */
    .multi-step-card { max-width: 100%; margin: 0 auto; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    @media (min-width: 992px) { .multi-step-card { max-width: 900px !important; } }

    .stepper-header { padding: 1.5rem 2rem; border-bottom: 1px solid #F3F4F6; }
    @media (max-width: 768px) {
        .multi-step-card { border-radius: 0; border: none; box-shadow: none; }
        .stepper-header { padding: 1rem; }
        .form-body { padding: 1.5rem 1rem; }
        .form-footer { padding: 1rem; flex-wrap: wrap; }
    }
    .stepper-title { font-size: 1.25rem; font-weight: 700; color: #111827; display: block; margin-bottom: 0.25rem; }
    .stepper-count { font-size: 0.875rem; color: #6B7280; font-weight: 500; }
    .progress-bar-container { height: 4px; background: #F3F4F6; border-radius: 2px; margin-top: 1rem; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: #4F46E5; transition: width 0.3s ease; }
    .form-body { padding: 2rem; }
    .form-section-title { font-size: 1.125rem; color: #111827; font-weight: 700; margin-bottom: 1.5rem; }
    .type-card-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .type-card { border: 1px solid #E5E7EB; border-radius: 12px; padding: 1.5rem; cursor: pointer; transition: all 0.2s; }
    .type-card.active { border-color: #4F46E5; background: #F5F7FF; border-width: 2px; }
    .type-title { font-weight: 700; font-size: 1rem; color: #111827; }
    .type-subtitle { font-size: 0.8rem; color: #6B7280; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; display: block; }
    .form-label span { color: #DC2626; }
    .btn-primary-provider { background-color: #5548ea !important; border: none !important; font-weight: 600; font-size: 0.9375rem; padding: 0.75rem 2.5rem; border-radius: 5px; color: white !important; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-primary-provider:hover { background-color: #4338CA !important; transform: translateY(-1px); }
    .btn-back { font-size: 0.9375rem; color: #4B5563; font-weight: 600; background: transparent; border: none; }
    .form-footer { padding: 1.5rem 2rem; border-top: 1px solid #F3F4F6; display: flex; align-items: center; }
    .preview-thumb { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #E5E7EB; }
    .select2-container--default .select2-selection--multiple { border: 1px solid #D1D5DB; border-radius: 8px; min-height: 44px; padding: 2px; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #4F46E5; color: white; border: none; border-radius: 4px; padding: 1px 8px; font-size: 0.875rem; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: white; margin-right: 5px; }
    .btn-batch-day { border-radius: 8px; padding: 0.5rem 0.85rem !important; font-size: 0.8rem !important; font-weight: 500; color: #4B5563; border-color: #E5E7EB; background: white; }
    .btn-check:checked + .btn-batch-day { background-color: #F3F4F6 !important; border-color: #9CA3AF !important; color: #111827 !important; font-weight: 700; }
    .batch-card { transition: all 0.2s; }
    .batch-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.05) !important; }
    .fw-600 { font-weight: 600 !important; }
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
    const titles = ["Edit Your Class", "Edit Your Class", "Edit Your Class", "Edit Your Class"];

    function updateStepper() {
        $('#stepTitle').text(titles[currentStep - 1]);
        $('#stepCount').text(`Step ${currentStep} of ${totalSteps}`);
        $('#progressBar').css('width', (currentStep / totalSteps) * 100 + '%');
        $('.step-content').addClass('d-none');
        $(`#step${currentStep}`).removeClass('d-none');
        $('#prevBtn').css('visibility', currentStep === 1 ? 'hidden' : 'visible');
        if (currentStep === totalSteps) {
            $('#nextBtn').addClass('d-none');
            $('#submitBtn').removeClass('d-none');
        } else {
            $('#nextBtn').removeClass('d-none');
            $('#submitBtn').addClass('d-none');
        }
    }

    $('#nextBtn').on('click', function() {
        if (currentStep < totalSteps) {
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
        handleTypeChange($(this).data('type'));
    });

    function handleTypeChange(type) {
        $('#sectionRegular, #sectionWorkshop, #sectionCourse, #sharedInstructorSection').addClass('d-none');
        if (type === 'regular') $('#sectionRegular').removeClass('d-none');
        else if (type === 'workshop') { $('#sectionWorkshop, #sharedInstructorSection').removeClass('d-none'); }
        else if (type === 'course') { $('#sectionCourse, #sharedInstructorSection').removeClass('d-none'); }
    }

    const $subcategorySelect = $('#subcategorySelect').select2({
        theme: 'default', width: '100%', placeholder: "Select subcategories"
    });

    const initialSubcategoryIds = '<?= $listing->subcategory_ids ?? "" ?>'.split(',').map(s => s.trim()).filter(id => id.length > 0);

    async function loadSubcategories(catId, selectedIds = []) {
        $subcategorySelect.empty().prop('disabled', true);
        try {
            const res = await fetch(`<?= base_url('provider/api/subcategories') ?>?category_id=${catId}`);
            const data = await res.json();
            $subcategorySelect.prop('disabled', false);
            data.forEach(sub => {
                const isSelected = selectedIds.includes(sub.id.toString());
                $subcategorySelect.append(new Option(sub.name, sub.id, isSelected, isSelected));
            });
            $subcategorySelect.trigger('change');
        } catch (e) { console.error('Subcategory load error:', e); }
    }

    $('#categorySelect').on('change', function() { loadSubcategories(this.value); });
    if ($('#categorySelect').val()) loadSubcategories($('#categorySelect').val(), initialSubcategoryIds);

    $('#instituteName').on('input', function() { $('#classTitle').val(this.value); });
    handleTypeChange('<?= $listing->type ?>');
    updateStepper();

    // Image Upload Preview Logic
    $('#imageInput').on('change', function() {
        $('#imagePreview').empty();
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => $('#imagePreview').append(`<img src="${e.target.result}" class="preview-thumb">`);
            reader.readAsDataURL(file);
        });
    });

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

    $('#editListingForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');
        const formData = new FormData(this);
        $.ajax({
            url: '<?= base_url('provider/listings/update/'.$listing->id) ?>',
            type: 'POST', data: formData, processData: false, contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Updated!', text: 'Listing has been updated successfully.' }).then(() => {
                        window.location.href = '<?= base_url('provider/listings') ?>';
                    });
                } else { Swal.fire({ icon: 'error', title: 'Oops', text: res.message }); btn.prop('disabled', false).text('Save Changes'); }
            },
            error: function() { Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' }); btn.prop('disabled', false).text('Save Changes'); }
        });
    });

    // ── Batch Management Logic sync from create_listing ────────────────────
    let batchCount = 0;
    const instructorsConfig = <?= json_encode($instructors) ?>;
    const existingBatches = <?= json_encode($listing->batches ?? []) ?>;

    const initPickers = (container = 'body') => {
        $(`${container} .timepicker`).flatpickr({ enableTime: true, noCalendar: true, dateFormat: "H:i", altInput: true, altFormat: "h:i K" });
        $(`${container} .datepicker`).flatpickr({ dateFormat: "Y-m-d", altInput: true, altFormat: "d-m-Y" });
    };

    function addBatch(data = null) {
        batchCount++;
        const id = `batch_${batchCount}`;
        const index = batchCount - 1;
        let instructorOptions = '<option value="new" selected>+ Add New Instructor</option>';
        instructorsConfig.forEach(inst => {
            const selected = data && data.instructor_id == inst.id ? 'selected' : '';
            instructorOptions += `<option value="${inst.id}" ${selected}>${inst.name}</option>`;
        });

        const batchDays = data && data.days ? (Array.isArray(data.days) ? data.days : data.days.split(',')) : [];
        const dayCheck = (day) => batchDays.includes(day) ? 'checked' : '';

        const html = `
            <div class="batch-card bg-white border border-light-subtle rounded-4 p-4 mb-4 shadow-sm position-relative" id="${id}">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-dark">Batch ${batchCount}</h5>
                    ${batchCount > 1 ? `<button type="button" class="btn btn-link text-danger p-0 text-decoration-none shadow-none" onclick="removeBatch('${id}')"><i class="bi bi-trash me-1"></i> Remove</button>` : ''}
                </div>
                <div class="row g-4">
                    <div class="col-md-7">
                        <label class="form-label fw-600 mb-2">Batch Name</label>
                        <input type="text" name="batches[${index}][batch_name]" class="form-control" value="${data ? data.name || data.batch_name || '' : ''}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-600 mb-2">Days</label>
                        <div class="d-flex flex-wrap gap-2">
                            ${['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].map(day => {
                                const val = day === 'Thu' ? 'Th' : (day === 'Sat' ? 'Sa' : (day === 'Sun' ? 'S' : day[0]));
                                return `
                                <input type="checkbox" class="btn-check" id="${id}_${day}" name="batches[${index}][days][]" value="${val}" ${dayCheck(val)} autocomplete="off">
                                <label class="btn btn-outline-secondary btn-batch-day px-2 py-1" for="${id}_${day}">${day}</label>
                            `}).join('')}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 mb-2">Start Date</label>
                        <input type="text" name="batches[${index}][batch_start_date]" class="form-control datepicker" value="${data ? data.batch_start_date || '' : ''}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 mb-2">Start Time</label>
                        <input type="text" name="batches[${index}][start_time]" class="form-control timepicker" value="${data ? data.from_time || data.start_time || '' : ''}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 mb-2">End Time</label>
                        <input type="text" name="batches[${index}][end_time]" class="form-control timepicker" value="${data ? data.to_time || data.end_time || '' : ''}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600 mb-2">Price/4 Weeks (₹)</label>
                        <input type="number" name="batches[${index}][price]" class="form-control" value="${data ? data.price || '' : ''}" required>
                        <input type="hidden" name="batches[${index}][price_type]" value="monthly">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600 mb-2">Max Students</label>
                        <input type="number" name="batches[${index}][batch_size]" class="form-control" value="${data ? data.batch_size || '' : ''}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600 mb-2">Instructor</label>
                        <select name="batches[${index}][instructor_option]" class="form-select" onchange="toggleBatchInstructor(this, '${id}')" required>
                            ${instructorOptions}
                        </select>
                    </div>
                    <div class="col-12 ${data && data.instructor_id ? 'd-none' : ''}" id="${id}_new_instructor_wrap">
                         <div class="row g-3 p-3 mt-1 rounded-3 bg-light border">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Instructor Name *</label>
                                <input type="text" name="batches[${index}][instructor_name]" class="form-control form-control-sm" value="${data ? data.instructor_name || '' : ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Experience (Years)</label>
                                <input type="text" name="batches[${index}][experience]" class="form-control form-control-sm" value="${data ? data.experience || '' : ''}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold mb-1">KYC / Certification Document</label>
                                <input type="file" name="batch_instructor_kyc[${index}]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text small opacity-75">Upload ID proof or certification for verification.</div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        `;
        $('#batchesContainer').append(html);
        initPickers(`#${id}`);
    }

    window.removeBatch = (id) => $(`#${id}`).remove();
    window.toggleBatchInstructor = (select, batchId) => {
        const wrap = $(`#${batchId}_new_instructor_wrap`);
        wrap.toggleClass('d-none', select.value !== 'new');
    };
    window.toggleSharedInstructor = (select, context = 'workshop') => {
        const target = (context === 'course') ? '#courseInstructorNew' : '#workshopInstructorNew';
        $(target).toggleClass('d-none', select.value !== 'new');
    };
    window.toggleEarlyBird = (type) => {
        const check = $(`#earlyBirdCheck${type}`);
        $(`#earlyBirdFields${type}`).toggleClass('d-none', !check.is(':checked'));
    };
    window.deleteImage = function(id, type = null) {
        const wrapId = type ? `#img_wrap_${type}_${id}` : `#img_wrap_${id}`;
        Swal.fire({
            title: 'Delete this photo?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('provider/api/listings/image/delete') ?>/${id}`, function(res) {
                    if (res.success) {
                        $(wrapId).fadeOut(300, function() { $(this).remove(); });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    };

    // Pre-populate batches if Any
    if (existingBatches.length > 0) {
        existingBatches.forEach(b => addBatch(b));
    } else if ('<?= $listing->type ?>' === 'regular') {
        addBatch();
    }

    handleTypeChange('<?= $listing->type ?>');
    updateStepper();
    initPickers();
});
</script>
<?= $this->endSection() ?>
