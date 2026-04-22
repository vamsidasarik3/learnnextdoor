<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold"><?= $title ?></h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?= url('admin/feedback') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to List</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope-open-text mr-3 text-primary" style="font-size: 1.5rem;"></i>
                            <div>
                                <h4 class="mb-0 font-weight-bold">Ticket #<?= $feedback->id ?></h4>
                                <small class="text-muted">Received on <?= date('M d, Y', strtotime($feedback->created_at)) ?></small>
                            </div>
                        </div>
                        <span class="badge badge-soft-success px-3 py-2">Status: Resolved/Read</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="text-xs text-uppercase font-weight-bold text-muted tracking-wider mb-2">User Details</label>
                            <div class="p-3 bg-light rounded-lg">
                                <h6 class="mb-1 font-weight-bold"><?= esc($feedback->user_id ? 'Registered User' : 'Guest') ?></h6>
                                <p class="mb-0 text-sm">User ID: <?= $feedback->user_id ?: 'N/A' ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="text-xs text-uppercase font-weight-bold text-muted tracking-wider mb-2">Message Content</label>
                            <div class="p-4 bg-light rounded-lg border" style="font-size: 1rem; line-height: 1.6; white-space: pre-wrap;"><?= esc($feedback->message) ?></div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right py-3 border-top">
                        <p class="small text-muted mb-0">Note: Opening this ticket marked it as 'Read' automatically.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
