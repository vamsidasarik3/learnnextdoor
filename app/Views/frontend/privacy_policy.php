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

    .privacy-container {
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
        /* Increased width as requested */
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

    .effective-date {
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.9rem;
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

    h3 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 22px 0 10px 0;
        color: #334155;
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
            border-left: none;
            border-right: none;
        }
        h1 {
            font-size: 2rem;
        }
        .privacy-container {
            padding: 20px 0;
        }
    }
</style>

<div class="privacy-container">
    <div class="container">
        <div class="policy-card">
            <header class="header">
                <h1>Privacy Policy</h1>
                <span class="effective-date">Effective Date: 1 April 2026</span>
            </header>

            <section id="introduction">
                <h2><i class="bi bi-info-circle-fill"></i> 1. Introduction</h2>
                <p>Welcome to <strong>LearnNextDoor</strong>. We are committed to protecting your privacy and ensuring that your personal information is handled in a safe and responsible manner.</p>
                <p>This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform, including our website, mobile services, and integrations such as WhatsApp.</p>
            </section>

            <section id="information-collection">
                <h2><i class="bi bi-database-fill"></i> 2. Information We Collect</h2>
                
                <h3>2.1 Personal Information</h3>
                <p>We may collect the following personal information:</p>
                <ul>
                    <li>Name</li>
                    <li>Phone number</li>
                    <li>Email address</li>
                    <li>Location (for discovering nearby classes)</li>
                </ul>

                <h3>2.2 Usage Data</h3>
                <ul>
                    <li>Device information</li>
                    <li>IP address</li>
                    <li>Browser type</li>
                    <li>Pages visited and interactions</li>
                </ul>

                <h3>2.3 Third-Party Integrations</h3>
                <p>If you interact with us via WhatsApp or other third-party platforms, we may collect information such as:</p>
                <ul>
                    <li>Phone number</li>
                    <li>Messages and responses</li>
                    <li>Interaction timestamps</li>
                </ul>
            </section>

            <section id="usage">
                <h2><i class="bi bi-gear-fill"></i> 3. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide and improve our services</li>
                    <li>Help users discover relevant classes and activities</li>
                    <li>Communicate updates, offers, and important notifications</li>
                    <li>Respond to inquiries and support requests</li>
                    <li>Ensure platform safety and prevent misuse</li>
                </ul>
            </section>

            <section id="sharing">
                <h2><i class="bi bi-shield-lock-fill"></i> 4. Sharing of Information</h2>
                <p>We do not sell your personal data. We may share your information with:</p>
                <ul>
                    <li><strong>Service providers:</strong> Hosting, analytics, and messaging tools.</li>
                    <li><strong>Class providers:</strong> Only when you express interest or enroll.</li>
                    <li><strong>Legal authorities:</strong> When required by law.</li>
                </ul>
            </section>

            <section id="retention">
                <h2><i class="bi bi-calendar-event-fill"></i> 5. Data Retention</h2>
                <p>We retain your data only as long as necessary for:</p>
                <ul>
                    <li>Providing services</li>
                    <li>Legal and compliance requirements</li>
                    <li>Resolving disputes</li>
                </ul>
            </section>

            <section id="security">
                <h2><i class="bi bi-shield-fill-check"></i> 6. Data Security</h2>
                <p>We implement appropriate technical and organizational measures to protect your data from unauthorized access, loss, or misuse.</p>
            </section>

            <section id="rights">
                <h2><i class="bi bi-person-fill-gear"></i> 7. Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal data</li>
                    <li>Request correction or deletion</li>
                    <li>Withdraw consent at any time</li>
                    <li>Opt-out of marketing communications</li>
                </ul>
                <p>To exercise your rights, contact us at: <a href="mailto:support@learnnextdoor.com" class="contact-link">support@learnnextdoor.com</a></p>
            </section>

            <section id="third-party">
                <h2><i class="bi bi-link-45deg"></i> 8. Third-Party Links</h2>
                <p>Our platform may contain links to third-party websites. We are not responsible for their privacy practices.</p>
            </section>

            <section id="updates">
                <h2><i class="bi bi-arrow-repeat"></i> 10. Updates to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated effective date.</p>
            </section>

            <section id="contact" class="contact-box">
                <h2><i class="bi bi-envelope-paper-fill"></i> 11. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, you can contact us at:</p>
                <p>
                    <strong>Email:</strong> <a href="mailto:support@learnnextdoor.com" class="contact-link">support@learnnextdoor.com</a><br>
                    <strong>Company Name:</strong> Learn Next Door
                </p>
            </section>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
