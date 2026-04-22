<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('css') ?>
<style>
    .stepper-title { font-size: 2rem; font-weight: 800; color: #111827; letter-spacing: -0.025em; margin-bottom: 0.25rem; }
    
    /* Main Landing Cards */
    .module-card { 
        background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; padding: 2.5rem; 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; height: 100%;
        display: flex; flex-direction: column; align-items: center; text-align: center;
    }
    .module-card:hover { 
        transform: translateY(-8px); border-color: #4F46E5; 
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .module-card .icon-box {
        width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center;
        margin-bottom: 1.5rem; transition: all 0.3s;
    }
    .module-card.holiday .icon-box { background: #EEF2FF; color: #4F46E5; }
    .module-card.cancel .icon-box { background: #FFF1F2; color: #E11D48; }
    
    .module-card:hover .icon-box { transform: scale(1.1); }
    
    /* Form Sections */
    .module-section { display: none; background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; padding: 2rem; animation: slideUp 0.4s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .compensation-card {
        border: 1px solid #E5E7EB; border-radius: 12px; padding: 1.25rem; cursor: pointer; transition: all 0.2s;
        margin-bottom: 1rem; position: relative;
    }
    .compensation-card:hover { border-color: #4F46E5; background: #F9FAFB; }
    .compensation-card.active { border-color: #4F46E5; background: #EEF2FF; }
    .compensation-card .form-check-input { margin: 0; margin-right: 1rem; flex-shrink: 0; width: 1.25rem; height: 1.25rem; cursor: pointer; }
    .compensation-card label { cursor: pointer; flex-grow: 1; margin-bottom: 0; }
    .compensation-card .d-flex { align-items: flex-start; }

    /* Flatpickr */
    .flatpickr-calendar { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #E5E7EB !important; }
    .flatpickr-day.session-day { background: #EEF2FF !important; color: #4F46E5 !important; font-weight: 800 !important; border-radius: 8px; }
    
    .batch-chip { cursor: pointer; padding: 0.5rem 1.25rem; border-radius: 8px; border: 1px solid #E5E7EB; font-size: 0.875rem; font-weight: 600; color: #4B5563; transition: all 0.2s; background: white; }
    .batch-chip.active { background: #4F46E5; color: #fff; border-color: #4F46E5; }

    .preview-panel { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.25rem; }
    
    .btn-primary-provider {
        background-color: #4F46E5 !important; border: none !important; font-weight: 600; font-size: 0.9375rem;
        padding: 0.75rem 2rem; border-radius: 8px; color: white !important; transition: all 0.2s;
    }
    .btn-primary-provider:hover { background-color: #4338CA !important; transform: translateY(-1px); }

    .alert-permanent { background: #FFF1F2; color: #9F1239; border-left: 4px solid #E11D48; border-radius: 8px; padding: 1rem 1.25rem; }
    .confirm-cancel-input { border: 2px solid #FECACA !important; color: #E11D48; font-weight: 800; text-align: center; letter-spacing: 0.1em; height: 3.5rem; font-size: 1.25rem; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="availability-header mb-5 mt-2">
    <h1 class="stepper-title mb-1">Holiday & Cancellation</h1>
    <p class="text-muted small mb-0">Manage your class availability, declare holidays, or end batches with automated parent notifications.</p>
</div>

<div class="container-fluid p-0">
    <!-- Main Landing Choice -->
    <div id="landingView" class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="module-card holiday" id="card-holiday" onclick="showModule('holiday')">
                <div class="icon-box">
                    <i class="bi bi-calendar-event fs-1"></i>
                </div>
                <h3 class="fw-bold mb-2">Declare Holiday</h3>
                <p class="text-muted mb-0">Pause specific batches or all classes for scheduled breaks.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="module-card cancel" id="card-cancel" onclick="showModule('cancel')">
                <div class="icon-box">
                    <i class="bi bi-stop-circle fs-1"></i>
                </div>
                <h3 class="fw-bold mb-2">Cancel Class / Batch</h3>
                <p class="text-muted mb-0">Permanently end a class and process pro-rata refunds.</p>
            </div>
        </div>
    </div>

    <!-- Declare Holiday Section -->
    <div id="holidaySection" class="module-section">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
            <h3 class="fw-bold m-0 text-indigo-700">Declare Holiday</h3>
            <span class="badge bg-indigo-100 text-indigo-700 ms-3 rounded-pill px-3">Step 2: Configure</span>
        </div>

        <form id="holidayForm">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Class/Batch *</label>
                        <select name="listing_id" id="holidayListingSelect" class="form-select form-select-lg" required>
                            <option value="">Choose a class...</option>
                        </select>
                    </div>

                    <div id="holidayFormFields" class="d-none">
                        <div id="batchSelectionSection" class="mb-4 d-none">
                            <label class="form-label fw-bold mb-2">Select Affected Batches</label>
                            <div id="batchCheckboxes" class="d-flex flex-wrap gap-2"></div>
                            <input type="hidden" name="batch_ids" id="selectedBatchIds">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">Select Holiday Dates *</label>
                            <input type="text" name="holiday_dates" id="holidayDatePicker" class="form-control form-control-lg" placeholder="Select dates (dd-mm-yyyy)" required readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Compensation Method *</label>
                            
                            <div class="compensation-card active" onclick="selectCompensation('extend')">
                                <div class="d-flex w-100">
                                    <input class="form-check-input" type="radio" name="action" id="actionExtend" value="extend" checked>
                                    <label class="form-check-label" for="actionExtend">
                                        <span class="fw-bold d-block text-dark">Extend Enrollment Period</span>
                                        <span class="small text-muted">Extend billing end date by selected holiday day(s).</span>
                                    </label>
                                </div>
                            </div>

                            <div class="compensation-card" onclick="selectCompensation('refund')">
                                <div class="d-flex w-100">
                                    <input class="form-check-input" type="radio" name="action" id="actionRefund" value="refund">
                                    <label class="form-check-label" for="actionRefund">
                                        <span class="fw-bold d-block text-danger">Issue Pro-rata Refund</span>
                                        <span class="small text-muted">Refund calculated based on missed sessions.</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-light px-4 fw-bold" onclick="showModule('landing')">Cancel</button>
                            <button type="submit" class="btn btn-primary-provider shadow-sm" id="btnSubmitHoliday">
                                Declare Holiday & Notify <i class="bi bi-send-fill ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div id="holidayPreview" class="preview-panel sticky-top d-none" style="top: 2rem;">
                        <h6 class="fw-bold mb-3 text-uppercase small letter-spacing-sm">Impact Preview</h6>
                        <div class="refund-table-container bg-white border mb-3" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr class="x-small text-uppercase">
                                        <th class="ps-3 py-2">Student</th>
                                        <th>Impact</th>
                                    </tr>
                                </thead>
                                <tbody id="studentImpactBody" class="small"></tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-white border rounded-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Impacted Students</span>
                                <span class="fw-bold text-dark" id="impactedStudentsCount">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small" id="totalImpactLabel">Total Refund Est.</span>
                                <span class="fw-bold text-primary" id="totalImpactValue">₹0</span>
                            </div>
                        </div>
                    </div>
                    <div id="placeholderPreview" class="preview-panel d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted border-dashed">
                        <i class="bi bi-eye fs-1 opacity-25 mb-2"></i>
                        <p class="small mb-0">Select a class and dates to see the impact preview here.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Cancel Section -->
    <div id="cancelSection" class="module-section">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
            <h3 class="fw-bold m-0 text-danger">Cancel Class / Batch</h3>
            <span class="badge bg-danger-100 text-danger ms-3 rounded-pill px-3">Permanent Action</span>
        </div>

        <form id="cancelForm">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="alert-permanent mb-4">
                        <div class="d-flex">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Permanent Action</h6>
                                <p class="small mb-0">Cancelling a class will process refunds for all enrolled students. This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Class/Batch to Cancel *</label>
                        <select name="listing_id" id="cancelListingSelect" class="form-select form-select-lg" required>
                            <option value="">Choose a class...</option>
                        </select>
                    </div>

                    <div id="cancelFormFields" class="d-none">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Cancellation Reason (will be sent to students) *</label>
                            <textarea name="reason" id="cancelReason" class="form-control" rows="4" placeholder="Explain the reason for cancellation..." required></textarea>
                        </div>

                        <div class="bg-light p-4 rounded-4 border mb-4">
                            <p class="small fw-bold text-dark mb-3">Confirmation Required</p>
                            <label class="form-label x-small fw-bold text-uppercase text-muted">Type <span class="text-danger">CANCEL</span> to confirm</label>
                            <input type="text" id="confirmCancelBox" class="form-control confirm-cancel-input" placeholder="CANCEL" autocomplete="off">
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-light px-4 fw-bold" onclick="showModule('landing')">Back</button>
                            <button type="submit" class="btn btn-danger px-5 fw-bold" id="confirmCancelBtn" disabled style="border-radius: 8px; height: 3.5rem;">
                                Cancel Class & Process Refunds <i class="bi bi-check2-circle ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div id="cancelPreview" class="preview-panel sticky-top d-none" style="top: 2rem;">
                        <h6 class="fw-bold mb-3 text-uppercase small text-danger letter-spacing-sm">Refund Summary</h6>
                        
                        <div class="p-4 bg-white border rounded-4 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Total Students</span>
                                <span class="fw-bold text-dark" id="cancelStudentCount">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="text-muted small">Estimated Refund Amount</span>
                                <span class="fw-bold text-danger" id="cancelTotalRefund">₹0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Processing Time</span>
                                <span class="badge bg-light text-dark fw-600">5-7 business days</span>
                            </div>
                        </div>
                        
                        <label class="form-label x-small fw-bold text-uppercase text-muted mb-2">Impacted Students</label>
                        <div class="refund-table-container bg-white border" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr class="x-small text-uppercase">
                                        <th class="ps-3 py-2">Student</th>
                                        <th class="text-end pe-3">Refund</th>
                                    </tr>
                                </thead>
                                <tbody id="cancelImpactBody" class="small"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="placeholderCancelPreview" class="preview-panel d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted border-dashed">
                        <i class="bi bi-shield-lock-fill fs-1 opacity-25 mb-2"></i>
                        <p class="small mb-0">Select a class to see the cancellation impact summary.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Success Popup -->
<div class="modal fade" id="successPopup" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center p-5" style="border-radius: 16px;">
            <div class="bg-success text-white rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 72px; height: 72px;">
                <i class="bi bi-check-lg fs-1"></i>
            </div>
            <h4 class="fw-800 mb-2">Action Confirmed</h4>
            <p class="text-muted mb-4" id="successMsg">Enrollments updated and students notified.</p>
            <button type="button" class="btn btn-primary-provider w-100" onclick="window.location.reload()">Great!</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let allListings = [];
    let activeListing = null;
    let selectedBatches = [];
    
    const toYMD = (d) => {
        const date = new Date(d);
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    };

    const holidayPicker = flatpickr("#holidayDatePicker", {
        mode: "multiple", dateFormat: "d-m-Y", minDate: "today",
        onDayCreate: (dObj, dStr, fp, dayElem) => {
            if (!activeListing) return;
            const ymd = toYMD(dayElem.dateObj);
            const hl = Array.isArray(activeListing.holidays) ? activeListing.holidays : [];
            if (hl.includes(ymd)) { dayElem.classList.add('declared-holiday'); dayElem.title = 'Declared Holiday'; }
            if (isSessionDay(dayElem.dateObj)) dayElem.classList.add('session-day');
        },
        onChange: (dates) => updateHolidayPreview(dates)
    });

    async function fetchListings() {
        try {
            const res = await fetch('<?= base_url('provider/api/listings') ?>');
            const json = await res.json();
            if (json.success) { 
                allListings = json.data;
                const hSelect = document.getElementById('holidayListingSelect');
                const cSelect = document.getElementById('cancelListingSelect');
                allListings.forEach(l => {
                    const opt = `<option value="${l.id}">${l.title} (${l.type})</option>`;
                    hSelect.innerHTML += opt;
                    cSelect.innerHTML += opt;
                });
            }
        } catch (e) { console.error(e); }
    }

    function showModule(type) {
        document.querySelectorAll('.module-card').forEach(c => c.classList.remove('active', 'border-primary', 'border-danger'));
        document.getElementById('holidaySection').style.display = type === 'holiday' ? 'block' : 'none';
        document.getElementById('cancelSection').style.display = type === 'cancel' ? 'block' : 'none';
        
        if (type === 'holiday') {
            document.getElementById('card-holiday').classList.add('active', 'border-primary');
            document.getElementById('holidaySection').scrollIntoView({ behavior: 'smooth' });
        } else if (type === 'cancel') {
            document.getElementById('card-cancel').classList.add('active', 'border-danger');
            document.getElementById('cancelSection').scrollIntoView({ behavior: 'smooth' });
        }
    }

    function selectCompensation(type) {
        document.querySelectorAll('.compensation-card').forEach(c => c.classList.remove('active'));
        if (type === 'extend') {
            document.getElementById('actionExtend').checked = true;
            document.querySelector('.compensation-card .form-check-input#actionExtend').closest('.compensation-card').classList.add('active');
        } else {
            document.getElementById('actionRefund').checked = true;
            document.querySelector('.compensation-card .form-check-input#actionRefund').closest('.compensation-card').classList.add('active');
        }
        updateHolidayPreview(holidayPicker.selectedDates);
    }

    // Holiday Select Logic
    $('#holidayListingSelect').on('change', async function() {
        const id = $(this).val();
        if (!id) { $('#holidayFormFields').addClass('d-none'); return; }
        
        const res = await fetch(`<?= base_url('provider/api/listing-details') ?>/${id}`);
        const json = await res.json();
        if (json.success) {
            activeListing = json; selectedBatches = [];
            $('#holidayFormFields').removeClass('d-none');
            holidayPicker.clear(); 
            
            const batchWrap = document.getElementById('batchSelectionSection');
            const batchContainer = document.getElementById('batchCheckboxes');
            batchContainer.innerHTML = '';
            if (json.data.type === 'regular' && json.schedule.length > 0) {
                batchWrap.classList.remove('d-none');
                json.schedule.forEach(s => {
                  batchContainer.innerHTML += `<div class="batch-chip" onclick="toggleBatch(this, '${s.name}')">${s.name}</div>`;
                });
            } else batchWrap.classList.add('d-none');
            holidayPicker.redraw();
        }
    });

    window.toggleBatch = (el, name) => {
        el.classList.toggle('active');
        if (el.classList.contains('active')) selectedBatches.push(name);
        else selectedBatches = selectedBatches.filter(b => b !== name);
        document.getElementById('selectedBatchIds').value = selectedBatches.join(',');
        holidayPicker.clear();
    };

    function isSessionDay(date) {
        if (!activeListing) return false;
        const dayMap = {'S':0,'M':1,'T':2,'W':3,'Th':4,'F':5,'Sa':6};
        const dow = date.getDay(); const ymd = toYMD(date);
        let ok = false;
        activeListing.schedule.forEach(s => {
            if (s.date === ymd) ok = true;
            else if (s.days && (selectedBatches.length === 0 || selectedBatches.includes(s.name))) {
                if (s.days.map(d=>dayMap[d]).includes(dow)) {
                    if (date >= new Date(s.start_date)) ok = true;
                }
            }
        });
        return ok;
    }

    async function updateHolidayPreview(dates) {
        const hPreview = document.getElementById('holidayPreview');
        const hPlaceholder = document.getElementById('placeholderPreview');
        if (dates.length === 0) { hPreview.classList.add('d-none'); hPlaceholder.classList.remove('d-none'); return; }

        const action = document.querySelector('input[name="action"]:checked').value;
        const res = await fetch(`<?= base_url('provider/api/holiday-preview') ?>`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                listing_id: activeListing.data.id,
                dates: dates.map(d => toYMD(d)),
                batch_ids: selectedBatches,
                action: action
            })
        });
        const json = await res.json();
        if (json.success) {
            hPreview.classList.remove('d-none'); hPlaceholder.classList.add('d-none');
            document.getElementById('impactedStudentsCount').innerText = json.student_count;
            document.getElementById('totalImpactLabel').innerText = action === 'refund' ? 'Total Refund Est.' : 'Total Extensions';
            document.getElementById('totalImpactValue').innerText = action === 'refund' ? '₹' + json.total_impact : json.total_impact;
            
            const tbody = document.getElementById('studentImpactBody');
            tbody.innerHTML = '';
            json.students.forEach(s => {
                const impactHtml = action === 'refund' 
                   ? (s.is_trial ? `<span class="text-primary fw-600">Trial Extend</span>` : `<span class="text-danger fw-600">₹${s.impact}</span>`)
                   : `<span class="text-primary fw-600">${s.impact}</span>`;
                tbody.innerHTML += `<tr><td class="ps-3 py-2 fw-bold text-dark text-truncate" style="max-width:150px;">${s.name}</td><td class="pe-3 text-end">${impactHtml}</td></tr>`;
            });
        }
    }

    // Cancel Select Logic
    $('#cancelListingSelect').on('change', async function() {
        const id = $(this).val();
        if (!id) { $('#cancelFormFields').addClass('d-none'); return; }
        
        const res = await fetch(`<?= base_url('provider/api/cancellation-preview') ?>/${id}`);
        const json = await res.json();
        if (json.success) {
            $('#cancelFormFields').removeClass('d-none');
            $('#placeholderCancelPreview').addClass('d-none');
            $('#cancelPreview').removeClass('d-none');
            
            document.getElementById('cancelStudentCount').innerText = json.breakdown.length;
            document.getElementById('cancelTotalRefund').innerText = '₹' + json.total_refund;
            
            const tbody = document.getElementById('cancelImpactBody');
            tbody.innerHTML = '';
            json.breakdown.forEach(row => {
                tbody.innerHTML += `<tr><td class="ps-3 py-2 fw-bold text-dark text-truncate" style="max-width:150px;">${row.student_name}</td><td class="pe-3 text-end text-danger fw-600">₹${row.refund_amount}</td></tr>`;
            });
        }
    });

    document.getElementById('confirmCancelBox').addEventListener('input', function() {
        document.getElementById('confirmCancelBtn').disabled = (this.value !== 'CANCEL');
    });

    // Form Submissions
    $('#holidayForm').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitHoliday');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Notifying...');
        const res = await fetch('<?= base_url('provider/availability/declare-holiday') ?>', { method: 'POST', body: new FormData(this) });
        const json = await res.json();
        if (json.success) {
            document.getElementById('successMsg').innerText = json.message;
            new bootstrap.Modal(document.getElementById('successPopup')).show();
        } else alert(json.message);
        btn.prop('disabled', false).html('Declare Holiday & Notify <i class="bi bi-send-fill ms-2"></i>');
    });

    $('#cancelForm').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#confirmCancelBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
        const res = await fetch('<?= base_url('provider/availability/end-class') ?>', { method: 'POST', body: new FormData(this) });
        const json = await res.json();
        if (json.success) {
            document.getElementById('successMsg').innerText = json.message;
            new bootstrap.Modal(document.getElementById('successPopup')).show();
        } else alert(json.message);
        btn.prop('disabled', false).html('Cancel Class & Process Refunds <i class="bi bi-check2-circle ms-2"></i>');
    });

    fetchListings();
</script>
<?= $this->endSection() ?>
