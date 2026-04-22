<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('css') ?>
<style>
    .back-btn { display: flex; align-items: center; gap: 8px; color: #6B7280; text-decoration: none; font-weight: 700; font-size: 0.9rem; margin-bottom: 2rem; transition: color 0.2s; }
    .back-btn:hover { color: #4F46E5; }

    .ticket-meta-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 20px; padding: 1.5rem; height: sticky; top: 2rem; }
    .meta-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #9CA3AF; margin-bottom: 0.5rem; display: block; }
    .meta-value { font-weight: 700; color: #111827; margin-bottom: 1.5rem; display: block; }

    .chat-wrapper { background: #fff; border: 1px solid #E5E7EB; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column; height: calc(100vh - 250px); }
    .chat-header { padding: 1.5rem 2rem; border-bottom: 1px solid #F3F4F6; background: #fff; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 2rem; background: #F9FAFB; display: flex; flex-direction: column; gap: 1.5rem; }
    
    .msg-bubble { max-width: 80%; padding: 1.25rem; border-radius: 20px; font-size: 0.95rem; line-height: 1.5; position: relative; }
    .msg-provider { align-self: flex-end; background: #4F46E5; color: white; border-bottom-right-radius: 4px; }
    .msg-admin { align-self: flex-start; background: #fff; color: #111827; border: 1px solid #E5E7EB; border-bottom-left-radius: 4px; }
    
    .msg-time { font-size: 0.7rem; margin-top: 0.5rem; opacity: 0.7; font-weight: 600; display: block; }
    .msg-provider .msg-time { text-align: right; }

    .reply-area { padding: 1.5rem 2rem; border-top: 1px solid #F3F4F6; background: #fff; }
    .reply-form { display: flex; gap: 1rem; align-items: flex-end; }
    .reply-input { border-radius: 12px; border: 1.5px solid #E5E7EB; padding: 0.75rem 1rem; flex: 1; resize: none; transition: all 0.2s; }
    .reply-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }

    .status-badge { font-size: 10px; font-weight: 800; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; }
    .status-open { background: #FEF3C7; color: #92400E; }
    .status-resolved { background: #D1FAE5; color: #065F46; }

    .tkt-original-msg { padding: 1.5rem; background: #EEF2FF; border-radius: 16px; border-left: 4px solid #4F46E5; margin-bottom: 2rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('provider/support') ?>" class="back-btn">
    <i class="bi bi-arrow-left"></i> Back to All Tickets
</a>

<div class="row">
    <!-- Sidebar Meta -->
    <div class="col-md-3">
        <div class="ticket-meta-card shadow-sm border-0">
            <span class="meta-label">Reference ID</span>
            <span class="meta-value text-primary">#REQ-<?= str_pad($ticket->id, 4, '0', STR_PAD_LEFT) ?></span>

            <span class="meta-label">Current Status</span>
            <div class="mb-4">
                <span class="status-badge <?= $ticket->status === 'resolved' ? 'status-resolved' : 'status-open' ?>">
                    <?= ucfirst($ticket->status) ?>
                </span>
            </div>

            <span class="meta-label">Priority</span>
            <span class="meta-value opacity-75">High Priority</span>

            <span class="meta-label">Category</span>
            <span class="meta-value small"><?= $ticket->category ?? 'General Inquiry' ?></span>

            <span class="meta-label">Last Updated</span>
            <span class="meta-value small text-muted"><?= date('d M Y, h:i A', strtotime($ticket->updated_at)) ?></span>
        </div>
    </div>

    <!-- Communication History -->
    <div class="col-md-9">
        <div class="chat-wrapper shadow-sm border-0">
            <div class="chat-header">
                <h5 class="fw-800 m-0">Communication Thread</h5>
            </div>

            <div class="chat-messages" id="chatHistory">
                <!-- Original Concern -->
                <div class="tkt-original-msg">
                    <span class="meta-label text-primary">Initial Concern - <?= date('d M, h:i A', strtotime($ticket->created_at)) ?></span>
                    <div class="fw-700 text-dark"><?= esc($ticket->issue) ?></div>
                </div>

                <?php foreach($messages as $msg): ?>
                    <div class="msg-bubble <?= $msg->role === 'provider' ? 'msg-provider' : 'msg-admin' ?>">
                        <div class="fw-600"><?= nl2br(esc($msg->message)) ?></div>
                        <span class="msg-time"><?= date('h:i A', strtotime($msg->created_at)) ?></span>
                    </div>
                <?php endforeach; ?>

                <?php if($ticket->status === 'resolved' && !empty($ticket->resolution_note)): ?>
                    <div class="msg-bubble msg-admin border-primary">
                        <div class="x-small fw-800 text-primary text-uppercase mb-2">Resolution Note from Admin</div>
                        <div class="fw-700"><?= esc($ticket->resolution_note) ?></div>
                        <span class="msg-time"><?= date('d M, h:i A', strtotime($ticket->resolved_at ?? $ticket->updated_at)) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($ticket->status !== 'resolved'): ?>
                <div class="reply-area">
                    <form action="<?= base_url('provider/support/reply') ?>" method="POST" class="reply-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="ticket_id" value="<?= $ticket->id ?>">
                        <textarea name="message" class="reply-input" placeholder="Type your reply here..." rows="1" required oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                        <button type="submit" class="btn btn-raise-ticket py-2 px-4 rounded-pill">
                            <i class="bi bi-send-fill me-2"></i> Send
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="reply-area bg-light text-center py-4">
                    <span class="small fw-800 text-muted"><i class="bi bi-lock-fill me-2"></i> This ticket is resolved and closed for further communication.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Auto scroll chat to bottom
    const chat = document.getElementById('chatHistory');
    chat.scrollTop = chat.scrollHeight;
</script>
<?= $this->endSection() ?>
