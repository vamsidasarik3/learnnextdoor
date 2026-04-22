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

    .terms-container {
        background: #f8fafc;
        padding: 60px 0;
        min-height: 100vh;
    }

    .policy-card {
        background: var(--glass-bg);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        padding: 50px;
        box-shadow: var(--card-shadow);
        margin: 0 auto;
        max-width: 1000px;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .header {
        text-align: center;
        margin-bottom: 50px;
    }

    h1 {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 15px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    section {
        margin-bottom: 35px;
    }

    h2 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    h2 i {
        color: var(--primary-color);
        font-size: 1.2rem;
    }

    p {
        margin-bottom: 14px;
        color: #475569;
        font-size: 1rem;
        line-height: 1.7;
    }

    ul {
        list-style: none;
        margin-bottom: 18px;
        padding-left: 0;
    }

    li {
        position: relative;
        padding-left: 28px;
        margin-bottom: 8px;
        color: #475569;
        font-size: 1rem;
    }

    li::before {
        content: "\F2E5"; /* Bootstrap Icon Check */
        font-family: "bootstrap-icons";
        position: absolute;
        left: 0;
        top: 2px;
        color: var(--secondary-color);
        font-weight: bold;
    }

    .contact-box {
        background: rgba(45, 63, 230, 0.04);
        border-radius: 16px;
        padding: 30px;
        margin-top: 40px;
        border: 1px solid rgba(45, 63, 230, 0.08);
    }

    .contact-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .contact-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .policy-card {
            padding: 30px 20px;
            border-radius: 0;
        }
        h1 {
            font-size: 2rem;
        }
        .terms-container {
            padding: 20px 0;
        }
    }
</style>

<div class="terms-container">
    <div class="container">
        <div class="policy-card">
            <header class="header">
                <h1>Terms of Service</h1>
            </header>

            <section id="acceptance">
                <h2><i class="bi bi-file-earmark-check-fill"></i> 1. Acceptance of Terms</h2>
                <p>By accessing or using this website, you agree to comply with and be bound by these Terms of Service. If you do not agree, please do not use our platform.</p>
            </section>

            <section id="use">
                <h2><i class="bi bi-pc-display"></i> 2. Use of the Platform</h2>
                <p>You agree to use the platform only for lawful purposes. You must not:</p>
                <ul>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Provide false or misleading information</li>
                    <li>Interfere with the platform’s functionality or security</li>
                    <li>Attempt unauthorized access to any part of the system</li>
                </ul>
            </section>

            <section id="accounts">
                <h2><i class="bi bi-person-badge-fill"></i> 3. User Accounts</h2>
                <ul>
                    <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                    <li>You agree to provide accurate and complete information</li>
                    <li>Any activity under your account is your responsibility</li>
                </ul>
            </section>

            <section id="listings">
                <h2><i class="bi bi-layers-fill"></i> 4. Listings and Content</h2>
                <ul>
                    <li>Users (institutes/providers) are responsible for the accuracy of the information they post, including classes, workshops, and courses</li>
                    <li>We do not guarantee the quality, accuracy, or reliability of any listings</li>
                    <li>We reserve the right to remove or modify content that violates our policies</li>
                </ul>
            </section>

            <section id="payments">
                <h2><i class="bi bi-wallet2"></i> 5. Payments and Pricing</h2>
                <ul>
                    <li>All prices listed on the platform are set by the providers</li>
                    <li>We are not responsible for disputes related to pricing, refunds, or services unless explicitly stated</li>
                    <li>Any applicable fees or commissions will be clearly communicated</li>
                </ul>
            </section>

            <section id="verification">
                <h2><i class="bi bi-shield-lock-fill"></i> 6. Instructor and KYC Verification</h2>
                <ul>
                    <li>Instructors may be subject to verification (KYC) processes</li>
                    <li>Verification status is displayed for transparency but does not guarantee service quality</li>
                    <li>We reserve the right to approve or reject verification requests</li>
                </ul>
            </section>

            <section id="refunds">
                <h2><i class="bi bi-arrow-left-right"></i> 7. Cancellation and Refunds</h2>
                <ul>
                    <li>Cancellation and refund policies are defined by the individual providers</li>
                    <li>Users are advised to review these policies before making bookings</li>
                </ul>
            </section>

            <section id="intellectual-property">
                <h2><i class="bi bi-brush-fill"></i> 8. Intellectual Property</h2>
                <ul>
                    <li>All content on this platform (logos, design, text, etc.) is owned by or licensed to us</li>
                    <li>You may not copy, distribute, or reuse content without permission</li>
                </ul>
            </section>

            <section id="liability">
                <h2><i class="bi bi-exclamation-triangle-fill"></i> 9. Limitation of Liability</h2>
                <p>We are not liable for:</p>
                <ul>
                    <li>Any direct or indirect damages arising from use of the platform</li>
                    <li>Service quality, cancellations, or disputes between users and providers</li>
                    <li>Technical issues, downtime, or data loss</li>
                </ul>
            </section>

            <section id="termination">
                <h2><i class="bi bi-person-x-fill"></i> 10. Termination</h2>
                <p>We reserve the right to suspend or terminate accounts that:</p>
                <ul>
                    <li>Violate these terms</li>
                    <li>Engage in fraudulent or harmful activities</li>
                </ul>
            </section>

            <section id="changes">
                <h2><i class="bi bi-arrow-repeat"></i> 11. Changes to Terms</h2>
                <p>We may update these Terms of Service at any time. Continued use of the platform means you accept the updated terms.</p>
            </section>

            <section id="contact" class="contact-box">
                <h2><i class="bi bi-envelope-at-fill"></i> 12. Contact Information</h2>
                <p>If you have any questions about these Terms, please contact us at:</p>
                <p>
                    <strong>Email:</strong> <a href="mailto:support@learnnextdoor.com" class="contact-link">support@learnnextdoor.com</a>
                </p>
            </section>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
