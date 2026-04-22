<?= $this->extend('admin/layout/default') ?>
 
<?= $this->section('content') ?>
 
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">WhatsApp OTP Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">WhatsApp Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>
 
<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h3 class="card-title">Recent Status Updates</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Message ID</th>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Error</th>
                                <th>Time</th>
                                <th class="text-right px-4">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="px-4 small">
                                    <code class="text-muted"><?= esc($log->wa_message_id) ?></code>
                                </td>
                                <td><?= esc($log->recipient_id) ?></td>
                                <td>
                                    <?php 
                                        $badge = 'secondary';
                                        if ($log->status === 'read' || $log->status === 'delivered') $badge = 'success';
                                        if ($log->status === 'sent' || $log->status === 'accepted') $badge = 'info';
                                        if ($log->status === 'failed') $badge = 'danger';
                                    ?>
                                    <span class="badge badge-<?= $badge ?>"><?= strtoupper($log->status) ?></span>
                                </td>
                                <td class="small text-danger">
                                    <?= $log->error_message ? esc($log->error_message) : '-' ?>
                                </td>
                                <td>
                                    <?= date('d M, H:i:s', strtotime($log->updated_at)) ?>
                                </td>
                                <td class="text-right px-4">
                                    <button class="btn btn-xs btn-outline-primary" onclick="viewPayload(<?= $log->id ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div id="payload-<?= $log->id ?>" class="d-none">
                                        <?= esc($log->raw_payload) ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No WhatsApp logs found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
 
<!-- Modal for Raw Payload -->
<div class="modal fade" id="payloadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Raw JSON Payload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="jsonViewer" class="bg-light p-3 rounded" style="max-height: 500px; overflow: auto;"></pre>
            </div>
        </div>
    </div>
</div>
 
<?= $this->endSection() ?>
 
<?= $this->section('js') ?>
<script>
function viewPayload(id) {
    var raw = $('#payload-' + id).text();
    try {
        var json = JSON.parse(raw);
        $('#jsonViewer').text(JSON.stringify(json, null, 4));
    } catch (e) {
        $('#jsonViewer').text(raw);
    }
    $('#payloadModal').modal('show');
}
</script>
<?= $this->endSection() ?>
