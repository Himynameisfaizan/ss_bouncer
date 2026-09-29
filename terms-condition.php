<?php 
require_once 'config/connect.php';
$pageTitle = "Terms & Conditions | SS Bouncers";
include 'includes/header.php'; 
?>

<section class="page-header position-relative py-5">
    <div class="page-header-bg"></div>
    <div class="container position-relative z-2 py-4 text-center">
        <h1 class="display-4 fw-bold text-white mb-0">Terms & Conditions</h1>
    </div>
</section>

<section class="py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
            <div class="col-12 policy-content">
                <p class="text-muted mb-5"><strong>Last Updated:</strong> <?= date('F d, Y'); ?></p>

                <h4 class="mt-4 text-primary-dark fw-bold border-bottom pb-2">1. Introduction</h4>
                <p class="text-muted">Welcome to <strong>SS Bouncers</strong>. By accessing our website, hiring our security guards, bouncers, or utilizing our housekeeping services, you agree to comply with and be bound by the following Terms & Conditions. Please read them carefully before signing any service contract.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">2. Service Scope and Limitations</h4>
                <p class="text-muted">We provide professional manpower for security, vigilance, and facility management. Our personnel are trained to observe, deter, and report. However, SS Bouncers does not act as law enforcement. In extreme emergencies involving criminal activity, our personnel are instructed to protect life first and immediately coordinate with local police authorities.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">3. Deployment & Working Hours</h4>
                <p class="text-muted">Deployment of security guards and bouncers is subject to the signing of an official service agreement outlining specific shifts (8 hours, 12 hours, or 24/7 rotations). Clients must ensure a safe and humane working environment for deployed personnel, including access to basic amenities (shelter, drinking water, and washroom facilities).</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">4. Payment Terms</h4>
                <p class="text-muted">Invoices for contractual security services are generated monthly. Payment must be cleared within the stipulated time frame mentioned in your service agreement. For short-term event bouncer deployments, an advance payment is required before personnel are dispatched.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">5. Liability Disclaimer</h4>
                <p class="text-muted">While SS Bouncers implements strict background checks and verification protocols, the agency shall not be held liable for any loss of property, damage, or theft occurring on the client's premises, provided our personnel have adhered to all agreed-upon SOPs and vigilance protocols.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">6. Governing Law & Jurisdiction</h4>
                <p class="text-muted">These terms are governed by the laws of India. Any disputes arising out of service agreements or website usage shall be subject to the exclusive jurisdiction of the competent courts in <strong>Hyderabad, Telangana, India</strong>.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>