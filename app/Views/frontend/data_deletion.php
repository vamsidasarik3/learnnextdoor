<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<style>
    :root {
        --primary-color: #2D3FE6;
        --secondary-color: #6366f1;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.95);
        --card-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
    }

    .data-deletion-container {
        background: #f8fafc;
        padding: 80px 0;
        min-height: 70vh;
        display: flex;
        align-items: center;
    }

    .deletion-card {
        background: var(--glass-bg);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 24px;
        padding: 60px;
        box-shadow: var(--card-shadow);
        margin: 0 auto;
        max-width: 800px;
        text-align: center;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .icon-wrapper {
        width: 80px;
        height: 80px;
        background: rgba(244, 63, 94, 0.1);
        color: #f43f5e;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 30px auto;
    }

    h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 20px;
    }

    .deletion-content {
        font-size: 1.15rem;
        color: #475569;
        line-height: 1.8;
        max-width: 600px;
        margin: 0 auto;
    }

    .email-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 700;
        border-bottom: 2px solid rgba(45, 63, 230, 0.2);
        transition: all 0.2s;
    }

    .email-link:hover {
        border-bottom-color: var(--primary-color);
        background: rgba(45, 63, 230, 0.05);
    }

    @media (max-width: 768px) {
        .deletion-card {
            padding: 40px 20px;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
        h1 {
            font-size: 1.8rem;
        }
        .data-deletion-container {
            padding: 40px 0;
        }
    }
</style>

<div class="data-deletion-container">
    <div class="container">
        <div class="deletion-card">
            <div class="icon-wrapper">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <h1>Data Deletion Request</h1>
            <div class="deletion-content">
                <p>
                    To request the deletion of your account and associated phone number, 
                    please email us at <a href="mailto:support@learnnextdoor.com" class="email-link">support@learnnextdoor.com</a>.
                </p>
                <p>
                    We will process your request within <strong>48 hours</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
