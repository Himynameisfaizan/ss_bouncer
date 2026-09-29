<?php 
require_once 'config/connect.php';
$pageTitle = "Privacy Policy | SS Bouncers";
include 'includes/header.php'; 
?>

<section class="page-header position-relative py-5">
    <div class="page-header-bg"></div>
    <div class="container position-relative z-2 py-4 text-center">
        <h1 class="display-4 fw-bold text-white mb-0">Privacy Policy</h1>
    </div>
</section>

<section class="py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
            <div class="col-12 policy-content">
                <p class="text-muted mb-5"><strong>Last Updated:</strong> <?= date('F d, Y'); ?></p>

                <p>Welcome to <strong>SS Bouncers</strong>. We respect your privacy and are highly committed to protecting your personal and corporate data. This Privacy Policy outlines how we collect, use, process, and safeguard your information when you visit our website, inquire about our security guard and housekeeping services, or enter into a contract with us.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">1. Information We Collect</h4>
                <p>To provide you with secure and tailored personnel solutions, we collect the following types of information:</p>
                <ul class="text-muted">
                    <li><strong>Personal & Corporate Information:</strong> Name, email address, phone number, company name, and official designation.</li>
                    <li><strong>Service Requirements:</strong> Location of deployment, nature of premises (residential/commercial), and specific security needs.</li>
                    <li><strong>Technical Data:</strong> IP address, browser type, and time zone setting when you interact with our website.</li>
                </ul>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">2. How We Use Your Information</h4>
                <p>We use the collected information exclusively for the following purposes:</p>
                <ul class="text-muted">
                    <li>To evaluate your security needs and provide customized quotations.</li>
                    <li>To communicate regarding deployment schedules, service agreements, and invoicing.</li>
                    <li>To conduct site assessments and assign appropriate verified security personnel.</li>
                    <li>To improve our website functionality, customer service, and emergency response times.</li>
                </ul>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">3. Confidentiality & Security Protocols</h4>
                <p>Security is our core business. We apply the same strict vigilance to your data as we do to your physical premises. We do not sell, trade, or rent your personal or corporate security configurations to external parties. Your operational requirements and contact details are maintained under strict confidentiality agreements accessible only to authorized management.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">4. Third-Party Sharing</h4>
                <p>We may share your information with trusted third parties strictly for necessary operational reasons, such as:</p>
                <ul class="text-muted">
                    <li><strong>Legal & Law Enforcement Authorities:</strong> In cases of security breaches, investigations, or legal mandates.</li>
                    <li><strong>Payment Gateways:</strong> For processing secure digital payments for our services.</li>
                </ul>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">5. Contact Us</h4>
                <p>If you have any questions or concerns regarding this Privacy Policy or data handling, please reach out to our administration at:</p>
                <div class="p-4 mt-4 rounded-3 bg-light-custom" style="border-left: 4px solid var(--secondary-color);">
                    <p class="mb-1 text-primary-dark"><strong>SS Bouncers</strong></p>
                    <p class="mb-1 text-muted"><strong>Address:</strong> Padmanagar opposite venkateshwara swamy temple, Hyderabad, Telangana - 500085.</p>
                    <p class="mb-1 text-muted"><strong>Phone:</strong> +91 7097059293</p>
                    <p class="mb-0 text-muted"><strong>Email:</strong> sirajshaik225@gmail.com</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>