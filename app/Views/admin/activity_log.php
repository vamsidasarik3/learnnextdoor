<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-dark pl-3">Administrative Audit Log</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <h3 class="card-title fw-bold text-muted small text-uppercase">System Activity & Accountability Trail</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable-audit">
                        <thead class="bg-light text-muted uppercase small fw-bold">
                            <tr>
                                <th class="px-4 py-3">Timestamp</th>
                                <th>Administrator</th>
                                <th>Action / Event</th>
                                <th>IP Address</th>
                                <th class="px-4 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="text-dark small fw-bold"><?= date('d M Y', strtotime($log->created_at)) ?></div>
                                        <div class="text-muted text-xs"><?= date('h:i:s A', strtotime($log->created_at)) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?= esc($log->user_name) ?></div>
                                        <div class="text-xs text-muted"><?= esc($log->user_email) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-600" style="max-width: 400px; white-space: normal;">
                                            <?= esc($log->title) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-xs bg-light p-1 rounded"><?= esc($log->ip_address) ?></code>
                                    </td>
                                    <td class="px-4 text-right">
                                        <?php if ($log->old_value || $log->new_value): ?>
                                            <button class="btn btn-xs btn-outline-dark rounded-pill px-3 fw-bold" onclick='viewChanges(<?= json_encode($log) ?>)'>
                                                <i class="fas fa-history mr-1"></i> Diff
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small italic">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Diff Modal -->
<div class="modal fade" id="diffModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">Activity Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="row m-0 bg-light border-bottom">
                    <div class="col-6 p-3 fw-bold small text-uppercase text-muted border-right">Before Changes</div>
                    <div class="col-6 p-3 fw-bold small text-uppercase text-muted">After Changes</div>
                </div>
                <div class="row m-0" style="max-height: 500px; overflow-y: auto;">
                    <div class="col-6 p-3 border-right">
                        <pre id="oldValue" class="text-xs mb-0 bg-white p-2 rounded border"></pre>
                    </div>
                    <div class="col-6 p-3">
                        <pre id="newValue" class="text-xs mb-0 bg-white p-2 rounded border"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.fw-900 { font-weight: 900; }
.text-xs { font-size: 0.75rem; }
.rounded-4 { border-radius: 1rem !important; }
pre { white-space: pre-wrap; word-wrap: break-word; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('.datatable-audit').DataTable({
        "order": [[ 0, "desc" ]]
    });
});

function viewChanges(log) {
    try {
        const oldVal = log.old_value ? JSON.parse(log.old_value) : null;
        const newVal = log.new_value ? JSON.parse(log.new_value) : null;
        
        $('#oldValue').text(oldVal ? JSON.stringify(oldVal, null, 4) : 'No previous data');
        $('#newValue').text(newVal ? JSON.stringify(newVal, null, 4) : 'No new data');
        $('#diffModal').modal('show');
    } catch (e) {
        $('#oldValue').text(log.old_value || 'None');
        $('#newValue').text(log.new_value || 'None');
        $('#diffModal').modal('show');
    }
}
</script>
<?= $this->endSection() ?>
