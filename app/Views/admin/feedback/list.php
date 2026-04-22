<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold"><?= $title ?></h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-dark">User Concerns & Feedback</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="<?= url('admin/feedback') ?>" class="btn btn-sm <?= !$status ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
                            <a href="<?= url('admin/feedback?status=new') ?>" class="btn btn-sm <?= $status === 'new' ? 'btn-primary' : 'btn-outline-primary' ?>">New</a>
                            <a href="<?= url('admin/feedback?status=read') ?>" class="btn btn-sm <?= $status === 'read' ? 'btn-primary' : 'btn-outline-primary' ?>">Read</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="px-4">User</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedbacks as $f): ?>
                            <tr>
                                <td class="px-4">
                                    <div class="font-weight-bold text-dark"><?= esc($f->user_name) ?></div>
                                    <small class="text-muted"><?= esc($f->user_email) ?></small>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;"><?= esc($f->message) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-<?= $f->status === 'new' ? 'danger' : 'success' ?>">
                                        <?= ucfirst($f->status) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($f->created_at)) ?></td>
                                <td>
                                    <a href="<?= url('admin/feedback/view/'.$f->id) ?>" class="btn btn-xs btn-outline-primary px-3 rounded-pill">View</a>
                                </td>
                            </tr>
                            <?php endforeach; if (empty($feedbacks)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No feedback found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
