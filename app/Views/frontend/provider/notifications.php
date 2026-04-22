<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('css') ?>
<style>
    .notif-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
    .notif-title { font-size: 2.5rem; font-weight: 800; color: #111827; letter-spacing: -0.02em; margin-bottom: 0.25rem; }
    .notif-summary { color: #6B7280; font-size: 1.1rem; }
    .mark-all-link { color: #4F46E5; text-decoration: none; font-weight: 700; font-size: 0.95rem; }
    .mark-all-link:hover { text-decoration: underline; }

    .notif-card {
        background: #fff; border: 1px solid #E5E7EB; border-radius: 20px; transition: all 0.2s;
        display: flex; position: relative; overflow: hidden; margin-bottom: 1.25rem;
    }
    .notif-card:hover { border-color: #D1D5DB; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

    .notif-icon-box {
        width: 70px; margin: 1.5rem; border-radius: 25px; 
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;
    }
    .icon-info { background: #EEF2FF; color: #4F46E5; }
    .icon-success { background: #ECFDF5; color: #10B981; }
    .icon-update { background: #F5F3FF; color: #8B5CF6; }

    .notif-body { flex: 1; padding: 2rem 1rem 2rem 0; }
    .notif-card-title { font-size: 1.15rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px; margin-bottom: 0.5rem; }
    .unread-dot { width: 8px; height: 8px; background: #4F46E5; border-radius: 50%; }
    
    .notif-card-desc { color: #6B7280; font-size: 0.95rem; margin-bottom: 1rem; line-height: 1.5; }
    .notif-card-date { color: #9CA3AF; font-size: 0.9rem; font-weight: 600; }

    .notif-actions { padding: 2rem; display: flex; align-items: flex-end; gap: 1.5rem; }
    .action-link { font-size: 0.9rem; font-weight: 700; text-decoration: none; cursor: pointer; transition: color 0.2s; }
    .link-primary { color: #4F46E5; }
    .link-muted { color: #9CA3AF; }
    .action-link:hover { opacity: 0.8; text-decoration: underline; }

    .info-banner {
        background: #F0F7FF; border: 1px solid #D1E9FF; border-radius: 16px; 
        padding: 1.25rem 1.5rem; display: flex; gap: 1rem; align-items: center; margin-top: 3rem;
    }
    .info-banner i { color: #0070F3; font-size: 1.25rem; }
    .info-banner span { color: #0056B3; font-weight: 600; font-size: 0.9rem; }
    .info-banner p { color: #4F46E5; font-weight: 500; font-size: 0.9rem; margin: 0; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="notif-header">
    <div>
        <h1 class="notif-title">Notifications</h1>
        <?php $unreadCnt = array_reduce($notifications, function($carry, $item) { return $carry + ($item->is_read == 0 ? 1 : 0); }, 0); ?>
        <p class="notif-summary">You have <?= $unreadCnt ?> new notifications</p>
    </div>
    <a href="#" class="mark-all-link d-none">Mark all as read</a>
</div>

<div class="notif-list">
    <?php if(empty($notifications)): ?>
        <div class="text-center py-5 bg-white rounded-4 border">
            <i class="bi bi-bell-slash fs-1 text-light d-block mb-3"></i>
            <h5 class="fw-bold text-muted">Awaiting activity...</h5>
        </div>
    <?php else: ?>
        <?php foreach($notifications as $n): ?>
            <?php 
                $iconClass = 'icon-info';
                $icon = 'bi-info-circle';
                $title = esc($n->title);
                if(strpos(strtolower($title), 'kyc') !== false || strpos(strtolower($title), 'approve') !== false) { $iconClass = 'icon-success'; $icon = 'bi-check-circle'; }
                elseif(strpos(strtolower($title), 'settle') !== false || strpos(strtolower($title), 'payout') !== false) { $iconClass = 'icon-success'; $icon = 'bi-check-circle'; }
                elseif(strpos(strtolower($title), 'platform') !== false || strpos(strtolower($title), 'update') !== false) { $iconClass = 'icon-update'; $icon = 'bi-bell'; }
            ?>
            <div class="notif-card border-0 shadow-sm">
                <div class="notif-icon-box <?= $iconClass ?>">
                    <i class="bi <?= $icon ?>"></i>
                </div>
                <div class="notif-body">
                    <h5 class="notif-card-title">
                        <?= $title ?> 
                        <?php if($n->is_read == 0): ?>
                            <span class="unread-dot"></span>
                        <?php endif; ?>
                    </h5>
                    <p class="notif-card-desc"><?= esc($n->new_value ?: 'New activity detected on your account.') ?></p>
                    <span class="notif-card-date"><?= date('n/j/Y', strtotime($n->created_at)) ?></span>
                </div>
                <div class="notif-actions">
                    <a class="action-link link-primary">Mark as read</a>
                    <a class="action-link link-muted">Dismiss</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Info Banner -->
<div class="info-banner">
    <i class="bi bi-info-circle"></i>
    <div>
        <span>About Notifications</span>
        <p>Notifications older than 90 days are automatically archived. Critical notifications persist until dismissed.</p>
    </div>
</div>

<?= $this->endSection() ?>
