<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('css') ?>
<style>
    .page-header-support { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
    .support-title { font-size: 2.5rem; font-weight: 800; color: #111827; letter-spacing: -0.02em; margin-bottom: 0.25rem; }
    .support-subtitle { color: #6B7280; font-size: 1.1rem; }

    .btn-raise-ticket {
        background: #4F46E5; color: white !important; font-weight: 600; padding: 0.75rem 1.5rem;
        border-radius: 10px; border: none; display: flex; align-items: center; gap: 8px;
        transition: all 0.2s;
    }
    .btn-raise-ticket:hover { background: #4338CA; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

    .ticket-card {
        background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem;
        display: flex; justify-content: space-between; transition: all 0.2s;
    }
    .ticket-card:hover { border-color: #4F46E5; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04); }

    .tkt-id { font-family: monospace; font-weight: 700; color: #9CA3AF; margin-right: 1.5rem; font-size: 0.95rem; }
    .tkt-status { font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 6px; text-transform: uppercase; margin-right: 1rem; }
    .status-in-progress { background: #FEF3C7; color: #92400E; }
    .status-resolved { background: #D1FAE5; color: #065F46; }
    
    .tkt-priority { font-size: 12px; font-weight: 700; color: #EF4444; } /* Defaulting to red for visual parity */

    .tkt-title { font-size: 1.25rem; font-weight: 700; color: #111827; margin-top: 1rem; margin-bottom: 0.5rem; }
    .tkt-desc { color: #6B7280; font-size: 0.95rem; margin-bottom: 1.25rem; }

    .tkt-footer { display: flex; align-items: center; gap: 1rem; }
    .tkt-category { background: #F3F4F6; color: #4B5563; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 6px; }
    .tkt-dates { font-size: 12px; color: #9CA3AF; }

    .view-details-link { color: #4F46E5; font-weight: 700; text-decoration: none; display: flex; align-items: center; }
    .view-details-link:hover { text-decoration: underline; }

    /* Modal Styling */
    .modal-content { border-radius: 20px; border: none; padding: 1.5rem; }
    .form-label { font-weight: 700; color: #374151; font-size: 0.9rem; margin-bottom: 0.5rem; }
    .form-label span { color: #EF4444; }
    .form-select, .form-control { border-radius: 10px; border: 1px solid #E5E7EB; padding: 0.75rem; font-size: 0.95rem; }
    .form-select:focus, .form-control:focus { border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-support">
    <div>
        <h1 class="support-title">Provider Support</h1>
        <p class="support-subtitle">Get help with your provider account and listings</p>
    </div>
    <button class="btn btn-raise-ticket" data-bs-toggle="modal" data-bs-target="#raiseTicketModal">
        <i class="bi bi-plus-lg"></i> Raise Ticket
    </button>
</div>

<div class="container-fluid p-0">
    <?php if(empty($recentConcerns)): ?>
        <div class="text-center py-5 bg-white rounded-4 border">
            <i class="bi bi-chat-left-text fs-1 text-light d-block mb-3"></i>
            <h5 class="fw-bold text-muted">No support tickets found</h5>
            <p class="text-muted small">Need help? Click the "Raise Ticket" button above.</p>
        </div>
    <?php else: ?>
        <?php foreach($recentConcerns as $c): ?>
            <div class="ticket-card shadow-sm border-0">
                <div>
                    <div class="d-flex align-items-center">
                        <span class="tkt-id">TKT-<?= str_pad($c->id, 3, '0', STR_PAD_LEFT) ?></span>
                        <span class="tkt-status <?= $c->status === 'resolved' ? 'status-resolved' : 'status-in-progress' ?>">
                            <i class="bi <?= $c->status === 'resolved' ? 'bi-check-circle' : 'bi-clock' ?> me-1"></i>
                            <?= $c->status === 'resolved' ? 'RESOLVED' : 'IN PROGRESS' ?>
                        </span>
                        <span class="tkt-priority">High Priority</span>
                    </div>
                    
                    <h4 class="tkt-title"><?= esc($c->issue) ?></h4>
                    <p class="tkt-desc">Ticket details or resolution notes from admin go here...</p>
                    
                    <div class="tkt-footer">
                        <span class="tkt-category">Account & Platform</span>
                        <span class="tkt-dates">
                            • Created <?= date('n/j/Y', strtotime($c->created_at)) ?> 
                            • Updated <?= date('n/j/Y', strtotime($c->updated_at)) ?>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <a href="<?= base_url('provider/support/view/'.$c->id) ?>" class="view-details-link">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Raise Support Ticket Modal -->
<div class="modal fade" id="raiseTicketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="raiseTicketForm">
            <h4 class="fw-bold mb-4">Raise Support Ticket</h4>
            
            <div class="mb-3">
                <label class="form-label">Issue Category <span>*</span></label>
                <select name="category" class="form-select" required>
                    <option value="">Select category</option>
                    <option value="KYC Issue">KYC Issue</option>
                    <option value="Listing Problem">Listing Problem</option>
                    <option value="Payment & Payout">Payment & Payout</option>
                    <option value="Enrollment Dispute">Enrollment Dispute</option>
                    <option value="Technical Bug">Technical Bug</option>
                    <option value="Policy Question">Policy Question</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Related Listing (Optional)</label>
                <select name="listing_id" class="form-select">
                    <option value="">Not listing-specific</option>
                    <?php foreach($listings as $listing): ?>
                        <option value="<?= $listing->id ?>"><?= esc($listing->title) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Description <span>*</span></label>
                <textarea name="issue" class="form-control" rows="5" placeholder="Describe your issue in detail..." required></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-raise-ticket px-4" id="btnSubmitTicket">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>


<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-5">
            <div class="bg-success text-white rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 72px; height: 72px;">
                <i class="bi bi-check-lg fs-1"></i>
            </div>
            <h4 class="fw-bold mb-2">Ticket Raised</h4>
            <p class="text-muted mb-4">Your support ticket has been successfully logged. We will get back to you soon.</p>
            <button type="button" class="btn btn-raise-ticket w-100 justify-content-center" onclick="window.location.reload()">Great!</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>

    $('#raiseTicketForm').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitTicket');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        try {
            const res = await fetch('<?= base_url('provider/api/concerns/raise') ?>', {
                method: 'POST',
                body: new FormData(this)
            });
            const json = await res.json();
            if (json.success) {
                bootstrap.Modal.getInstance(document.getElementById('raiseTicketModal')).hide();
                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                alert(json.message);
                btn.prop('disabled', false).text('Submit Ticket');
            }
        } catch (err) {
            console.error(err);
            btn.prop('disabled', false).text('Submit Ticket');
        }
    });
</script>
<?= $this->endSection() ?>
